<?php

namespace App\Http\Controllers;

use App\Models\ProductList;
use App\Models\CategoryProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\ValidationException;

class ProductListController extends Controller
{
    /**
     * Constructor to apply permissions middleware
     */
    public function __construct()
    {
        $this->middleware('permission:Product List', ['only' => ['index']]);
        $this->middleware('permission:Product Create', ['only' => ['store']]);
        $this->middleware('permission:Product Update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:Product Delete', ['only' => ['destroy']]);
        $this->middleware('permission:Product View', ['only' => ['show']]);
    }

    /**
     * Custom validation messages
     */
    private function getValidationMessages()
    {
        return [
            'category_id.required' => 'Silahkan pilih kategori produk',
            'category_id.exists' => 'Kategori yang dipilih tidak valid',
            'sku.required' => 'Silahkan masukkan SKU produk',
            'sku.string' => 'SKU produk harus berupa teks',
            'sku.max' => 'SKU produk terlalu panjang (maksimal 255 karakter)',
            'sku.unique' => 'SKU produk ini sudah digunakan. Silahkan gunakan SKU yang berbeda',
            'pack.required' => 'Silahkan masukkan pack produk',
            'pack.string' => 'Pack produk harus berupa teks',
            'pack.max' => 'Pack produk terlalu panjang (maksimal 255 karakter)',
            'product_name.required' => 'Silahkan masukkan nama produk',
            'product_name.string' => 'Nama produk harus berupa teks',
            'product_name.max' => 'Nama produk terlalu panjang (maksimal 255 karakter)',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ProductList::with('category')
                ->select([
                    'product_list.id',
                    'product_list.category_id',
                    'product_list.sku',
                    'product_list.pack',
                    'product_list.product_name',
                    'product_list.created_at',
                    'product_list.updated_at'
                ]);

            // Filter by category if provided
            if ($request->has('category_id') && !empty($request->category_id)) {
                $query->where('product_list.category_id', $request->category_id);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->orderColumn('product_name', function ($query, $order) {
                    $query->orderBy('product_name', $order);
                })
                ->addColumn('action', function ($row) {
                    return $row->id;
                })
                ->addColumn('category', function ($row) {
                    return $row->category ? $row->category->name : '';
                })
                ->filterColumn('product_name', function ($query, $keyword) {
                    $query->where('product_list.product_name', 'like', "%{$keyword}%");
                })
                ->filterColumn('sku', function ($query, $keyword) {
                    $query->where('product_list.sku', 'like', "%{$keyword}%");
                })
                ->filterColumn('category', function ($query, $keyword) {
                    $query->whereHas('category', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns(['action'])
                ->smart(true)
                ->startsWithSearch()
                ->make(true);
        }

        try {
            // Get all categories for the form with error handling
            $categories = CategoryProduct::orderBy('name')->get();

            // Log if no categories were found as this may indicate a problem
            if ($categories->isEmpty()) {
                Log::warning('No categories found in the database for ProductList form');
            } else {
                Log::info('Categories loaded successfully for ProductList form', ['count' => $categories->count()]);
            }

            // Get initial data for the view with pagination
            $items = [
                'Daftar Produk' => route('products.index'),
            ];

            // Log activity
            addActivity('product_list', 'view', 'Pengguna melihat daftar produk', null);

            return view('product.index', compact('items', 'categories'));
        } catch (\Exception $e) {
            // Log the error
            Log::error('Failed to load categories for ProductList index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return view with error message and empty categories collection
            return view('product.index', [
                'items' => ['Daftar Produk' => route('products.index')],
                'categories' => collect([]),
                'error_message' => 'Gagal memuat data kategori. Silakan coba refresh halaman.'
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'category_id' => 'required|exists:category_products,id',
                'sku' => 'required|string|max:255|unique:product_list',
                'pack' => 'required|string|max:255',
                'product_name' => 'required|string|max:255',
            ], $this->getValidationMessages());

            $product = ProductList::create($validated);

            // Get the category name for logging
            $categoryName = CategoryProduct::find($validated['category_id'])->name;

            // Log activity with category information
            addActivity('product_list', 'create', 'Pengguna membuat produk baru: ' . $product->product_name . ' dengan kategori: ' . $categoryName, $product->id);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Produk telah ditambahkan ke dalam sistem.',
                'data' => $product
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi Kesalahan',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Maaf! Terjadi kesalahan saat menambahkan produk. Silahkan coba lagi.'
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            // Find the product
            $product = ProductList::findOrFail($id);

            // Load the relation
            $product->load('category');

            Log::info('Permintaan edit produk diterima', ['product' => $product->toArray()]);

            // Log activity
            addActivity('product_list', 'edit', 'Pengguna melihat form edit produk: ' . $product->product_name, $product->id);

            return response()->json([
                'success' => true,
                'data' => $product
            ]);
        } catch (\Exception $e) {
            Log::error('Error pada edit produk', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Maaf! Kami tidak dapat menemukan informasi produk. Silahkan muat ulang dan coba lagi.'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            // Find the product
            $product = ProductList::findOrFail($id);

            $validated = $request->validate([
                'category_id' => 'required|exists:category_products,id',
                'sku' => 'required|string|max:255|unique:product_list,sku,' . $product->id,
                'pack' => 'required|string|max:255',
                'product_name' => 'required|string|max:255',
            ], $this->getValidationMessages());

            $oldName = $product->product_name;
            $oldCategoryName = $product->category ? $product->category->name : 'Tidak ada';

            $product->update($validated);

            // Get the new category name
            $newCategoryName = CategoryProduct::find($validated['category_id'])->name;

            // Log activity with category information
            addActivity(
                'product_list',
                'update',
                'Pengguna mengubah produk dari "' . $oldName . '" menjadi "' . $product->product_name . '" dan kategori dari "' . $oldCategoryName . '" menjadi "' . $newCategoryName . '"',
                $product->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Informasi produk telah diperbarui.',
                'data' => $product
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi Kesalahan',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Maaf! Terjadi kesalahan saat memperbarui produk. Silahkan coba lagi.'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // Find the product
            $product = ProductList::findOrFail($id);
            $productName = $product->product_name;
            $productId = $product->id;

            $product->delete();

            // Log activity
            addActivity('product_list', 'delete', 'Pengguna menghapus produk: ' . $productName, $productId);

            return response()->json([
                'success' => true,
                'message' => 'Produk telah berhasil dihapus dari sistem.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Maaf! Kami tidak dapat menghapus produk saat ini. Silahkan coba lagi.'
            ], 500);
        }
    }
}
