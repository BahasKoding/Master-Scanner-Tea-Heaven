<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\CatatanProduksi;
use App\Models\HistorySale;
use App\Models\Product;
use App\Models\BahanBaku;
use App\Models\User;
use App\Models\Sticker;
use App\Models\Backend\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Simple date range parsing
            $year = (int) $request->get('year', Carbon::now()->year);
            $month = (int) $request->get('month', Carbon::now()->month);
            $range = $request->get('range', 'month');

            // Basic validation
            if ($year < 2020) $year = Carbon::now()->year;
            if ($month < 1 || $month > 12) $month = Carbon::now()->month;

            // Calculate dates
            if ($range === 'year') {
                $startDate = Carbon::createFromDate($year, 1, 1)->startOfYear();
                $endDate = Carbon::createFromDate($year, 12, 31)->endOfYear();
                $label = "Year {$year}";
            } elseif ($range === 'all') {
                $startDate = Carbon::createFromDate(2020, 1, 1);
                $endDate = Carbon::now()->endOfDay();
                $label = 'All Time';
            } else {
                $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
                $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
                $label = Carbon::createFromDate($year, $month, 1)->format('F Y');
            }

            $dateRange = [
                'start' => $startDate,
                'end' => $endDate,
                'year' => $year,
                'month' => $month,
                'range' => $range,
                'label' => $label
            ];

            // Get basic master data
            $masterData = [
                'total_products' => 0,
                'total_bahan_baku' => 0,
                'total_users' => 0,
                'products_low_stock' => 0,
                'stickers_need_order' => 0
            ];

            // Try to get real counts safely
            try {
                $masterData['total_products'] = Product::count();
            } catch (\Exception $e) {
                Log::error('Product count error: ' . $e->getMessage());
            }
            try {
                $masterData['total_bahan_baku'] = BahanBaku::count();
            } catch (\Exception $e) {
                Log::error('BahanBaku count error: ' . $e->getMessage());
            }
            try {
                $masterData['total_users'] = User::count();
            } catch (\Exception $e) {
                Log::error('User count error: ' . $e->getMessage());
            }
            try {
                $masterData['stickers_need_order'] = Sticker::where('stok_awal', '<', 30)->count();
            } catch (\Exception $e) {
                Log::error('Sticker count error: ' . $e->getMessage());
            }

            // Get activity data with date filter
            $activityData = [
                'sales_count' => 0,
                'production_count' => 0,
                'purchases_count' => 0,
                'total_qty_sold' => 0,
                'total_production_qty' => 0,
                'total_purchase_qty' => 0
            ];

            try {
                $activityData['sales_count'] = HistorySale::whereBetween('created_at', [$startDate, $endDate])->count();
            } catch (\Exception $e) {
                Log::error('Sales count error: ' . $e->getMessage());
            }
            try {
                $activityData['production_count'] = CatatanProduksi::whereBetween('created_at', [$startDate, $endDate])->count();
            } catch (\Exception $e) {
                Log::error('Production count error: ' . $e->getMessage());
            }
            try {
                $activityData['purchases_count'] = Purchase::whereBetween('created_at', [$startDate, $endDate])->count();
            } catch (\Exception $e) {
                Log::error('Purchase count error: ' . $e->getMessage());
            }

            // Simple quantity calculations
            try {
                $activityData['total_production_qty'] = CatatanProduksi::whereBetween('created_at', [$startDate, $endDate])->sum('quantity') ?? 0;
            } catch (\Exception $e) {
                Log::error('Production qty error: ' . $e->getMessage());
            }

            try {
                $activityData['total_purchase_qty'] = Purchase::whereBetween('created_at', [$startDate, $endDate])->sum('qty_pembelian') ?? 0;
            } catch (\Exception $e) {
                Log::error('Purchase qty error: ' . $e->getMessage());
            }

            // Calculate total qty sold safely using model method
            try {
                $activityData['total_qty_sold'] = HistorySale::getTotalQuantitySold($startDate, $endDate);
            } catch (\Exception $e) {
                Log::error('Total qty sold error: ' . $e->getMessage());
                $activityData['total_qty_sold'] = 0;
            }

            // Performance data
            $performanceData = [
                'top_selling_skus' => [],
                'recent_sales' => collect(),
                'recent_production' => collect(),
                'recent_purchases' => collect()
            ];

            // Calculate top selling products safely using model method
            try {
                $performanceData['top_selling_skus'] = HistorySale::getTopSellingSKUs($startDate, $endDate, 5);
            } catch (\Exception $e) {
                Log::error('Top selling calculation error: ' . $e->getMessage());
                $performanceData['top_selling_skus'] = [];
            }

            try {
                $performanceData['recent_sales'] = HistorySale::whereBetween('created_at', [$startDate, $endDate])->latest()->take(10)->get();
            } catch (\Exception $e) {
                Log::error('Recent sales error: ' . $e->getMessage());
            }

            try {
                $performanceData['recent_production'] = CatatanProduksi::whereBetween('created_at', [$startDate, $endDate])->latest()->take(10)->get();
            } catch (\Exception $e) {
                Log::error('Recent production error: ' . $e->getMessage());
            }

            try {
                $performanceData['recent_purchases'] = Purchase::whereBetween('created_at', [$startDate, $endDate])->latest()->take(10)->get();
            } catch (\Exception $e) {
                Log::error('Recent purchases error: ' . $e->getMessage());
            }

            // System health
            $systemHealth = [
                'total_transactions' => 0,
                'data_integrity' => 'Good',
                'last_activity' => null,
                'database_status' => 'Connected'
            ];

            try {
                $systemHealth['total_transactions'] = HistorySale::count() + CatatanProduksi::count() + Purchase::count();
            } catch (\Exception $e) {
                Log::error('Total transactions error: ' . $e->getMessage());
            }

            // Get last activity for current user from Activity table
            try {
                $lastActivity = Activity::where('user_id', auth()->id())
                    ->latest()
                    ->first();
                $systemHealth['last_activity'] = $lastActivity ? $lastActivity->created_at : null;
            } catch (\Exception $e) {
                Log::error('Last activity error: ' . $e->getMessage());
                $systemHealth['last_activity'] = null;
            }

            // Get user activity statistics
            $userStats = [
                'total_logins' => 0,
                'last_login' => null,
                'activities_today' => 0
            ];

            try {
                // Count total logins for current user
                $userStats['total_logins'] = Activity::where('user_id', auth()->id())
                    ->where('category', 'auth')
                    ->where('action', 'login')
                    ->count();

                // Get last login (excluding current session)
                $lastLogin = Activity::where('user_id', auth()->id())
                    ->where('category', 'auth')
                    ->where('action', 'login')
                    ->latest()
                    ->skip(1) // Skip current login
                    ->first();
                $userStats['last_login'] = $lastLogin ? $lastLogin->created_at : null;

                // Count activities today
                $userStats['activities_today'] = Activity::where('user_id', auth()->id())
                    ->whereDate('created_at', Carbon::today())
                    ->count();
            } catch (\Exception $e) {
                Log::error('User stats error: ' . $e->getMessage());
            }

            // Available years - simple approach
            $availableYears = [2023, 2024, Carbon::now()->year];
            $availableYears = array_unique($availableYears);
            sort($availableYears);

            $availableMonths = [
                1 => 'January',
                2 => 'February',
                3 => 'March',
                4 => 'April',
                5 => 'May',
                6 => 'June',
                7 => 'July',
                8 => 'August',
                9 => 'September',
                10 => 'October',
                11 => 'November',
                12 => 'December'
            ];

            return view('dashboard', [
                'masterData' => $masterData,
                'activityData' => $activityData,
                'performanceData' => $performanceData,
                'systemHealth' => $systemHealth,
                'userStats' => $userStats,
                'dateRange' => $dateRange,
                'availableYears' => $availableYears,
                'availableMonths' => $availableMonths
            ]);
        } catch (\Exception $e) {
            Log::error('Dashboard Critical Error: ' . $e->getMessage() . ' at line ' . $e->getLine());

            // Ultra-safe fallback
            return view('dashboard', [
                'masterData' => [
                    'total_products' => 0,
                    'total_bahan_baku' => 0,
                    'total_users' => 0,
                    'products_low_stock' => 0,
                    'stickers_need_order' => 0,
                ],
                'activityData' => [
                    'sales_count' => 0,
                    'production_count' => 0,
                    'purchases_count' => 0,
                    'total_qty_sold' => 0,
                    'total_production_qty' => 0,
                    'total_purchase_qty' => 0,
                ],
                'performanceData' => [
                    'top_selling_skus' => [],
                    'recent_sales' => collect(),
                    'recent_production' => collect(),
                    'recent_purchases' => collect(),
                ],
                'systemHealth' => [
                    'total_transactions' => 0,
                    'data_integrity' => 'Unknown',
                    'last_activity' => null,
                    'database_status' => 'Error'
                ],
                'userStats' => [
                    'total_logins' => 0,
                    'last_login' => null,
                    'activities_today' => 0
                ],
                'dateRange' => [
                    'start' => Carbon::now()->startOfMonth(),
                    'end' => Carbon::now()->endOfMonth(),
                    'year' => Carbon::now()->year,
                    'month' => Carbon::now()->month,
                    'range' => 'month',
                    'label' => Carbon::now()->format('F Y')
                ],
                'availableYears' => [Carbon::now()->year],
                'availableMonths' => [
                    1 => 'January',
                    2 => 'February',
                    3 => 'March',
                    4 => 'April',
                    5 => 'May',
                    6 => 'June',
                    7 => 'July',
                    8 => 'August',
                    9 => 'September',
                    10 => 'October',
                    11 => 'November',
                    12 => 'December'
                ]
            ]);
        }
    }
}
