<?php

namespace App\Http\Controllers;

use App\Models\PurchaseSticker;
use App\Models\Product;
use App\Models\Sticker;
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
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getDataTableResponse($request);
        }

        return $this->getIndexView();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $products = Product::whereIn('label', [1, 5, 10])->orderBy('name_product')->get();

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
            $validated = $this->validateRequest($request);
            $purchaseSticker = PurchaseSticker::create($validated);

            $this->logActivity('create', $purchaseSticker);

            return $this->successResponse('Berhasil! Purchase stiker telah ditambahkan ke dalam sistem.', $purchaseSticker, $request);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e, $request);
        } catch (\Exception $e) {
            return $this->errorResponse('store', $e, $request);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $purchaseSticker = PurchaseSticker::with('product')->findOrFail($id);
            $this->logActivity('view', $purchaseSticker);

            return view('purchase-sticker.show', compact('purchaseSticker'))
                ->with('item', $this->item);
        } catch (\Exception $e) {
            Log::error('Failed to show purchase sticker', ['id' => $id, 'error' => $e->getMessage()]);
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
            $this->logActivity('edit', $purchaseSticker);

            if (request()->ajax()) {
                return response()->json(['success' => true, 'data' => $purchaseSticker]);
            }

            $products = $this->getEligibleProducts();
            return view('purchase-sticker.edit', compact('purchaseSticker', 'products'))
                ->with('item', $this->item);
        } catch (\Exception $e) {
            return $this->handleEditError($e, $id);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $purchaseSticker = PurchaseSticker::findOrFail($id);
            $validated = $this->validateRequest($request);

            $oldProduct = $purchaseSticker->product;
            $purchaseSticker->update($validated);

            $this->logUpdateActivity($purchaseSticker, $oldProduct);

            return $this->successResponse('Berhasil! Informasi purchase stiker telah diperbarui.', $purchaseSticker, $request);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e, $request);
        } catch (\Exception $e) {
            return $this->errorResponse('update', $e, $request, $id);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $purchaseSticker = PurchaseSticker::with('product')->findOrFail($id);
            $productName = $purchaseSticker->product?->name_product ?? 'Unknown';

            $purchaseSticker->delete();
            addActivity('purchase_sticker', 'delete', 'Pengguna menghapus purchase stiker: ' . $productName, $id);

            return response()->json(['success' => true, 'message' => 'Purchase stiker telah berhasil dihapus dari sistem.']);
        } catch (\Exception $e) {
            Log::error('Failed to delete purchase sticker', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Maaf! Kami tidak dapat menghapus purchase stiker saat ini. Silahkan coba lagi.'], 500);
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
     * Get sticker data by product_id for auto-filling form
     */
    public function getStickerData($productId)
    {
        try {
            $sticker = Sticker::where('product_id', $productId)->first();

            if (!$sticker) {
                return response()->json(['success' => false, 'message' => 'Data sticker untuk produk ini tidak ditemukan']);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'ukuran_stiker' => $sticker->ukuran,
                    'jumlah_per_a3' => $sticker->jumlah
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get sticker data', ['product_id' => $productId, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengambil data sticker'], 500);
        }
    }

    // ========== PRIVATE HELPER METHODS ==========

    /**
     * Get validation rules and messages
     */
    private function validateRequest(Request $request): array
    {
        $rules = [
            'product_id' => 'required|exists:products,id',
            'ukuran_stiker' => 'required|string|max:255',
            'jumlah_stiker' => 'required|integer|min:1',
            'jumlah_order' => 'required|integer|min:0',
            'stok_masuk' => 'nullable|integer|min:0',
            'total_order' => 'nullable|integer|min:0',
        ];

        $messages = [
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

        $validated = $request->validate($rules, $messages);

        // Set default values
        $validated['stok_masuk'] = $validated['stok_masuk'] ?? 0;
        $validated['total_order'] = $validated['total_order'] ?? $validated['jumlah_order'];

        return $validated;
    }

    /**
     * Get eligible products for stickers
     */
    private function getEligibleProducts()
    {
        return Product::whereIn('label', [1, 5, 10])->orderBy('name_product')->get();
    }

    /**
     * Get DataTable response for AJAX requests
     */
    private function getDataTableResponse(Request $request)
    {
        try {
            $query = PurchaseSticker::with('product')->select([
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

            // Apply filters
            if ($request->filled('product_id')) {
                $query->where('purchase_stickers.product_id', $request->product_id);
            }

            if ($request->filled('ukuran_stiker')) {
                $query->where('purchase_stickers.ukuran_stiker', 'like', '%' . $request->ukuran_stiker . '%');
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', fn($row) => $row->id)
                ->addColumn('product_name', fn($row) => $row->product?->name_product ?? '-')
                ->addColumn('product_sku', fn($row) => $row->product?->sku ?? '-')
                ->editColumn('jumlah_stiker', fn($row) => number_format($row->jumlah_stiker))
                ->editColumn('jumlah_order', fn($row) => number_format($row->jumlah_order))
                ->editColumn('stok_masuk', fn($row) => number_format($row->stok_masuk))
                ->editColumn('total_order', fn($row) => number_format($row->total_order))
                ->editColumn('ukuran_stiker', fn($row) => $row->ukuran_stiker ?: '-')
                ->filterColumn('product_name', function ($query, $keyword) {
                    $query->whereHas('product', function ($q) use ($keyword) {
                        $q->where('name_product', 'like', "%{$keyword}%")
                            ->orWhere('sku', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns(['action'])
                ->smart(true)
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Purchase Sticker DataTable generation failed', ['error' => $e->getMessage(), 'request' => $request->all()]);
            return response()->json(['error' => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get index view with required data
     */
    private function getIndexView()
    {
        try {
            $products = $this->getEligibleProducts();
            $items = ['Purchase Stiker' => route('purchase-sticker.index')];

            addActivity('purchase_sticker', 'view', 'Pengguna melihat daftar purchase stiker', null);

            return view('purchase-sticker.index', compact('items', 'products'))->with('item', $this->item);
        } catch (\Exception $e) {
            Log::error('Failed to load Purchase Sticker index', ['error' => $e->getMessage()]);

            return view('purchase-sticker.index', [
                'items' => ['Purchase Stiker' => route('purchase-sticker.index')],
                'products' => [],
                'error_message' => 'Gagal memuat data. Silakan coba refresh halaman.'
            ])->with('item', $this->item);
        }
    }

    /**
     * Log activity for purchase sticker operations
     */
    private function logActivity(string $action, PurchaseSticker $purchaseSticker)
    {
        $productName = $purchaseSticker->product?->name_product ?? 'Unknown';

        $messages = [
            'create' => "Pengguna membuat purchase stiker baru untuk: {$productName} ukuran {$purchaseSticker->ukuran_stiker} sebanyak {$purchaseSticker->jumlah_stiker} pcs",
            'view' => "Pengguna melihat detail purchase stiker: {$productName}",
            'edit' => "Pengguna melihat form edit purchase stiker: {$productName}"
        ];

        addActivity('purchase_sticker', $action, $messages[$action], $purchaseSticker->id);
    }

    /**
     * Log update activity
     */
    private function logUpdateActivity(PurchaseSticker $purchaseSticker, $oldProduct)
    {
        $newProduct = Product::find($purchaseSticker->product_id);
        $newProductName = $newProduct?->name_product ?? 'Unknown';
        $oldProductName = $oldProduct?->name_product ?? 'Unknown';

        addActivity('purchase_sticker', 'update', "Pengguna mengubah purchase stiker dari \"{$oldProductName}\" menjadi \"{$newProductName}\"", $purchaseSticker->id);
    }

    /**
     * Handle successful responses
     */
    private function successResponse(string $message, $data = null, Request $request = null)
    {
        if ($request && $request->ajax()) {
            return response()->json(['success' => true, 'message' => $message, 'data' => $data]);
        }

        return redirect()->route('purchase-sticker.index')->with('success', 'Purchase stiker berhasil disimpan.');
    }

    /**
     * Handle validation error responses
     */
    private function validationErrorResponse(ValidationException $e, Request $request)
    {
        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => 'Terjadi Kesalahan', 'errors' => $e->errors()], 422);
        }

        return redirect()->back()->withErrors($e->errors())->withInput();
    }

    /**
     * Handle general error responses
     */
    private function errorResponse(string $action, \Exception $e, Request $request, $id = null)
    {
        $errorData = ['error' => $e->getMessage(), 'data' => $request->all()];
        if ($id) $errorData['id'] = $id;

        Log::error("Failed to {$action} purchase sticker", $errorData);

        $message = "Maaf! Terjadi kesalahan saat " . ($action === 'store' ? 'menambahkan' : 'memperbarui') . " purchase stiker. Silahkan coba lagi.";

        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => $message], 500);
        }

        return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses data.')->withInput();
    }

    /**
     * Handle edit error responses
     */
    private function handleEditError(\Exception $e, $id)
    {
        Log::error('Failed to edit purchase sticker', ['id' => $id, 'error' => $e->getMessage()]);

        if (request()->ajax()) {
            return response()->json(['success' => false, 'message' => 'Maaf! Kami tidak dapat menemukan informasi purchase stiker. Silahkan muat ulang dan coba lagi.'], 500);
        }

        return redirect()->route('purchase-sticker.index')->with('error', 'Purchase stiker tidak ditemukan.');
    }
}
