<?php

namespace App\Http\Controllers;

use App\Models\FinishedGoods;
use App\Models\Product;
use App\Models\CatatanProduksi;
use App\Models\HistorySale;
use App\Services\FinishedGoodsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\ValidationException;

class FinishedGoodsController extends Controller
{
    protected $finishedGoodsService;

    /**
     * Constructor to apply permissions middleware and inject service
     */
    public function __construct(FinishedGoodsService $finishedGoodsService)
    {
        $this->finishedGoodsService = $finishedGoodsService;

        $this->middleware('permission:Finished Goods List', ['only' => ['index']]);
        $this->middleware('permission:Finished Goods Create', ['only' => ['store']]);
        $this->middleware('permission:Finished Goods Update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:Finished Goods Delete', ['only' => ['destroy']]);
        $this->middleware('permission:Finished Goods View', ['only' => ['show']]);
    }

    /**
     * Custom validation messages
     */
    private function getValidationMessages()
    {
        return [
            'product_id.required' => 'Silahkan pilih produk',
            'product_id.exists' => 'Produk yang dipilih tidak ditemukan',

            'stok_awal.required' => 'Silahkan masukkan stok awal',
            'stok_awal.integer' => 'Stok awal harus berupa angka bulat',
            'stok_awal.min' => 'Stok awal minimal 0',

            'defective.required' => 'Silahkan masukkan jumlah produk cacat',
            'defective.integer' => 'Jumlah produk cacat harus berupa angka bulat',
            'defective.min' => 'Jumlah produk cacat minimal 0',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                // Query semua produk dengan LEFT JOIN ke finished_goods
                $query = Product::leftJoin('finished_goods', 'products.id', '=', 'finished_goods.product_id')
                    ->select([
                        'products.id as product_id',
                        'products.sku',
                        'products.name_product',
                        'products.packaging',
                        'products.category_product',
                        'products.label',
                        'finished_goods.id as finished_goods_id',
                        'finished_goods.stok_awal',
                        'finished_goods.stok_masuk',
                        'finished_goods.stok_keluar',
                        'finished_goods.defective',
                        'finished_goods.live_stock',
                        'finished_goods.created_at as fg_created_at',
                        'finished_goods.updated_at as fg_updated_at'
                    ]);

                // Apply filters
                if ($request->has('product_id') && !empty($request->product_id)) {
                    $query->where('products.id', $request->product_id);
                }

                if ($request->has('category_product') && !empty($request->category_product)) {
                    $query->where('products.category_product', $request->category_product);
                }

                if ($request->has('label') && !empty($request->label)) {
                    $query->where('products.label', $request->label);
                }

                if ($request->has('sku') && !empty($request->sku)) {
                    $query->where('products.sku', 'like', '%' . $request->sku . '%');
                }

                $dataTable = DataTables::of($query)
                    ->addIndexColumn()
                    ->orderColumn('name_product', function ($query, $order) {
                        $query->orderBy('products.name_product', $order);
                    })
                    ->addColumn('action', function ($row) {
                        return $row->product_id;
                    })
                    ->addColumn('category_name', function ($row) {
                        try {
                            $categories = Product::getCategoryOptions();
                            return $categories[$row->category_product] ?? 'Unknown Category';
                        } catch (\Exception $e) {
                            Log::error('Error getting category name', ['error' => $e->getMessage()]);
                            return 'Unknown Category';
                        }
                    })
                    ->addColumn('label_name', function ($row) {
                        try {
                            $labels = Product::getLabelOptions();
                            return $labels[$row->label] ?? '-';
                        } catch (\Exception $e) {
                            Log::error('Error getting label name', ['error' => $e->getMessage()]);
                            return '-';
                        }
                    })
                    ->addColumn('stok_awal_display', function ($row) {
                        return $row->stok_awal ?? 0;
                    })
                    ->addColumn('stok_masuk_display', function ($row) {
                        // Show dynamic value from CatatanProduksi
                        try {
                            $dynamicValue = CatatanProduksi::where('product_id', $row->product_id)->sum('quantity');
                            return $dynamicValue;
                        } catch (\Exception $e) {
                            Log::error('Error calculating dynamic stok_masuk', ['error' => $e->getMessage()]);
                            return $row->stok_masuk ?? 0;
                        }
                    })
                    ->addColumn('stok_keluar_display', function ($row) {
                        // Show dynamic value from HistorySale with improved validation
                        try {
                            $product = Product::find($row->product_id);
                            if (!$product) {
                                Log::warning('Product not found for stok_keluar calculation', ['product_id' => $row->product_id]);
                                return $row->stok_keluar ?? 0;
                            }

                            $totalSales = 0;
                            $historySales = HistorySale::whereNotNull('no_sku')->get();

                            foreach ($historySales as $sale) {
                                try {
                                    $skuArray = is_string($sale->no_sku) ? json_decode($sale->no_sku, true) : $sale->no_sku;
                                    $qtyArray = is_string($sale->qty) ? json_decode($sale->qty, true) : $sale->qty;

                                    // Validate data integrity
                                    if (!is_array($skuArray) || !is_array($qtyArray)) {
                                        Log::warning('Invalid SKU or QTY data in HistorySale', [
                                            'sale_id' => $sale->id,
                                            'no_sku' => $sale->no_sku,
                                            'qty' => $sale->qty
                                        ]);
                                        continue;
                                    }

                                    // Ensure arrays have same length
                                    if (count($skuArray) !== count($qtyArray)) {
                                        Log::warning('SKU and QTY arrays length mismatch', [
                                            'sale_id' => $sale->id,
                                            'sku_count' => count($skuArray),
                                            'qty_count' => count($qtyArray)
                                        ]);
                                        continue;
                                    }

                                    foreach ($skuArray as $index => $sku) {
                                        // Validate SKU exists in products table
                                        if (trim($sku) === $product->sku) {
                                            $quantity = $qtyArray[$index] ?? 0;
                                            if (is_numeric($quantity) && $quantity > 0) {
                                                $totalSales += (int)$quantity;
                                            }
                                        }
                                    }
                                } catch (\Exception $saleError) {
                                    Log::error('Error processing individual sale for stok_keluar', [
                                        'sale_id' => $sale->id,
                                        'error' => $saleError->getMessage()
                                    ]);
                                    continue;
                                }
                            }

                            return $totalSales;
                        } catch (\Exception $e) {
                            Log::error('Error calculating dynamic stok_keluar', [
                                'product_id' => $row->product_id,
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString()
                            ]);
                            return $row->stok_keluar ?? 0;
                        }
                    })
                    ->addColumn('defective_display', function ($row) {
                        return $row->defective ?? 0;
                    })
                    ->addColumn('live_stock_display', function ($row) {
                        // Calculate dynamic live stock with improved validation
                        try {
                            $stokAwal = $row->stok_awal ?? 0;
                            $stokMasuk = CatatanProduksi::where('product_id', $row->product_id)->sum('quantity');
                            $defective = $row->defective ?? 0;

                            $product = Product::find($row->product_id);
                            $stokKeluar = 0;

                            if ($product) {
                                $historySales = HistorySale::whereNotNull('no_sku')->get();

                                foreach ($historySales as $sale) {
                                    try {
                                        $skuArray = is_string($sale->no_sku) ? json_decode($sale->no_sku, true) : $sale->no_sku;
                                        $qtyArray = is_string($sale->qty) ? json_decode($sale->qty, true) : $sale->qty;

                                        // Validate data integrity
                                        if (!is_array($skuArray) || !is_array($qtyArray)) {
                                            continue;
                                        }

                                        // Ensure arrays have same length
                                        if (count($skuArray) !== count($qtyArray)) {
                                            continue;
                                        }

                                        foreach ($skuArray as $index => $sku) {
                                            if (trim($sku) === $product->sku) {
                                                $quantity = $qtyArray[$index] ?? 0;
                                                if (is_numeric($quantity) && $quantity > 0) {
                                                    $stokKeluar += (int)$quantity;
                                                }
                                            }
                                        }
                                    } catch (\Exception $saleError) {
                                        // Skip problematic sales data
                                        continue;
                                    }
                                }
                            }

                            $liveStock = $stokAwal + $stokMasuk - $stokKeluar - $defective;
                            return max(0, $liveStock); // Ensure not negative
                        } catch (\Exception $e) {
                            Log::error('Error calculating dynamic live_stock', [
                                'product_id' => $row->product_id,
                                'error' => $e->getMessage()
                            ]);
                            return $row->live_stock ?? 0;
                        }
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

                return $dataTable;
            } catch (\Exception $e) {
                Log::error('DataTables Ajax error in FinishedGoods index', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'request' => $request->all()
                ]);

                return response()->json([
                    'draw' => intval($request->get('draw')),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'Terjadi kesalahan saat memuat data. Silakan refresh halaman atau hubungi administrator.'
                ], 200); // Return 200 to prevent DataTables error popup
            }
        }

        try {
            // Get all products for dropdown
            $products = Product::orderBy('name_product')->get();

            // Get category options
            $categories = Product::getCategoryOptions();

            // Get label options
            $labels = Product::getLabelOptions();

            // Get initial data for the view with pagination
            $items = [
                'Daftar Finished Goods' => route('finished-goods.index'),
            ];

            // Log activity
            addActivity('finished_goods', 'view', 'Pengguna melihat daftar finished goods', null);

            return view('finished-goods.index', compact('items', 'products', 'categories', 'labels'));
        } catch (\Exception $e) {
            // Log the error
            Log::error('Failed to load data for Finished Goods index', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return view with error message
            return view('finished-goods.index', [
                'items' => ['Daftar Finished Goods' => route('finished-goods.index')],
                'products' => [],
                'categories' => [],
                'labels' => [],
                'error_message' => 'Gagal memuat data. Silakan coba refresh halaman.'
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
                'product_id' => 'required|exists:products,id',
                'stok_awal' => 'required|integer|min:0',
                'defective' => 'required|integer|min:0',
            ], $this->getValidationMessages());

            // Use FinishedGoodsService for consistent transaction handling
            $finishedGoods = $this->finishedGoodsService->createOrUpdateFinishedGoods($validated);

            // Get product name for logging
            $productName = Product::find($validated['product_id'])->name_product;

            // Log activity
            $action = $finishedGoods->wasRecentlyCreated ? 'create' : 'update';
            $message = $finishedGoods->wasRecentlyCreated
                ? 'Pengguna membuat finished goods baru untuk produk: ' . $productName
                : 'Pengguna memperbarui finished goods untuk produk: ' . $productName;

            addActivity('finished_goods', $action, $message, $finishedGoods->id);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Data stok finished goods telah diperbarui dengan service layer. Stok masuk dan keluar dihitung otomatis.',
                'data' => [
                    'id' => $finishedGoods->id,
                    'product_id' => $finishedGoods->product_id,
                    'stok_awal' => $finishedGoods->stok_awal,
                    'stok_masuk' => $finishedGoods->stok_masuk,
                    'stok_keluar' => $finishedGoods->stok_keluar,
                    'defective' => $finishedGoods->defective,
                    'live_stock' => $finishedGoods->live_stock,
                    'stok_masuk_dynamic' => $finishedGoods->stok_masuk_dynamic,
                    'stok_keluar_dynamic' => $finishedGoods->stok_keluar_dynamic,
                    'live_stock_dynamic' => $finishedGoods->live_stock_dynamic,
                ]
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi Kesalahan',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error saat memperbarui finished goods', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Maaf! Terjadi kesalahan saat memperbarui finished goods. Silahkan coba lagi.'
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($productId)
    {
        try {
            // Use FinishedGoodsService to get product data
            $finishedGoods = $this->finishedGoodsService->getFinishedGoodsForProduct($productId);

            // Prepare response data with both static and dynamic values
            $responseData = [
                'id' => $finishedGoods->id,
                'product_id' => $finishedGoods->product_id,
                'stok_awal' => $finishedGoods->stok_awal,
                'stok_masuk' => $finishedGoods->stok_masuk,
                'stok_keluar' => $finishedGoods->stok_keluar,
                'defective' => $finishedGoods->defective,
                'live_stock' => $finishedGoods->live_stock,
                'stok_masuk_dynamic' => $finishedGoods->stok_masuk_dynamic,
                'stok_keluar_dynamic' => $finishedGoods->stok_keluar_dynamic,
                'live_stock_dynamic' => $finishedGoods->live_stock_dynamic,
                'product_name' => $finishedGoods->product->name_product,
                'product_sku' => $finishedGoods->product->sku,
            ];

            Log::info('Permintaan edit finished goods diterima via service', [
                'product_id' => $productId,
                'product_name' => $finishedGoods->product->name_product,
                'finished_goods' => $responseData
            ]);

            // Log activity
            addActivity('finished_goods', 'edit', 'Pengguna melihat form edit finished goods untuk produk: ' . $finishedGoods->product->name_product, $productId);

            return response()->json([
                'success' => true,
                'data' => $responseData
            ]);
        } catch (\Exception $e) {
            Log::error('Error pada edit finished goods', ['error' => $e->getMessage()]);

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
            // Find the product first
            $product = Product::findOrFail($id);

            $validated = $request->validate([
                'stok_awal' => 'required|integer|min:0',
                'defective' => 'required|integer|min:0',
            ], $this->getValidationMessages());

            // Get existing finished goods record or create new
            $finishedGoods = FinishedGoods::firstOrNew(['product_id' => $id]);

            // Use FinishedGoodsService for consistent transaction handling
            $finishedGoods = $this->finishedGoodsService->updateFinishedGoods($finishedGoods, $validated);

            // Log activity
            $action = $finishedGoods->wasRecentlyCreated ? 'create' : 'update';
            $message = $finishedGoods->wasRecentlyCreated
                ? 'Pengguna membuat finished goods baru untuk produk: ' . $product->name_product
                : 'Pengguna memperbarui finished goods untuk produk: ' . $product->name_product;

            addActivity('finished_goods', $action, $message . ' (via service layer)', $finishedGoods->id);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Data stok finished goods telah diperbarui dengan service layer.',
                'data' => [
                    'id' => $finishedGoods->id,
                    'product_id' => $finishedGoods->product_id,
                    'stok_awal' => $finishedGoods->stok_awal,
                    'stok_masuk' => $finishedGoods->stok_masuk,
                    'stok_keluar' => $finishedGoods->stok_keluar,
                    'defective' => $finishedGoods->defective,
                    'live_stock' => $finishedGoods->live_stock,
                    'updated_at' => $finishedGoods->updated_at->format('Y-m-d H:i:s')
                ]
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi Kesalahan',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error saat memperbarui finished goods', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Maaf! Terjadi kesalahan saat memperbarui finished goods. Silahkan coba lagi.'
            ], 500);
        }
    }

    /**
     * Get finished goods data for DataTables.
     */
    public function data(Request $request)
    {
        return $this->index($request);
    }

    /**
     * Sync finished goods stock data for consistency check
     */
    public function sync(Request $request)
    {
        try {
            $productId = $request->get('product_id');

            $syncResults = $this->finishedGoodsService->syncFinishedGoodsStock($productId);

            $successCount = collect($syncResults)->where('status', 'success')->count();
            $errorCount = collect($syncResults)->where('status', 'error')->count();

            // Log activity
            $scope = $productId ? 'untuk produk ID: ' . $productId : 'untuk semua produk';
            addActivity('finished_goods', 'sync', 'Pengguna melakukan sync finished goods stock ' . $scope . ' (Success: ' . $successCount . ', Error: ' . $errorCount . ')', null);

            return response()->json([
                'success' => true,
                'message' => 'Sync selesai. Berhasil: ' . $successCount . ', Gagal: ' . $errorCount,
                'results' => $syncResults
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to sync finished goods stock', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan sync data finished goods stock.'
            ], 500);
        }
    }

    /**
     * Get finished goods statistics for a specific product
     */
    public function statistics(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|integer|exists:products,id'
            ]);

            $statistics = $this->finishedGoodsService->getFinishedGoodsStatistics($validated['product_id']);

            return response()->json([
                'success' => true,
                'data' => $statistics
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get finished goods statistics', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik finished goods.'
            ], 500);
        }
    }

    /**
     * Get low stock finished goods items
     */
    public function lowStock(Request $request)
    {
        try {
            $threshold = $request->get('threshold', 10);
            $lowStockItems = $this->finishedGoodsService->getLowStockFinishedGoods($threshold);

            return response()->json([
                'success' => true,
                'data' => $lowStockItems,
                'count' => $lowStockItems->count(),
                'threshold' => $threshold
            ]);
                    } catch (\Exception $e) {
            Log::error('Failed to get low stock finished goods', [
                            'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data finished goods dengan stok rendah.'
            ], 500);
        }
    }

    /**
     * Reset finished goods to default values
     */
    public function reset($productId)
    {
        try {
            // Use FinishedGoodsService for consistent transaction handling
            $finishedGoods = $this->finishedGoodsService->resetFinishedGoods($productId);

            // Get product name for logging
            $product = Product::find($productId);

            // Log activity
            addActivity('finished_goods', 'reset', 'Pengguna mereset finished goods untuk produk: ' . $product->name_product . ' (via service layer)', $finishedGoods->id);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Data finished goods telah direset dengan service layer.',
                'data' => [
                    'id' => $finishedGoods->id,
                    'product_id' => $finishedGoods->product_id,
                    'stok_awal' => $finishedGoods->stok_awal,
                    'stok_masuk' => $finishedGoods->stok_masuk,
                    'stok_keluar' => $finishedGoods->stok_keluar,
                    'defective' => $finishedGoods->defective,
                    'live_stock' => $finishedGoods->live_stock,
                    'updated_at' => $finishedGoods->updated_at->format('Y-m-d H:i:s')
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat reset finished goods via service', [
                'product_id' => $productId,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Maaf! Terjadi kesalahan saat reset finished goods. Silahkan coba lagi.'
            ], 500);
        }
    }

    /**
     * Verify stock consistency
     */
    public function verifyConsistency(Request $request)
    {
        try {
            $productId = $request->get('product_id');
            $consistencyCheck = $this->finishedGoodsService->verifyStockConsistency($productId);

            return response()->json([
                'success' => true,
                'data' => $consistencyCheck
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to verify finished goods stock consistency', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan verifikasi konsistensi stock finished goods.'
            ], 500);
        }
    }
}
