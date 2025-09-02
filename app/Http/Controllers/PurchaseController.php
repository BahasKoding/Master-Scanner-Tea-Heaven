<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\BahanBaku;
use App\Models\Product;
use App\Models\InventoryBahanBaku;
use App\Services\PurchaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\ValidationException;

class PurchaseController extends Controller
{
    protected $item = 'Purchase Items';

    /**
     * Constructor to apply permissions middleware
     */
    public function __construct()
    {
        $this->middleware('permission:Purchase List', ['only' => ['index', 'data']]);
        $this->middleware('permission:Purchase Create', ['only' => ['create', 'store']]);
        $this->middleware('permission:Purchase Update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:Purchase Delete', ['only' => ['destroy']]);
        $this->middleware('permission:Purchase View', ['only' => ['show']]);
    }

    /**
     * Custom validation messages
     */
    private function getValidationMessages()
    {
        return [
            'kategori.required' => 'Silahkan pilih kategori',
            'kategori.in' => 'Kategori yang dipilih tidak valid',
            'bahan_baku_id.required' => 'Silahkan pilih item',
            'bahan_baku_id.exists' => 'Item yang dipilih tidak valid. Pastikan item yang dipilih sesuai dengan kategori.',
            'qty_pembelian.required' => 'Silahkan masukkan quantity pembelian',
            'qty_pembelian.integer' => 'Quantity pembelian harus berupa angka',
            'qty_pembelian.min' => 'Quantity pembelian minimal 1',
            'tanggal_kedatangan_barang.date' => 'Format tanggal tidak valid',
            'qty_barang_masuk.integer' => 'Quantity barang masuk harus berupa angka',
            'qty_barang_masuk.min' => 'Quantity barang masuk tidak boleh negatif',
            'barang_defect_tanpa_retur.integer' => 'Barang defect harus berupa angka',
            'barang_defect_tanpa_retur.min' => 'Barang defect tidak boleh negatif',
            'barang_diretur_ke_supplier.integer' => 'Barang retur harus berupa angka',
            'barang_diretur_ke_supplier.min' => 'Barang retur tidak boleh negatif',
            'checker_penerima_barang.string' => 'Nama penerima harus berupa teks',
            'checker_penerima_barang.max' => 'Nama penerima terlalu panjang (maksimal 255 karakter)',
            // Calculation validation messages
            'calculation_error' => 'Terjadi kesalahan kalkulasi pada data penerimaan barang',
            'defect_exceeds_incoming' => 'Jumlah barang defect tidak boleh melebihi jumlah barang masuk',
            'return_exceeds_incoming' => 'Jumlah barang retur tidak boleh melebihi jumlah barang masuk',
            'total_exceeds_incoming' => 'Total defect + retur tidak boleh melebihi jumlah barang masuk',
            'negative_total_stock' => 'Total stok masuk tidak boleh negatif',
            'unrealistic_defect_rate' => 'Tingkat defect terlalu tinggi (>50% dari barang masuk)',
            'unrealistic_return_rate' => 'Tingkat retur terlalu tinggi (>50% dari barang masuk)',
            'qty_exceeds_purchase' => 'Jumlah barang masuk tidak boleh melebihi quantity pembelian',
        ];
    }

    /**
     * Validate calculation consistency for purchase quantities
     *
     * @param array $data
     * @return array
     * @throws ValidationException
     */
    private function validateCalculations(array $data)
    {
        $errors = [];
        
        $qtyMasuk      = (int) ($data['qty_barang_masuk'] ?? 0);
        $defect        = (int) ($data['barang_defect_tanpa_retur'] ?? 0);
        $retur         = (int) ($data['barang_diretur_ke_supplier'] ?? 0);
        $qtyPembelian  = (int) ($data['qty_pembelian'] ?? 0);

        // 1) Basic logical checks
        if ($defect > $qtyMasuk) {
            $errors['barang_defect_tanpa_retur'] = [
                'Jumlah barang defect (' . number_format($defect) . ') tidak boleh melebihi jumlah barang masuk (' . number_format($qtyMasuk) . ')'
            ];
        }

        if ($retur > $qtyMasuk) {
            $errors['barang_diretur_ke_supplier'] = [
                'Jumlah barang retur ke supplier (' . number_format($retur) . ') tidak boleh melebihi jumlah barang masuk (' . number_format($qtyMasuk) . ')'
            ];
        }

        // 2) Defect + retur tidak boleh melebihi qty masuk
        if (($defect + $retur) > $qtyMasuk) {
            $errors['calculation_error'] = [
                'Total defect + retur (' . number_format($defect + $retur) . ') tidak boleh melebihi jumlah barang masuk (' . number_format($qtyMasuk) . ')'
            ];
        }

        // 3) Total stok masuk = masuk - defect - retur  (tidak boleh negatif)
        $calculatedTotal = $qtyMasuk - $defect - $retur;
        if ($calculatedTotal < 0) {
            $errors['negative_total_stock'] = [
                'Total stok masuk tidak boleh negatif. Periksa kembali kalkulasi: ' . $qtyMasuk . ' - ' . $defect . ' - ' . $retur . ' = ' . $calculatedTotal
            ];
        }

        // 4) Business warning: defect/retur rate tinggi
        if ($qtyMasuk > 0) {
            $defectRate = ($defect / $qtyMasuk) * 100;
            $returnRate = ($retur  / $qtyMasuk) * 100;

            if ($defectRate > 50) {
                $errors['barang_defect_tanpa_retur'] = [
                    'Tingkat defect terlalu tinggi (' . number_format($defectRate, 1) . '%). Periksa kembali jumlah barang defect.'
                ];
            }

            if ($returnRate > 50) {
                $errors['barang_diretur_ke_supplier'] = [
                    'Tingkat retur ke supplier terlalu tinggi (' . number_format($returnRate, 1) . '%). Periksa kembali jumlah retur.'
                ];
            }
        }

        // 5) Qty masuk tidak boleh melebihi qty pembelian (opsional, tetap dipertahankan)
        if ($qtyPembelian > 0 && $qtyMasuk > $qtyPembelian) {
            $errors['qty_barang_masuk'] = [
                'Jumlah barang masuk (' . number_format($qtyMasuk) . ') tidak boleh melebihi quantity pembelian (' . number_format($qtyPembelian) . '). Periksa kembali data.'
            ];
        }

        // 6) Tidak mungkin ada defect/retur bila qty masuk = 0
        if ($qtyMasuk == 0 && ($defect > 0 || $retur > 0)) {
            $errors['qty_barang_masuk'] = [
                'Tidak dapat memiliki barang defect atau retur jika tidak ada barang masuk.'
            ];
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }

        return $data;
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $query = Purchase::with(['bahanBaku', 'product'])
                    ->select([
                        'purchases.id',
                        'purchases.kategori',
                        'purchases.bahan_baku_id',
                        'purchases.qty_pembelian',
                        'purchases.tanggal_kedatangan_barang',
                        'purchases.qty_barang_masuk',
                        'purchases.barang_defect_tanpa_retur',
                        'purchases.barang_diretur_ke_supplier',
                        'purchases.total_stok_masuk',
                        'purchases.checker_penerima_barang',
                        'purchases.created_at',
                        'purchases.updated_at'
                    ]);

                // Filter by kategori if provided
                if ($request->has('kategori') && !empty($request->kategori)) {
                    $query->where('purchases.kategori', $request->kategori);
                }

                // Filter by bahan baku if provided
                if ($request->has('bahan_baku_id') && !empty($request->bahan_baku_id)) {
                    $query->where('purchases.bahan_baku_id', $request->bahan_baku_id);
                }

                // Filter by date range if provided
                if (
                    $request->has('start_date') && $request->has('end_date') &&
                    !empty($request->start_date) && !empty($request->end_date)
                ) {
                    $query->whereBetween('tanggal_kedatangan_barang', [$request->start_date, $request->end_date]);
                }

                $dataTable = DataTables::of($query)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        return $row->id;
                    })
                    ->addColumn('kategori_display', function ($row) {
                        return $row->kategori === 'finished_goods' ? 'Finished Goods' : 'Bahan Baku';
                    })
                    ->addColumn('item_name', function ($row) {
                        return $row->item_name;
                    })
                    ->addColumn('item_sku', function ($row) {
                        return $row->item_sku;
                    })
                    ->addColumn('satuan', function ($row) {
                        if ($row->kategori === 'finished_goods') {
                            return $row->product ? $row->product->satuan : 'pcs';
                        }
                        return $row->bahanBaku ? $row->bahanBaku->satuan : '-';
                    })
                    ->editColumn('qty_pembelian', function ($row) {
                        return number_format($row->qty_pembelian);
                    })
                    ->editColumn('qty_barang_masuk', function ($row) {
                        return number_format($row->qty_barang_masuk);
                    })
                    ->editColumn('barang_defect_tanpa_retur', function ($row) {
                        return number_format($row->barang_defect_tanpa_retur);
                    })
                    ->editColumn('barang_diretur_ke_supplier', function ($row) {
                        return number_format($row->barang_diretur_ke_supplier);
                    })
                    ->editColumn('total_stok_masuk', function ($row) {
                        return number_format($row->total_stok_masuk);
                    })
                    ->editColumn('tanggal_kedatangan_barang', function ($row) {
                        return $row->tanggal_kedatangan_barang ? $row->tanggal_kedatangan_barang->format('d/m/Y') : '-';
                    })
                    ->editColumn('checker_penerima_barang', function ($row) {
                        return $row->checker_penerima_barang ?: '-';
                    })
                    ->filterColumn('item_name', function ($query, $keyword) {
                        $query->where(function ($q) use ($keyword) {
                            $q->whereHas('bahanBaku', function ($subQ) use ($keyword) {
                                $subQ->where('nama_barang', 'like', "%{$keyword}%")
                                    ->orWhere('sku_induk', 'like', "%{$keyword}%");
                            })->orWhereHas('product', function ($subQ) use ($keyword) {
                                $subQ->where('name_product', 'like', "%{$keyword}%")
                                    ->orWhere('sku', 'like', "%{$keyword}%");
                            });
                        });
                    })
                    ->rawColumns(['action'])
                    ->smart(true)
                    ->make(true);

                return $dataTable;
            } catch (\Exception $e) {
                Log::error('Purchase DataTable generation failed', [
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

            // Get product options for filter
            $products = Product::orderBy('name_product')->get();

            // Get initial data for the view
            $items = [
                'Purchase Items' => route('purchase.index'),
            ];

            // Log activity
            addActivity('purchase', 'view', 'Pengguna melihat daftar purchase bahan baku', null);

            return view('purchase.index', compact('items', 'bahanBakus', 'products'))
                ->with('item', $this->item);
        } catch (\Exception $e) {
            Log::error('Failed to load Purchase index', [
                'error' => $e->getMessage()
            ]);

            return view('purchase.index', [
                'items' => ['Purchase Items' => route('purchase.index')],
                'bahanBakus' => [],
                'products' => [],
                'error_message' => 'Gagal memuat data. Silakan coba refresh halaman.'
            ])->with('item', $this->item);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $bahanBakus = BahanBaku::orderBy('nama_barang')->get();
            $products = Product::orderBy('name_product')->get();

            // Log activity
            addActivity('purchase', 'view_create_form', 'Pengguna melihat form tambah purchase items', null);

            return view('purchase.create', compact('bahanBakus', 'products'))
                ->with('item', $this->item);
        } catch (\Exception $e) {
            Log::error('Failed to load Purchase create form', [
                'error' => $e->getMessage()
            ]);

            return redirect()->route('purchase.index')
                ->with('error', 'Gagal memuat form tambah purchase.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate kategori first to determine which table to check
            $request->validate([
                'kategori' => 'required|in:bahan_baku,finished_goods',
            ], $this->getValidationMessages());

            // Custom validation based on kategori
            $rules = [
                'kategori' => 'required|in:bahan_baku,finished_goods',
                'qty_pembelian' => 'required|integer|min:1',
                'tanggal_kedatangan_barang' => 'nullable|date',
                'qty_barang_masuk' => 'nullable|integer|min:0',
                'barang_defect_tanpa_retur' => 'nullable|integer|min:0',
                'barang_diretur_ke_supplier' => 'nullable|integer|min:0',
                'checker_penerima_barang' => 'nullable|string|max:255',
            ];

            // Add dynamic validation for bahan_baku_id based on kategori
            if ($request->kategori === 'finished_goods') {
                $rules['bahan_baku_id'] = 'required|exists:products,id';
            } else {
                $rules['bahan_baku_id'] = 'required|exists:bahan_bakus,id';
            }

            $validated = $request->validate($rules, $this->getValidationMessages());

            // Set default values
            $validated['qty_barang_masuk'] = $validated['qty_barang_masuk'] ?? 0;
            $validated['barang_defect_tanpa_retur'] = $validated['barang_defect_tanpa_retur'] ?? 0;
            $validated['barang_diretur_ke_supplier'] = $validated['barang_diretur_ke_supplier'] ?? 0;

            // Validate calculation consistency
            $this->validateCalculations($validated);

            // Calculate and set total_stok_masuk
            $validated['total_stok_masuk'] = $validated['qty_barang_masuk'] - $validated['barang_defect_tanpa_retur'] - $validated['barang_diretur_ke_supplier'];

            // Use PurchaseService for consistent stock integration
            $purchaseService = app(PurchaseService::class);
            $purchase = $purchaseService->createPurchase($validated);

            // Get item name for logging
            if ($validated['kategori'] === 'finished_goods') {
                $product = Product::find($validated['bahan_baku_id']);
                $itemName = $product ? $product->name_product : 'Unknown';
                $satuan = $product ? $product->satuan : 'pcs';
                $stockType = 'FinishedGoods';
            } else {
                $bahanBaku = BahanBaku::find($validated['bahan_baku_id']);
                $itemName = $bahanBaku ? $bahanBaku->full_name : 'Unknown';
                $satuan = $bahanBaku ? $bahanBaku->satuan : '';
                $stockType = 'InventoryBahanBaku';
            }

            // Log activity
            addActivity('purchase', 'create', 'Pengguna membuat purchase baru untuk: ' . $itemName . ' sebanyak ' . $purchase->qty_pembelian . ' ' . $satuan . ' (Stock: ' . $stockType . ' updated)', $purchase->id);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil! Purchase item telah ditambahkan ke dalam sistem dan inventory ' . strtolower($stockType) . ' telah diperbarui.',
                    'data' => $purchase
                ]);
            }

            return redirect()->route('purchase.index')
                ->with('success', 'Purchase item berhasil ditambahkan dan inventory telah diperbarui.');
        } catch (ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi Kesalahan',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to store purchase', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maaf! Terjadi kesalahan saat menambahkan purchase. Silahkan coba lagi.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menambahkan purchase.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $purchase = Purchase::with(['bahanBaku', 'product'])->findOrFail($id);

            // Log activity
            addActivity('purchase', 'view', 'Pengguna melihat detail purchase: ' . $purchase->item_name, $purchase->id);

            return view('purchase.show', compact('purchase'))
                ->with('item', $this->item);
        } catch (\Exception $e) {
            Log::error('Failed to show purchase', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('purchase.index')
                ->with('error', 'Purchase tidak ditemukan.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $purchase = Purchase::with(['bahanBaku', 'product'])->findOrFail($id);
            $bahanBakus = BahanBaku::orderBy('nama_barang')->get();
            $products = Product::orderBy('name_product')->get();

            // Log activity
            addActivity('purchase', 'edit', 'Pengguna melihat form edit purchase: ' . $purchase->item_name, $purchase->id);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $purchase
                ]);
            }

            return view('purchase.edit', compact('purchase', 'bahanBakus', 'products'))
                ->with('item', $this->item);
        } catch (\Exception $e) {
            Log::error('Failed to edit purchase', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maaf! Kami tidak dapat menemukan informasi purchase. Silahkan muat ulang dan coba lagi.'
                ], 500);
            }

            return redirect()->route('purchase.index')
                ->with('error', 'Purchase tidak ditemukan.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $purchase = Purchase::findOrFail($id);
            $oldBahanBakuId = $purchase->bahan_baku_id;
            $oldKategori = $purchase->kategori;

            // Custom validation based on kategori
            $rules = [
                'kategori' => 'required|in:bahan_baku,finished_goods',
                'qty_pembelian' => 'required|integer|min:1',
                'tanggal_kedatangan_barang' => 'nullable|date',
                'qty_barang_masuk' => 'nullable|integer|min:0',
                'barang_defect_tanpa_retur' => 'nullable|integer|min:0',
                'barang_diretur_ke_supplier' => 'nullable|integer|min:0',
                'checker_penerima_barang' => 'nullable|string|max:255',
            ];

            // Add dynamic validation for bahan_baku_id based on kategori
            if ($request->kategori === 'finished_goods') {
                $rules['bahan_baku_id'] = 'required|exists:products,id';
            } else {
                $rules['bahan_baku_id'] = 'required|exists:bahan_bakus,id';
            }

            $validated = $request->validate($rules, $this->getValidationMessages());

            // Set default values
            $validated['qty_barang_masuk'] = $validated['qty_barang_masuk'] ?? 0;
            $validated['barang_defect_tanpa_retur'] = $validated['barang_defect_tanpa_retur'] ?? 0;
            $validated['barang_diretur_ke_supplier'] = $validated['barang_diretur_ke_supplier'] ?? 0;

            // Validate calculation consistency
            $this->validateCalculations($validated);

            // Calculate and set total_stok_masuk
            $validated['total_stok_masuk'] = $validated['qty_barang_masuk'] - $validated['barang_defect_tanpa_retur'] - $validated['barang_diretur_ke_supplier'];

            $oldItemName = $purchase->item_name;

            // Use PurchaseService for consistent stock integration
            $purchaseService = app(PurchaseService::class);
            $purchase = $purchaseService->updatePurchase($purchase, $validated);

            // Get new item name for logging
            if ($validated['kategori'] === 'finished_goods') {
                $product = Product::find($validated['bahan_baku_id']);
                $newItemName = $product ? $product->name_product : 'Unknown';
                $stockType = 'FinishedGoods';
            } else {
                $bahanBaku = BahanBaku::find($validated['bahan_baku_id']);
                $newItemName = $bahanBaku ? $bahanBaku->full_name : 'Unknown';
                $stockType = 'InventoryBahanBaku';
            }

            // Determine if category changed for logging
            $categoryChanged = $oldKategori !== $validated['kategori'] ? ' (kategori berubah)' : '';
            $itemChanged = $oldBahanBakuId !== $validated['bahan_baku_id'] ? ' (item berubah)' : '';

            // Log activity
            addActivity('purchase', 'update', 'Pengguna mengubah purchase dari "' . $oldItemName . '" menjadi "' . $newItemName . '"' . $categoryChanged . $itemChanged . ' (Stock: ' . $stockType . ' updated)', $purchase->id);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil! Informasi purchase telah diperbarui dan inventory ' . strtolower($stockType) . ' telah diperbarui.',
                    'data' => $purchase
                ]);
            }

            return redirect()->route('purchase.index')
                ->with('success', 'Purchase berhasil diperbarui dan inventory telah diperbarui.');
        } catch (ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi Kesalahan',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to update purchase', [
                'id' => $id,
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maaf! Terjadi kesalahan saat memperbarui purchase. Silahkan coba lagi.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui purchase.')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $purchase = Purchase::with(['bahanBaku', 'product'])->findOrFail($id);
            $itemName = $purchase->item_name;
            $purchaseId = $purchase->id;
            $kategori = $purchase->kategori;

            // Determine stock type for logging
            $stockType = $kategori === 'finished_goods' ? 'FinishedGoods' : 'InventoryBahanBaku';

            // Use PurchaseService for consistent stock integration
            $purchaseService = app(PurchaseService::class);
            $result = $purchaseService->deletePurchase($purchase);

            // Log activity
            addActivity('purchase', 'delete', 'Pengguna menghapus purchase: ' . $itemName . ' (Stock: ' . $stockType . ' updated)', $purchaseId);

            return response()->json([
                'success' => true,
                'message' => 'Purchase telah berhasil dihapus dari sistem dan inventory ' . strtolower($stockType) . ' telah diperbarui.'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete purchase', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Maaf! Kami tidak dapat menghapus purchase saat ini. Silahkan coba lagi.'
            ], 500);
        }
    }

    /**
     * Get data for DataTables AJAX
     */
    public function data(Request $request)
    {
        return $this->index($request);
    }

    /**
     * Sync purchase data for consistency check
     */
    public function syncPurchaseData(Request $request)
    {
        try {
            $purchaseId = $request->get('purchase_id');

            $purchaseService = app(PurchaseService::class);
            $syncResults = $purchaseService->syncPurchaseData($purchaseId);

            $successCount = collect($syncResults)->where('status', 'success')->count();
            $errorCount = collect($syncResults)->where('status', 'error')->count();

            // Log activity
            addActivity('purchase', 'sync', 'Pengguna melakukan sync purchase data (Success: ' . $successCount . ', Error: ' . $errorCount . ')', null);

            return response()->json([
                'success' => true,
                'message' => 'Sync selesai. Berhasil: ' . $successCount . ', Gagal: ' . $errorCount,
                'results' => $syncResults
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to sync purchase data', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan sync data purchase.'
            ], 500);
        }
    }

    /**
     * Get purchase statistics for a specific item
     */
    public function getStatistics(Request $request)
    {
        try {
            $validated = $request->validate([
                'kategori' => 'required|in:bahan_baku,finished_goods',
                'item_id' => 'required|integer'
            ]);

            $purchaseService = app(PurchaseService::class);
            $statistics = $purchaseService->getPurchaseStatistics($validated['kategori'], $validated['item_id']);

            return response()->json([
                'success' => true,
                'data' => $statistics
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get purchase statistics', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik purchase.'
            ], 500);
        }
    }

    /**
     * Validate purchase calculations in real-time
     */
    public function validatePurchaseCalculations(Request $request)
    {
        try {
            $data = $request->validate([
                'qty_pembelian'               => 'required|integer|min:0',
                'qty_barang_masuk'            => 'nullable|integer|min:0',
                'barang_defect_tanpa_retur'   => 'nullable|integer|min:0',
                'barang_diretur_ke_supplier'  => 'nullable|integer|min:0',
            ]);

            // defaults
            $data['qty_barang_masuk']           = $data['qty_barang_masuk'] ?? 0;
            $data['barang_defect_tanpa_retur']  = $data['barang_defect_tanpa_retur'] ?? 0;
            $data['barang_diretur_ke_supplier'] = $data['barang_diretur_ke_supplier'] ?? 0;

            // server-side logical validation
            $this->validateCalculations($data);

            // total = masuk - defect - retur
            $totalStokMasuk = $data['qty_barang_masuk']
                            - $data['barang_defect_tanpa_retur']
                            - $data['barang_diretur_ke_supplier'];

            $defectRate = $data['qty_barang_masuk'] > 0
                ? ($data['barang_defect_tanpa_retur'] / $data['qty_barang_masuk']) * 100 : 0;
            $returnRate = $data['qty_barang_masuk'] > 0
                ? ($data['barang_diretur_ke_supplier'] / $data['qty_barang_masuk']) * 100 : 0;

            return response()->json([
                'success' => true,
                'message' => 'Kalkulasi valid',
                'data' => [
                    'total_stok_masuk'    => max(0, $totalStokMasuk), // guard non-negatif
                    'defect_rate'         => round($defectRate, 2),
                    'return_rate'         => round($returnRate, 2),
                    'calculation_formula' => $data['qty_barang_masuk'].' - '.$data['barang_defect_tanpa_retur'].' - '.$data['barang_diretur_ke_supplier'].' = '.max(0, $totalStokMasuk),
                ]
            ]);
        } catch (ValidationException $e) {
            $masuk = (int) ($request->input('qty_barang_masuk', 0));
            $def   = (int) ($request->input('barang_defect_tanpa_retur', 0));
            $ret   = (int) ($request->input('barang_diretur_ke_supplier', 0));

            return response()->json([
                'success' => false,
                'message' => 'Terdapat kesalahan kalkulasi',
                'errors'  => $e->errors(),
                'data'    => [
                    'total_stok_masuk' => max(0, $masuk - $def - $ret)
                ]
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to validate purchase calculations', [
                'error'   => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memvalidasi kalkulasi'
            ], 500);
        }
    }

}
