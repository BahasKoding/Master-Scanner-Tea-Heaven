<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\CatatanProduksi;
use App\Models\HistorySale;
use App\Models\Product;
use App\Models\BahanBaku;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with statistics
     */
    public function index()
    {
        try {
            // Get current date for filtering
            $today = Carbon::today();
            $thisMonth = Carbon::now()->startOfMonth();
            $thisYear = Carbon::now()->startOfYear();

            // Master Data Statistics
            $masterData = [
                'total_products' => Product::count(),
                'total_bahan_baku' => BahanBaku::count(),
                'active_users' => User::whereNull('deleted_at')->count(),
                'products_low_stock' => Product::where('stok', '<=', 10)->count(),
            ];

            // Recent Activity Statistics (Today)
            $todayActivity = [
                'sales_today' => HistorySale::whereDate('created_at', $today)->count(),
                'production_today' => CatatanProduksi::whereDate('created_at', $today)->count(),
                'purchases_today' => Purchase::whereDate('created_at', $today)->count(),
                'qty_sold_today' => $this->getTotalQuantitySold($today, $today),
            ];

            // Monthly Statistics
            $monthlyStats = [
                'sales_this_month' => HistorySale::where('created_at', '>=', $thisMonth)->count(),
                'production_this_month' => CatatanProduksi::where('created_at', '>=', $thisMonth)->count(),
                'purchases_this_month' => Purchase::where('created_at', '>=', $thisMonth)->count(),
                'qty_sold_this_month' => $this->getTotalQuantitySold($thisMonth, Carbon::now()),
            ];

            // Top Performance Data
            $topPerformance = [
                'top_selling_skus' => $this->getTopSellingSkus(5),
                'recent_production' => CatatanProduksi::with('product')
                    ->latest()
                    ->take(5)
                    ->get(),
                'recent_purchases' => Purchase::with(['bahanBaku', 'product'])
                    ->latest()
                    ->take(5)
                    ->get(),
                'recent_sales' => HistorySale::latest()
                    ->take(5)
                    ->get(),
            ];

            // System Health
            $systemHealth = [
                'total_transactions' => HistorySale::count() + CatatanProduksi::count() + Purchase::count(),
                'data_integrity' => $this->checkDataIntegrity(),
                'last_activity' => $this->getLastActivity(),
            ];

            return view('dashboard', compact(
                'masterData',
                'todayActivity',
                'monthlyStats',
                'topPerformance',
                'systemHealth'
            ));
        } catch (\Exception $e) {
            // Fallback data in case of error
            return view('dashboard', [
                'masterData' => [
                    'total_products' => 0,
                    'total_bahan_baku' => 0,
                    'active_users' => 0,
                    'products_low_stock' => 0,
                ],
                'todayActivity' => [
                    'sales_today' => 0,
                    'production_today' => 0,
                    'purchases_today' => 0,
                    'qty_sold_today' => 0,
                ],
                'monthlyStats' => [
                    'sales_this_month' => 0,
                    'production_this_month' => 0,
                    'purchases_this_month' => 0,
                    'qty_sold_this_month' => 0,
                ],
                'topPerformance' => [
                    'top_selling_skus' => [],
                    'recent_production' => [],
                    'recent_purchases' => [],
                    'recent_sales' => [],
                ],
                'systemHealth' => [
                    'total_transactions' => 0,
                    'data_integrity' => 'Unknown',
                    'last_activity' => null,
                ]
            ]);
        }
    }

    /**
     * Get total quantity sold in a date range
     */
    private function getTotalQuantitySold($startDate, $endDate)
    {
        $sales = HistorySale::whereBetween('created_at', [$startDate, $endDate])->get();
        $totalQty = 0;

        foreach ($sales as $sale) {
            if (is_array($sale->qty_terjual)) {
                $totalQty += array_sum($sale->qty_terjual);
            } elseif (is_string($sale->qty_terjual)) {
                $quantities = json_decode($sale->qty_terjual, true);
                if (is_array($quantities)) {
                    $totalQty += array_sum($quantities);
                }
            }
        }

        return $totalQty;
    }

    /**
     * Get top selling SKUs
     */
    private function getTopSellingSkus($limit = 5)
    {
        $skuSales = [];
        $sales = HistorySale::all();

        foreach ($sales as $sale) {
            $skus = is_array($sale->produk_terjual) ? $sale->produk_terjual : json_decode($sale->produk_terjual, true);
            $qtys = is_array($sale->qty_terjual) ? $sale->qty_terjual : json_decode($sale->qty_terjual, true);

            if (is_array($skus) && is_array($qtys)) {
                foreach ($skus as $index => $sku) {
                    $qty = isset($qtys[$index]) ? (int)$qtys[$index] : 1;

                    if (!isset($skuSales[$sku])) {
                        $product = Product::where('sku', $sku)->first();
                        $skuSales[$sku] = [
                            'sku' => $sku,
                            'name' => $product ? $product->name_product : 'Unknown Product',
                            'total_sold' => 0,
                            'transactions' => 0
                        ];
                    }

                    $skuSales[$sku]['total_sold'] += $qty;
                    $skuSales[$sku]['transactions']++;
                }
            }
        }

        // Sort by total sold and take top results
        uasort($skuSales, function ($a, $b) {
            return $b['total_sold'] - $a['total_sold'];
        });

        return array_slice($skuSales, 0, $limit);
    }

    /**
     * Check basic data integrity
     */
    private function checkDataIntegrity()
    {
        try {
            $issues = 0;

            // Check for products without SKU
            $productsWithoutSku = Product::whereNull('sku')->orWhere('sku', '')->count();
            if ($productsWithoutSku > 0) $issues++;

            // Check for bahan baku without SKU
            $bahanBakuWithoutSku = BahanBaku::whereNull('sku_induk')->orWhere('sku_induk', '')->count();
            if ($bahanBakuWithoutSku > 0) $issues++;

            // Check for sales with invalid SKUs
            $invalidSales = 0;
            $recentSales = HistorySale::take(100)->get();
            foreach ($recentSales as $sale) {
                $skus = is_array($sale->produk_terjual) ? $sale->produk_terjual : json_decode($sale->produk_terjual, true);
                if (is_array($skus)) {
                    foreach ($skus as $sku) {
                        $exists = Product::where('sku', $sku)->exists();
                        if (!$exists) {
                            $invalidSales++;
                            break;
                        }
                    }
                }
            }
            if ($invalidSales > 0) $issues++;

            if ($issues === 0) return 'Good';
            if ($issues <= 2) return 'Fair';
            return 'Needs Attention';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    /**
     * Get last activity timestamp
     */
    private function getLastActivity()
    {
        try {
            $lastSale = HistorySale::latest()->first();
            $lastProduction = CatatanProduksi::latest()->first();
            $lastPurchase = Purchase::latest()->first();

            $activities = array_filter([
                $lastSale ? $lastSale->created_at : null,
                $lastProduction ? $lastProduction->created_at : null,
                $lastPurchase ? $lastPurchase->created_at : null,
            ]);

            if (empty($activities)) return null;

            return collect($activities)->max();
        } catch (\Exception $e) {
            return null;
        }
    }
}
