<?php

namespace App\Http\Controllers;

use App\Models\InventoryBahanBaku;
use App\Models\BahanBaku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class InventoryBahanBakuController extends Controller
{
    /**
     * Constructor to apply permissions middleware
     */
    public function __construct()
    {
        $this->middleware('permission:Inventory Bahan Baku List', ['only' => ['index']]);
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
            'bahan_baku_id.exists' => 'Bahan baku yang dipilih tidak ditemukan',

            'stok_awal.required' => 'Silahkan masukkan stok awal',
            'stok_awal.integer' => 'Stok awal harus berupa angka bulat',
            'stok_awal.min' => 'Stok awal minimal 0',

            'stok_masuk.required' => 'Silahkan masukkan stok masuk',
            'stok_masuk.integer' => 'Stok masuk harus berupa angka bulat',
            'stok_masuk.min' => 'Stok masuk minimal 0',

            'terpakai.required' => 'Silahkan masukkan jumlah terpakai',
            'terpakai.integer' => 'Jumlah terpakai harus berupa angka bulat',
            'terpakai.min' => 'Jumlah terpakai minimal 0',

            'surplus_stok.required' => 'Silahkan masukkan surplus stok',
            'surplus_stok.integer' => 'Surplus stok harus berupa angka bulat',
            'surplus_stok.min' => 'Surplus stok minimal 0',

            'defect.required' => 'Silahkan masukkan jumlah defect',
            'defect.integer' => 'Jumlah defect harus berupa angka bulat',
            'defect.min' => 'Jumlah defect minimal 0',

            'terjual.required' => 'Silahkan masukkan jumlah terjual',
            'terjual.integer' => 'Jumlah terjual harus berupa angka bulat',
            'terjual.min' => 'Jumlah terjual minimal 0',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
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
                    'inventory_bahan_bakus.surplus_stok',
                    'inventory_bahan_bakus.defect',
                    'inventory_bahan_bakus.terjual',
                    'inventory_bahan_bakus.live_stok_gudang',
                    'inventory_bahan_bakus.created_at as inv_created_at',
                    'inventory_bahan_bakus.updated_at as inv_updated_at'
                ]);

            // Filter by bahan baku if provided
            if ($request->has('bahan_baku_id') && !empty($request->bahan_baku_id)) {
                $query->where('bahan_bakus.id', $request->bahan_baku_id);
            }

            // Filter by category if provided
            if ($request->has('kategori') && !empty($request->kategori)) {
                $query->where('bahan_bakus.kategori', $request->kategori);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->orderColumn('nama_barang', function ($query, $order) {
                    $query->orderBy('bahan_bakus.nama_barang', $order);
                })
                ->addColumn('action', function ($row) {
                    return $row->bahan_baku_id;
                })
                ->addColumn('kategori_name', function ($row) {
                    $categories = BahanBaku::getCategoryOptions();
                    return $categories[$row->kategori] ?? 'Unknown Category';
                })
                ->addColumn('stok_awal_display', function ($row) {
                    return $row->stok_awal ?? 0;
                })
                ->addColumn('stok_masuk_display', function ($row) {
                    return $row->stok_masuk ?? 0;
                })
                ->addColumn('terpakai_display', function ($row) {
                    return $row->terpakai ?? 0;
                })
                ->addColumn('surplus_stok_display', function ($row) {
                    return $row->surplus_stok ?? 0;
                })
                ->addColumn('defect_display', function ($row) {
                    return $row->defect ?? 0;
                })
                ->addColumn('terjual_display', function ($row) {
                    return $row->terjual ?? 0;
                })
                ->addColumn('live_stok_gudang_display', function ($row) {
                    return $row->live_stok_gudang ?? 0;
                })
                ->filterColumn('nama_barang', function ($query, $keyword) {
                    $query->where('bahan_bakus.nama_barang', 'like', "%{$keyword}%");
                })
                ->filterColumn('sku_induk', function ($query, $keyword) {
                    $query->where('bahan_bakus.sku_induk', 'like', "%{$keyword}%");
                })
                ->rawColumns(['action'])
                ->smart(true)
                ->startsWithSearch()
                ->make(true);
        }

        try {
            // Get all bahan baku for dropdown
            $bahanBakus = BahanBaku::orderBy('nama_barang')->get();

            // Get category options
            $categories = BahanBaku::getCategoryOptions();

            // Get initial data for the view with pagination
            $items = [
                'Daftar Inventory Bahan Baku' => route('inventory-bahan-baku.index'),
            ];

            // Log activity
            addActivity('inventory_bahan_baku', 'view', 'Pengguna melihat daftar inventory bahan baku', null);

            return view('inventory-bahan-baku.index', compact('items', 'bahanBakus', 'categories'));
        } catch (\Exception $e) {
            // Log the error
            Log::error('Failed to load data for Inventory Bahan Baku index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return view with error message
            return view('inventory-bahan-baku.index', [
                'items' => ['Daftar Inventory Bahan Baku' => route('inventory-bahan-baku.index')],
                'bahanBakus' => [],
                'categories' => [],
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
                'bahan_baku_id' => 'required|exists:bahan_bakus,id',
                'stok_awal' => 'required|integer|min:0',
                'stok_masuk' => 'required|integer|min:0',
                'terpakai' => 'required|integer|min:0',
                'surplus_stok' => 'required|integer|min:0',
                'defect' => 'required|integer|min:0',
                'terjual' => 'required|integer|min:0',
            ], $this->getValidationMessages());

            // Get bahan baku for satuan
            $bahanBaku = BahanBaku::find($validated['bahan_baku_id']);

            // Calculate live stock gudang
            $liveStokGudang = $validated['stok_awal'] + $validated['stok_masuk'] - $validated['terpakai'] - $validated['defect'] - $validated['terjual'];

            // Add calculated fields to validated data
            $validated['live_stok_gudang'] = $liveStokGudang;
            $validated['satuan'] = $bahanBaku->satuan;

            // Use updateOrCreate to update existing record or create new one
            $inventoryBahanBaku = InventoryBahanBaku::updateOrCreate(
                ['bahan_baku_id' => $validated['bahan_baku_id']],
                [
                    'stok_awal' => $validated['stok_awal'],
                    'stok_masuk' => $validated['stok_masuk'],
                    'terpakai' => $validated['terpakai'],
                    'surplus_stok' => $validated['surplus_stok'],
                    'defect' => $validated['defect'],
                    'terjual' => $validated['terjual'],
                    'live_stok_gudang' => $liveStokGudang,
                    'satuan' => $bahanBaku->satuan
                ]
            );

            // Log activity
            $action = $inventoryBahanBaku->wasRecentlyCreated ? 'create' : 'update';
            $message = $inventoryBahanBaku->wasRecentlyCreated
                ? 'Pengguna membuat inventory bahan baku baru untuk: ' . $bahanBaku->nama_barang
                : 'Pengguna memperbarui inventory bahan baku untuk: ' . $bahanBaku->nama_barang;

            addActivity('inventory_bahan_baku', $action, $message, $inventoryBahanBaku->id);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Data inventory bahan baku telah diperbarui.',
                'data' => $inventoryBahanBaku
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi Kesalahan',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
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
                $inventoryBahanBaku->surplus_stok = 0;
                $inventoryBahanBaku->defect = 0;
                $inventoryBahanBaku->terjual = 0;
                $inventoryBahanBaku->live_stok_gudang = 0;
                $inventoryBahanBaku->satuan = $bahanBaku->satuan;
            }

            // Add bahan baku information to the response
            $inventoryBahanBaku->bahan_baku_id = $bahanBakuId;

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
            // Validate that the bahan baku exists
            $bahanBaku = BahanBaku::findOrFail($bahanBakuId);

            $validated = $request->validate([
                'stok_awal' => 'required|integer|min:0',
                'stok_masuk' => 'required|integer|min:0',
                'terpakai' => 'required|integer|min:0',
                'surplus_stok' => 'required|integer|min:0',
                'defect' => 'required|integer|min:0',
                'terjual' => 'required|integer|min:0',
            ], $this->getValidationMessages());

            // Calculate live stock gudang
            $liveStokGudang = $validated['stok_awal'] + $validated['stok_masuk'] - $validated['terpakai'] - $validated['defect'] - $validated['terjual'];

            // Use updateOrCreate to update existing record or create new one
            $inventoryBahanBaku = InventoryBahanBaku::updateOrCreate(
                ['bahan_baku_id' => $bahanBakuId],
                [
                    'stok_awal' => $validated['stok_awal'],
                    'stok_masuk' => $validated['stok_masuk'],
                    'terpakai' => $validated['terpakai'],
                    'surplus_stok' => $validated['surplus_stok'],
                    'defect' => $validated['defect'],
                    'terjual' => $validated['terjual'],
                    'live_stok_gudang' => $liveStokGudang,
                    'satuan' => $bahanBaku->satuan
                ]
            );

            // Log activity
            $action = $inventoryBahanBaku->wasRecentlyCreated ? 'create' : 'update';
            $message = $inventoryBahanBaku->wasRecentlyCreated
                ? 'Pengguna membuat inventory bahan baku baru untuk: ' . $bahanBaku->nama_barang
                : 'Pengguna memperbarui inventory bahan baku untuk: ' . $bahanBaku->nama_barang;

            addActivity('inventory_bahan_baku', $action, $message, $inventoryBahanBaku->id);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Data inventory bahan baku telah diperbarui.',
                'data' => $inventoryBahanBaku
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi Kesalahan',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
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
                'inventory_bahan_bakus.surplus_stok',
                'inventory_bahan_bakus.defect',
                'inventory_bahan_bakus.terjual',
                'inventory_bahan_bakus.live_stok_gudang'
            ]);

        // Filter by bahan baku if provided
        if ($request->has('bahan_baku_id') && !empty($request->bahan_baku_id)) {
            $query->where('bahan_bakus.id', $request->bahan_baku_id);
        }

        // Filter by category if provided
        if ($request->has('kategori') && !empty($request->kategori)) {
            $query->where('bahan_bakus.kategori', $request->kategori);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('kategori_name', function ($row) {
                $categories = BahanBaku::getCategoryOptions();
                return $categories[$row->kategori] ?? 'Unknown Category';
            })
            ->addColumn('stok_awal_display', function ($row) {
                return $row->stok_awal ?? 0;
            })
            ->addColumn('stok_masuk_display', function ($row) {
                return $row->stok_masuk ?? 0;
            })
            ->addColumn('terpakai_display', function ($row) {
                return $row->terpakai ?? 0;
            })
            ->addColumn('surplus_stok_display', function ($row) {
                return $row->surplus_stok ?? 0;
            })
            ->addColumn('defect_display', function ($row) {
                return $row->defect ?? 0;
            })
            ->addColumn('terjual_display', function ($row) {
                return $row->terjual ?? 0;
            })
            ->addColumn('live_stok_gudang_display', function ($row) {
                return $row->live_stok_gudang ?? 0;
            })
            ->addColumn('action', function ($row) {
                return '
                    <button type="button" class="btn btn-sm btn-warning edit-btn" data-id="' . $row->bahan_baku_id . '">
                        <i class="fas fa-edit"></i> Edit Stok
                    </button>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Display the specified resource.
     */
    public function show(InventoryBahanBaku $inventoryBahanBaku)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InventoryBahanBaku $inventoryBahanBaku)
    {
        //
    }
}
