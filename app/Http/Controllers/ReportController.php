<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\CatatanProduksi;
use App\Models\HistorySale;
use App\Models\Product;
use App\Models\BahanBaku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Constructor to apply permissions middleware
     */
    public function __construct()
    {
        $this->middleware('permission:Reports View', ['only' => ['purchaseIndex', 'catatanProduksiIndex', 'scannerIndex', 'scannerSummary']]);
        $this->middleware('permission:Reports Export', ['only' => ['purchaseExport', 'catatanProduksiExport', 'scannerExport', 'scannerSummaryExport']]);
    }

    /**
     * Purchase Report Index
     */
    public function purchaseIndex(Request $request)
    {
        if ($request->ajax()) {
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

            // Apply filters
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $endDate = Carbon::parse($request->end_date)->endOfDay();
                $query->whereBetween('purchases.tanggal_kedatangan_barang', [$startDate, $endDate]);
            }

            if ($request->filled('kategori')) {
                $query->where('kategori', $request->kategori);
            }

            if ($request->filled('bahan_baku_id')) {
                $query->where('bahan_baku_id', $request->bahan_baku_id);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('item_name', function ($row) {
                    if ($row->kategori === 'finished_goods') {
                        return $row->product ? $row->product->name_product : 'Unknown Product';
                    }
                    return $row->bahanBaku ? $row->bahanBaku->nama_barang : 'Unknown Bahan Baku';
                })
                ->addColumn('item_sku', function ($row) {
                    if ($row->kategori === 'finished_goods') {
                        return $row->product ? $row->product->sku : '-';
                    }
                    return $row->bahanBaku ? $row->bahanBaku->sku_induk : '-';
                })
                ->editColumn('kategori', function ($row) {
                    return $row->kategori === 'finished_goods' ? 'Finished Goods' : 'Bahan Baku';
                })
                ->editColumn('tanggal_kedatangan_barang', function ($row) {
                    return $row->tanggal_kedatangan_barang ? Carbon::parse($row->tanggal_kedatangan_barang)->format('d/m/Y') : '-';
                })
                ->editColumn('qty_pembelian', function ($row) {
                    return number_format($row->qty_pembelian, 0, ',', '.');
                })
                ->editColumn('qty_barang_masuk', function ($row) {
                    return number_format($row->qty_barang_masuk, 0, ',', '.');
                })
                ->editColumn('total_stok_masuk', function ($row) {
                    return number_format($row->total_stok_masuk, 0, ',', '.');
                })
                ->make(true);
        }

        $bahanBaku = BahanBaku::orderBy('nama_barang')->get();
        $products = Product::orderBy('name_product')->get();

        return view('reports.purchase.index', compact('bahanBaku', 'products'));
    }

    /**
     * Catatan Produksi Report Index
     */
    public function catatanProduksiIndex(Request $request)
    {
        if ($request->ajax()) {
            $query = CatatanProduksi::with(['product'])
                ->select([
                    'catatan_produksis.id',
                    'catatan_produksis.product_id',
                    'catatan_produksis.packaging',
                    'catatan_produksis.quantity',
                    'catatan_produksis.sku_induk',
                    'catatan_produksis.gramasi',
                    'catatan_produksis.total_terpakai',
                    'catatan_produksis.created_at',
                    'catatan_produksis.updated_at'
                ]);

            // Apply filters
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $endDate = Carbon::parse($request->end_date)->endOfDay();
                $query->whereBetween('catatan_produksis.created_at', [$startDate, $endDate]);
            }

            if ($request->filled('product_id')) {
                $query->where('product_id', $request->product_id);
            }

            if ($request->filled('packaging')) {
                $query->where('packaging', 'like', '%' . $request->packaging . '%');
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('product_name', function ($row) {
                    return $row->product ? $row->product->name_product : '-';
                })
                ->addColumn('product_sku', function ($row) {
                    return $row->product ? $row->product->sku : '-';
                })
                ->editColumn('sku_induk', function ($row) {
                    // Get bahan baku details based on IDs
                    $bahanBakuItems = BahanBaku::whereIn('id', $row->sku_induk ?? [])->get();
                    $bahanBakuDetails = $bahanBakuItems->map(function ($item) {
                        return $item->sku_induk . ' - ' . $item->nama_barang;
                    });

                    return $bahanBakuDetails->implode(', ');
                })
                ->editColumn('gramasi', function ($row) {
                    $result = [];
                    if (is_array($row->sku_induk) && is_array($row->gramasi)) {
                        $bahanBakuItems = BahanBaku::whereIn('id', $row->sku_induk)->get()->keyBy('id');

                        foreach ($row->sku_induk as $index => $bahanId) {
                            if (isset($row->gramasi[$index]) && isset($bahanBakuItems[$bahanId])) {
                                $bahan = $bahanBakuItems[$bahanId];
                                $result[] = $bahan->nama_barang . ': ' . $row->gramasi[$index] . ' ' . $bahan->satuan;
                            }
                        }
                    }

                    return !empty($result) ? implode(', ', $result) : implode(', ', $row->gramasi ?? []);
                })
                ->editColumn('total_terpakai', function ($row) {
                    $result = [];
                    if (is_array($row->sku_induk) && is_array($row->total_terpakai)) {
                        $bahanBakuItems = BahanBaku::whereIn('id', $row->sku_induk)->get()->keyBy('id');

                        foreach ($row->sku_induk as $index => $bahanId) {
                            if (isset($row->total_terpakai[$index]) && isset($bahanBakuItems[$bahanId])) {
                                $bahan = $bahanBakuItems[$bahanId];
                                $result[] = $bahan->nama_barang . ': ' . $row->total_terpakai[$index] . ' ' . $bahan->satuan;
                            }
                        }
                    }

                    return !empty($result) ? implode(', ', $result) : implode(', ', $row->total_terpakai ?? []);
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('d/m/Y H:i') : '-';
                })
                ->make(true);
        }

        $products = Product::orderBy('name_product')->get();

        return view('reports.catatan-produksi.index', compact('products'));
    }

    /**
     * Scanner Report Index
     */
    public function scannerIndex(Request $request)
    {
        if ($request->ajax()) {
            // Check if requesting summary data
            if ($request->input('action') === 'get_summary') {
                return $this->getScannerSummary($request);
            }

            $query = HistorySale::select([
                'history_sales.id',
                'history_sales.no_resi',
                'history_sales.no_sku',
                'history_sales.qty',
                'history_sales.created_at',
                'history_sales.updated_at'
            ]);

            // Apply filters
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $endDate = Carbon::parse($request->end_date)->endOfDay();
                $query->whereBetween('history_sales.created_at', [$startDate, $endDate]);
            }

            if ($request->filled('no_resi')) {
                $query->where('no_resi', 'like', '%' . $request->no_resi . '%');
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('no_sku', function ($row) {
                    $products = [];
                    $skus = is_array($row->no_sku) ? $row->no_sku : json_decode($row->no_sku, true);
                    if (is_array($skus)) {
                        foreach ($skus as $sku) {
                            $product = Product::where('sku', $sku)->first();
                            $products[] = $product ? $product->name_product . ' (' . $sku . ')' : $sku;
                        }
                    }
                    return implode(', ', $products);
                })
                ->editColumn('qty', function ($row) {
                    $quantities = is_array($row->qty) ? $row->qty : json_decode($row->qty, true);
                    if (is_array($quantities)) {
                        return implode(', ', $quantities);
                    }
                    return $row->qty;
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('d/m/Y H:i') : '-';
                })
                ->make(true);
        }

        return view('reports.scanner.index');
    }

    /**
     * Get Scanner Summary Data
     */
    private function getScannerSummary(Request $request)
    {
        try {
            $query = HistorySale::select([
                'history_sales.no_sku',
                'history_sales.qty',
                'history_sales.created_at'
            ]);

            // Apply same filters as main data
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $endDate = Carbon::parse($request->end_date)->endOfDay();
                $query->whereBetween('history_sales.created_at', [$startDate, $endDate]);
            }

            if ($request->filled('no_resi')) {
                $query->where('no_resi', 'like', '%' . $request->no_resi . '%');
            }

            $historySales = $query->get();

            // Initialize summary arrays
            $skuSummary = [];
            $categorySummary = [];
            $labelSummary = [];
            $totalQty = 0;
            $totalTransactions = $historySales->count();

            // Process each history sale record
            foreach ($historySales as $sale) {
                $skus = is_array($sale->no_sku) ? $sale->no_sku : json_decode($sale->no_sku, true);
                $quantities = is_array($sale->qty) ? $sale->qty : json_decode($sale->qty, true);

                if (is_array($skus) && is_array($quantities)) {
                    foreach ($skus as $index => $sku) {
                        $qty = isset($quantities[$index]) ? (int)$quantities[$index] : 0;
                        $totalQty += $qty;

                        // Get product details
                        $product = Product::where('sku', $sku)->first();

                        if ($product) {
                            // SKU Summary
                            if (!isset($skuSummary[$sku])) {
                                $skuSummary[$sku] = [
                                    'sku' => $sku,
                                    'name_product' => $product->name_product,
                                    'category' => $product->category_name ?? 'Unknown',
                                    'label' => $product->label_name ?? 'Unknown',
                                    'packaging' => $product->packaging ?? '-',
                                    'qty' => 0,
                                    'transactions' => 0
                                ];
                            }
                            $skuSummary[$sku]['qty'] += $qty;
                            $skuSummary[$sku]['transactions']++;

                            // Category Summary
                            $categoryName = $product->category_name ?? 'Unknown Category';
                            if (!isset($categorySummary[$categoryName])) {
                                $categorySummary[$categoryName] = [
                                    'category' => $categoryName,
                                    'qty' => 0,
                                    'unique_skus' => 0,
                                    'transactions' => 0,
                                    'skus' => []
                                ];
                            }
                            $categorySummary[$categoryName]['qty'] += $qty;
                            $categorySummary[$categoryName]['transactions']++;
                            if (!in_array($sku, $categorySummary[$categoryName]['skus'])) {
                                $categorySummary[$categoryName]['skus'][] = $sku;
                                $categorySummary[$categoryName]['unique_skus']++;
                            }

                            // Label Summary
                            $labelName = $product->label_name ?? 'Unknown Label';
                            if (!isset($labelSummary[$labelName])) {
                                $labelSummary[$labelName] = [
                                    'label' => $labelName,
                                    'qty' => 0,
                                    'unique_skus' => 0,
                                    'transactions' => 0,
                                    'skus' => []
                                ];
                            }
                            $labelSummary[$labelName]['qty'] += $qty;
                            $labelSummary[$labelName]['transactions']++;
                            if (!in_array($sku, $labelSummary[$labelName]['skus'])) {
                                $labelSummary[$labelName]['skus'][] = $sku;
                                $labelSummary[$labelName]['unique_skus']++;
                            }
                        } else {
                            // Handle unknown SKU
                            if (!isset($skuSummary[$sku])) {
                                $skuSummary[$sku] = [
                                    'sku' => $sku,
                                    'name_product' => 'Unknown Product',
                                    'category' => 'Unknown',
                                    'label' => 'Unknown',
                                    'packaging' => '-',
                                    'qty' => 0,
                                    'transactions' => 0
                                ];
                            }
                            $skuSummary[$sku]['qty'] += $qty;
                            $skuSummary[$sku]['transactions']++;
                        }
                    }
                }
            }

            // Sort summaries
            uasort($skuSummary, function ($a, $b) {
                return $b['qty'] - $a['qty']; // Sort by quantity descending
            });

            uasort($categorySummary, function ($a, $b) {
                return $b['qty'] - $a['qty'];
            });

            uasort($labelSummary, function ($a, $b) {
                return $b['qty'] - $a['qty'];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'sku_summary' => array_values($skuSummary),
                    'category_summary' => array_values($categorySummary),
                    'label_summary' => array_values($labelSummary),
                    'total_stats' => [
                        'total_qty' => $totalQty,
                        'total_transactions' => $totalTransactions,
                        'unique_skus' => count($skuSummary),
                        'unique_categories' => count($categorySummary),
                        'unique_labels' => count($labelSummary)
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error generating scanner summary', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil ringkasan data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export Purchase Report
     */
    public function purchaseExport(Request $request)
    {
        // Implementation for export functionality
        // This can be implemented later based on requirements
        return response()->json(['message' => 'Export functionality will be implemented']);
    }

    /**
     * Export Catatan Produksi Report
     */
    public function catatanProduksiExport(Request $request)
    {
        // Implementation for export functionality
        // This can be implemented later based on requirements
        return response()->json(['message' => 'Export functionality will be implemented']);
    }

    /**
     * Export Scanner Report
     */
    public function scannerExport(Request $request)
    {
        try {
            $query = HistorySale::select([
                'history_sales.id',
                'history_sales.no_resi',
                'history_sales.no_sku',
                'history_sales.qty',
                'history_sales.created_at',
                'history_sales.updated_at'
            ]);

            // Apply filters only if not exporting all data
            if (!$request->has('export_all')) {
                if ($request->filled('start_date') && $request->filled('end_date')) {
                    $startDate = Carbon::parse($request->start_date)->startOfDay();
                    $endDate = Carbon::parse($request->end_date)->endOfDay();
                    $query->whereBetween('history_sales.created_at', [$startDate, $endDate]);
                }

                if ($request->filled('no_resi')) {
                    $query->where('no_resi', 'like', '%' . $request->no_resi . '%');
                }
            }

            $historySales = $query->orderBy('created_at', 'desc')->get();

            // Process data for export - group by no_resi like the sales report
            $exportData = [];
            $groupedData = [];

            // Group by no_resi first
            foreach ($historySales as $sale) {
                $noResi = $sale->no_resi;

                if (!isset($groupedData[$noResi])) {
                    $groupedData[$noResi] = [
                        'no_resi' => $noResi,
                        'created_at' => $sale->created_at,
                        'updated_at' => $sale->updated_at,
                        'skus' => [],
                        'quantities' => []
                    ];
                }

                // Decode SKUs and quantities
                $skus = is_array($sale->no_sku) ? $sale->no_sku : json_decode($sale->no_sku, true);
                $quantities = is_array($sale->qty) ? $sale->qty : json_decode($sale->qty, true);

                if (is_array($skus)) {
                    foreach ($skus as $index => $sku) {
                        $qty = isset($quantities[$index]) ? $quantities[$index] : 0;

                        // Get product details
                        $product = Product::where('sku', $sku)->first();
                        $productName = $product ? $product->name_product : 'Unknown Product';

                        $groupedData[$noResi]['skus'][] = $sku . ' - ' . $productName;
                        $groupedData[$noResi]['quantities'][] = $qty;
                    }
                }
            }

            // Convert grouped data to export format
            $counter = 1;
            foreach ($groupedData as $data) {
                $exportData[] = [
                    'No' => $counter++,
                    'Tanggal' => $data['created_at'] ? $data['created_at']->format('d/m/Y H:i') : '-',
                    'No Resi' => $data['no_resi'],
                    'Produk Terjual' => implode(', ', $data['skus']),
                    'Quantity Terjual' => implode(', ', $data['quantities'])
                ];
            }

            return response()->json([
                'status' => 'success',
                'data' => $exportData,
                'count' => count($exportData),
                'message' => 'Data berhasil disiapkan untuk export'
            ]);
        } catch (\Exception $e) {
            Log::error('Error exporting scanner data', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengekspor data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function SummaryFinishedGoods(Request $request)
    {
        if ($request->ajax()) {
            try {
                // Check if requesting monthly sales summary
                if ($request->input('action') === 'get_monthly_summary') {
                    return $this->getMonthlySalesSummary($request);
                }

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

                $this->applyFilters($query, $request);

                return $this->buildDataTableResponse($query, $request);
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
                ], 200);
            }
        }

        return $this->renderView();
    }
    public function SummaryFinishedGoodsData(Request $request)
    {
        return $this->SummaryFinishedGoods($request);
    }

    /**
     * Get Monthly Sales Summary Data
     */
    private function getMonthlySalesSummary(Request $request)
    {
        try {
            $month = $request->input('month', date('m'));
            $year = $request->input('year', date('Y'));

            // Validate month and year
            if (!is_numeric($month) || $month < 1 || $month > 12) {
                $month = date('m');
            }
            if (!is_numeric($year) || $year < 2020 || $year > date('Y') + 1) {
                $year = date('Y');
            }

            // Get all finished goods products first
            $allProducts = Product::select(['id', 'sku', 'name_product', 'category_product', 'label', 'packaging'])
                ->orderBy('name_product')
                ->get();

            // Get sales data for the specified month/year
            $query = HistorySale::select([
                'history_sales.no_sku',
                'history_sales.qty',
                'history_sales.created_at'
            ])->whereYear('created_at', $year)
              ->whereMonth('created_at', $month);

            $historySales = $query->get();

            // Initialize summary arrays
            $skuSummary = [];
            $categorySummary = [];
            $labelSummary = [];
            $dailySummary = [];
            $totalQty = 0;
            $totalTransactions = $historySales->count();

            // Initialize all products with zero values
            foreach ($allProducts as $product) {
                $skuSummary[$product->sku] = [
                    'sku' => $product->sku,
                    'name_product' => $product->name_product,
                    'category' => $product->category_name ?? 'Unknown',
                    'label' => $product->label_name ?? 'Unknown',
                    'packaging' => $product->packaging ?? '-',
                    'qty' => 0,
                    'transactions' => 0
                ];

                // Initialize category summary
                $categoryName = $product->category_name ?? 'Unknown Category';
                if (!isset($categorySummary[$categoryName])) {
                    $categorySummary[$categoryName] = [
                        'category' => $categoryName,
                        'qty' => 0,
                        'unique_skus' => 0,
                        'transactions' => 0,
                        'skus' => []
                    ];
                }
                if (!in_array($product->sku, $categorySummary[$categoryName]['skus'])) {
                    $categorySummary[$categoryName]['skus'][] = $product->sku;
                    $categorySummary[$categoryName]['unique_skus']++;
                }

                // Initialize label summary
                $labelName = $product->label_name ?? 'Unknown Label';
                if (!isset($labelSummary[$labelName])) {
                    $labelSummary[$labelName] = [
                        'label' => $labelName,
                        'qty' => 0,
                        'unique_skus' => 0,
                        'transactions' => 0,
                        'skus' => []
                    ];
                }
                if (!in_array($product->sku, $labelSummary[$labelName]['skus'])) {
                    $labelSummary[$labelName]['skus'][] = $product->sku;
                    $labelSummary[$labelName]['unique_skus']++;
                }
            }

        // Process each history sale record
        foreach ($historySales as $sale) {
            $skus = is_array($sale->no_sku) ? $sale->no_sku : json_decode($sale->no_sku, true);
            $quantities = is_array($sale->qty) ? $sale->qty : json_decode($sale->qty, true);
            $saleDate = $sale->created_at->format('Y-m-d');

            // Initialize daily summary for this date
            if (!isset($dailySummary[$saleDate])) {
                $dailySummary[$saleDate] = [
                    'date' => $saleDate,
                    'formatted_date' => $sale->created_at->format('d/m/Y'),
                    'day_name' => $sale->created_at->format('l'),
                    'qty' => 0,
                    'transactions' => 0,
                    'unique_skus' => []
                ];
            }

            if (is_array($skus) && is_array($quantities)) {
                foreach ($skus as $index => $sku) {
                    $qty = isset($quantities[$index]) ? (int)$quantities[$index] : 0;
                    $totalQty += $qty;

                    // Daily summary
                    $dailySummary[$saleDate]['qty'] += $qty;
                    if (!in_array($sku, $dailySummary[$saleDate]['unique_skus'])) {
                        $dailySummary[$saleDate]['unique_skus'][] = $sku;
                    }

                    // Get product details
                    $product = Product::where('sku', $sku)->first();

                    if ($product) {
                        // Update SKU Summary (already initialized)
                        if (isset($skuSummary[$sku])) {
                            $skuSummary[$sku]['qty'] += $qty;
                            $skuSummary[$sku]['transactions']++;
                        }

                        // Update Category Summary
                        $categoryName = $product->category_name ?? 'Unknown Category';
                        if (isset($categorySummary[$categoryName])) {
                            $categorySummary[$categoryName]['qty'] += $qty;
                            $categorySummary[$categoryName]['transactions']++;
                        }

                        // Update Label Summary
                        $labelName = $product->label_name ?? 'Unknown Label';
                        if (isset($labelSummary[$labelName])) {
                            $labelSummary[$labelName]['qty'] += $qty;
                            $labelSummary[$labelName]['transactions']++;
                        }
                    } else {
                        // Handle unknown SKU (not in products table)
                        if (!isset($skuSummary[$sku])) {
                            $skuSummary[$sku] = [
                                'sku' => $sku,
                                'name_product' => 'Unknown Product',
                                'category' => 'Unknown',
                                'label' => 'Unknown',
                                'packaging' => '-',
                                'qty' => 0,
                                'transactions' => 0
                            ];
                        }
                        $skuSummary[$sku]['qty'] += $qty;
                        $skuSummary[$sku]['transactions']++;
                    }
                }
            }

            // Count transactions per day
            $dailySummary[$saleDate]['transactions']++;
        }

        // Process daily summary to count unique SKUs
        foreach ($dailySummary as &$day) {
            $day['unique_skus_count'] = count($day['unique_skus']);
            unset($day['unique_skus']); // Remove the array to reduce response size
        }

        // Sort summaries
        uasort($skuSummary, function ($a, $b) {
            return $b['qty'] - $a['qty'];
        });

        uasort($categorySummary, function ($a, $b) {
            return $b['qty'] - $a['qty'];
        });

        uasort($labelSummary, function ($a, $b) {
            return $b['qty'] - $a['qty'];
        });

        // Sort daily summary by date
        ksort($dailySummary);

            return response()->json([
                'success' => true,
                'data' => [
                    'sku_summary' => array_values($skuSummary),
                    'category_summary' => array_values($categorySummary),
                    'label_summary' => array_values($labelSummary),
                    'daily_summary' => array_values($dailySummary),
                    'total_stats' => [
                        'total_qty' => $totalQty,
                        'total_transactions' => $totalTransactions,
                        'unique_skus' => count($allProducts), // Total products, not just sold ones
                        'unique_categories' => count($categorySummary),
                        'unique_labels' => count($labelSummary),
                        'total_days' => count($dailySummary),
                        'avg_qty_per_day' => count($dailySummary) > 0 ? round($totalQty / count($dailySummary), 2) : 0,
                        'avg_transactions_per_day' => count($dailySummary) > 0 ? round($totalTransactions / count($dailySummary), 2) : 0
                    ],
                    'period' => [
                        'month' => $month,
                        'year' => $year,
                        'month_name' => Carbon::create($year, $month, 1)->format('F'),
                        'period_display' => Carbon::create($year, $month, 1)->format('F Y'),
                        'is_filtered' => true
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error generating monthly sales summary', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil ringkasan data: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function renderView()
    {
        try {
            $items = ['Daftar Scanner Summary' => route('reports.scanner.summary')];
            $products = Product::orderBy('name_product')->get();
            $categories = Product::getCategoryOptions();
            $labels = Product::getLabelOptions();

            // Generate month and year options
            $months = [];
            for ($i = 1; $i <= 12; $i++) {
                $months[$i] = Carbon::create(null, $i, 1)->format('F');
            }

            $years = [];
            $currentYear = date('Y');
            for ($i = 2020; $i <= $currentYear + 1; $i++) {
                $years[$i] = $i;
            }

            addActivity('scanner', 'view', 'Pengguna melihat daftar scanner summary', null);

            return view('reports.scanner.summary', compact('items', 'products', 'categories', 'labels', 'months', 'years'));
        } catch (\Exception $e) {
            Log::error('Failed to load data for Scanner Summary index', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('reports.scanner.summary', [
                'items' => ['Daftar Scanner Summary' => route('reports.scanner.summary')],
                'products' => [],
                'categories' => [],
                'labels' => [],
                'months' => [],
                'years' => [],
                'error_message' => 'Gagal memuat data. Silakan coba refresh halaman.'
            ]);
        }
    }

}
