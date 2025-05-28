<?php

namespace App\Http\Controllers;

use App\Models\Sticker;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\ValidationException;

class StickerController extends Controller
{
    /**
     * Constructor to apply permissions middleware
     */
    public function __construct()
    {
        $this->middleware('permission:Sticker List', ['only' => ['index']]);
        $this->middleware('permission:Sticker Create', ['only' => ['store']]);
        $this->middleware('permission:Sticker Update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:Sticker Delete', ['only' => ['destroy']]);
        $this->middleware('permission:Sticker View', ['only' => ['show']]);
    }

    /**
     * Custom validation messages
     */
    private function getValidationMessages()
    {
        return [
            'product_id.required' => 'Silahkan pilih produk',
            'product_id.exists' => 'Produk yang dipilih tidak valid',
            'ukuran.required' => 'Silahkan masukkan ukuran sticker',
            'ukuran.string' => 'Ukuran sticker harus berupa teks',
            'ukuran.max' => 'Ukuran sticker terlalu panjang (maksimal 255 karakter)',
            'jumlah.required' => 'Silahkan masukkan jumlah sticker',
            'jumlah.string' => 'Jumlah sticker harus berupa teks',
            'jumlah.max' => 'Jumlah sticker terlalu panjang (maksimal 255 karakter)',
            'stok_awal.required' => 'Silahkan masukkan stok awal',
            'stok_awal.integer' => 'Stok awal harus berupa angka',
            'stok_awal.min' => 'Stok awal tidak boleh kurang dari 0',
            'stok_masuk.required' => 'Silahkan masukkan stok masuk',
            'stok_masuk.integer' => 'Stok masuk harus berupa angka',
            'stok_masuk.min' => 'Stok masuk tidak boleh kurang dari 0',
            'produksi.required' => 'Silahkan masukkan jumlah produksi',
            'produksi.integer' => 'Jumlah produksi harus berupa angka',
            'produksi.min' => 'Jumlah produksi tidak boleh kurang dari 0',
            'defect.required' => 'Silahkan masukkan jumlah defect',
            'defect.integer' => 'Jumlah defect harus berupa angka',
            'defect.min' => 'Jumlah defect tidak boleh kurang dari 0',
            'sisa.required' => 'Silahkan masukkan sisa sticker',
            'sisa.integer' => 'Sisa sticker harus berupa angka',
            'sisa.min' => 'Sisa sticker tidak boleh kurang dari 0',
            'status.required' => 'Silahkan pilih status',
            'status.string' => 'Status harus berupa teks',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Sticker::with('product')
                ->select([
                    'stickers.id',
                    'stickers.product_id',
                    'stickers.ukuran',
                    'stickers.jumlah',
                    'stickers.stok_awal',
                    'stickers.stok_masuk',
                    'stickers.produksi',
                    'stickers.defect',
                    'stickers.sisa',
                    'stickers.status',
                    'stickers.created_at',
                    'stickers.updated_at'
                ]);

            // Filter by product if provided
            if ($request->has('product_id') && !empty($request->product_id)) {
                $query->where('stickers.product_id', $request->product_id);
            }

            // Filter by status if provided
            if ($request->has('status') && !empty($request->status)) {
                $query->where('stickers.status', $request->status);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return $row->id;
                })
                ->addColumn('product_name', function ($row) {
                    return $row->product ? $row->product->name_product : '-';
                })
                ->addColumn('product_sku', function ($row) {
                    return $row->product ? $row->product->sku : '-';
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
                ->rawColumns(['action'])
                ->smart(true)
                ->startsWithSearch()
                ->make(true);
        }

        try {
            // Get eligible products (only specific labels)
            $products = Sticker::getEligibleProducts();

            // Get status options
            $statuses = Sticker::getStatusOptions();

            // Get initial data for the view
            $items = [
                'Daftar Sticker' => route('stickers.index'),
            ];

            // Log activity
            addActivity('sticker', 'view', 'Pengguna melihat daftar sticker', null);

            return view('sticker.index', compact('items', 'products', 'statuses'));
        } catch (\Exception $e) {
            // Log the error
            Log::error('Failed to load data for Sticker index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return view with error message
            return view('sticker.index', [
                'items' => ['Daftar Sticker' => route('stickers.index')],
                'products' => [],
                'statuses' => [],
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
            // Log the incoming request data for debugging
            Log::info('Sticker store request received', [
                'request_data' => $request->all(),
                'request_method' => $request->method(),
                'content_type' => $request->header('Content-Type'),
                'is_ajax' => $request->ajax()
            ]);

            // Validate the request
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'ukuran' => 'required|string|max:255',
                'jumlah' => 'required|string|max:255',
                'stok_awal' => 'required|integer|min:0',
                'stok_masuk' => 'required|integer|min:0',
                'produksi' => 'required|integer|min:0',
                'defect' => 'required|integer|min:0',
                'sisa' => 'required|integer|min:0',
                'status' => 'required|string',
            ], $this->getValidationMessages());

            Log::info('Validation passed', ['validated_data' => $validated]);

            // Verify the product has eligible label
            $product = Product::find($validated['product_id']);

            if (!$product) {
                Log::error('Product not found', ['product_id' => $validated['product_id']]);
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan.'
                ], 404);
            }

            Log::info('Product found', [
                'product_id' => $product->id,
                'product_name' => $product->name_product,
                'product_label' => $product->label
            ]);

            if (!in_array($product->label, [1, 2, 5])) {
                Log::warning('Product label not eligible', [
                    'product_id' => $product->id,
                    'product_label' => $product->label,
                    'eligible_labels' => [1, 2, 5]
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Produk yang dipilih tidak memenuhi kriteria label untuk sticker.'
                ], 422);
            }

            // Attempt to create the sticker
            Log::info('Attempting to create sticker', ['data' => $validated]);

            $sticker = Sticker::create($validated);

            Log::info('Sticker created successfully', [
                'sticker_id' => $sticker->id,
                'sticker_data' => $sticker->toArray()
            ]);

            // Log activity
            addActivity('sticker', 'create', 'Pengguna membuat sticker baru untuk produk: ' . $product->name_product, $sticker->id);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Sticker telah ditambahkan ke dalam sistem.',
                'data' => $sticker
            ]);
        } catch (ValidationException $e) {
            Log::error('Validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi Kesalahan Validasi',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database query error', [
                'error_code' => $e->getCode(),
                'error_message' => $e->getMessage(),
                'sql' => $e->getSql() ?? 'N/A',
                'bindings' => $e->getBindings() ?? [],
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan database: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            Log::error('General error in sticker store', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Maaf! Terjadi kesalahan saat menambahkan sticker: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            // Find the sticker
            $sticker = Sticker::with('product')->findOrFail($id);

            Log::info('Permintaan edit sticker diterima', ['sticker' => $sticker->toArray()]);

            // Log activity
            addActivity('sticker', 'edit', 'Pengguna melihat form edit sticker untuk produk: ' . $sticker->product->name_product, $sticker->id);

            return response()->json([
                'success' => true,
                'data' => $sticker
            ]);
        } catch (\Exception $e) {
            Log::error('Error pada edit sticker', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Maaf! Terjadi kesalahan saat mengambil data sticker.'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $sticker = Sticker::findOrFail($id);

            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'ukuran' => 'required|string|max:255',
                'jumlah' => 'required|string|max:255',
                'stok_awal' => 'required|integer|min:0',
                'stok_masuk' => 'required|integer|min:0',
                'produksi' => 'required|integer|min:0',
                'defect' => 'required|integer|min:0',
                'sisa' => 'required|integer|min:0',
                'status' => 'required|string',
            ], $this->getValidationMessages());

            // Verify the product has eligible label
            $product = Product::find($validated['product_id']);
            if (!in_array($product->label, [1, 2, 5])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk yang dipilih tidak memenuhi kriteria label untuk sticker.'
                ], 422);
            }

            $sticker->update($validated);

            // Log activity
            addActivity('sticker', 'update', 'Pengguna mengupdate sticker untuk produk: ' . $product->name_product, $sticker->id);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Data sticker telah diperbarui.',
                'data' => $sticker
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi Kesalahan',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating sticker', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Maaf! Terjadi kesalahan saat memperbarui sticker. Silahkan coba lagi.'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $sticker = Sticker::with('product')->findOrFail($id);
            $productName = $sticker->product ? $sticker->product->name_product : 'Unknown Product';

            $sticker->delete();

            // Log activity
            addActivity('sticker', 'delete', 'Pengguna menghapus sticker untuk produk: ' . $productName, $id);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Sticker telah dihapus dari sistem.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting sticker', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Maaf! Terjadi kesalahan saat menghapus sticker. Silahkan coba lagi.'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $sticker = Sticker::with('product')->findOrFail($id);

            // Log activity
            addActivity('sticker', 'view', 'Pengguna melihat detail sticker untuk produk: ' . $sticker->product->name_product, $sticker->id);

            return response()->json([
                'success' => true,
                'data' => $sticker
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sticker tidak ditemukan.'
            ], 404);
        }
    }
}
