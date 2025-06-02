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

class FinishedGoodsController extends Controller
{
    /**
     * Constructor to apply permissions middleware
     */
    public function __construct()
    {
        $this->middleware('permission:Finished Goods List', ['only' => ['index']]);
        $this->middleware('permission:Finished Goods Create', ['only' => ['store']]);
        $this->middleware('permission:Finished Goods Update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:Finished Goods Delete', ['only' => ['destroy']]);
        $this->middleware('permission:Finished Goods View', ['only' => ['show']]);
    }

    /**
     * Custom validation messages
     */
    private function getValidationMessages()
    {
        return [
            'product_id.required' => 'Silahkan pilih produk',
            'product_id.exists' => 'Produk yang dipilih tidak ditemukan',

            'stok_awal.required' => 'Silahkan masukkan stok awal',
            'stok_awal.integer' => 'Stok awal harus berupa angka bulat',
            'stok_awal.min' => 'Stok awal minimal 0',

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
            try {
                // Query semua produk dengan LEFT JOIN ke finished_goods
                $query = Product::leftJoin('finished_goods', 'products.id', '=', 'finished_goods.product_id')
                    ->select([
                        'products.id as product_id',
                        'products.sku',
                        'products.name_product',
                        'products.packaging',
                        'products.category_product',
                        'products.label',
                        'finished_goods.id as finished_goods_id',
                        'finished_goods.stok_awal',
                        'finished_goods.stok_masuk',
                        'finished_goods.stok_keluar',
                        'finished_goods.defective',
                        'finished_goods.live_stock',
                        'finished_goods.created_at as fg_created_at',
                        'finished_goods.updated_at as fg_updated_at'
                    ]);

                // Filter by product if provided
                if ($request->has('product_id') && !empty($request->product_id)) {
                    $query->where('products.id', $request->product_id);
                }

                // Filter by category if provided
                if ($request->has('category_product') && !empty($request->category_product)) {
                    $query->where('products.category_product', $request->category_product);
                }

                // Filter by label if provided
                if ($request->has('label') && !empty($request->label)) {
                    $query->where('products.label', $request->label);
                }

                $dataTable = DataTables::of($query)
                    ->addIndexColumn()
                    ->orderColumn('name_product', function ($query, $order) {
                        $query->orderBy('products.name_product', $order);
                    })
                    ->addColumn('action', function ($row) {
                        return $row->product_id;
                    })
                    ->addColumn('category_name', function ($row) {
                        try {
                            $categories = Product::getCategoryOptions();
                            return $categories[$row->category_product] ?? 'Unknown Category';
                        } catch (\Exception $e) {
                            Log::error('Error getting category name', ['error' => $e->getMessage()]);
                            return 'Unknown Category';
                        }
                    })
                    ->addColumn('label_name', function ($row) {
                        try {
                            $labels = Product::getLabelOptions();
                            return $labels[$row->label] ?? '-';
                        } catch (\Exception $e) {
                            Log::error('Error getting label name', ['error' => $e->getMessage()]);
                            return '-';
                        }
                    })
                    ->addColumn('stok_awal_display', function ($row) {
                        return $row->stok_awal ?? 0;
                    })
                    ->addColumn('stok_masuk_display', function ($row) {
                        // Show dynamic value from CatatanProduksi
                        try {
                            $dynamicValue = \App\Models\CatatanProduksi::where('product_id', $row->product_id)->sum('quantity');
                            return $dynamicValue;
                        } catch (\Exception $e) {
                            Log::error('Error calculating dynamic stok_masuk', ['error' => $e->getMessage()]);
                            return $row->stok_masuk ?? 0;
                        }
                    })
                    ->addColumn('stok_keluar_display', function ($row) {
                        // Show dynamic value from HistorySale
                        try {
                            $product = \App\Models\Product::find($row->product_id);
                            if (!$product) {
                                return $row->stok_keluar ?? 0;
                            }

                            $totalSales = 0;
                            $historySales = \App\Models\HistorySale::whereNotNull('no_sku')->get();

                            foreach ($historySales as $sale) {
                                $skuArray = is_string($sale->no_sku) ? json_decode($sale->no_sku, true) : $sale->no_sku;
                                $qtyArray = is_string($sale->qty) ? json_decode($sale->qty, true) : $sale->qty;

                                if (is_array($skuArray) && is_array($qtyArray)) {
                                    foreach ($skuArray as $index => $sku) {
                                        if ($sku === $product->sku) {
                                            $totalSales += $qtyArray[$index] ?? 0;
                                        }
                                    }
                                }
                            }

                            return $totalSales;
                        } catch (\Exception $e) {
                            Log::error('Error calculating dynamic stok_keluar', ['error' => $e->getMessage()]);
                            return $row->stok_keluar ?? 0;
                        }
                    })
                    ->addColumn('defective_display', function ($row) {
                        return $row->defective ?? 0;
                    })
                    ->addColumn('live_stock_display', function ($row) {
                        // Calculate dynamic live stock
                        try {
                            $stokAwal = $row->stok_awal ?? 0;
                            $stokMasuk = \App\Models\CatatanProduksi::where('product_id', $row->product_id)->sum('quantity');
                            $defective = $row->defective ?? 0;

                            $product = \App\Models\Product::find($row->product_id);
                            $stokKeluar = 0;

                            if ($product) {
                                $historySales = \App\Models\HistorySale::whereNotNull('no_sku')->get();

                                foreach ($historySales as $sale) {
                                    $skuArray = is_string($sale->no_sku) ? json_decode($sale->no_sku, true) : $sale->no_sku;
                                    $qtyArray = is_string($sale->qty) ? json_decode($sale->qty, true) : $sale->qty;

                                    if (is_array($skuArray) && is_array($qtyArray)) {
                                        foreach ($skuArray as $index => $sku) {
                                            if ($sku === $product->sku) {
                                                $stokKeluar += $qtyArray[$index] ?? 0;
                                            }
                                        }
                                    }
                                }
                            }

                            $liveStock = $stokAwal + $stokMasuk - $stokKeluar - $defective;
                            return max(0, $liveStock); // Ensure not negative
                        } catch (\Exception $e) {
                            Log::error('Error calculating dynamic live_stock', ['error' => $e->getMessage()]);
                            return $row->live_stock ?? 0;
                        }
                    })
                    ->filterColumn('name_product', function ($query, $keyword) {
                        $query->where('products.name_product', 'like', "%{$keyword}%");
                    })
                    ->filterColumn('sku', function ($query, $keyword) {
                        $query->where('products.sku', 'like', "%{$keyword}%");
                    })
                    ->rawColumns(['action'])
                    ->smart(true)
                    ->startsWithSearch()
                    ->make(true);

                return $dataTable;
            } catch (\Exception $e) {
                Log::error('DataTables Ajax error in FinishedGoods index', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'request' => $request->all()
                ]);

                return response()->json([
                    'draw' => intval($request->get('draw')),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'Terjadi kesalahan saat memuat data. Silakan refresh halaman atau hubungi administrator.'
                ], 200); // Return 200 to prevent DataTables error popup
            }
        }

        try {
            // Get all products for dropdown
            $products = Product::orderBy('name_product')->get();

            // Get category options
            $categories = Product::getCategoryOptions();

            // Get label options
            $labels = Product::getLabelOptions();

            // Get initial data for the view with pagination
            $items = [
                'Daftar Finished Goods' => route('finished-goods.index'),
            ];

            // Log activity
            addActivity('finished_goods', 'view', 'Pengguna melihat daftar finished goods', null);

            return view('finished-goods.index', compact('items', 'products', 'categories', 'labels'));
        } catch (\Exception $e) {
            // Log the error
            Log::error('Failed to load data for Finished Goods index', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return view with error message
            return view('finished-goods.index', [
                'items' => ['Daftar Finished Goods' => route('finished-goods.index')],
                'products' => [],
                'categories' => [],
                'labels' => [],
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
                'product_id' => 'required|exists:products,id',
                'stok_awal' => 'required|integer|min:0',
                'defective' => 'required|integer|min:0',
            ], $this->getValidationMessages());

            // Get or create finished goods record
            $finishedGoods = FinishedGoods::firstOrNew(['product_id' => $validated['product_id']]);

            // Set manual input values
            $finishedGoods->stok_awal = $validated['stok_awal'];
            $finishedGoods->defective = $validated['defective'];

            // Auto-calculate stok_masuk and stok_keluar
            $finishedGoods->updateStokMasukFromCatatanProduksi();
            $finishedGoods->updateStokKeluarFromHistorySales();

            // Save the record
            $finishedGoods->save();

            // Get product name for logging
            $productName = Product::find($validated['product_id'])->name_product;

            // Log activity
            $action = $finishedGoods->wasRecentlyCreated ? 'create' : 'update';
            $message = $finishedGoods->wasRecentlyCreated
                ? 'Pengguna membuat finished goods baru untuk produk: ' . $productName
                : 'Pengguna memperbarui finished goods untuk produk: ' . $productName;

            addActivity('finished_goods', $action, $message, $finishedGoods->id);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Data stok finished goods telah diperbarui. Stok masuk dan keluar dihitung otomatis.',
                'data' => [
                    'id' => $finishedGoods->id,
                    'product_id' => $finishedGoods->product_id,
                    'stok_awal' => $finishedGoods->stok_awal,
                    'stok_masuk' => $finishedGoods->stok_masuk,
                    'stok_keluar' => $finishedGoods->stok_keluar,
                    'defective' => $finishedGoods->defective,
                    'live_stock' => $finishedGoods->live_stock,
                    'stok_masuk_dynamic' => $finishedGoods->stok_masuk_dynamic,
                    'stok_keluar_dynamic' => $finishedGoods->stok_keluar_dynamic,
                    'live_stock_dynamic' => $finishedGoods->live_stock_dynamic,
                ]
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
     * Show the form for editing the specified resource.
     */
    public function edit($productId)
    {
        try {
            // Find the product first
            $product = Product::findOrFail($productId);

            // Find or create default finished goods record
            $finishedGoods = FinishedGoods::firstOrNew(['product_id' => $productId]);

            // If it's a new record, set default values
            if (!$finishedGoods->exists) {
                $finishedGoods->stok_awal = 0;
                $finishedGoods->defective = 0;
                // Auto-calculate stok_masuk and stok_keluar for new records
                $finishedGoods->updateStokMasukFromCatatanProduksi();
                $finishedGoods->updateStokKeluarFromHistorySales();
            }

            // Add product information to the response
            $finishedGoods->product_id = $productId;

            // Prepare response data with both static and dynamic values
            $responseData = [
                'id' => $finishedGoods->id,
                'product_id' => $finishedGoods->product_id,
                'stok_awal' => $finishedGoods->stok_awal,
                'stok_masuk' => $finishedGoods->stok_masuk,
                'stok_keluar' => $finishedGoods->stok_keluar,
                'defective' => $finishedGoods->defective,
                'live_stock' => $finishedGoods->live_stock,
                'stok_masuk_dynamic' => $finishedGoods->stok_masuk_dynamic,
                'stok_keluar_dynamic' => $finishedGoods->stok_keluar_dynamic,
                'live_stock_dynamic' => $finishedGoods->live_stock_dynamic,
                'product_name' => $product->name_product,
                'product_sku' => $product->sku,
            ];

            Log::info('Permintaan edit finished goods diterima', [
                'product_id' => $productId,
                'product_name' => $product->name_product,
                'finished_goods' => $responseData
            ]);

            // Log activity
            addActivity('finished_goods', 'edit', 'Pengguna melihat form edit finished goods untuk produk: ' . $product->name_product, $productId);

            return response()->json([
                'success' => true,
                'data' => $responseData
            ]);
        } catch (\Exception $e) {
            Log::error('Error pada edit finished goods', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Maaf! Kami tidak dapat menemukan informasi produk. Silahkan muat ulang dan coba lagi.'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $productId)
    {
        try {
            // Validate that the product exists
            $product = Product::findOrFail($productId);

            $validated = $request->validate([
                'stok_awal' => 'required|integer|min:0',
                'defective' => 'required|integer|min:0',
            ], $this->getValidationMessages());

            // Get or create finished goods record
            $finishedGoods = FinishedGoods::firstOrNew(['product_id' => $productId]);

            // Set manual input values
            $finishedGoods->stok_awal = $validated['stok_awal'];
            $finishedGoods->defective = $validated['defective'];

            // Auto-calculate stok_masuk and stok_keluar
            $finishedGoods->updateStokMasukFromCatatanProduksi();
            $finishedGoods->updateStokKeluarFromHistorySales();

            // Save the record
            $finishedGoods->save();

            // Log activity
            $action = $finishedGoods->wasRecentlyCreated ? 'create' : 'update';
            $message = $finishedGoods->wasRecentlyCreated
                ? 'Pengguna membuat finished goods baru untuk produk: ' . $product->name_product
                : 'Pengguna memperbarui finished goods untuk produk: ' . $product->name_product;

            addActivity('finished_goods', $action, $message, $finishedGoods->id);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Data stok finished goods telah diperbarui. Stok masuk dan keluar dihitung otomatis.',
                'data' => [
                    'id' => $finishedGoods->id,
                    'product_id' => $finishedGoods->product_id,
                    'stok_awal' => $finishedGoods->stok_awal,
                    'stok_masuk' => $finishedGoods->stok_masuk,
                    'stok_keluar' => $finishedGoods->stok_keluar,
                    'defective' => $finishedGoods->defective,
                    'live_stock' => $finishedGoods->live_stock,
                    'stok_masuk_dynamic' => $finishedGoods->stok_masuk_dynamic,
                    'stok_keluar_dynamic' => $finishedGoods->stok_keluar_dynamic,
                    'live_stock_dynamic' => $finishedGoods->live_stock_dynamic,
                ]
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
     * Get finished goods data for DataTables.
     */
    public function data(Request $request)
    {
        try {
            // Validate CSRF token
            if (!$request->hasValidSignature() && !hash_equals(csrf_token(), $request->get('_token'))) {
                Log::warning('Invalid CSRF token in finished goods data request', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
            }

            // Query semua produk dengan LEFT JOIN ke finished_goods
            $query = Product::leftJoin('finished_goods', 'products.id', '=', 'finished_goods.product_id')
                ->select([
                    'products.id as product_id',
                    'products.sku',
                    'products.name_product',
                    'products.packaging',
                    'products.category_product',
                    'products.label',
                    'finished_goods.id as finished_goods_id',
                    'finished_goods.stok_awal',
                    'finished_goods.stok_masuk',
                    'finished_goods.stok_keluar',
                    'finished_goods.defective',
                    'finished_goods.live_stock'
                ]);

            // Filter by product if provided
            if ($request->has('product_id') && !empty($request->product_id)) {
                $query->where('products.id', $request->product_id);
            }

            // Filter by category if provided
            if ($request->has('category_product') && !empty($request->category_product)) {
                $query->where('products.category_product', $request->category_product);
            }

            // Filter by label if provided
            if ($request->has('label') && !empty($request->label)) {
                $query->where('products.label', $request->label);
            }

            $dataTable = DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('category_name', function ($row) {
                    try {
                        $categories = Product::getCategoryOptions();
                        return $categories[$row->category_product] ?? 'Unknown Category';
                    } catch (\Exception $e) {
                        Log::error('Error getting category name in data method', ['error' => $e->getMessage()]);
                        return 'Unknown Category';
                    }
                })
                ->addColumn('label_name', function ($row) {
                    try {
                        $labels = Product::getLabelOptions();
                        return $labels[$row->label] ?? '-';
                    } catch (\Exception $e) {
                        Log::error('Error getting label name in data method', ['error' => $e->getMessage()]);
                        return '-';
                    }
                })
                ->addColumn('stok_awal_display', function ($row) {
                    return $row->stok_awal ?? 0;
                })
                ->addColumn('stok_masuk_display', function ($row) {
                    // Show dynamic value from CatatanProduksi
                    try {
                        $dynamicValue = \App\Models\CatatanProduksi::where('product_id', $row->product_id)->sum('quantity');
                        return $dynamicValue;
                    } catch (\Exception $e) {
                        Log::error('Error calculating dynamic stok_masuk in data method', ['error' => $e->getMessage()]);
                        return $row->stok_masuk ?? 0;
                    }
                })
                ->addColumn('stok_keluar_display', function ($row) {
                    // Show dynamic value from HistorySale
                    try {
                        $product = \App\Models\Product::find($row->product_id);
                        if (!$product) {
                            return $row->stok_keluar ?? 0;
                        }

                        $totalSales = 0;
                        $historySales = \App\Models\HistorySale::whereNotNull('no_sku')->get();

                        foreach ($historySales as $sale) {
                            $skuArray = is_string($sale->no_sku) ? json_decode($sale->no_sku, true) : $sale->no_sku;
                            $qtyArray = is_string($sale->qty) ? json_decode($sale->qty, true) : $sale->qty;

                            if (is_array($skuArray) && is_array($qtyArray)) {
                                foreach ($skuArray as $index => $sku) {
                                    if ($sku === $product->sku) {
                                        $totalSales += $qtyArray[$index] ?? 0;
                                    }
                                }
                            }
                        }

                        return $totalSales;
                    } catch (\Exception $e) {
                        Log::error('Error calculating dynamic stok_keluar in data method', ['error' => $e->getMessage()]);
                        return $row->stok_keluar ?? 0;
                    }
                })
                ->addColumn('defective_display', function ($row) {
                    return $row->defective ?? 0;
                })
                ->addColumn('live_stock_display', function ($row) {
                    // Calculate dynamic live stock
                    try {
                        $stokAwal = $row->stok_awal ?? 0;
                        $stokMasuk = \App\Models\CatatanProduksi::where('product_id', $row->product_id)->sum('quantity');
                        $defective = $row->defective ?? 0;

                        $product = \App\Models\Product::find($row->product_id);
                        $stokKeluar = 0;

                        if ($product) {
                            $historySales = \App\Models\HistorySale::whereNotNull('no_sku')->get();

                            foreach ($historySales as $sale) {
                                $skuArray = is_string($sale->no_sku) ? json_decode($sale->no_sku, true) : $sale->no_sku;
                                $qtyArray = is_string($sale->qty) ? json_decode($sale->qty, true) : $sale->qty;

                                if (is_array($skuArray) && is_array($qtyArray)) {
                                    foreach ($skuArray as $index => $sku) {
                                        if ($sku === $product->sku) {
                                            $stokKeluar += $qtyArray[$index] ?? 0;
                                        }
                                    }
                                }
                            }
                        }

                        $liveStock = $stokAwal + $stokMasuk - $stokKeluar - $defective;
                        return max(0, $liveStock); // Ensure not negative
                    } catch (\Exception $e) {
                        Log::error('Error calculating dynamic live_stock in data method', ['error' => $e->getMessage()]);
                        return $row->live_stock ?? 0;
                    }
                })
                ->addColumn('action', function ($row) {
                    return '
                        <button type="button" class="btn btn-sm btn-warning edit-btn" data-id="' . $row->product_id . '">
                            <i class="fas fa-edit"></i> Edit Stok
                        </button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);

            return $dataTable;
        } catch (\Exception $e) {
            Log::error('Error in FinishedGoods data method', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'draw' => intval($request->get('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Terjadi kesalahan saat memuat data. Silakan refresh halaman atau hubungi administrator.'
            ], 200); // Return 200 to prevent DataTables error popup
        }
    }
}
