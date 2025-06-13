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
        $this->middleware('permission:Sticker Export', ['only' => ['export']]);
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
            'stok_masuk.integer' => 'Stok masuk harus berupa angka',
            'stok_masuk.min' => 'Stok masuk tidak boleh kurang dari 0',
            'defect.integer' => 'Jumlah defect harus berupa angka',
            'defect.min' => 'Jumlah defect tidak boleh kurang dari 0',
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
                ->addColumn('dynamic_stok_masuk', function ($row) {
                    return $row->stok_masuk_dynamic;
                })
                ->addColumn('dynamic_produksi', function ($row) {
                    return $row->produksi_dynamic;
                })
                ->addColumn('dynamic_sisa', function ($row) {
                    return $row->sisa_dynamic;
                })
                ->addColumn('formatted_created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('d-m-Y H:i:s') : '-';
                })
                ->addColumn('formatted_status', function ($row) {
                    $autoStatus = $row->auto_status;
                    $statusLabels = [
                        'available' => '<span class="badge bg-success">Available</span>',
                        'need_order' => '<span class="badge bg-danger">Need Order</span>',
                        'active' => '<span class="badge bg-success">Active</span>',
                        'inactive' => '<span class="badge bg-secondary">Inactive</span>',
                        'out_of_stock' => '<span class="badge bg-danger">Out of Stock</span>'
                    ];
                    return $statusLabels[$autoStatus] ?? '<span class="badge bg-warning">Unknown</span>';
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
                ->rawColumns(['action', 'formatted_status'])
                ->smart(true)
                ->startsWithSearch()
                ->make(true);
        }

        try {
            // Get products that are eligible for stickers but don't have stickers yet
            $products = Sticker::getAvailableProductsForStickers();

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
                'stok_masuk' => 'nullable|integer|min:0',
                'defect' => 'nullable|integer|min:0',
                'status' => 'required|string',
            ], $this->getValidationMessages());

            // Set default values for nullable fields
            $validated['stok_masuk'] = $validated['stok_masuk'] ?? 0;
            $validated['defect'] = $validated['defect'] ?? 0;

            // Set default produksi to 0, will be updated automatically
            $validated['produksi'] = 0;

            // Calculate sisa automatically
            $calculatedSisa = $validated['stok_awal'] + $validated['stok_masuk'] - $validated['produksi'] - $validated['defect'];
            $validated['sisa'] = $calculatedSisa;

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
                'product_label' => $product->label,
                'product_packaging' => $product->packaging
            ]);

            // Use centralized eligibility check
            if (!Sticker::isProductEligible($validated['product_id'])) {
                Log::warning('Product not eligible for stickers', [
                    'product_id' => $product->id,
                    'product_label' => $product->label,
                    'product_packaging' => $product->packaging,
                    'eligible_labels' => [1, 2, 5, 10],
                    'eligible_packaging' => ['P1', 'T1', 'T2', '-']
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Produk yang dipilih tidak memenuhi kriteria label atau packaging untuk sticker.'
                ], 422);
            }

            // Attempt to create the sticker
            Log::info('Attempting to create sticker', ['data' => $validated]);

            $sticker = Sticker::create($validated);

            // Auto-sync produksi from catatan produksi
            $sticker->updateProduksiFromCatatanProduksi();

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

            // For inline editing, we only validate the fields that can be changed
            $validated = $request->validate([
                'stok_awal' => 'nullable|integer|min:0',
                'defect' => 'nullable|integer|min:0',
                'sisa' => 'nullable|integer|min:0', // This will be recalculated
                'status' => 'nullable|string',
            ], [
                'stok_awal.integer' => 'Stok awal harus berupa angka',
                'stok_awal.min' => 'Stok awal tidak boleh kurang dari 0',
                'defect.integer' => 'Defect harus berupa angka',
                'defect.min' => 'Defect tidak boleh kurang dari 0',
            ]);

            // Set default values for nullable fields
            $validated['stok_awal'] = $validated['stok_awal'] ?? $sticker->stok_awal ?? 0;
            $validated['defect'] = $validated['defect'] ?? $sticker->defect ?? 0;

            // Auto-calculate sisa using dynamic values
            $stokMasuk = $sticker->stok_masuk_dynamic;
            $produksi = $sticker->produksi_dynamic;
            $calculatedSisa = $validated['stok_awal'] + $stokMasuk - $produksi - $validated['defect'];

            // Set default status if not provided
            if (!isset($validated['status']) || empty($validated['status'])) {
                $validated['status'] = $calculatedSisa < 30 ? 'need_order' : 'active';
            }

            // Update only the static fields, calculated sisa
            $updateData = [
                'stok_awal' => $validated['stok_awal'],
                'defect' => $validated['defect'],
                'sisa' => $calculatedSisa,
                'status' => $validated['status']
            ];

            $sticker->update($updateData);

            // Auto-sync produksi from catatan produksi after update
            $sticker->updateProduksiFromCatatanProduksi();

            // Get product for logging
            $product = $sticker->product;

            // Log activity
            addActivity('sticker', 'update', 'Pengguna mengupdate sticker untuk produk: ' . ($product ? $product->name_product : 'Unknown'), $sticker->id);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Data sticker telah diperbarui.',
                'data' => [
                    'sticker' => $sticker->fresh(),
                    'sisa_calculated' => $calculatedSisa,
                    'stok_masuk_dynamic' => $stokMasuk,
                    'produksi_dynamic' => $produksi
                ]
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi Kesalahan Validasi',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating sticker', [
                'error' => $e->getMessage(),
                'sticker_id' => $id,
                'request_data' => $request->all()
            ]);

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

    /**
     * Export data to Excel/CSV
     */
    public function export(Request $request)
    {
        try {
            // Set memory limit higher for large exports
            ini_set('memory_limit', '512M');

            $query = Sticker::with('product');

            // Apply filters if provided
            if ($request->has('product_id') && !empty($request->product_id)) {
                $query->where('product_id', $request->product_id);
            }

            if ($request->has('status') && !empty($request->status)) {
                $query->where('status', $request->status);
            }

            if ($request->has('ukuran') && !empty($request->ukuran)) {
                $query->where('ukuran', $request->ukuran);
            }

            // Get total count for logging
            $totalRecords = $query->count();
            Log::info("Starting sticker export of {$totalRecords} records");

            // Process in chunks to avoid memory issues
            $data = [];
            $query->orderBy('created_at', 'desc')
                ->chunk(500, function ($stickers) use (&$data) {
                    foreach ($stickers as $sticker) {
                        try {
                            $data[] = [
                                'ID' => $sticker->id,
                                'SKU Produk' => $sticker->product ? $sticker->product->sku : '-',
                                'Nama Produk' => $sticker->product ? $sticker->product->name_product : '-',
                                'Label' => $sticker->product ? $sticker->product->label : '-',
                                'Packaging' => $sticker->product ? $sticker->product->packaging : '-',
                                'Ukuran Sticker' => $sticker->ukuran,
                                'Jumlah per A3' => $sticker->jumlah,
                                'Stok Awal' => $sticker->stok_awal,
                                'Stok Masuk' => $sticker->stok_masuk_dynamic,
                                'Produksi' => $sticker->produksi_dynamic,
                                'Defect' => $sticker->defect,
                                'Sisa' => $sticker->sisa_dynamic,
                                'Status' => ucfirst(str_replace('_', ' ', $sticker->auto_status)),
                                'Dibuat Pada' => $sticker->created_at->format('Y-m-d H:i:s'),
                                'Diperbarui Pada' => $sticker->updated_at->format('Y-m-d H:i:s'),
                            ];
                        } catch (\Exception $e) {
                            Log::error("Error processing sticker record ID {$sticker->id}: " . $e->getMessage());
                            // Add error record for missing data
                            $data[] = [
                                'ID' => $sticker->id,
                                'SKU Produk' => 'ERROR',
                                'Nama Produk' => 'ERROR: ' . substr($e->getMessage(), 0, 30) . '...',
                                'Label' => 'ERROR',
                                'Packaging' => 'ERROR',
                                'Ukuran Sticker' => $sticker->ukuran ?? 'ERROR',
                                'Jumlah per A3' => $sticker->jumlah ?? 'ERROR',
                                'Stok Awal' => $sticker->stok_awal ?? 'ERROR',
                                'Stok Masuk' => $sticker->stok_masuk_dynamic ?? 'ERROR',
                                'Produksi' => $sticker->produksi_dynamic ?? 'ERROR',
                                'Defect' => $sticker->defect ?? 'ERROR',
                                'Sisa' => $sticker->sisa_dynamic ?? 'ERROR',
                                'Status' => $sticker->auto_status ?? 'ERROR',
                                'Dibuat Pada' => $sticker->created_at ? $sticker->created_at->format('Y-m-d H:i:s') : '',
                                'Diperbarui Pada' => $sticker->updated_at ? $sticker->updated_at->format('Y-m-d H:i:s') : '',
                            ];
                        }
                    }
                });

            // Log activity
            $exportDescription = 'Pengguna mengekspor data sticker';
            if ($request->has('product_id') && !empty($request->product_id)) {
                $product = \App\Models\Product::find($request->product_id);
                $exportDescription .= ' untuk produk: ' . ($product ? $product->name_product : 'ID ' . $request->product_id);
            }
            if ($request->has('status') && !empty($request->status)) {
                $exportDescription .= ' dengan status: ' . $request->status;
            }
            if ($request->has('ukuran') && !empty($request->ukuran)) {
                $exportDescription .= ' dengan ukuran: ' . $request->ukuran;
            }
            $exportDescription .= " ({$totalRecords} data)";

            addActivity('sticker', 'export', $exportDescription, null);
            Log::info("Sticker export completed successfully for {$totalRecords} records");

            return response()->json([
                'status' => 'success',
                'data' => $data,
                'count' => count($data)
            ]);
        } catch (\Exception $e) {
            Log::error('Sticker export error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengekspor data sticker: ' . $e->getMessage()
            ], 500);
        }
    }
}
