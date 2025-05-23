<?php

namespace App\Http\Controllers;

use App\Models\FinishedGoods;
use App\Models\Product;
use App\Models\CatatanProduksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Services\StockService;

class FinishedGoodsController extends Controller
{
    protected $stockService;

    /**
     * Constructor to apply permissions middleware
     */
    public function __construct(StockService $stockService)
    {
        $this->middleware('permission:Finished Goods List', ['only' => ['index']]);
        $this->middleware('permission:Finished Goods Create', ['only' => ['store']]);
        $this->middleware('permission:Finished Goods Update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:Finished Goods Delete', ['only' => ['destroy']]);
        $this->middleware('permission:Finished Goods View', ['only' => ['show']]);
        $this->stockService = $stockService;
    }

    /**
     * Custom validation messages
     */
    private function getValidationMessages()
    {
        return [
            'id_product.required' => 'Silahkan pilih produk',
            'id_product.exists' => 'Produk yang dipilih tidak ditemukan',

            'stok_awal.required' => 'Silahkan masukkan stok awal',
            'stok_awal.integer' => 'Stok awal harus berupa angka bulat',
            'stok_awal.min' => 'Stok awal minimal 0',

            'stok_masuk.required' => 'Silahkan masukkan stok masuk',
            'stok_masuk.integer' => 'Stok masuk harus berupa angka bulat',
            'stok_masuk.min' => 'Stok masuk minimal 0',

            'stok_keluar.required' => 'Silahkan masukkan stok keluar',
            'stok_keluar.integer' => 'Stok keluar harus berupa angka bulat',
            'stok_keluar.min' => 'Stok keluar minimal 0',

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
            $query = FinishedGoods::with('product')
                ->select([
                    'finished_goods.id',
                    'finished_goods.id_product',
                    'finished_goods.stok_awal',
                    'finished_goods.stok_masuk',
                    'finished_goods.stok_keluar',
                    'finished_goods.defective',
                    'finished_goods.live_stock',
                    'finished_goods.created_at',
                    'finished_goods.updated_at'
                ]);

            // Apply filters from request
            if ($request->filled('id_product')) {
                $query->where('id_product', $request->id_product);
            }

            // Filter berdasarkan tanggal (default: hari ini)
            $startDate = $request->filled('start_date') ? $request->start_date : now()->startOfDay()->format('Y-m-d');
            $endDate = $request->filled('end_date') ? $request->end_date : now()->endOfDay()->format('Y-m-d');

            // Konversi end_date untuk mencakup seluruh hari
            if ($request->filled('end_date')) {
                $endDate = date('Y-m-d', strtotime($endDate . ' +1 day'));
            } else {
                $endDate = now()->addDay()->startOfDay()->format('Y-m-d');
            }

            $query->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('finished_goods.created_at', [$startDate, $endDate])
                    ->orWhereBetween('finished_goods.updated_at', [$startDate, $endDate]);
            });

            return DataTables::of($query)
                ->addIndexColumn()
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('finished_goods.created_at', $order);
                })
                ->addColumn('action', function ($row) {
                    return $row->id;
                })
                ->addColumn('product_name', function ($row) {
                    return $row->product ? $row->product->name_product : '';
                })
                ->addColumn('product_sku', function ($row) {
                    return $row->product ? $row->product->sku : '';
                })
                ->addColumn('product_packaging', function ($row) {
                    return $row->product ? $row->product->packaging : '';
                })
                ->filterColumn('product_name', function ($query, $keyword) {
                    $query->whereHas('product', function ($q) use ($keyword) {
                        $q->where('name_product', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('product_sku', function ($query, $keyword) {
                    $query->whereHas('product', function ($q) use ($keyword) {
                        $q->where('sku', 'like', "%{$keyword}%");
                    });
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : '';
                })
                ->rawColumns(['action'])
                ->smart(true)
                ->startsWithSearch()
                ->make(true);
        }

        // Get all products for dropdown
        $products = Product::orderBy('name_product')->get();

        // Get initial data for the view with pagination
        $items = [
            'Daftar Finished Goods' => route('finished-goods.index'),
        ];

        // Log activity
        addActivity('finished_goods', 'view', 'Pengguna melihat daftar finished goods', null);

        return view('finished-goods.index', compact('items', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_product' => 'required|exists:products,id',
                'stok_awal' => 'required|integer|min:0',
                'stok_masuk' => 'required|integer|min:0',
                'stok_keluar' => 'required|integer|min:0',
                'defective' => 'required|integer|min:0',
            ], $this->getValidationMessages());

            // Calculate live stock
            $liveStock = $validated['stok_awal'] + $validated['stok_masuk'] - $validated['stok_keluar'] - $validated['defective'];

            // Add live_stock to validated data
            $validated['live_stock'] = $liveStock;

            $finishedGoods = FinishedGoods::create($validated);

            // Get product name for logging
            $productName = Product::find($validated['id_product'])->name_product;

            // Log activity
            addActivity('finished_goods', 'create', 'Pengguna membuat finished goods baru untuk produk: ' . $productName, $finishedGoods->id);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Finished goods telah ditambahkan ke dalam sistem.',
                'data' => $finishedGoods
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi Kesalahan',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error saat membuat finished goods', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Maaf! Terjadi kesalahan saat menambahkan finished goods. Silahkan coba lagi.'
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FinishedGoods $finishedGood)
    {
        try {
            // Eager load the product
            $finishedGood->load('product');

            Log::info('Permintaan edit finished goods diterima', ['finished_goods' => $finishedGood->toArray()]);

            // Log activity
            $productName = $finishedGood->product ? $finishedGood->product->name_product : 'Unknown';
            addActivity('finished_goods', 'edit', 'Pengguna melihat form edit finished goods untuk produk: ' . $productName, $finishedGood->id);

            return response()->json([
                'success' => true,
                'data' => $finishedGood
            ]);
        } catch (\Exception $e) {
            Log::error('Error pada edit finished goods', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Maaf! Kami tidak dapat menemukan informasi finished goods. Silahkan muat ulang dan coba lagi.'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FinishedGoods $finishedGood)
    {
        try {
            $validated = $request->validate([
                'id_product' => 'required|exists:products,id',
                'stok_awal' => 'required|integer|min:0',
                'stok_masuk' => 'required|integer|min:0',
                'stok_keluar' => 'required|integer|min:0',
                'defective' => 'required|integer|min:0',
            ], $this->getValidationMessages());

            // Calculate live stock
            $liveStock = $validated['stok_awal'] + $validated['stok_masuk'] - $validated['stok_keluar'] - $validated['defective'];

            // Add live_stock to validated data
            $validated['live_stock'] = $liveStock;

            // Store old values for logging
            $oldValues = $finishedGood->toArray();
            $oldProductName = $finishedGood->product ? $finishedGood->product->name_product : 'Unknown';

            // Update the record
            $finishedGood->update($validated);

            // Get current product name
            $productName = Product::find($validated['id_product'])->name_product;

            // Log activity
            addActivity('finished_goods', 'update', 'Pengguna mengubah finished goods untuk produk: ' . $productName, $finishedGood->id);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Informasi finished goods telah diperbarui.',
                'data' => $finishedGood
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
     * Remove the specified resource from storage.
     */
    public function destroy(FinishedGoods $finishedGood)
    {
        try {
            // Get product name for logging
            $productName = $finishedGood->product ? $finishedGood->product->name_product : 'Unknown';
            $finishedGoodId = $finishedGood->id;

            $finishedGood->delete();

            // Log activity
            addActivity('finished_goods', 'delete', 'Pengguna menghapus finished goods untuk produk: ' . $productName, $finishedGoodId);

            return response()->json([
                'success' => true,
                'message' => 'Finished goods telah berhasil dihapus dari sistem.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat menghapus finished goods', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Maaf! Kami tidak dapat menghapus finished goods saat ini. Silahkan coba lagi.'
            ], 500);
        }
    }

    /**
     * Update defective stock.
     */
    public function updateDefective(Request $request, FinishedGoods $finishedGood)
    {
        $request->validate([
            'defective' => 'required|integer|min:0',
        ]);

        try {
            $this->stockService->updateDefectiveStock($finishedGood->id_product, $request->defective);
            return redirect()->route('finished-goods.index')
                ->with('success', 'Data stok defective berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Gagal memperbarui stok defective: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Get finished goods data for DataTables.
     */
    public function data(Request $request)
    {
        $query = FinishedGoods::with('product');

        // Filter berdasarkan tanggal (default: hari ini)
        $startDate = $request->filled('start_date') ? $request->start_date : now()->startOfDay()->format('Y-m-d');
        $endDate = $request->filled('end_date') ? $request->end_date : now()->endOfDay()->format('Y-m-d');

        // Konversi end_date untuk mencakup seluruh hari
        if ($request->filled('end_date')) {
            $endDate = date('Y-m-d', strtotime($endDate . ' +1 day'));
        } else {
            $endDate = now()->addDay()->startOfDay()->format('Y-m-d');
        }

        $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('finished_goods.created_at', [$startDate, $endDate])
                ->orWhereBetween('finished_goods.updated_at', [$startDate, $endDate]);
        });

        // Filter berdasarkan produk
        if ($request->filled('id_product')) {
            $query->where('id_product', $request->id_product);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('product_sku', function ($finishedGood) {
                return $finishedGood->product ? $finishedGood->product->sku : '-';
            })
            ->addColumn('product_name', function ($finishedGood) {
                return $finishedGood->product ? $finishedGood->product->name_product : '-';
            })
            ->addColumn('product_packaging', function ($finishedGood) {
                return $finishedGood->product ? $finishedGood->product->packaging : '-';
            })
            ->addColumn('action', function ($finishedGood) {
                return '
                    <button type="button" class="btn btn-sm btn-warning edit-btn" data-id="' . $finishedGood->id . '">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' . $finishedGood->id . '">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
