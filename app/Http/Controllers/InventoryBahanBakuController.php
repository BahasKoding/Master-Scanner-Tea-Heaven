<?php

namespace App\Http\Controllers;

use App\Models\InventoryBahanBaku;
use App\Models\BahanBaku;
use App\Models\CatatanProduksi;
use App\Models\Purchase;
use App\Services\InventoryBahanBakuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\ValidationException;

class InventoryBahanBakuController extends Controller
{
    protected $inventoryBahanBakuService;

    /**
     * Constructor to apply permissions middleware and inject service
     */
    public function __construct(InventoryBahanBakuService $inventoryBahanBakuService)
    {
        $this->inventoryBahanBakuService = $inventoryBahanBakuService;

        $this->middleware('permission:Inventory Bahan Baku List', ['only' => ['index', 'data']]);
        $this->middleware('permission:Inventory Bahan Baku Create', ['only' => ['store']]);
        $this->middleware('permission:Inventory Bahan Baku Update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:Inventory Bahan Baku Delete', ['only' => ['destroy']]);
        $this->middleware('permission:Inventory Bahan Baku View', ['only' => ['show']]);
    }

    /**
     * Custom validation messages
     */
    private function getValidationMessages()
    {
        return [
            'bahan_baku_id.required' => 'Silahkan pilih bahan baku',
            'bahan_baku_id.exists' => 'Bahan baku yang dipilih tidak valid',

            'stok_awal.required' => 'Silahkan masukkan stok awal',
            'stok_awal.integer' => 'Stok awal harus berupa angka bulat',
            'stok_awal.min' => 'Stok awal minimal 0',

            'stok_masuk.required' => 'Silahkan masukkan stok masuk',
            'stok_masuk.integer' => 'Stok masuk harus berupa angka bulat',
            'stok_masuk.min' => 'Stok masuk minimal 0',

            'terpakai.required' => 'Silahkan masukkan jumlah terpakai',
            'terpakai.integer' => 'Jumlah terpakai harus berupa angka bulat',
            'terpakai.min' => 'Jumlah terpakai minimal 0',

            'defect.required' => 'Silahkan masukkan jumlah defect',
            'defect.integer' => 'Jumlah defect harus berupa angka bulat',
            'defect.min' => 'Jumlah defect minimal 0',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                // Query semua bahan baku dengan LEFT JOIN ke inventory_bahan_bakus
                $query = BahanBaku::leftJoin('inventory_bahan_bakus', 'bahan_bakus.id', '=', 'inventory_bahan_bakus.bahan_baku_id')
                    ->select([
                        'bahan_bakus.id as bahan_baku_id',
                        'bahan_bakus.sku_induk',
                        'bahan_bakus.nama_barang',
                        'bahan_bakus.satuan',
                        'bahan_bakus.kategori',
                        'inventory_bahan_bakus.id as inventory_id',
                        'inventory_bahan_bakus.stok_awal',
                        'inventory_bahan_bakus.stok_masuk',
                        'inventory_bahan_bakus.terpakai',
                        'inventory_bahan_bakus.defect',
                        'inventory_bahan_bakus.stok_sisa',
                        'inventory_bahan_bakus.live_stok_gudang'
                    ]);

                $this->applyFilters($query, $request);

                return $this->buildDataTableResponse($query, $request);
            } catch (\Exception $e) {
                Log::error('Inventory Bahan Baku DataTable generation failed', [
                    'error' => $e->getMessage(),
                    'request' => $request->all()
                ]);

                return response()->json([
                    'draw' => intval($request->get('draw')),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage()
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
                'bahan_baku_id' => 'required|exists:bahan_bakus,id',
                'stok_awal' => 'required|integer|min:0',
                'defect' => 'required|integer|min:0',
            ], $this->getValidationMessages());

            // Use InventoryBahanBakuService for consistent transaction handling
            $inventoryBahanBaku = $this->inventoryBahanBakuService->createOrUpdateInventory($validated);

            // Get bahan baku name for logging
            $bahanBaku = BahanBaku::find($validated['bahan_baku_id']);

            // Log activity
            $action = $inventoryBahanBaku->wasRecentlyCreated ? 'create' : 'update';
            $message = $inventoryBahanBaku->wasRecentlyCreated
                ? 'Pengguna membuat inventory bahan baku baru untuk: ' . $bahanBaku->nama_barang
                : 'Pengguna memperbarui inventory bahan baku untuk: ' . $bahanBaku->nama_barang;

            addActivity('inventory_bahan_baku', $action, $message . ' (via service layer)', $inventoryBahanBaku->id);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Data inventory bahan baku telah diperbarui dengan service layer dan terintegrasi dengan data purchase & produksi.',
                'data' => $inventoryBahanBaku
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi Kesalahan',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error saat memperbarui inventory bahan baku via service', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Maaf! Terjadi kesalahan saat memperbarui inventory bahan baku. Silahkan coba lagi.'
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($bahanBakuId)
    {
        try {
            // Use InventoryBahanBakuService to get inventory data
            $inventoryBahanBaku = $this->inventoryBahanBakuService->getInventoryForBahanBaku($bahanBakuId);

            // Add bahan baku information to the response
            $inventoryBahanBaku->bahan_baku_id = $bahanBakuId;


            // Log activity
            addActivity('inventory_bahan_baku', 'edit', 'Pengguna melihat form edit inventory bahan baku untuk: ' . $inventoryBahanBaku->bahan_baku->nama_barang, $bahanBakuId);

            return response()->json([
                'success' => true,
                'data' => $inventoryBahanBaku
            ]);
        } catch (\Exception $e) {
            Log::error('Error pada edit inventory bahan baku via service', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Maaf! Kami tidak dapat menemukan informasi bahan baku. Silahkan muat ulang dan coba lagi.'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $bahanBakuId)
    {
        try {
            $validated = $request->validate([
                'stok_awal' => 'required|integer|min:0',
                'defect' => 'required|integer|min:0',
            ], $this->getValidationMessages());

            // Use InventoryBahanBakuService for consistent transaction handling
            $inventoryBahanBaku = $this->inventoryBahanBakuService->updateInventory($bahanBakuId, $validated);

            // Get bahan baku name for logging
            $bahanBaku = BahanBaku::find($bahanBakuId);

            // Log activity
            $action = $inventoryBahanBaku->wasRecentlyCreated ? 'create' : 'update';
            $message = $inventoryBahanBaku->wasRecentlyCreated
                ? 'Pengguna membuat inventory bahan baku baru untuk: ' . $bahanBaku->nama_barang
                : 'Pengguna memperbarui inventory bahan baku untuk: ' . $bahanBaku->nama_barang;

            addActivity('inventory_bahan_baku', $action, $message . ' (via service layer)', $inventoryBahanBaku->id);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Data inventory bahan baku telah diperbarui dengan service layer dan terintegrasi dengan data purchase & produksi.',
                'data' => $inventoryBahanBaku
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi Kesalahan',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error saat memperbarui inventory bahan baku via service', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Maaf! Terjadi kesalahan saat memperbarui inventory bahan baku. Silahkan coba lagi.'
            ], 500);
        }
    }

    /**
     * Get inventory bahan baku data for DataTables.
     */
    public function data(Request $request)
    {
        try {
            // Auto-sync inventory data to ensure consistency with latest production data
            // Commented out to prevent timeout issues during search
            // $this->autoSyncInventoryData();

            // Query semua bahan baku dengan LEFT JOIN ke inventory_bahan_bakus
            $query = BahanBaku::leftJoin('inventory_bahan_bakus', 'bahan_bakus.id', '=', 'inventory_bahan_bakus.bahan_baku_id')
                ->select([
                    'bahan_bakus.id as bahan_baku_id',
                    'bahan_bakus.sku_induk',
                    'bahan_bakus.nama_barang',
                    'bahan_bakus.satuan',
                    'bahan_bakus.kategori',
                    'inventory_bahan_bakus.id as inventory_id',
                    'inventory_bahan_bakus.stok_awal',
                    'inventory_bahan_bakus.stok_masuk',
                    'inventory_bahan_bakus.terpakai',
                    'inventory_bahan_bakus.defect',
                    'inventory_bahan_bakus.stok_sisa',
                    'inventory_bahan_bakus.live_stok_gudang'
                ]);

            // Apply filters
            if ($request->filled('kategori')) {
                $query->where('bahan_bakus.kategori', $request->kategori);
            }

            if ($request->filled('sku_induk')) {
                $query->where('bahan_bakus.sku_induk', 'like', '%' . $request->sku_induk . '%');
            }

            if ($request->filled('nama_barang')) {
                $query->where('bahan_bakus.nama_barang', 'like', '%' . $request->nama_barang . '%');
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return $row->bahan_baku_id;
                })
                ->addColumn('kategori_name', function ($row) {
                    try {
                        $categories = BahanBaku::getCategoryOptions();
                        return $categories[$row->kategori] ?? 'Unknown Category';
                    } catch (\Exception $e) {
                        Log::error('Error getting category name', ['error' => $e->getMessage()]);
                        return 'Unknown Category';
                    }
                })
                ->editColumn('stok_awal', function ($row) {
                    return intval($row->stok_awal ?? 0);
                })
                ->editColumn('stok_masuk', function ($row) {
                    return intval($row->stok_masuk ?? 0);
                })
                ->editColumn('terpakai', function ($row) {
                    return intval($row->terpakai ?? 0);
                })
                ->editColumn('defect', function ($row) {
                    return intval($row->defect ?? 0);
                })
                ->editColumn('live_stok_gudang', function ($row) {
                    return intval($row->live_stok_gudang ?? 0);
                })
                ->addColumn('status_stock', function ($row) {
                    $liveStock = intval($row->live_stok_gudang ?? 0);
                    if ($liveStock <= 10) {
                        return '<span class="badge bg-danger">Low Stock</span>';
                    } elseif ($liveStock <= 30) {
                        return '<span class="badge bg-warning">Medium Stock</span>';
                    } else {
                        return '<span class="badge bg-success">Good Stock</span>';
                    }
                })
                ->rawColumns(['status_stock'])
                ->smart(true)
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Inventory Bahan Baku DataTable generation failed', [
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
                'error' => 'Terjadi kesalahan saat memuat data. Silakan refresh halaman atau hubungi administrator. Error: ' . $e->getMessage()
            ], 200); // Return 200 to prevent DataTables error popup
        }
    }

    /**
     * Auto-sync inventory data to ensure consistency with production and purchase data
     */
    private function autoSyncInventoryData()
    {
        try {

            $bahanBakus = BahanBaku::all();
            $syncedCount = 0;

            foreach ($bahanBakus as $bahanBaku) {
                // Only recalculate if there are related production or purchase records
                $hasProductionData = \App\Models\CatatanProduksi::whereJsonContains('sku_induk', $bahanBaku->id)->exists();
                $hasPurchaseData = \App\Models\Purchase::where('bahan_baku_id', $bahanBaku->id)
                    ->where('kategori', 'bahan_baku')->exists();

                if ($hasProductionData || $hasPurchaseData) {
                    InventoryBahanBaku::recalculateStokMasukFromPurchases($bahanBaku->id);
                    InventoryBahanBaku::recalculateTerpakaiFromProduksi($bahanBaku->id);
                    $syncedCount++;
                }
            }

        } catch (\Exception $e) {
            Log::error('Auto-sync inventory failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Sync all inventory data from purchases and production
     */
    public function syncAll()
    {
        try {
            // Use InventoryBahanBakuService for consistent transaction handling
            $syncResults = $this->inventoryBahanBakuService->syncAllInventory();

            addActivity('inventory_bahan_baku', 'sync', 'Pengguna melakukan sinkronisasi semua data inventory bahan baku (' . $syncResults['synced_count'] . ' items) via service layer', null);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Semua data inventory bahan baku telah disinkronisasi dengan service layer.',
                'synced_count' => $syncResults['synced_count'],
                'total_count' => $syncResults['total_count'],
                'results' => $syncResults['results']
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat sinkronisasi inventory bahan baku via service', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Maaf! Terjadi kesalahan saat sinkronisasi data. Silahkan coba lagi.'
            ], 500);
        }
    }

    /**
     * Force sync specific bahan baku inventory
     * This method is useful when catatan produksi is deleted and we need to ensure consistency
     */
    public function forceSync(Request $request)
    {
        try {
            $bahanBakuIds = $request->input('bahan_baku_ids', []);

            // Use InventoryBahanBakuService for consistent transaction handling
            $syncResults = $this->inventoryBahanBakuService->forceSyncInventory($bahanBakuIds);

            // Log activity
            addActivity(
                'inventory_bahan_baku',
                'force_sync',
                'Pengguna melakukan force sync inventory bahan baku untuk ' . $syncResults['total_count'] . ' items via service layer',
                null
            );

            return response()->json([
                'success' => true,
                'message' => "Berhasil! {$syncResults['synced_count']} dari {$syncResults['total_count']} inventory bahan baku telah disinkronisasi dengan service layer.",
                'synced_count' => $syncResults['synced_count'],
                'total_count' => $syncResults['total_count'],
                'results' => $syncResults['results']
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat force sync inventory bahan baku via service', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Maaf! Terjadi kesalahan saat force sync data. Silahkan coba lagi.'
            ], 500);
        }
    }

    /**
     * Get low stock items
     */
    public function getLowStock($threshold = 10)
    {
        try {
            // Use InventoryBahanBakuService for consistent data handling
            $lowStockItems = $this->inventoryBahanBakuService->getLowStockItems($threshold);

            return response()->json([
                'success' => true,
                'data' => $lowStockItems,
                'count' => $lowStockItems->count(),
                'threshold' => $threshold
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting low stock items via service', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data stok rendah.'
            ], 500);
        }
    }

    /**
     * Get real-time inventory status for specific bahan baku
     */
    public function getInventoryStatus(Request $request)
    {
        try {
            $bahanBakuId = $request->input('bahan_baku_id');

            if (!$bahanBakuId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bahan baku ID diperlukan'
                ], 400);
            }

            // Use InventoryBahanBakuService for consistent data handling
            $inventoryStatus = $this->inventoryBahanBakuService->getInventoryStatus($bahanBakuId);

            return response()->json([
                'success' => true,
                'data' => $inventoryStatus
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting inventory status via service', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil status inventory.'
            ], 500);
        }
    }

    /**
     * Reset inventory bahan baku to default values
     */
    public function reset($bahanBakuId)
    {
        try {
            // Use InventoryBahanBakuService for consistent transaction handling
            $inventoryBahanBaku = $this->inventoryBahanBakuService->resetInventory($bahanBakuId);

            // Get bahan baku name for logging
            $bahanBaku = BahanBaku::find($bahanBakuId);

            // Log activity
            addActivity('inventory_bahan_baku', 'reset', 'Pengguna mereset inventory bahan baku untuk: ' . $bahanBaku->nama_barang . ' (via service layer)', $inventoryBahanBaku->id);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Data inventory bahan baku telah direset dengan service layer.',
                'data' => $inventoryBahanBaku
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat reset inventory bahan baku via service', [
                'bahan_baku_id' => $bahanBakuId,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Maaf! Terjadi kesalahan saat reset inventory bahan baku. Silahkan coba lagi.'
            ], 500);
        }
    }

    /**
     * Verify inventory consistency
     */
    public function verifyConsistency(Request $request)
    {
        try {
            $bahanBakuId = $request->get('bahan_baku_id');

            // Use InventoryBahanBakuService for consistency check
            $consistencyResults = $this->inventoryBahanBakuService->verifyInventoryConsistency($bahanBakuId);

            // Log activity
            $scope = $bahanBakuId ? 'untuk bahan baku ID: ' . $bahanBakuId : 'untuk semua bahan baku';
            addActivity('inventory_bahan_baku', 'verify_consistency', 'Pengguna melakukan verifikasi konsistensi inventory ' . $scope . ' via service layer', null);

            return response()->json([
                'success' => true,
                'data' => $consistencyResults
            ]);
        } catch (\Exception $e) {
            Log::error('Error verifying inventory consistency via service', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan verifikasi konsistensi inventory.'
            ], 500);
        }
    }

    /**
     * Render the main view with required data
     */
    protected function renderView()
    {
        try {
            // Get bahan baku options for filter
            $bahanBakus = BahanBaku::orderBy('nama_barang')->get();
            $categories = BahanBaku::getCategoryOptions();
            
            // Set default filter to current month
            $filterMonthYear = date('Y-m');

            // Log activity
            addActivity('inventory_bahan_baku', 'view', 'Pengguna melihat daftar inventory bahan baku', null);

            return view('inventory-bahan-baku.index', compact('bahanBakus', 'categories', 'filterMonthYear'))
                ->with('item', 'Inventory Bahan Baku');
        } catch (\Exception $e) {
            Log::error('Failed to load Inventory Bahan Baku index', [
                'error' => $e->getMessage()
            ]);

            return view('inventory-bahan-baku.index', [
                'bahanBakus' => [],
                'categories' => [],
                'filterMonthYear' => date('Y-m'),
                'error_message' => 'Gagal memuat data. Silakan coba refresh halaman.'
            ])->with('item', 'Inventory Bahan Baku');
        }
    }

    /**
     * Apply filters to the query
     */
    protected function applyFilters($query, $request)
    {
        // Apply existing filters
        if ($request->filled('kategori')) {
            $query->where('bahan_bakus.kategori', $request->kategori);
        }

        if ($request->filled('sku_induk')) {
            $query->where('bahan_bakus.sku_induk', 'like', '%' . $request->sku_induk . '%');
        }

        if ($request->filled('nama_barang')) {
            $query->where('bahan_bakus.nama_barang', 'like', '%' . $request->nama_barang . '%');
        }
    }

    /**
     * Build DataTable response with monthly filtering support
     */
    protected function buildDataTableResponse($query, $request)
    {
        $filterMonthYear = $request->get('filter_month_year');
        
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return $row->bahan_baku_id;
            })
            ->addColumn('kategori_name', function ($row) {
                $categories = BahanBaku::getCategoryOptions();
                return $categories[$row->kategori] ?? 'Unknown Category';
            })
            ->editColumn('stok_awal', function ($row) {
                return $row->stok_awal ?? 0;
            })
            ->addColumn('stok_masuk_display', fn($row) => $this->getStokMasuk($row, $filterMonthYear))
            ->addColumn('terpakai_display', fn($row) => $this->getTerpakai($row, $filterMonthYear))
            ->editColumn('defect', function ($row) {
                return $row->defect ?? 0;
            })
            ->editColumn('stok_sisa', function ($row) {
                return $row->stok_sisa ?? 0;
            })
            ->addColumn('live_stok_display', fn($row) => $this->getLiveStock($row, $filterMonthYear))
            ->addColumn('status_stock', function ($row) use ($filterMonthYear) {
                $liveStock = $this->getLiveStock($row, $filterMonthYear);
                if ($liveStock <= 10) {
                    return '<span class="badge bg-danger">Low Stock</span>';    
                } elseif ($liveStock <= 30) {
                    return '<span class="badge bg-warning">Medium Stock</span>';
                } else {
                    return '<span class="badge bg-success">Good Stock</span>';
                }
            })
            ->filterColumn('nama_barang', fn($query, $keyword) => $query->where('bahan_bakus.nama_barang', 'like', "%{$keyword}%"))
            ->filterColumn('sku_induk', fn($query, $keyword) => $query->where('bahan_bakus.sku_induk', 'like', "%{$keyword}%"))
            ->rawColumns(['status_stock'])
            ->smart(false) // Disable smart search for better performance
            ->make(true);
    }

    /**
     * Get stok masuk with monthly filtering
     */
    protected function getStokMasuk($row, $filterMonthYear = null)
    {
        try {
            // Use cached value if no monthly filter and value exists
            if (!$filterMonthYear && $row->inventory_id && $row->stok_masuk !== null) {
                return $row->stok_masuk;
            }

            // Optimized query with single execution
            $purchaseSum = Purchase::where('bahan_baku_id', $row->bahan_baku_id)
                ->where('kategori', 'bahan_baku')
                ->when($filterMonthYear, function($query) use ($filterMonthYear) {
                    $year = date('Y', strtotime($filterMonthYear . '-01'));
                    $month = date('m', strtotime($filterMonthYear . '-01'));
                    return $query->whereYear('created_at', $year)
                                ->whereMonth('created_at', $month);
                })
                ->sum('total_stok_masuk');
            
            return $purchaseSum;
        } catch (\Exception $e) {
            Log::error('Error calculating stok_masuk with monthly filter', [
                'bahan_baku_id' => $row->bahan_baku_id,
                'filter_month_year' => $filterMonthYear,
                'error' => $e->getMessage()
            ]);
            return $row->stok_masuk ?? 0;
        }
    }

    /**
     * Get terpakai with monthly filtering
     */
    protected function getTerpakai($row, $filterMonthYear = null)
    {
        try {
            // Use cached value if no monthly filter and value exists
            if (!$filterMonthYear && $row->inventory_id && $row->terpakai !== null) {
                return $row->terpakai;
            }

            // Optimized query with single execution
            $productionData = CatatanProduksi::whereJsonContains('sku_induk', (string)$row->bahan_baku_id)
                ->when($filterMonthYear, function($query) use ($filterMonthYear) {
                    $year = date('Y', strtotime($filterMonthYear . '-01'));
                    $month = date('m', strtotime($filterMonthYear . '-01'));
                    return $query->whereYear('created_at', $year)
                                ->whereMonth('created_at', $month);
                })
                ->select('sku_induk', 'total_terpakai')
                ->get();
            
            $total = 0;
            foreach ($productionData as $catatan) {
                $bahanBakuIds = $catatan->sku_induk ?? [];
                $totalTerpakai = $catatan->total_terpakai ?? [];
                $index = array_search((string)$row->bahan_baku_id, $bahanBakuIds);
                if ($index !== false) {
                    $total += ($totalTerpakai[$index] ?? 0);
                }
            }
            
            return $total;
        } catch (\Exception $e) {
            Log::error('Error calculating terpakai with monthly filter', [
                'bahan_baku_id' => $row->bahan_baku_id,
                'filter_month_year' => $filterMonthYear,
                'error' => $e->getMessage()
            ]);
            return $row->terpakai ?? 0;
        }
    }

    /**
     * Get live stock with monthly filtering
     */
    protected function getLiveStock($row, $filterMonthYear = null)
    {
        try {
            // If no monthly filter, use database value
            if (!$filterMonthYear && $row->inventory_id && $row->live_stok_gudang !== null) {
                return $row->live_stok_gudang;
            }

            $stokAwal = $row->stok_awal ?? 0;
            $stokMasuk = $this->getStokMasuk($row, $filterMonthYear);
            $terpakai = $this->getTerpakai($row, $filterMonthYear);
            $defect = $row->defect ?? 0;

            // return $stokAwal + $stokMasuk - $terpakai - $defect;
            return $stokAwal + $stokMasuk - $terpakai - $defect;
        } catch (\Exception $e) {
            Log::error('Error calculating live stock with monthly filter', [
                'bahan_baku_id' => $row->bahan_baku_id,
                'filter_month_year' => $filterMonthYear,
                'error' => $e->getMessage()
            ]);
            return $row->live_stok_gudang ?? 0;
        }
    }

    /**
     * Bulk update inventory bahan baku
     */
    public function bulkUpdate(Request $request)
    {
        try {
            $updates = $request->input('updates', []);
            
            if (empty($updates)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data yang akan diperbarui'
                ], 400);
            }

            // Validate each update before processing
            $validatedUpdates = [];
            $validationErrors = [];

            foreach ($updates as $index => $update) {
                try {
                    $validated = validator($update, [
                        'bahan_baku_id' => 'required|exists:bahan_bakus,id',
                        'stok_awal' => 'required|integer|min:0',
                        'defect' => 'required|integer|min:0',
                    ], $this->getValidationMessages())->validate();

                    $validatedUpdates[] = $validated;
                } catch (ValidationException $e) {
                    $validationErrors[] = [
                        'index' => $index,
                        'bahan_baku_id' => $update['bahan_baku_id'] ?? 'unknown',
                        'errors' => $e->errors()
                    ];
                }
            }

            // If there are validation errors, return them
            if (!empty($validationErrors)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terdapat kesalahan validasi pada beberapa item',
                    'validation_errors' => $validationErrors
                ], 422);
            }

            // Use InventoryBahanBakuService for bulk update with transaction handling
            $bulkResults = $this->inventoryBahanBakuService->bulkUpdateInventory($validatedUpdates);

            // Log activity
            addActivity(
                'inventory_bahan_baku', 
                'bulk_update', 
                "Pengguna melakukan bulk update inventory bahan baku: {$bulkResults['success_count']} berhasil, {$bulkResults['error_count']} gagal (via service layer)", 
                null
            );

            $message = "Bulk update selesai: {$bulkResults['success_count']} item berhasil diperbarui";
            if ($bulkResults['error_count'] > 0) {
                $message .= ", {$bulkResults['error_count']} item gagal";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $bulkResults
            ]);

        } catch (\Exception $e) {
            Log::error('Error saat bulk update inventory bahan baku via service', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Maaf! Terjadi kesalahan saat bulk update inventory bahan baku. Silahkan coba lagi.'
            ], 500);
        }
    }
}
