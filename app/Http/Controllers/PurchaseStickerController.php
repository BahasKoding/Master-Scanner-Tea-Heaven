<?php

namespace App\Http\Controllers;

use App\Models\PurchaseSticker;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\ValidationException;

class PurchaseStickerController extends Controller
{
    protected $item = 'Purchase Stiker';

    /**
     * Constructor to apply permissions middleware
     */
    public function __construct()
    {
        $this->middleware('permission:Purchase Stiker List', ['only' => ['index', 'data']]);
        $this->middleware('permission:Purchase Stiker Create', ['only' => ['create', 'store']]);
        $this->middleware('permission:Purchase Stiker Update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:Purchase Stiker Delete', ['only' => ['destroy']]);
        $this->middleware('permission:Purchase Stiker View', ['only' => ['show']]);
    }

    /**
     * Custom validation messages
     */
    private function getValidationMessages()
    {
        return [
            'product_id.required' => 'Silahkan pilih produk',
            'product_id.exists' => 'Produk yang dipilih tidak valid',
            'ukuran_stiker.required' => 'Silahkan masukkan ukuran stiker',
            'ukuran_stiker.string' => 'Ukuran stiker harus berupa teks',
            'ukuran_stiker.max' => 'Ukuran stiker terlalu panjang (maksimal 255 karakter)',
            'jumlah_stiker.required' => 'Silahkan masukkan jumlah stiker',
            'jumlah_stiker.integer' => 'Jumlah stiker harus berupa angka',
            'jumlah_stiker.min' => 'Jumlah stiker minimal 1',
            'jumlah_order.required' => 'Silahkan masukkan jumlah order',
            'jumlah_order.integer' => 'Jumlah order harus berupa angka',
            'jumlah_order.min' => 'Jumlah order tidak boleh negatif',
            'stok_masuk.integer' => 'Stok masuk harus berupa angka',
            'stok_masuk.min' => 'Stok masuk tidak boleh negatif',
            'total_order.integer' => 'Total order harus berupa angka',
            'total_order.min' => 'Total order tidak boleh negatif',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $query = PurchaseSticker::with('product')
                    ->select([
                        'purchase_stickers.id',
                        'purchase_stickers.product_id',
                        'purchase_stickers.ukuran_stiker',
                        'purchase_stickers.jumlah_stiker',
                        'purchase_stickers.jumlah_order',
                        'purchase_stickers.stok_masuk',
                        'purchase_stickers.total_order',
                        'purchase_stickers.created_at',
                        'purchase_stickers.updated_at'
                    ]);

                // Filter by product if provided
                if ($request->has('product_id') && !empty($request->product_id)) {
                    $query->where('purchase_stickers.product_id', $request->product_id);
                }

                // Filter by ukuran stiker if provided
                if ($request->has('ukuran_stiker') && !empty($request->ukuran_stiker)) {
                    $query->where('purchase_stickers.ukuran_stiker', 'like', '%' . $request->ukuran_stiker . '%');
                }

                $dataTable = DataTables::of($query)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        return $row->id;
                    })
                    ->addColumn('product_name', function ($row) {
                        return $row->product ? $row->product->nama_produk : '-';
                    })
                    ->addColumn('product_sku', function ($row) {
                        return $row->product ? $row->product->sku_produk : '-';
                    })
                    ->editColumn('jumlah_stiker', function ($row) {
                        return number_format($row->jumlah_stiker);
                    })
                    ->editColumn('jumlah_order', function ($row) {
                        return number_format($row->jumlah_order);
                    })
                    ->editColumn('stok_masuk', function ($row) {
                        return number_format($row->stok_masuk);
                    })
                    ->editColumn('total_order', function ($row) {
                        return number_format($row->total_order);
                    })
                    ->editColumn('ukuran_stiker', function ($row) {
                        return $row->ukuran_stiker ?: '-';
                    })
                    ->filterColumn('product_name', function ($query, $keyword) {
                        $query->whereHas('product', function ($q) use ($keyword) {
                            $q->where('nama_produk', 'like', "%{$keyword}%")
                                ->orWhere('sku_produk', 'like', "%{$keyword}%");
                        });
                    })
                    ->rawColumns(['action'])
                    ->smart(true)
                    ->make(true);

                return $dataTable;
            } catch (\Exception $e) {
                Log::error('Purchase Sticker DataTable generation failed', [
                    'error' => $e->getMessage(),
                    'request' => $request->all()
                ]);

                return response()->json([
                    'error' => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage()
                ], 500);
            }
        }

        try {
            // Get products options for filter (only eligible products for stickers)
            $products = Product::whereIn('label', [1, 2, 5])->orderBy('nama_produk')->get();

            // Get initial data for the view
            $items = [
                'Purchase Stiker' => route('purchase-sticker.index'),
            ];

            // Log activity
            addActivity('purchase_sticker', 'view', 'Pengguna melihat daftar purchase stiker', null);

            return view('purchase-sticker.index', compact('items', 'products'))
                ->with('item', $this->item);
        } catch (\Exception $e) {
            Log::error('Failed to load Purchase Sticker index', [
                'error' => $e->getMessage()
            ]);

            return view('purchase-sticker.index', [
                'items' => ['Purchase Stiker' => route('purchase-sticker.index')],
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
            $products = Product::whereIn('label', [1, 2, 5])->orderBy('nama_produk')->get();

            // Log activity
            addActivity('purchase_sticker', 'view_create_form', 'Pengguna melihat form tambah purchase stiker', null);

            return view('purchase-sticker.create', compact('products'))
                ->with('item', $this->item);
        } catch (\Exception $e) {
            Log::error('Failed to load Purchase Sticker create form', [
                'error' => $e->getMessage()
            ]);

            return redirect()->route('purchase-sticker.index')
                ->with('error', 'Gagal memuat form tambah purchase stiker.');
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
                'ukuran_stiker' => 'required|string|max:255',
                'jumlah_stiker' => 'required|integer|min:1',
                'jumlah_order' => 'required|integer|min:0',
                'stok_masuk' => 'nullable|integer|min:0',
                'total_order' => 'nullable|integer|min:0',
            ], $this->getValidationMessages());

            // Set default values
            $validated['stok_masuk'] = $validated['stok_masuk'] ?? 0;
            $validated['total_order'] = $validated['total_order'] ?? $validated['jumlah_order'];

            $purchaseSticker = PurchaseSticker::create($validated);

            // Get product name for logging
            $product = Product::find($validated['product_id']);
            $productName = $product ? $product->nama_produk : 'Unknown';

            // Log activity
            addActivity('purchase_sticker', 'create', 'Pengguna membuat purchase stiker baru untuk: ' . $productName . ' ukuran ' . $purchaseSticker->ukuran_stiker . ' sebanyak ' . $purchaseSticker->jumlah_stiker . ' pcs', $purchaseSticker->id);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil! Purchase stiker telah ditambahkan ke dalam sistem.',
                    'data' => $purchaseSticker
                ]);
            }

            return redirect()->route('purchase-sticker.index')
                ->with('success', 'Purchase stiker berhasil ditambahkan.');
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
            Log::error('Failed to store purchase sticker', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maaf! Terjadi kesalahan saat menambahkan purchase stiker. Silahkan coba lagi.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menambahkan purchase stiker.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $purchaseSticker = PurchaseSticker::with('product')->findOrFail($id);

            // Log activity
            addActivity('purchase_sticker', 'view', 'Pengguna melihat detail purchase stiker: ' . ($purchaseSticker->product ? $purchaseSticker->product->nama_produk : 'Unknown'), $purchaseSticker->id);

            return view('purchase-sticker.show', compact('purchaseSticker'))
                ->with('item', $this->item);
        } catch (\Exception $e) {
            Log::error('Failed to show purchase sticker', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('purchase-sticker.index')
                ->with('error', 'Purchase stiker tidak ditemukan.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $purchaseSticker = PurchaseSticker::with('product')->findOrFail($id);
            $products = Product::whereIn('label', [1, 2, 5])->orderBy('nama_produk')->get();

            // Log activity
            addActivity('purchase_sticker', 'edit', 'Pengguna melihat form edit purchase stiker: ' . ($purchaseSticker->product ? $purchaseSticker->product->nama_produk : 'Unknown'), $purchaseSticker->id);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $purchaseSticker
                ]);
            }

            return view('purchase-sticker.edit', compact('purchaseSticker', 'products'))
                ->with('item', $this->item);
        } catch (\Exception $e) {
            Log::error('Failed to edit purchase sticker', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maaf! Kami tidak dapat menemukan informasi purchase stiker. Silahkan muat ulang dan coba lagi.'
                ], 500);
            }

            return redirect()->route('purchase-sticker.index')
                ->with('error', 'Purchase stiker tidak ditemukan.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $purchaseSticker = PurchaseSticker::findOrFail($id);

            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'ukuran_stiker' => 'required|string|max:255',
                'jumlah_stiker' => 'required|integer|min:1',
                'jumlah_order' => 'required|integer|min:0',
                'stok_masuk' => 'nullable|integer|min:0',
                'total_order' => 'nullable|integer|min:0',
            ], $this->getValidationMessages());

            // Set default values
            $validated['stok_masuk'] = $validated['stok_masuk'] ?? 0;
            $validated['total_order'] = $validated['total_order'] ?? $validated['jumlah_order'];

            $oldProduct = $purchaseSticker->product;
            $purchaseSticker->update($validated);

            // Get new product name for logging
            $newProduct = Product::find($validated['product_id']);
            $newProductName = $newProduct ? $newProduct->nama_produk : 'Unknown';
            $oldProductName = $oldProduct ? $oldProduct->nama_produk : 'Unknown';

            // Log activity
            addActivity('purchase_sticker', 'update', 'Pengguna mengubah purchase stiker dari "' . $oldProductName . '" menjadi "' . $newProductName . '"', $purchaseSticker->id);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil! Informasi purchase stiker telah diperbarui.',
                    'data' => $purchaseSticker
                ]);
            }

            return redirect()->route('purchase-sticker.index')
                ->with('success', 'Purchase stiker berhasil diperbarui.');
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
            Log::error('Failed to update purchase sticker', [
                'id' => $id,
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maaf! Terjadi kesalahan saat memperbarui purchase stiker. Silahkan coba lagi.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui purchase stiker.')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $purchaseSticker = PurchaseSticker::with('product')->findOrFail($id);
            $productName = $purchaseSticker->product ? $purchaseSticker->product->nama_produk : 'Unknown';
            $purchaseStickerId = $purchaseSticker->id;

            $purchaseSticker->delete();

            // Log activity
            addActivity('purchase_sticker', 'delete', 'Pengguna menghapus purchase stiker: ' . $productName, $purchaseStickerId);

            return response()->json([
                'success' => true,
                'message' => 'Purchase stiker telah berhasil dihapus dari sistem.'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete purchase sticker', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Maaf! Kami tidak dapat menghapus purchase stiker saat ini. Silahkan coba lagi.'
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
}
