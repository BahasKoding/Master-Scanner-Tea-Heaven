<?php

namespace App\Http\Controllers;

use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use App\Services\StockOpnameService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use Exception;

class StockOpnameController extends Controller
{
    protected $stockOpnameService;

    public function __construct(StockOpnameService $stockOpnameService)
    {
        $this->stockOpnameService = $stockOpnameService;
        $this->middleware('auth');
        $this->middleware('permission:view-stock-opname', ['only' => ['index', 'show', 'data']]);
        $this->middleware('permission:create-stock-opname', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-stock-opname', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-stock-opname', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of stock opname sessions
     */
    public function index()
    {
        return view('stock-opname.index');
    }

    /**
     * DataTables data for stock opname sessions
     */
    public function data(Request $request)
    {
        try {
            // Build base query with eager loading
            $query = StockOpname::with(['creator'])
                ->select('stock_opnames.*');

            // Apply filters
            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('tanggal_dari')) {
                $query->whereDate('tanggal_opname', '>=', $request->tanggal_dari);
            }

            if ($request->filled('tanggal_sampai')) {
                $query->whereDate('tanggal_opname', '<=', $request->tanggal_sampai);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actions = '';
                    if (auth()->user()->can('edit-stock-opname')) {
                        if ($row->status !== 'completed') {
                            $actions .= '<a href="' . route('stock-opname.show', $row->id) . '" class="btn btn-sm btn-primary me-1">Input Stok</a>';
                        } else {
                            $actions .= '<a href="' . route('stock-opname.show', $row->id) . '" class="btn btn-sm btn-info me-1">Lihat Detail</a>';
                        }
                    }
                    return $actions;
                })
                ->editColumn('type', function ($row) {
                    $typeNames = [
                        'bahan_baku' => 'Bahan Baku',
                        'finished_goods' => 'Finished Goods'
                    ];
                    return $typeNames[$row->type] ?? $row->type;
                })
                ->editColumn('status', function ($row) {
                    $statusNames = [
                        'draft' => 'Draft',
                        'in_progress' => 'Sedang Berlangsung',
                        'completed' => 'Selesai'
                    ];
                    $badges = [
                        'draft' => 'secondary',
                        'in_progress' => 'warning',
                        'completed' => 'success'
                    ];
                    $badge = $badges[$row->status] ?? 'secondary';
                    $statusName = $statusNames[$row->status] ?? $row->status;
                    return '<span class="badge bg-' . $badge . '">' . $statusName . '</span>';
                })
                ->editColumn('tanggal_opname', function ($row) {
                    return \Carbon\Carbon::parse($row->tanggal_opname)->format('d/m/Y');
                })
                ->addColumn('total_items', function ($row) {
                    return $row->items()->count();
                })
                ->addColumn('creator_name', function ($row) {
                    return $row->creator->name ?? '-';
                })
                ->editColumn('created_at', function ($row) {
                    return \Carbon\Carbon::parse($row->created_at)->format('d/m/Y H:i');
                })
                ->orderColumn('DT_RowIndex', function ($query, $order) {
                    $query->orderBy('id', $order);
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error fetching data: ' . $e->getMessage(),
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ], 500);
        }
    }

    /**
     * Show the form for creating a new stock opname
     */
    public function create()
    {
        return view('stock-opname.create');
    }

    /**
     * Store a newly created stock opname and auto-populate items
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:bahan_baku,finished_goods',
            'tanggal_opname' => 'required|date',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            $opname = $this->stockOpnameService->createStockOpname([
                'type' => $request->type,
                'tanggal_opname' => $request->tanggal_opname,
                'notes' => $request->notes
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Stock Opname berhasil dibuat',
                    'redirect_url' => route('stock-opname.show', $opname->id)
                ]);
            }

            return redirect()->route('stock-opname.show', $opname->id)
                           ->with('success', 'Stock Opname berhasil dibuat');

        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat Stock Opname: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal membuat Stock Opname: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified stock opname for physical count input
     */
    public function show(StockOpname $stockOpname)
    {
        // Refresh stok_sistem to current live stock before displaying
        $refreshResult = $this->stockOpnameService->refreshStokSistem($stockOpname);
        
        // Add notification about stock changes
        if ($refreshResult['total_changes'] > 0) {
            session()->flash('info', $refreshResult['message']);
            session()->flash('stock_changes', $refreshResult['changes']);
        }
        
        // Eager load items with their master data relationships based on opname type
        switch ($stockOpname->type) {
            case 'bahan_baku':
                $stockOpname->load(['items.bahanBaku']);
                break;
            case 'finished_goods':
                $stockOpname->load(['items.product']);
                break;
            default:
                $stockOpname->load('items');
                break;
        }
        
        return view('stock-opname.show', compact('stockOpname'));
    }

    /**
     * Update stock opname item with physical count
     */
    public function update(Request $request, StockOpname $stockOpname)
    {
        // Check if opname is still editable
        if ($stockOpname->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Stock Opname sudah selesai dan tidak dapat diubah'
            ], 422);
        }

        // Validate the items data structure
        $request->validate([
            'items' => 'required|array',
        ]);

        try {
            $updatedItems = [];
            
            // Process each item from the form data
            foreach ($request->items as $itemId => $itemData) {
                // Skip if no stok_fisik provided or if it's empty
                if (!isset($itemData['stok_fisik']) || $itemData['stok_fisik'] === '') {
                    continue;
                }
                
                // Validate individual item data
                $stokFisik = (int) $itemData['stok_fisik']; // Convert to integer
                if ($stokFisik < 0) {
                    continue; // Skip negative values
                }
                
                // Find the item
                $item = StockOpnameItem::where('id', $itemId)
                    ->where('opname_id', $stockOpname->id)
                    ->first();
                    
                if ($item) {
                    $item->stok_fisik = $stokFisik;
                    if (isset($itemData['notes'])) {
                        $item->notes = $itemData['notes'];
                    }
                    $item->save();
                    
                    $updatedItems[] = $item->id;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan (' . count($updatedItems) . ' items updated)'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update stock opname items (chunked approach to avoid max_input_vars limit)
     */
    public function bulkUpdate(Request $request, StockOpname $stockOpname)
    {
        // Check if opname is still editable
        if ($stockOpname->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Stock Opname sudah selesai dan tidak dapat diubah'
            ], 422);
        }

        // Validate the items data structure
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer',
            'items.*.stok_fisik' => 'required|numeric|min:0'
        ]);

        try {
            $updatedItems = [];
            
            DB::beginTransaction();
            
            // Process each item from the chunked data
            foreach ($request->items as $itemData) {
                $itemId = $itemData['id'];
                $stokFisik = (int) $itemData['stok_fisik']; // Convert to integer
                
                // Find the item
                $item = StockOpnameItem::where('id', $itemId)
                    ->where('opname_id', $stockOpname->id)
                    ->first();
                    
                if ($item) {
                    $item->stok_fisik = $stokFisik;
                    if (isset($itemData['notes'])) {
                        $item->notes = $itemData['notes'];
                    }
                    $item->save();
                    
                    $updatedItems[] = $item->id;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Chunk berhasil disimpan',
                'updated_count' => count($updatedItems)
            ]);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan chunk: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process/finalize the stock opname
     */
    public function process(Request $request, StockOpname $stockOpname)
    {
        if ($stockOpname->status === 'completed') {
            return redirect()->back()->with('error', 'Stock Opname sudah selesai.');
        }

        $updateStock = $request->has('update_stock');
        $resetStokAwal = $request->has('reset_stok_awal');

        DB::beginTransaction();
        try {
            if ($updateStock) {
                $this->updateSystemStock($stockOpname);
            }

            // KONDISI 1: Auto-reset stok awal when opname completed
            if ($resetStokAwal) {
                $resetResult = $this->stockOpnameService->resetStokAwalFromOpname($stockOpname);
                Log::info('StockOpname: Auto-reset stok awal completed', $resetResult);
            }

            $stockOpname->status = 'completed';
            $stockOpname->save();

            DB::commit();

            $message = $updateStock 
                ? 'Stock Opname selesai dan stok sistem telah diupdate.'
                : 'Stock Opname selesai tanpa mengupdate stok sistem.';
            
            if ($resetStokAwal) {
                $message .= ' Stok awal telah direset sesuai hasil opname.';
            }

            return redirect()->route('stock-opname.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Gagal menyelesaikan Stock Opname: ' . $e->getMessage());
        }
    }

    /**
     * Get variance analysis for stock opname
     */
    public function varianceAnalysis(StockOpname $stockOpname)
    {
        try {
            $analysis = $this->stockOpnameService->getVarianceAnalysis($stockOpname);
            $recommendations = $this->stockOpnameService->getRecommendations($stockOpname);
            
            // Check for concurrent transactions
            $concurrentWarnings = $this->stockOpnameService->checkConcurrentTransactions($stockOpname);
            
            // Get stock movement summary
            $stockMovements = $this->stockOpnameService->getStockMovementSummary($stockOpname);
            
            return response()->json([
                'status' => 'success',
                'analysis' => $analysis,
                'recommendations' => $recommendations,
                'concurrent_warnings' => $concurrentWarnings,
                'stock_movements' => $stockMovements
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menganalisis variance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update individual stock opname item
     */
    public function updateItem(Request $request, StockOpname $stockOpname, $itemId)
    {
        // Find the item manually
        $item = StockOpnameItem::where('id', $itemId)
            ->where('opname_id', $stockOpname->id)
            ->first();
            
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan dalam opname ini'
            ], 404);
        }

        // Check if opname is still editable
        if ($stockOpname->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Stock Opname sudah selesai dan tidak dapat diubah'
            ], 422);
        }

        $request->validate([
            'stok_fisik' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:255',
            'update_stok_awal' => 'nullable|boolean'
        ]);

        try {
            // Get current live stock for accurate variance calculation
            $currentLiveStock = $this->stockOpnameService->getCurrentLiveStock($stockOpname->type, $item->item_id);
            
            // Update item data with real-time stock
            $item->stok_fisik = $request->stok_fisik;
            $item->stok_sistem = $currentLiveStock; // Update to current live stock
            $item->selisih = $request->stok_fisik - $currentLiveStock; // Calculate based on live stock
            if ($request->filled('notes')) {
                $item->notes = $request->notes;
            }
            $item->save();

            // KONDISI 2: Per-row update to stok awal if requested
            if ($request->boolean('update_stok_awal')) {
                $stokAwalResult = $this->stockOpnameService->updateStokAwalPerRow($stockOpname, $item);
                Log::info('StockOpname: Per-row stok awal update completed', [
                    'item_id' => $item->item_id,
                    'item_name' => $item->item_name,
                    'result' => $stokAwalResult
                ]);
            }

            // Get calculated variance
            $selisih = $item->selisih;
            
            return response()->json([
                'success' => true,
                'message' => 'Item berhasil diupdate' . ($request->boolean('update_stok_awal') ? ' dan stok awal telah direset' : ''),
                'data' => [
                    'id' => $item->id,
                    'stok_fisik' => $item->stok_fisik,
                    'selisih' => $selisih,
                    'status' => $selisih > 0 ? 'surplus' : ($selisih < 0 ? 'kurang' : 'sesuai')
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export completed stock opname data to Excel/PDF
     */
    public function export(Request $request, StockOpname $stockOpname)
    {
        try {
            // Only allow export for completed opname
            if ($stockOpname->status !== 'completed') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Hanya stock opname yang sudah selesai yang dapat diekspor'
                ], 422);
            }

            // Set memory limit higher for large exports
            ini_set('memory_limit', '512M');

            // Get opname items - the model accessors will handle getting the correct data
            $items = $stockOpname->items()->get();

            // Type names mapping
            $typeNames = [
                'bahan_baku' => 'Bahan Baku',
                'finished_goods' => 'Finished Goods'
            ];

            // Prepare export data
            $exportData = [];
            
            // Add header information
            $exportData[] = [
                'No' => '',
                'SKU' => '',
                'Nama Item' => 'LAPORAN STOCK OPNAME',
                'Stok Sistem' => '',
                'Stok Fisik' => '',
                'Selisih' => '',
                'Satuan' => '',
                'Status' => '',
                'Catatan' => ''
            ];
            
            $exportData[] = [
                'No' => '',
                'SKU' => '',
                'Nama Item' => 'Tipe: ' . ($typeNames[$stockOpname->type] ?? $stockOpname->type),
                'Stok Sistem' => '',
                'Stok Fisik' => '',
                'Selisih' => '',
                'Satuan' => '',
                'Status' => '',
                'Catatan' => ''
            ];
            
            $exportData[] = [
                'No' => '',
                'SKU' => '',
                'Nama Item' => 'Tanggal: ' . $stockOpname->tanggal_opname->format('d/m/Y'),
                'Stok Sistem' => '',
                'Stok Fisik' => '',
                'Selisih' => '',
                'Satuan' => '',
                'Status' => '',
                'Catatan' => ''
            ];
            
            $exportData[] = [
                'No' => '',
                'SKU' => '',
                'Nama Item' => 'Dibuat oleh: ' . ($stockOpname->creator->name ?? '-'),
                'Stok Sistem' => '',
                'Stok Fisik' => '',
                'Selisih' => '',
                'Satuan' => '',
                'Status' => '',
                'Catatan' => ''
            ];
            
            // Add empty row
            $exportData[] = [
                'No' => '',
                'SKU' => '',
                'Nama Item' => '',
                'Stok Sistem' => '',
                'Stok Fisik' => '',
                'Selisih' => '',
                'Satuan' => '',
                'Status' => '',
                'Catatan' => ''
            ];
            
            // Add table headers
            $exportData[] = [
                'No' => 'No',
                'SKU' => 'SKU',
                'Nama Item' => 'Nama Item',
                'Stok Sistem' => 'Stok Sistem',
                'Stok Fisik' => 'Stok Fisik',
                'Selisih' => 'Selisih',
                'Satuan' => 'Satuan',
                'Status' => 'Status',
                'Catatan' => 'Catatan'
            ];

            // Add items data
            foreach ($items as $index => $item) {
                // Use the model's correct variance calculation
                $selisih = $item->selisih ?? 0;
                
                // If selisih is not calculated yet, calculate it properly
                if ($item->stok_fisik !== null && $selisih === 0 && $item->stok_fisik !== $item->stok_sistem) {
                    $item->calculateSelisih();
                    $selisih = $item->selisih;
                }
                
                // Determine status
                $status = 'Belum Input';
                if ($item->stok_fisik !== null) {
                    if ($selisih > 0) {
                        $status = 'Surplus';
                    } elseif ($selisih < 0) {
                        $status = 'Kurang';
                    } else {
                        $status = 'Sesuai';
                    }
                }

                $exportData[] = [
                    'No' => $index + 1,
                    'SKU' => $item->master_item_sku ?? '-',
                    'Nama Item' => $item->master_item_name,
                    'Stok Sistem' => number_format($item->stok_sistem, 0, ',', '.'),
                    'Stok Fisik' => $item->stok_fisik !== null ? number_format($item->stok_fisik, 0, ',', '.') : '-',
                    'Selisih' => $item->stok_fisik !== null ? 
                        ($selisih > 0 ? '+' : '') . number_format($selisih, 0, ',', '.') : '-',
                    'Satuan' => $item->satuan,
                    'Status' => $status,
                    'Catatan' => $item->notes ?? ''
                ];
            }

            // Log activity
            $exportDescription = 'Pengguna mengekspor data stock opname ' . 
                ($typeNames[$stockOpname->type] ?? $stockOpname->type) . 
                ' tanggal ' . $stockOpname->tanggal_opname->format('d/m/Y') . 
                ' (' . count($items) . ' items)';

            addActivity('stock-opname', 'export', $exportDescription, $stockOpname->id);
            
            return response()->json([
                'status' => 'success',
                'data' => $exportData,
                'count' => count($items),
                'message' => 'Data berhasil disiapkan untuk export',
                'opname_info' => [
                    'type' => $typeNames[$stockOpname->type] ?? $stockOpname->type,
                    'date' => $stockOpname->tanggal_opname->format('d/m/Y'),
                    'creator' => $stockOpname->creator->name ?? '-'
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Stock opname export error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengekspor data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh stock sistem for all items in opname to current live stock
     */
    public function refreshStock(StockOpname $stockOpname)
    {
        try {
            $refreshResult = $this->stockOpnameService->refreshStokSistem($stockOpname);
            
            return response()->json([
                'status' => 'success',
                'message' => $refreshResult['message'],
                'total_changes' => $refreshResult['total_changes'],
                'changes' => $refreshResult['changes']
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal refresh stock sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset stok awal from completed opname results (Manual trigger for KONDISI 1)
     */
    public function resetStokAwal(StockOpname $stockOpname)
    {
        // Only allow for completed opname
        if ($stockOpname->status !== 'completed') {
            return response()->json([
                'status' => 'error',
                'message' => 'Hanya stock opname yang sudah selesai yang dapat direset stok awalnya'
            ], 422);
        }

        try {
            $resetResult = $this->stockOpnameService->resetStokAwalFromOpname($stockOpname);
            
            // Log activity
            addActivity(
                'stock-opname', 
                'reset-stok-awal', 
                'Reset stok awal dari hasil opname ' . $stockOpname->type . ' tanggal ' . $stockOpname->tanggal_opname->format('d/m/Y'),
                $stockOpname->id
            );
            
            return response()->json([
                'status' => 'success',
                'message' => 'Stok awal berhasil direset dari hasil opname',
                'summary' => $resetResult
            ]);
        } catch (Exception $e) {
            Log::error('StockOpname: Reset stok awal failed', [
                'opname_id' => $stockOpname->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal reset stok awal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update system stock based on physical count results
     */
    private function updateSystemStock(StockOpname $stockOpname)
    {
        // Use the service to update system stock
        $updateResult = $this->stockOpnameService->updateSystemStock($stockOpname);
        
        // Log the update result
        Log::info('StockOpname: System stock update completed', [
            'opname_id' => $stockOpname->id,
            'result' => $updateResult
        ]);
    }
}
