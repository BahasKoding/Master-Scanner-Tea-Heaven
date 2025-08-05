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

                $this->applyFilters($query, $request);

                return $this->buildDataTableResponse($query, $request);
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
                ], 200);
            }
        }

        return $this->renderView();
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

            // Verify numeric values before proceeding
            if (!is_numeric($validated['stok_awal']) || !is_numeric($validated['defective'])) {
                throw new \InvalidArgumentException('Stok awal dan defective harus berupa angka yang valid');
            }

            // Get existing finished goods record or create new
            $finishedGoods = FinishedGoods::firstOrNew(['product_id' => $id]);

            try {
                // Use FinishedGoodsService for consistent transaction handling
                $finishedGoods = $this->finishedGoodsService->updateFinishedGoods($finishedGoods, $validated);
            } catch (\Exception $serviceError) {
                throw new \RuntimeException('Terjadi kesalahan saat memproses data. ' . $serviceError->getMessage());
            }

            // Log activity
            $action = $finishedGoods->wasRecentlyCreated ? 'create' : 'update';
            $message = $finishedGoods->wasRecentlyCreated
                ? 'Pengguna membuat finished goods baru untuk produk: ' . $product->name_product
                : 'Pengguna memperbarui finished goods untuk produk: ' . $product->name_product;

            addActivity('finished_goods', $action, $message, $finishedGoods->id);
            
            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Data stok finished goods telah diperbarui.',
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
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Format data tidak valid. Pastikan stok awal dan defective berupa angka.',
                'error' => $e->getMessage()
            ], 400);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Terjadi kesalahan saat memproses data. Silahkan coba lagi.'
            ], 500);
        } catch (\Exception $e) {
            Log::error('Unexpected error during finished goods update', [
                'product_id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Maaf! Terjadi kesalahan yang tidak terduga. Silahkan coba lagi.'
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
     * Implements chunked processing to prevent timeout with large datasets
     */
    public function sync(Request $request)
    {
        try {
            // Get parameters for chunked processing
            $productId = $request->get('product_id');
            $chunkSize = $request->get('chunk_size', 50); // Default 50 records per chunk
            $offset = $request->get('offset', 0); // Starting offset
            $totalRecords = $request->get('total_records'); // Total records to process
            
            // Process chunk
            $syncResults = $this->finishedGoodsService->syncFinishedGoodsStock(
                $productId,
                $chunkSize,
                $offset,
                $totalRecords
            );
            
            // If this is the first request (offset=0) or the last chunk, log the activity
            if ($offset == 0 || $syncResults['completed']) {
                $scope = $productId ? 'untuk produk ID: ' . $productId : 'untuk semua produk';
                $progressInfo = $syncResults['completed'] ? 
                    '(Completed 100%)' : 
                    '(Started, estimated ' . $syncResults['total_records'] . ' records)';
                    
                addActivity(
                    'finished_goods', 
                    'sync', 
                    'Pengguna melakukan sync finished goods stock ' . $scope . ' ' . $progressInfo, 
                    null
                );
            }

            return response()->json($syncResults);
        } catch (\Exception $e) {
            Log::error('Failed to sync finished goods stock', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan sync data finished goods stock: ' . $e->getMessage(),
                'error' => $e->getMessage()
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
    
    /**
     * Test endpoint to verify update route functionality
     * This is for diagnostic purposes only
     */
    public function testUpdate($productId)
    {

        
        return response()->json([
            'success' => true,
            'message' => 'Test route for updates is working properly',
            'product_id' => $productId,
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);
    }

    /* helper method Arif */
        protected function applyFilters($query, $request)
    {
        $filters = [
            'product_id' => 'products.id',
            'category_product' => 'products.category_product',
            'label' => 'products.label',
            'sku' => 'products.sku'
        ];

        foreach ($filters as $param => $column) {
            if ($request->has($param) && !empty($request->$param)) {
                $query->where($column, $param === 'sku' ? 'like' : '=', 
                    $param === 'sku' ? '%'.$request->$param.'%' : $request->$param);
            }
        }
    }

    protected function buildDataTableResponse($query, $request)
    {
        return DataTables::of($query)
            ->addIndexColumn()
            ->orderColumn('name_product', fn($query, $order) => $query->orderBy('products.name_product', $order))
            ->addColumn('action', fn($row) => $row->product_id)
            ->addColumn('category_name', fn($row) => $this->getCategoryName($row))
            ->addColumn('label_name', fn($row) => $this->getLabelName($row))
            ->addColumn('stok_awal_display', fn($row) => $row->stok_awal ?? 0)
            ->addColumn('stok_masuk_display', fn($row) => $this->getStokMasuk($row))
            ->addColumn('stok_keluar_display', fn($row) => $this->getStokKeluar($row))
            ->addColumn('defective_display', fn($row) => $row->defective ?? 0)
            ->addColumn('live_stock_display', fn($row) => $this->getLiveStock($row))
            ->filterColumn('name_product', fn($query, $keyword) => $query->where('products.name_product', 'like', "%{$keyword}%"))
            ->filterColumn('sku', fn($query, $keyword) => $query->where('products.sku', 'like', "%{$keyword}%"))
            ->rawColumns(['action'])
            ->smart(true)
            ->startsWithSearch()
            ->make(true);
    }

    protected function getCategoryName($row)
    {
        try {
            return Product::getCategoryOptions()[$row->category_product] ?? 'Unknown Category';
        } catch (\Exception $e) {
            Log::error('Error getting category name', ['error' => $e->getMessage()]);
            return 'Unknown Category';
        }
    }

    protected function getLabelName($row)
    {
        try {
            return Product::getLabelOptions()[$row->label] ?? '-';
        } catch (\Exception $e) {
            Log::error('Error getting label name', ['error' => $e->getMessage()]);
            return '-';
        }
    }

    protected function getStokMasuk($row)
    {
        try {
            return CatatanProduksi::where('product_id', $row->product_id)->sum('quantity');
        } catch (\Exception $e) {
            Log::error('Error calculating dynamic stok_masuk', ['error' => $e->getMessage()]);
            return $row->stok_masuk ?? 0;
        }
    }

    protected function getStokKeluar($row)
    {
        try {
            if ($row->finished_goods_id && $row->stok_keluar !== null) {
                return $row->stok_keluar;
            }

            $product = Product::find($row->product_id);
            if (!$product) return 0;

            return HistorySale::whereNotNull('no_sku')->get()->reduce(function($total, $sale) use ($product) {
                try {
                    $skuArray = is_string($sale->no_sku) ? json_decode($sale->no_sku, true) : $sale->no_sku;
                    $qtyArray = is_string($sale->qty) ? json_decode($sale->qty, true) : $sale->qty;

                    if (!is_array($skuArray) || !is_array($qtyArray) || count($skuArray) !== count($qtyArray)) {
                        return $total;
                    }

                    foreach ($skuArray as $index => $sku) {
                        if (trim($sku) === $product->sku) {
                            $quantity = $qtyArray[$index] ?? 0;
                            if (is_numeric($quantity) && $quantity > 0) {
                                $total += (int)$quantity;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    // Skip problematic sales data
                }
                return $total;
            }, 0);
        } catch (\Exception $e) {
            Log::error('Error calculating stok_keluar_display', [
                'product_id' => $row->product_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $row->stok_keluar ?? 0;
        }
    }

    protected function getLiveStock($row)
    {
        try {
            if ($row->finished_goods_id && $row->live_stock !== null) {
                return $row->live_stock;
            }

            $stokAwal = $row->stok_awal ?? 0;
            $stokMasuk = $this->getStokMasuk($row);
            $defective = $row->defective ?? 0;
            $stokKeluar = $this->getStokKeluar($row);

            return $stokAwal + $stokMasuk - $stokKeluar - $defective;
        } catch (\Exception $e) {
            Log::error('Error calculating live_stock_display', [
                'product_id' => $row->product_id,
                'error' => $e->getMessage()
            ]);
            return $row->live_stock ?? 0;
        }
    }

    protected function renderView()
    {
        try {
            $items = ['Daftar Finished Goods' => route('finished-goods.index')];
            $products = Product::orderBy('name_product')->get();
            $categories = Product::getCategoryOptions();
            $labels = Product::getLabelOptions();

            addActivity('finished_goods', 'view', 'Pengguna melihat daftar finished goods', null);

            return view('finished-goods.index', compact('items', 'products', 'categories', 'labels'));
        } catch (\Exception $e) {
            Log::error('Failed to load data for Finished Goods index', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('finished-goods.index', [
                'items' => ['Daftar Finished Goods' => route('finished-goods.index')],
                'products' => [],
                'categories' => [],
                'labels' => [],
                'error_message' => 'Gagal memuat data. Silakan coba refresh halaman.'
            ]);
        }
    }

}
