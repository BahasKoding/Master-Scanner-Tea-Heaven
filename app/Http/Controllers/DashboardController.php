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
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Get current user and roles
            $currentUser = Auth::user();
            $userRoles = $currentUser->roles->pluck('name');
            $isAdmin = $userRoles->contains('Super Admin') || $userRoles->contains('Admin');

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

            // Get basic master data - filtered by role
            $masterData = [
                'total_products' => 0,
                'total_bahan_baku' => 0,
                'total_users' => 0, // Only for admins
                'products_low_stock' => 0,
                'stickers_need_order' => 0
            ];

            // Basic data available to all roles
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
                $masterData['stickers_need_order'] = Sticker::where('stok_awal', '<', 30)->count();
            } catch (\Exception $e) {
                Log::error('Sticker count error: ' . $e->getMessage());
            }

            // Sensitive data only for admins
            if ($isAdmin) {
                try {
                    $masterData['total_users'] = User::count();
                } catch (\Exception $e) {
                    Log::error('User count error: ' . $e->getMessage());
                }
            } else {
                // Non-admins don't see user count
                $masterData['total_users'] = null;
            }

            // Get activity data with date filter - role-based filtering
            $activityData = [
                'sales_count' => 0,
                'production_count' => 0,
                'purchases_count' => 0,
                'total_qty_sold' => 0,
                'total_production_qty' => 0,
                'total_purchase_qty' => 0
            ];

            // Basic activity data - available to all roles but may be limited
            try {
                if ($isAdmin) {
                    // Admins see all data
                    $activityData['sales_count'] = HistorySale::whereBetween('created_at', [$startDate, $endDate])->count();
                    $activityData['production_count'] = CatatanProduksi::whereBetween('created_at', [$startDate, $endDate])->count();
                    $activityData['purchases_count'] = Purchase::whereBetween('created_at', [$startDate, $endDate])->count();
                } else {
                    // Non-admins see limited data (last 30 days only for security)
                    $limitedStartDate = Carbon::now()->startOfMonth();
                    $limitedEndDate = Carbon::now()->endOfMonth();

                    $activityData['sales_count'] = HistorySale::whereBetween('created_at', [$limitedStartDate, $limitedEndDate])->count();
                    $activityData['production_count'] = CatatanProduksi::whereBetween('created_at', [$limitedStartDate, $limitedEndDate])->count();
                    // Non-admins don't see purchase data
                    $activityData['purchases_count'] = null;
                }
            } catch (\Exception $e) {
                Log::error('Activity count error: ' . $e->getMessage());
            }

            // Quantity calculations - role-based access
            try {
                if ($isAdmin) {
                    $activityData['total_production_qty'] = CatatanProduksi::whereBetween('created_at', [$startDate, $endDate])->sum('quantity') ?? 0;
                    $activityData['total_purchase_qty'] = Purchase::whereBetween('created_at', [$startDate, $endDate])->sum('qty_pembelian') ?? 0;
                    $activityData['total_qty_sold'] = HistorySale::getTotalQuantitySold($startDate, $endDate);
                } else {
                    // Non-admins see limited quantity data
                    $limitedStartDate = Carbon::now()->startOfMonth();
                    $limitedEndDate = Carbon::now()->endOfMonth();

                    $activityData['total_production_qty'] = CatatanProduksi::whereBetween('created_at', [$limitedStartDate, $limitedEndDate])->sum('quantity') ?? 0;
                    $activityData['total_purchase_qty'] = null; // Hidden for non-admins
                    $activityData['total_qty_sold'] = HistorySale::getTotalQuantitySold($limitedStartDate, $limitedEndDate);
                }
            } catch (\Exception $e) {
                Log::error('Quantity calculation error: ' . $e->getMessage());
            }

            // Performance data - role-based access
            $performanceData = [
                'top_selling_skus' => [],
                'recent_sales' => collect(),
                'recent_production' => collect(),
                'recent_purchases' => collect()
            ];

            try {
                if ($isAdmin) {
                    // Admins see full performance data
                    $performanceData['top_selling_skus'] = HistorySale::getTopSellingSKUs($startDate, $endDate, 5);
                    $performanceData['recent_sales'] = HistorySale::whereBetween('created_at', [$startDate, $endDate])->latest()->take(10)->get();
                    $performanceData['recent_production'] = CatatanProduksi::whereBetween('created_at', [$startDate, $endDate])->latest()->take(10)->get();
                    $performanceData['recent_purchases'] = Purchase::whereBetween('created_at', [$startDate, $endDate])->latest()->take(10)->get();
                } else {
                    // Non-admins see limited performance data (current month only)
                    $limitedStartDate = Carbon::now()->startOfMonth();
                    $limitedEndDate = Carbon::now()->endOfMonth();

                    $performanceData['top_selling_skus'] = HistorySale::getTopSellingSKUs($limitedStartDate, $limitedEndDate, 3); // Only top 3
                    $performanceData['recent_sales'] = HistorySale::whereBetween('created_at', [$limitedStartDate, $limitedEndDate])->latest()->take(5)->get(); // Only 5 recent
                    $performanceData['recent_production'] = CatatanProduksi::whereBetween('created_at', [$limitedStartDate, $limitedEndDate])->latest()->take(5)->get();
                    // Recent purchases hidden for non-admins
                    $performanceData['recent_purchases'] = collect();
                }
            } catch (\Exception $e) {
                Log::error('Performance data error: ' . $e->getMessage());
            }

            // System health - only for admins
            $systemHealth = [
                'total_transactions' => 0,
                'data_integrity' => 'Good',
                'last_activity' => null,
                'database_status' => 'Connected'
            ];

            if ($isAdmin) {
                try {
                    $systemHealth['total_transactions'] = HistorySale::count() + CatatanProduksi::count() + Purchase::count();

                    // Get last activity for current user from Activity table
                    $lastActivity = Activity::where('user_id', auth()->id())
                        ->latest()
                        ->first();
                    $systemHealth['last_activity'] = $lastActivity ? $lastActivity->created_at : null;
                } catch (\Exception $e) {
                    Log::error('System health error: ' . $e->getMessage());
                    $systemHealth['database_status'] = 'Error';
                }
            } else {
                // Non-admins see limited system health
                $systemHealth = [
                    'total_transactions' => null, // Hidden
                    'data_integrity' => 'Good', // Generic status
                    'last_activity' => null, // Hidden
                    'database_status' => 'Connected' // Basic status
                ];
            }

            // Get user activity statistics - personalized for all roles
            $userStats = [
                'total_logins' => 0,
                'last_login' => null,
                'activities_today' => 0
            ];

            try {
                // All users can see their own stats
                $userStats['total_logins'] = Activity::where('user_id', auth()->id())
                    ->where('category', 'auth')
                    ->where('action', 'login')
                    ->count();

                $lastLogin = Activity::where('user_id', auth()->id())
                    ->where('category', 'auth')
                    ->where('action', 'login')
                    ->latest()
                    ->skip(1)
                    ->first();
                $userStats['last_login'] = $lastLogin ? $lastLogin->created_at : null;

                $userStats['activities_today'] = Activity::where('user_id', auth()->id())
                    ->whereDate('created_at', Carbon::today())
                    ->count();
            } catch (\Exception $e) {
                Log::error('User stats error: ' . $e->getMessage());
            }

            // Available years - limited for non-admins
            if ($isAdmin) {
                $availableYears = [2023, 2024, Carbon::now()->year];
            } else {
                // Non-admins only see current year
                $availableYears = [Carbon::now()->year];
            }
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

            // Add role information to pass to view
            $roleInfo = [
                'is_admin' => $isAdmin,
                'current_role' => $userRoles->first(),
                'user_name' => $currentUser->name
            ];

            return view('dashboard', [
                'masterData' => $masterData,
                'activityData' => $activityData,
                'performanceData' => $performanceData,
                'systemHealth' => $systemHealth,
                'userStats' => $userStats,
                'dateRange' => $dateRange,
                'availableYears' => $availableYears,
                'availableMonths' => $availableMonths,
                'roleInfo' => $roleInfo
            ]);
        } catch (\Exception $e) {
            Log::error('Dashboard Critical Error: ' . $e->getMessage() . ' at line ' . $e->getLine());

            // Ultra-safe fallback with minimal data
            return view('dashboard', [
                'masterData' => [
                    'total_products' => 0,
                    'total_bahan_baku' => 0,
                    'total_users' => null,
                    'products_low_stock' => 0,
                    'stickers_need_order' => 0,
                ],
                'activityData' => [
                    'sales_count' => 0,
                    'production_count' => 0,
                    'purchases_count' => null,
                    'total_qty_sold' => 0,
                    'total_production_qty' => 0,
                    'total_purchase_qty' => null,
                ],
                'performanceData' => [
                    'top_selling_skus' => [],
                    'recent_sales' => collect(),
                    'recent_production' => collect(),
                    'recent_purchases' => collect(),
                ],
                'systemHealth' => [
                    'total_transactions' => null,
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
                ],
                'roleInfo' => [
                    'is_admin' => false,
                    'current_role' => 'Unknown',
                    'user_name' => 'User'
                ]
            ]);
        }
    }
}
