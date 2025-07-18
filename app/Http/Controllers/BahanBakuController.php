<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\ValidationException;

class BahanBakuController extends Controller
{
    /**
     * Constructor to apply permissions middleware
     */
    public function __construct()
    {
        $this->middleware('permission:Bahan Baku List', ['only' => ['index']]);
        $this->middleware('permission:Bahan Baku Create', ['only' => ['store']]);
        $this->middleware('permission:Bahan Baku Update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:Bahan Baku Delete', ['only' => ['destroy']]);
        $this->middleware('permission:Bahan Baku View', ['only' => ['show']]);
    }

    /**
     * Custom validation messages
     */
    private function getValidationMessages()
    {
        return [
            'kategori.required' => 'Silahkan pilih kategori bahan baku',
            'kategori.integer' => 'Kategori harus berupa angka',

            'sku_induk.required' => 'Silahkan masukkan SKU Induk',
            'sku_induk.string' => 'SKU Induk harus berupa teks',
            'sku_induk.max' => 'SKU Induk terlalu panjang (maksimal 255 karakter)',
            'sku_induk.unique' => 'SKU Induk ini sudah digunakan. Silahkan gunakan SKU yang berbeda',

            'nama_barang.required' => 'Silahkan masukkan nama barang',
            'nama_barang.string' => 'Nama barang harus berupa teks',
            'nama_barang.max' => 'Nama barang terlalu panjang (maksimal 255 karakter)',

            'satuan.required' => 'Silahkan pilih satuan untuk bahan baku',
            'satuan.string' => 'Satuan harus berupa teks',
            'satuan.in' => 'Satuan harus berupa PCS, GRAM, atau KG',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = BahanBaku::query();

            // Apply filters from request
            if ($request->filled('kategori')) {
                $query->where('kategori', $request->kategori);
            }

            if ($request->filled('sku_induk')) {
                $query->where('sku_induk', 'like', '%' . $request->sku_induk . '%');
            }

            if ($request->filled('nama_barang')) {
                $query->where('nama_barang', 'like', '%' . $request->nama_barang . '%');
            }

            if ($request->filled('satuan')) {
                $query->where('satuan', $request->satuan);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->addColumn('action', function ($row) {
                    return $row->id;
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : '';
                })
                ->editColumn('kategori', function ($row) {
                    return $row->category_name;
                })
                ->filterColumn('kategori', function ($query, $keyword) {
                    $query->where('kategori', 'like', "%{$keyword}%");
                })
                ->filterColumn('sku_induk', function ($query, $keyword) {
                    $query->where('sku_induk', 'like', "%{$keyword}%");
                })
                ->filterColumn('nama_barang', function ($query, $keyword) {
                    $query->where('nama_barang', 'like', "%{$keyword}%");
                })
                ->filterColumn('satuan', function ($query, $keyword) {
                    $query->where('satuan', 'like', "%{$keyword}%");
                })
                ->rawColumns(['action'])
                ->smart(true)
                ->startsWithSearch()
                ->make(true);
        }

        // Get initial data for the view
        $items = [
            'Daftar Bahan Baku' => route('bahan-baku.index'),
        ];

        // Use kategori options from the model
        $kategoriOptions = BahanBaku::getCategoryOptions();

        // Log activity
        addActivity('bahan-baku', 'view', 'Pengguna melihat daftar bahan baku', null);

        return view('bahan-baku.index', compact('items', 'kategoriOptions'));
    }

    /**
     * Get category name from integer value
     */
    private function getKategoriName($kategoriId)
    {
        $kategoriOptions = BahanBaku::getCategoryOptions();
        return $kategoriOptions[$kategoriId] ?? 'Unknown';
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'kategori'      => 'required|integer',
                'sku_induk'     => 'required|string|max:255|unique:bahan_bakus',
                'nama_barang'   => 'required|string|max:255',
                'satuan'        => 'required|string|in:PCS,GRAM,KG',
            ], $this->getValidationMessages());

            $bahanBaku = BahanBaku::create($validated);

            // Log activity
            addActivity('bahan-baku', 'create', 'Pengguna membuat bahan baku baru: ' . $bahanBaku->nama_barang, $bahanBaku->id);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Bahan Baku telah ditambahkan ke dalam sistem.',
                'data' => $bahanBaku
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi Kesalahan',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error saat membuat bahan baku', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Maaf! Terjadi kesalahan saat menambahkan bahan baku. Silahkan coba lagi.'
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BahanBaku $bahanBaku)
    {
        try {
            Log::info('Permintaan edit bahan baku diterima', ['bahan_baku' => $bahanBaku->toArray()]);

            // Log activity
            addActivity('bahan-baku', 'edit', 'Pengguna melihat form edit bahan baku: ' . $bahanBaku->nama_barang, $bahanBaku->id);

            return response()->json([
                'success' => true,
                'data' => $bahanBaku
            ]);
        } catch (\Exception $e) {
            Log::error('Error pada edit bahan baku', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Maaf! Kami tidak dapat menemukan informasi bahan baku. Silahkan muat ulang dan coba lagi.'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BahanBaku $bahanBaku)
    {
        try {
            $validated = $request->validate([
                'kategori'      => 'required|integer',
                'sku_induk'     => 'required|string|max:255|unique:bahan_bakus,sku_induk,' . $bahanBaku->id,
                'nama_barang'   => 'required|string|max:255',
                'satuan'        => 'required|string|in:PCS,GRAM,KG',
            ], $this->getValidationMessages());

            // Store old values for logging
            $oldValues = $bahanBaku->toArray();

            $bahanBaku->update($validated);

            // Log activity
            addActivity('bahan-baku', 'update', 'Pengguna mengubah bahan baku: ' . $bahanBaku->nama_barang, $bahanBaku->id);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Informasi bahan baku telah diperbarui.',
                'data' => $bahanBaku
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi Kesalahan',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error saat memperbarui bahan baku', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Maaf! Terjadi kesalahan saat memperbarui bahan baku. Silahkan coba lagi.'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BahanBaku $bahanBaku)
    {
        try {
            $bahanBakuName = $bahanBaku->nama_barang;
            $bahanBakuId = $bahanBaku->id;

            $bahanBaku->delete();

            // Log activity
            addActivity('bahan-baku', 'delete', 'Pengguna menghapus bahan baku: ' . $bahanBakuName, $bahanBakuId);

            return response()->json([
                'success' => true,
                'message' => 'Bahan Baku telah berhasil dihapus dari sistem.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat menghapus bahan baku', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Maaf! Kami tidak dapat menghapus bahan baku saat ini. Silahkan coba lagi.'
            ], 500);
        }
    }

    /**
     * Check if SKU Induk already exists in database
     */
    public function checkSkuInduk(Request $request)
    {
        try {
            $skuInduk = $request->input('sku_induk');
            $bahanBakuId = $request->input('bahan_baku_id'); // For edit mode

            if (empty($skuInduk)) {
                return response()->json([
                    'exists' => false,
                    'message' => ''
                ]);
            }

            $query = BahanBaku::where('sku_induk', $skuInduk);

            // If we're editing, exclude the current bahan baku
            if ($bahanBakuId) {
                $query->where('id', '!=', $bahanBakuId);
            }

            $exists = $query->exists();

            return response()->json([
                'exists' => $exists,
                'message' => $exists ? 'SKU Induk ini sudah digunakan. Silahkan gunakan SKU Induk yang berbeda.' : ''
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'exists' => false,
                'message' => 'Terjadi kesalahan saat mengecek SKU Induk'
            ], 500);
        }
    }
}
