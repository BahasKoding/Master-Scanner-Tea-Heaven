<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\CategoryProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
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
            'id_category_product.required' => 'Silahkan pilih kategori produk',
            'id_category_product.exists' => 'Kategori yang dipilih tidak valid',
            'sku.required' => 'Silahkan masukkan SKU produk',
            'sku.string' => 'SKU produk harus berupa teks',
            'sku.max' => 'SKU produk terlalu panjang (maksimal 255 karakter)',
            'sku.unique' => 'SKU produk ini sudah digunakan. Silahkan gunakan SKU yang berbeda',
            'packaging.required' => 'Silahkan masukkan packaging produk',
            'packaging.string' => 'Packaging produk harus berupa teks',
            'packaging.max' => 'Packaging produk terlalu panjang (maksimal 255 karakter)',
            'name_product.required' => 'Silahkan masukkan nama produk',
            'name_product.string' => 'Nama produk harus berupa teks',
            'name_product.max' => 'Nama produk terlalu panjang (maksimal 255 karakter)',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Product::with('categoryProduct')
                ->select([
                    'products.id',
                    'products.id_category_product',
                    'products.sku',
                    'products.packaging',
                    'products.name_product',
                    'products.created_at',
                    'products.updated_at'
                ]);

            // Filter by category if provided
            if ($request->has('id_category_product') && !empty($request->id_category_product)) {
                $query->where('products.id_category_product', $request->id_category_product);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->orderColumn('name_product', function ($query, $order) {
                    $query->orderBy('name_product', $order);
                })
                ->addColumn('action', function ($row) {
                    return $row->id;
                })
                ->addColumn('category', function ($row) {
                    return $row->categoryProduct ? $row->categoryProduct->name : '';
                })
                ->filterColumn('name_product', function ($query, $keyword) {
                    $query->where('products.name_product', 'like', "%{$keyword}%");
                })
                ->filterColumn('sku', function ($query, $keyword) {
                    $query->where('products.sku', 'like', "%{$keyword}%");
                })
                ->filterColumn('category', function ($query, $keyword) {
                    $query->whereHas('categoryProduct', function ($q) use ($keyword) {
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
                Log::warning('No categories found in the database for Product form');
            } else {
                Log::info('Categories loaded successfully for Product form', ['count' => $categories->count()]);
            }

            // Get initial data for the view with pagination
            $items = [
                'Daftar Produk' => route('products.index'),
            ];

            // Log activity
            addActivity('product', 'view', 'Pengguna melihat daftar produk', null);

            return view('product.index', compact('items', 'categories'));
        } catch (\Exception $e) {
            // Log the error
            Log::error('Failed to load categories for Product index', [
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
                'id_category_product' => 'required|exists:category_products,id',
                'sku' => 'required|string|max:255|unique:products',
                'packaging' => 'required|string|max:255',
                'name_product' => 'required|string|max:255',
            ], $this->getValidationMessages());

            $product = Product::create($validated);

            // Get the category name for logging
            $categoryName = CategoryProduct::find($validated['id_category_product'])->name;

            // Log activity with category information
            addActivity('product', 'create', 'Pengguna membuat produk baru: ' . $product->name_product . ' dengan kategori: ' . $categoryName, $product->id);

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
            $product = Product::findOrFail($id);

            // Load the relation
            $product->load('categoryProduct');

            Log::info('Permintaan edit produk diterima', ['product' => $product->toArray()]);

            // Log activity
            addActivity('product', 'edit', 'Pengguna melihat form edit produk: ' . $product->name_product, $product->id);

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
            $product = Product::findOrFail($id);

            $validated = $request->validate([
                'id_category_product' => 'required|exists:category_products,id',
                'sku' => 'required|string|max:255|unique:products,sku,' . $product->id,
                'packaging' => 'required|string|max:255',
                'name_product' => 'required|string|max:255',
            ], $this->getValidationMessages());

            $oldName = $product->name_product;
            $oldCategoryName = $product->categoryProduct ? $product->categoryProduct->name : 'Tidak ada';

            $product->update($validated);

            // Get the new category name
            $newCategoryName = CategoryProduct::find($validated['id_category_product'])->name;

            // Log activity with category information
            addActivity(
                'product',
                'update',
                'Pengguna mengubah produk dari "' . $oldName . '" menjadi "' . $product->name_product . '" dan kategori dari "' . $oldCategoryName . '" menjadi "' . $newCategoryName . '"',
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
            $product = Product::findOrFail($id);
            $productName = $product->name_product;
            $productId = $product->id;

            $product->delete();

            // Log activity
            addActivity('product', 'delete', 'Pengguna menghapus produk: ' . $productName, $productId);

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
