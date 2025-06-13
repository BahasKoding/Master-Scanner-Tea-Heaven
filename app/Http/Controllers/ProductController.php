<?php

namespace App\Http\Controllers;

use App\Models\Product;
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
            'category_product.required' => 'Silahkan pilih kategori produk',
            'category_product.integer' => 'Kategori yang dipilih tidak valid',
            'category_product.min' => 'Kategori yang dipilih tidak valid',
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
            'label.integer' => 'Label yang dipilih tidak valid',
            'label.min' => 'Label yang dipilih tidak valid',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Product::select([
                'products.id',
                'products.category_product',
                'products.sku',
                'products.packaging',
                'products.name_product',
                'products.label',
                'products.created_at',
                'products.updated_at'
            ]);

            // Filter by category if provided
            if ($request->has('category_product') && !empty($request->category_product)) {
                $query->where('products.category_product', $request->category_product);
            }

            // Filter by label if provided
            if ($request->has('label') && !empty($request->label)) {
                $query->where('products.label', $request->label);
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
                    return $row->category_name;
                })
                ->addColumn('label_name', function ($row) {
                    return $row->label_name;
                })
                ->filterColumn('name_product', function ($query, $keyword) {
                    $query->where('products.name_product', 'like', "%{$keyword}%");
                })
                ->filterColumn('sku', function ($query, $keyword) {
                    $query->where('products.sku', 'like', "%{$keyword}%");
                })
                ->rawColumns(['action'])
                ->smart(true)
                ->startsWithSearch()
                ->make(true);
        }

        try {
            // Get category options
            $categories = Product::getCategoryOptions();

            // Get label options
            $labels = Product::getLabelOptions();

            // Get initial data for the view with pagination
            $items = [
                'Daftar Produk' => route('products.index'),
            ];

            // Log activity
            addActivity('product', 'view', 'Pengguna melihat daftar produk', null);

            return view('product.index', compact('items', 'categories', 'labels'));
        } catch (\Exception $e) {
            // Log the error
            Log::error('Failed to load categories for Product index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return view with error message and empty categories collection
            return view('product.index', [
                'items' => ['Daftar Produk' => route('products.index')],
                'categories' => [],
                'labels' => [],
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
                'category_product' => 'required|integer|min:1',
                'sku' => 'required|string|max:255|unique:products',
                'packaging' => 'required|string|max:255',
                'name_product' => 'required|string|max:255',
                'label' => 'nullable|integer|min:0',
            ], $this->getValidationMessages());

            $product = Product::create($validated);

            // Get the category name for logging
            $categories = Product::getCategoryOptions();
            $categoryName = $categories[$validated['category_product']] ?? 'Unknown Category';

            // Get the label name for logging
            $labels = Product::getLabelOptions();
            $labelName = isset($validated['label']) ? ($labels[$validated['label']] ?? '-') : '-';

            // Log activity with category and label information
            addActivity('product', 'create', 'Pengguna membuat produk baru: ' . $product->name_product . ' dengan kategori: ' . $categoryName . ' dan label: ' . $labelName, $product->id);

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
                'category_product' => 'required|integer|min:1',
                'sku' => 'required|string|max:255|unique:products,sku,' . $product->id,
                'packaging' => 'required|string|max:255',
                'name_product' => 'required|string|max:255',
                'label' => 'nullable|integer|min:0',
            ], $this->getValidationMessages());

            $oldName = $product->name_product;
            $oldCategoryName = $product->category_name;
            $oldLabelName = $product->label_name;

            $product->update($validated);

            // Get the new category name
            $categories = Product::getCategoryOptions();
            $newCategoryName = $categories[$validated['category_product']] ?? 'Unknown Category';

            // Get the new label name
            $labels = Product::getLabelOptions();
            $newLabelName = isset($validated['label']) ? ($labels[$validated['label']] ?? '-') : '-';

            // Log activity with category and label information
            addActivity(
                'product',
                'update',
                'Pengguna mengubah produk dari "' . $oldName . '" menjadi "' . $product->name_product . '", kategori dari "' . $oldCategoryName . '" menjadi "' . $newCategoryName . '", dan label dari "' . $oldLabelName . '" menjadi "' . $newLabelName . '"',
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

    /**
     * Export all products to Excel
     */
    public function exportExcel(Request $request)
    {
        try {
            // Get all products with category information
            $query = Product::select([
                'products.id',
                'products.category_product',
                'products.sku',
                'products.packaging',
                'products.name_product',
                'products.label',
                'products.created_at',
                'products.updated_at'
            ]);

            // Apply filters if provided
            if ($request->has('category_product') && !empty($request->category_product)) {
                $query->where('products.category_product', $request->category_product);
            }

            if ($request->has('label') && !empty($request->label)) {
                $query->where('products.label', $request->label);
            }

            $products = $query->orderBy('name_product', 'asc')->get();

            // Get category and label options for mapping
            $categories = Product::getCategoryOptions();
            $labels = Product::getLabelOptions();

            // Prepare data for export
            $exportData = [];
            $exportData[] = [
                'No',
                'SKU',
                'Nama Produk',
                'Packaging',
                'Kategori',
                'Label',
                'Tanggal Dibuat',
                'Terakhir Diperbarui'
            ];

            $no = 1;
            foreach ($products as $product) {
                $categoryName = $categories[$product->category_product] ?? 'Unknown Category';
                $labelName = isset($product->label) ? ($labels[$product->label] ?? '-') : '-';

                $exportData[] = [
                    $no++,
                    $product->sku,
                    $product->name_product,
                    $product->packaging,
                    $categoryName,
                    $labelName,
                    $product->created_at ? $product->created_at->format('d/m/Y H:i:s') : '-',
                    $product->updated_at ? $product->updated_at->format('d/m/Y H:i:s') : '-'
                ];
            }

            // Generate filename with timestamp
            $timestamp = now()->format('Y-m-d_H-i-s');
            $filename = "Data_Produk_{$timestamp}.xlsx";

            // Log activity
            addActivity('product', 'export', 'Pengguna mengekspor data produk ke Excel (' . count($products) . ' produk)', null);

            // Return JSON response with data for client-side Excel generation
            return response()->json([
                'success' => true,
                'data' => $exportData,
                'filename' => $filename,
                'total_records' => count($products) - 1, // Exclude header row
                'message' => 'Data berhasil disiapkan untuk export'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to export products', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor data produk. Silakan coba lagi.'
            ], 500);
        }
    }
}
