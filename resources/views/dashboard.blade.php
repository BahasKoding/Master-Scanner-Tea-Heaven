@extends('layouts.main')

@section('title', 'Dashboard')
@section('breadcrumb-item', 'Dashboard')
@section('breadcrumb-item-active', 'Overview')

@section('content')
    <!-- [ Main Content ] start -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-primary text-white me-3">
                            <i class="ti ti-user"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Selamat Datang, {{ Auth::user()->name }}!</h5>
                            <small class="text-muted">
                                Role: <span class="badge bg-primary">{{ Auth::user()->getRoleNames()->first() }}</span>
                                | Last Login:
                                {{ Auth::user()->last_login_at ? Auth::user()->last_login_at->format('d/m/Y H:i') : '-' }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <h6 class="mb-3">System Overview</h6>
        </div>

        <!-- Master Data Stats -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Products</p>
                            <h4 class="mb-0">{{ number_format($masterData['total_products']) }}</h4>
                            @if ($masterData['products_low_stock'] > 0)
                                <small class="text-warning">{{ $masterData['products_low_stock'] }} low stock</small>
                            @endif
                        </div>
                        <div class="text-primary">
                            <i class="ti ti-box fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Bahan Baku</p>
                            <h4 class="mb-0">{{ number_format($masterData['total_bahan_baku']) }}</h4>
                            <small class="text-muted">Raw materials</small>
                        </div>
                        <div class="text-success">
                            <i class="ti ti-package fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Active Users</p>
                            <h4 class="mb-0">{{ number_format($masterData['active_users']) }}</h4>
                            <small class="text-muted">System users</small>
                        </div>
                        <div class="text-info">
                            <i class="ti ti-users fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Total Transactions</p>
                            <h4 class="mb-0">{{ number_format($systemHealth['total_transactions']) }}</h4>
                            <small class="text-muted">All time</small>
                        </div>
                        <div class="text-warning">
                            <i class="ti ti-chart-line fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Activity -->
    <div class="row mb-4">
        <div class="col-12">
            <h6 class="mb-3">Today's Activity</h6>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border">
                <div class="card-body p-3">
                    <div class="text-center">
                        <i class="ti ti-scan text-primary fs-2 mb-2"></i>
                        <h5 class="mb-1">{{ number_format($todayActivity['sales_today']) }}</h5>
                        <p class="text-muted mb-0 small">Sales Scanned</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border">
                <div class="card-body p-3">
                    <div class="text-center">
                        <i class="ph-duotone ph-factory text-success fs-2 mb-2"></i>
                        <h5 class="mb-1">{{ number_format($todayActivity['production_today']) }}</h5>
                        <p class="text-muted mb-0 small">Production</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border">
                <div class="card-body p-3">
                    <div class="text-center">
                        <i class="ti ti-shopping-cart text-info fs-2 mb-2"></i>
                        <h5 class="mb-1">{{ number_format($todayActivity['purchases_today']) }}</h5>
                        <p class="text-muted mb-0 small">Purchases</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border">
                <div class="card-body p-3">
                    <div class="text-center">
                        <i class="ti ti-package text-warning fs-2 mb-2"></i>
                        <h5 class="mb-1">{{ number_format($todayActivity['qty_sold_today']) }}</h5>
                        <p class="text-muted mb-0 small">Qty Sold</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Performance -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border">
                <div class="card-header">
                    <h6 class="mb-0">This Month Performance</h6>
                </div>
                <div class="card-body p-3">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <h5 class="text-primary mb-1">{{ number_format($monthlyStats['sales_this_month']) }}</h5>
                            <p class="text-muted mb-0 small">Sales</p>
                        </div>
                        <div class="col-6 mb-3">
                            <h5 class="text-success mb-1">{{ number_format($monthlyStats['production_this_month']) }}</h5>
                            <p class="text-muted mb-0 small">Production</p>
                        </div>
                        <div class="col-6">
                            <h5 class="text-info mb-1">{{ number_format($monthlyStats['purchases_this_month']) }}</h5>
                            <p class="text-muted mb-0 small">Purchases</p>
                        </div>
                        <div class="col-6">
                            <h5 class="text-warning mb-1">{{ number_format($monthlyStats['qty_sold_this_month']) }}</h5>
                            <p class="text-muted mb-0 small">Qty Sold</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border">
                <div class="card-header">
                    <h6 class="mb-0">System Health</h6>
                </div>
                <div class="card-body p-3">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <small>Data Integrity</small>
                            <span
                                class="badge 
                                @if ($systemHealth['data_integrity'] === 'Good') bg-success
                                @elseif($systemHealth['data_integrity'] === 'Fair') bg-warning  
                                @else bg-danger @endif">
                                {{ $systemHealth['data_integrity'] }}
                            </span>
                        </div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Last Activity:</small>
                        <br>
                        <small>{{ $systemHealth['last_activity'] ? $systemHealth['last_activity']->format('d/m/Y H:i') : 'No recent activity' }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performance & Recent Activity -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card border">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Top Selling Products</h6>
                    <small class="text-muted">All time</small>
                </div>
                <div class="card-body p-0">
                    @if (count($topPerformance['top_selling_skus']) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <tbody>
                                    @foreach ($topPerformance['top_selling_skus'] as $index => $sku)
                                        <tr>
                                            <td class="py-2">
                                                <span class="badge bg-secondary">#{{ $index + 1 }}</span>
                                            </td>
                                            <td class="py-2">
                                                <div>
                                                    <strong>{{ $sku['sku'] }}</strong>
                                                    <br><small
                                                        class="text-muted">{{ Str::limit($sku['name'], 30) }}</small>
                                                </div>
                                            </td>
                                            <td class="py-2 text-end">
                                                <span
                                                    class="badge bg-primary">{{ number_format($sku['total_sold']) }}</span>
                                                <br><small class="text-muted">{{ $sku['transactions'] }} orders</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="ti ti-chart-bar text-muted fs-3"></i>
                            <p class="text-muted mb-0">No sales data yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border">
                <div class="card-header">
                    <h6 class="mb-0">Recent Activities</h6>
                </div>
                <div class="card-body p-0">
                    <div class="activity-timeline" style="max-height: 300px; overflow-y: auto;">
                        @if (count($topPerformance['recent_sales']) > 0)
                            @foreach ($topPerformance['recent_sales'] as $sale)
                                <div class="d-flex align-items-center p-3 border-bottom">
                                    <div class="flex-shrink-0">
                                        <i class="ti ti-scan text-primary"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">{{ $sale->no_resi }}</h6>
                                        <small class="text-muted">
                                            {{ $sale->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="badge bg-light text-dark">
                                            @if (is_array($sale->produk_terjual))
                                                {{ count($sale->produk_terjual) }} items
                                            @else
                                                1 item
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        @if (count($topPerformance['recent_production']) > 0)
                            @foreach ($topPerformance['recent_production'] as $production)
                                <div class="d-flex align-items-center p-3 border-bottom">
                                    <div class="flex-shrink-0">
                                        <i class="ph-duotone ph-factory text-success"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">
                                            {{ $production->product ? $production->product->name_product : 'Unknown Product' }}
                                        </h6>
                                        <small class="text-muted">
                                            {{ $production->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="badge bg-success">{{ number_format($production->quantity) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        @if (count($topPerformance['recent_purchases']) > 0)
                            @foreach ($topPerformance['recent_purchases'] as $purchase)
                                <div class="d-flex align-items-center p-3 border-bottom">
                                    <div class="flex-shrink-0">
                                        <i class="ti ti-shopping-cart text-info"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">{{ $purchase->item_name ?? 'Purchase Item' }}</h6>
                                        <small class="text-muted">
                                            {{ $purchase->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="badge bg-info">{{ number_format($purchase->qty_pembelian) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access & Reports -->
    <div class="row mb-4">
        <div class="col-12">
            <h6 class="mb-3">Quick Access</h6>
        </div>

        <!-- Master Data -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-2">
                        <i class="ti ti-box text-primary me-2"></i>
                        <h6 class="mb-0">Products</h6>
                    </div>
                    <p class="text-muted mb-2 small">Manage products & inventory</p>
                    <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="ti ti-arrow-right me-1"></i>Manage
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-2">
                        <i class="ti ti-package text-success me-2"></i>
                        <h6 class="mb-0">Bahan Baku</h6>
                    </div>
                    <p class="text-muted mb-2 small">Raw materials management</p>
                    <a href="{{ route('bahan-baku.index') }}" class="btn btn-sm btn-outline-success">
                        <i class="ti ti-arrow-right me-1"></i>Manage
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-2">
                        <i class="ph-duotone ph-factory text-info me-2"></i>
                        <h6 class="mb-0">Production</h6>
                    </div>
                    <p class="text-muted mb-2 small">Production records</p>
                    <a href="{{ route('catatan-produksi.index') }}" class="btn btn-sm btn-outline-info">
                        <i class="ti ti-arrow-right me-1"></i>Records
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-2">
                        <i class="ti ti-scan text-warning me-2"></i>
                        <h6 class="mb-0">Scanner</h6>
                    </div>
                    <p class="text-muted mb-2 small">Sales transactions</p>
                    <a href="{{ route('history-sales.index') }}" class="btn btn-sm btn-outline-warning">
                        <i class="ti ti-arrow-right me-1"></i>Scan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports & System -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card border">
                <div class="card-header">
                    <h6 class="mb-0">Reports</h6>
                </div>
                <div class="card-body p-3">
                    <div class="d-grid gap-2">
                        <a href="{{ route('reports.scanner.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="ti ti-chart-line me-1"></i>Scanner Report
                        </a>
                        <a href="{{ route('reports.purchase.index') }}" class="btn btn-sm btn-outline-success">
                            <i class="ti ti-shopping-cart me-1"></i>Purchase Report
                        </a>
                        <a href="{{ route('reports.catatan-produksi.index') }}" class="btn btn-sm btn-outline-info">
                            <i class="ph-duotone ph-factory me-1"></i>Production Report
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            @if (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Super Admin'))
                <div class="card border">
                    <div class="card-header">
                        <h6 class="mb-0">System Management</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="d-grid gap-2">
                            <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="ti ti-users me-1"></i>Users
                            </a>
                            @if (Auth::user()->hasRole('Super Admin'))
                                <a href="{{ route('roles.index') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="ti ti-shield-lock me-1"></i>Roles
                                </a>
                            @endif
                            <a href="{{ route('activity') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="ti ti-activity me-1"></i>Activity Log
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="card border">
                    <div class="card-header">
                        <h6 class="mb-0">User Info</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="text-center">
                            <i class="ti ti-user-circle fs-3 text-muted mb-2"></i>
                            <h6>{{ Auth::user()->name }}</h6>
                            <p class="text-muted mb-0 small">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- [ Main Content ] end -->

    <style>
        .avatar {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .card {
            transition: box-shadow 0.2s ease;
        }

        .card:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .activity-timeline {
            max-height: 300px;
            overflow-y: auto;
        }

        .activity-timeline::-webkit-scrollbar {
            width: 4px;
        }

        .activity-timeline::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .activity-timeline::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 2px;
        }

        .activity-timeline::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
@endsection
