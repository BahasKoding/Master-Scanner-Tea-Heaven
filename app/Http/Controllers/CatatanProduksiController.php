<?php

namespace App\Http\Controllers;

use App\Models\CatatanProduksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class CatatanProduksiController extends Controller
{
    /**
     * Constructor to apply permissions middleware
     */
    public function __construct()
    {
        $this->middleware('permission:Catatan Produksi List', ['only' => ['index']]);
        $this->middleware('permission:Catatan Produksi Create', ['only' => ['store']]);
        $this->middleware('permission:Catatan Produksi Update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:Catatan Produksi Delete', ['only' => ['destroy']]);
        $this->middleware('permission:Catatan Produksi View', ['only' => ['show']]);
    }

    /**
     * Custom validation messages
     */
    private function getValidationMessages()
    {
        return [
            'sku_product.required' => 'Silahkan masukkan SKU produk',
            'sku_product.string' => 'SKU produk harus berupa teks',
            'sku_product.max' => 'SKU produk terlalu panjang (maksimal 255 karakter)',

            'nama_product.required' => 'Silahkan masukkan nama produk',
            'nama_product.string' => 'Nama produk harus berupa teks',
            'nama_product.max' => 'Nama produk terlalu panjang (maksimal 255 karakter)',

            'packaging.required' => 'Silahkan masukkan jenis packaging',
            'packaging.string' => 'Packaging harus berupa teks',
            'packaging.max' => 'Packaging terlalu panjang (maksimal 255 karakter)',

            'quantity.required' => 'Silahkan masukkan kuantitas',
            'quantity.integer' => 'Kuantitas harus berupa angka bulat',

            'sku_induk.required' => 'Silahkan masukkan SKU induk',
            'sku_induk.array' => 'SKU induk harus berupa array',

            'gramasi.required' => 'Silahkan masukkan gramasi',
            'gramasi.array' => 'Gramasi harus berupa array',

            'total_terpakai.required' => 'Silahkan masukkan total terpakai',
            'total_terpakai.string' => 'Total terpakai harus berupa teks',
            'total_terpakai.max' => 'Total terpakai terlalu panjang (maksimal 255 karakter)',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = CatatanProduksi::query()
                ->select([
                    'id',
                    'sku_product',
                    'nama_product',
                    'packaging',
                    'quantity',
                    'sku_induk',
                    'gramasi',
                    'total_terpakai',
                    'created_at',
                    'updated_at'
                ]);

            // Apply filters from request
            if ($request->filled('sku_product')) {
                $query->where('sku_product', 'like', '%' . $request->sku_product . '%');
            }

            if ($request->filled('nama_product')) {
                $query->where('nama_product', 'like', '%' . $request->nama_product . '%');
            }

            if ($request->filled('packaging')) {
                $query->where('packaging', 'like', '%' . $request->packaging . '%');
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->addColumn('action', function ($row) {
                    return $row->id;
                })
                ->editColumn('sku_induk', function ($row) {
                    return is_array($row->sku_induk) ? implode(', ', $row->sku_induk) : $row->sku_induk;
                })
                ->editColumn('gramasi', function ($row) {
                    return is_array($row->gramasi) ? implode(', ', $row->gramasi) : $row->gramasi;
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : '';
                })
                ->filterColumn('sku_product', function ($query, $keyword) {
                    $query->where('sku_product', 'like', "%{$keyword}%");
                })
                ->filterColumn('nama_product', function ($query, $keyword) {
                    $query->where('nama_product', 'like', "%{$keyword}%");
                })
                ->filterColumn('packaging', function ($query, $keyword) {
                    $query->where('packaging', 'like', "%{$keyword}%");
                })
                ->rawColumns(['action'])
                ->smart(true)
                ->startsWithSearch()
                ->make(true);
        }

        // Get initial data for the view with pagination
        $items = [
            'Daftar Catatan Produksi' => route('catatan-produksi.index'),
        ];

        // Log activity
        addActivity('catatan_produksi', 'view', 'Pengguna melihat daftar catatan produksi', null);

        return view('catatan-produksi.index', compact('items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'sku_product' => 'required|string|max:255',
                'nama_product' => 'required|string|max:255',
                'packaging' => 'required|string|max:255',
                'quantity' => 'required|integer',
                'sku_induk' => 'required|array',
                'gramasi' => 'required|array',
                'total_terpakai' => 'required|string|max:255',
            ], $this->getValidationMessages());

            $catatanProduksi = CatatanProduksi::create($validated);

            // Log activity
            addActivity('catatan_produksi', 'create', 'Pengguna membuat catatan produksi baru: ' . $catatanProduksi->nama_product, $catatanProduksi->id);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Catatan produksi telah ditambahkan ke dalam sistem.',
                'data' => $catatanProduksi
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi Kesalahan',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error saat membuat catatan produksi', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Maaf! Terjadi kesalahan saat menambahkan catatan produksi. Silahkan coba lagi.'
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CatatanProduksi $catatanProduksi)
    {
        try {
            Log::info('Permintaan edit catatan produksi diterima', ['catatan_produksi' => $catatanProduksi->toArray()]);

            // Log activity
            addActivity('catatan_produksi', 'edit', 'Pengguna melihat form edit catatan produksi: ' . $catatanProduksi->nama_product, $catatanProduksi->id);

            return response()->json([
                'success' => true,
                'data' => $catatanProduksi
            ]);
        } catch (\Exception $e) {
            Log::error('Error pada edit catatan produksi', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Maaf! Kami tidak dapat menemukan informasi catatan produksi. Silahkan muat ulang dan coba lagi.'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CatatanProduksi $catatanProduksi)
    {
        try {
            $validated = $request->validate([
                'sku_product' => 'required|string|max:255',
                'nama_product' => 'required|string|max:255',
                'packaging' => 'required|string|max:255',
                'quantity' => 'required|integer',
                'sku_induk' => 'required|array',
                'gramasi' => 'required|array',
                'total_terpakai' => 'required|string|max:255',
            ], $this->getValidationMessages());

            // Store old values for logging
            $oldValues = $catatanProduksi->toArray();

            $catatanProduksi->update($validated);

            // Log activity
            addActivity('catatan_produksi', 'update', 'Pengguna mengubah catatan produksi: ' . $catatanProduksi->nama_product, $catatanProduksi->id);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Informasi catatan produksi telah diperbarui.',
                'data' => $catatanProduksi
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi Kesalahan',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error saat memperbarui catatan produksi', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Maaf! Terjadi kesalahan saat memperbarui catatan produksi. Silahkan coba lagi.'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CatatanProduksi $catatanProduksi)
    {
        try {
            $produksiName = $catatanProduksi->nama_product;
            $produksiId = $catatanProduksi->id;

            $catatanProduksi->delete();

            // Log activity
            addActivity('catatan_produksi', 'delete', 'Pengguna menghapus catatan produksi: ' . $produksiName, $produksiId);

            return response()->json([
                'success' => true,
                'message' => 'Catatan produksi telah berhasil dihapus dari sistem.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat menghapus catatan produksi', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Maaf! Kami tidak dapat menghapus catatan produksi saat ini. Silahkan coba lagi.'
            ], 500);
        }
    }
}
