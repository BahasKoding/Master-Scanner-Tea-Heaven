<?php

namespace App\Http\Controllers;

use App\Models\CategoryProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class CategoryProductController extends Controller
{
    /**
     * Constructor to apply permissions middleware
     */
    public function __construct()
    {
        $this->middleware('permission:Category Products List', ['only' => ['index']]);
        $this->middleware('permission:Category Products Create', ['only' => ['store']]);
        $this->middleware('permission:Category Products Update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:Category Products Delete', ['only' => ['destroy']]);
        $this->middleware('permission:Category Products View', ['only' => ['show']]);
    }

    /**
     * Custom validation messages
     */
    private function getValidationMessages()
    {
        return [
            'name.required' => 'Silahkan masukkan nama kategori',
            'name.string' => 'Nama kategori harus berupa teks',
            'name.max' => 'Nama kategori terlalu panjang (maksimal 255 karakter)',
            'name.unique' => 'Nama kategori ini sudah digunakan. Silahkan gunakan nama yang berbeda',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = CategoryProduct::select([
                'category_products.id',
                'category_products.name',
                'category_products.created_at',
                'category_products.updated_at'
            ]);

            return DataTables::of($query)
                ->addIndexColumn()
                ->orderColumn('name', function ($query, $order) {
                    $query->orderBy('name', $order);
                })
                ->addColumn('action', function ($row) {
                    return $row->id;
                })
                ->filterColumn('name', function ($query, $keyword) {
                    $query->where('category_products.name', 'like', "%{$keyword}%");
                })
                ->rawColumns(['action'])
                ->smart(true)
                ->startsWithSearch()
                ->make(true);
        }

        try {
            // Get initial data for the view with pagination
            $items = [
                'Daftar Kategori Produk' => route('category-products.index'),
            ];

            // Log activity
            addActivity('category_product', 'view', 'Pengguna melihat daftar kategori produk', null);

            return view('category-product.index', compact('items'));
        } catch (\Exception $e) {
            // Log the error
            Log::error('Failed to load CategoryProduct index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return view with error message
            return view('category-product.index', [
                'items' => ['Daftar Kategori Produk' => route('category-products.index')],
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
                'name' => 'required|string|max:255|unique:category_products',
            ], $this->getValidationMessages());

            $categoryProduct = CategoryProduct::create($validated);

            // Log activity
            addActivity('category_product', 'create', 'Pengguna membuat kategori produk baru: ' . $categoryProduct->name, $categoryProduct->id);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Kategori telah ditambahkan ke dalam sistem.',
                'data' => $categoryProduct
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
                'message' => 'Maaf! Terjadi kesalahan saat menambahkan kategori. Silahkan coba lagi.'
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CategoryProduct $categoryProduct)
    {
        try {
            Log::info('Permintaan edit kategori produk diterima', ['categoryProduct' => $categoryProduct->toArray()]);

            // Log activity
            addActivity('category_product', 'edit', 'Pengguna melihat form edit kategori produk: ' . $categoryProduct->name, $categoryProduct->id);

            return response()->json([
                'success' => true,
                'data' => $categoryProduct
            ]);
        } catch (\Exception $e) {
            Log::error('Error pada edit kategori produk', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Maaf! Kami tidak dapat menemukan informasi kategori. Silahkan muat ulang dan coba lagi.'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CategoryProduct $categoryProduct)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:category_products,name,' . $categoryProduct->id,
            ], $this->getValidationMessages());

            $oldName = $categoryProduct->name;

            $categoryProduct->update($validated);

            // Log activity
            addActivity(
                'category_product',
                'update',
                'Pengguna mengubah kategori produk dari "' . $oldName . '" menjadi "' . $categoryProduct->name . '"',
                $categoryProduct->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Informasi kategori telah diperbarui.',
                'data' => $categoryProduct
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
                'message' => 'Maaf! Terjadi kesalahan saat memperbarui kategori. Silahkan coba lagi.'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CategoryProduct $categoryProduct)
    {
        try {
            $categoryName = $categoryProduct->name;
            $categoryId = $categoryProduct->id;

            // Check if this category is being used by any products
            $productCount = $categoryProduct->products()->count();

            if ($productCount > 0) {
                // Get product names to show in the error message
                $products = $categoryProduct->products()
                    ->select('name')
                    ->take(3)
                    ->get()
                    ->pluck('name')
                    ->toArray();

                $productNames = count($products) > 0
                    ? '"' . implode('", "', $products) . '"' . (count($products) < $productCount ? ' dan ' . ($productCount - count($products)) . ' lainnya' : '')
                    : '';

                // Category is in use, prevent deletion
                // Log activity
                addActivity('category_product', 'delete_failed', 'Pengguna mencoba menghapus kategori produk yang sedang digunakan: ' . $categoryName, $categoryId);

                return response()->json([
                    'success' => false,
                    'message' => 'Kategori ini tidak dapat dihapus karena sedang digunakan oleh ' . $productCount . ' produk' . ($productCount > 1 ? '' : '') .
                        ($productNames ? ' termasuk: ' . $productNames : '') . '. Silahkan ubah kategori atau hapus produk tersebut terlebih dahulu.',
                    'productCount' => $productCount
                ], 422);
            }

            $categoryProduct->delete();

            // Log activity
            addActivity('category_product', 'delete', 'Pengguna menghapus kategori produk: ' . $categoryName, $categoryId);

            return response()->json([
                'success' => true,
                'message' => 'Kategori telah berhasil dihapus dari sistem.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Maaf! Kami tidak dapat menghapus kategori saat ini. Silahkan coba lagi.'
            ], 500);
        }
    }

    /**
     * List all products for a specific category
     */
    public function listProducts(CategoryProduct $categoryProduct)
    {
        try {
            // Get all products for this category with pagination
            $products = $categoryProduct->products()
                ->select(['id', 'sku as code', 'name_product as name', 'packaging as unit'])
                ->orderBy('name_product')
                ->paginate(15);

            // Log activity
            addActivity('category_product', 'list_products', 'Pengguna melihat produk untuk kategori: ' . $categoryProduct->name, $categoryProduct->id);

            return response()->json([
                'success' => true,
                'data' => [
                    'category' => $categoryProduct->name,
                    'products' => $products
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error listing products for category: ', [
                'category_id' => $categoryProduct->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data produk: ' . $e->getMessage()
            ], 500);
        }
    }
}
