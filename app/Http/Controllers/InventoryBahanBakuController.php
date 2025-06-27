<?php

namespace App\Http\Controllers;

use App\Models\InventoryBahanBaku;
use App\Models\BahanBaku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\ValidationException;

class InventoryBahanBakuController extends Controller
{
    /**
     * Constructor to apply permissions middleware
     */
    public function __construct()
    {
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
                        $categories = BahanBaku::getCategoryOptions();
                        return $categories[$row->kategori] ?? 'Unknown Category';
                    })
                    ->editColumn('stok_awal', function ($row) {
                        return $row->stok_awal ?? 0;
                    })
                    ->editColumn('stok_masuk', function ($row) {
                        return $row->stok_masuk ?? 0;
                    })
                    ->editColumn('terpakai', function ($row) {
                        return $row->terpakai ?? 0;
                    })
                    ->editColumn('defect', function ($row) {
                        return $row->defect ?? 0;
                    })
                    ->editColumn('live_stok_gudang', function ($row) {
                        return $row->live_stok_gudang ?? 0;
                    })
                    ->addColumn('status_stock', function ($row) {
                        $liveStock = $row->live_stok_gudang ?? 0;
                        if ($liveStock <= 10) {
                            return '<span class="badge bg-danger">Low Stock</span>';
                        } elseif ($liveStock <= 30) {
                            return '<span class="badge bg-warning">Medium Stock</span>';
                        } else {
                            return '<span class="badge bg-success">Good Stock</span>';
                        }
                    })
                    ->rawColumns(['action', 'status_stock'])
                    ->smart(true)
                    ->make(true);
            } catch (\Exception $e) {
                Log::error('Inventory Bahan Baku DataTable generation failed', [
                    'error' => $e->getMessage(),
                    'request' => $request->all()
                ]);

                return response()->json([
                    'error' => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage()
                ], 500);
            }
        }

        try {
            // Get bahan baku options for filter
            $bahanBakus = BahanBaku::orderBy('nama_barang')->get();
            $categories = BahanBaku::getCategoryOptions();

            // Log activity
            addActivity('inventory_bahan_baku', 'view', 'Pengguna melihat daftar inventory bahan baku', null);

            return view('inventory-bahan-baku.index', compact('bahanBakus', 'categories'))
                ->with('item', 'Inventory Bahan Baku');
        } catch (\Exception $e) {
            Log::error('Failed to load Inventory Bahan Baku index', [
                'error' => $e->getMessage()
            ]);

            return view('inventory-bahan-baku.index', [
                'bahanBakus' => [],
                'categories' => [],
                'error_message' => 'Gagal memuat data. Silakan coba refresh halaman.'
            ])->with('item', 'Inventory Bahan Baku');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'bahan_baku_id' => 'required|exists:bahan_bakus,id',
                'stok_awal' => 'required|integer|min:0',
                'defect' => 'required|integer|min:0',
            ], $this->getValidationMessages());

            // Get bahan baku for satuan
            $bahanBaku = BahanBaku::findOrFail($validated['bahan_baku_id']);

            // Use updateOrCreate to update existing record or create new one
            $inventoryBahanBaku = InventoryBahanBaku::updateOrCreate(
                ['bahan_baku_id' => $validated['bahan_baku_id']],
                [
                    'stok_awal' => $validated['stok_awal'],
                    'defect' => $validated['defect'],
                    'satuan' => $bahanBaku->satuan
                ]
            );

            // Recalculate stok_masuk and terpakai from related data
            InventoryBahanBaku::recalculateStokMasukFromPurchases($validated['bahan_baku_id']);
            InventoryBahanBaku::recalculateTerpakaiFromProduksi($validated['bahan_baku_id']);

            // Log activity
            $action = $inventoryBahanBaku->wasRecentlyCreated ? 'create' : 'update';
            $message = $inventoryBahanBaku->wasRecentlyCreated
                ? 'Pengguna membuat inventory bahan baku baru untuk: ' . $bahanBaku->nama_barang
                : 'Pengguna memperbarui inventory bahan baku untuk: ' . $bahanBaku->nama_barang;

            addActivity('inventory_bahan_baku', $action, $message, $inventoryBahanBaku->id);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Data inventory bahan baku telah diperbarui dan terintegrasi dengan data purchase & produksi.',
                'data' => $inventoryBahanBaku
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi Kesalahan',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat memperbarui inventory bahan baku', ['error' => $e->getMessage()]);
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
            // Find the bahan baku first
            $bahanBaku = BahanBaku::findOrFail($bahanBakuId);

            // Find or create default inventory bahan baku record
            $inventoryBahanBaku = InventoryBahanBaku::firstOrNew(['bahan_baku_id' => $bahanBakuId]);

            // If it's a new record, set default values
            if (!$inventoryBahanBaku->exists) {
                $inventoryBahanBaku->stok_awal = 0;
                $inventoryBahanBaku->stok_masuk = 0;
                $inventoryBahanBaku->terpakai = 0;
                $inventoryBahanBaku->defect = 0;
                $inventoryBahanBaku->live_stok_gudang = 0;
                $inventoryBahanBaku->satuan = $bahanBaku->satuan;
            }

            // Add bahan baku information to the response
            $inventoryBahanBaku->bahan_baku_id = $bahanBakuId;
            $inventoryBahanBaku->bahan_baku = $bahanBaku;

            Log::info('Permintaan edit inventory bahan baku diterima', [
                'bahan_baku_id' => $bahanBakuId,
                'nama_barang' => $bahanBaku->nama_barang,
                'inventory_bahan_baku' => $inventoryBahanBaku->toArray()
            ]);

            // Log activity
            addActivity('inventory_bahan_baku', 'edit', 'Pengguna melihat form edit inventory bahan baku untuk: ' . $bahanBaku->nama_barang, $bahanBakuId);

            return response()->json([
                'success' => true,
                'data' => $inventoryBahanBaku
            ]);
        } catch (\Exception $e) {
            Log::error('Error pada edit inventory bahan baku', ['error' => $e->getMessage()]);

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
            DB::beginTransaction();

            // Validate that the bahan baku exists
            $bahanBaku = BahanBaku::findOrFail($bahanBakuId);

            $validated = $request->validate([
                'stok_awal' => 'required|integer|min:0',
                'defect' => 'required|integer|min:0',
            ], $this->getValidationMessages());

            // Use updateOrCreate to update existing record or create new one
            $inventoryBahanBaku = InventoryBahanBaku::updateOrCreate(
                ['bahan_baku_id' => $bahanBakuId],
                [
                    'stok_awal' => $validated['stok_awal'],
                    'defect' => $validated['defect'],
                    'satuan' => $bahanBaku->satuan
                ]
            );

            // Recalculate stok_masuk and terpakai from related data
            InventoryBahanBaku::recalculateStokMasukFromPurchases($bahanBakuId);
            InventoryBahanBaku::recalculateTerpakaiFromProduksi($bahanBakuId);

            // Log activity
            $action = $inventoryBahanBaku->wasRecentlyCreated ? 'create' : 'update';
            $message = $inventoryBahanBaku->wasRecentlyCreated
                ? 'Pengguna membuat inventory bahan baku baru untuk: ' . $bahanBaku->nama_barang
                : 'Pengguna memperbarui inventory bahan baku untuk: ' . $bahanBaku->nama_barang;

            addActivity('inventory_bahan_baku', $action, $message, $inventoryBahanBaku->id);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Data inventory bahan baku telah diperbarui dan terintegrasi dengan data purchase & produksi.',
                'data' => $inventoryBahanBaku
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi Kesalahan',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat memperbarui inventory bahan baku', ['error' => $e->getMessage()]);
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
        return $this->index($request);
    }

    /**
     * Sync all inventory data from purchases and production
     */
    public function syncAll()
    {
        try {
            DB::beginTransaction();

            $bahanBakus = BahanBaku::all();
            $syncedCount = 0;

            foreach ($bahanBakus as $bahanBaku) {
                // Recalculate both stok_masuk and terpakai
                InventoryBahanBaku::recalculateStokMasukFromPurchases($bahanBaku->id);
                InventoryBahanBaku::recalculateTerpakaiFromProduksi($bahanBaku->id);
                $syncedCount++;
            }

            DB::commit();

            addActivity('inventory_bahan_baku', 'sync', 'Pengguna melakukan sinkronisasi semua data inventory bahan baku (' . $syncedCount . ' items)', null);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Semua data inventory bahan baku telah disinkronisasi dengan data purchase dan produksi.',
                'synced_count' => $syncedCount
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat sinkronisasi inventory bahan baku', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Maaf! Terjadi kesalahan saat sinkronisasi data. Silahkan coba lagi.'
            ], 500);
        }
    }

    /**
     * Get low stock items
     */
    public function getLowStock($threshold = 10)
    {
        try {
            $lowStockItems = InventoryBahanBaku::getLowStockItems($threshold);

            return response()->json([
                'success' => true,
                'data' => $lowStockItems,
                'count' => $lowStockItems->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting low stock items', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data stok rendah.'
            ], 500);
        }
    }
}
