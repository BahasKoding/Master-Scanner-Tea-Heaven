@extends('layouts.main')

@section('title', 'Dashboard')
@section('breadcrumb-item', 'Dashboard')

@section('breadcrumb-item-active', 'Overview')

@section('css')
    <!-- map-vector css -->
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/jsvectormap.min.css') }}">
    <!-- Custom dashboard CSS -->
    <style>
        .stat-card {
            border-radius: 10px;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
        }

        .trend-indicator {
            font-size: 12px;
            padding: 3px 8px;
            border-radius: 20px;
        }

        .activity-timeline {
            position: relative;
        }

        .activity-timeline::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            height: 100%;
            width: 2px;
            background: #e0e0e0;
            z-index: 0;
        }

        .timeline-dot {
            position: relative;
            z-index: 1;
        }

        .map-container {
            height: 300px;
            border-radius: 10px;
            overflow: hidden;
        }
    </style>
@endsection

@section('content')
    <!-- [ Main Content ] start -->

    <!-- Stats Cards Row -->
    <div class="row mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card bg-light-primary border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary">
                            <i class="ti ti-users text-white f-20"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0 text-muted">Total Users</h6>
                            <h3 class="mb-0">{{ App\Models\User::count() }}</h3>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="trend-indicator bg-light-success text-success">
                            <i class="ti ti-arrow-up-right"></i> 12.8%
                        </span>
                        <span class="text-muted ms-2">vs last month</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card bg-light-success border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success">
                            <i class="ti ti-chart-pie text-white f-20"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0 text-muted">Activities</h6>
                            <h3 class="mb-0">{{ App\Models\Backend\Activity::count() }}</h3>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="trend-indicator bg-light-success text-success">
                            <i class="ti ti-arrow-up-right"></i> 8.5%
                        </span>
                        <span class="text-muted ms-2">vs last month</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card bg-light-warning border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning">
                            <i class="ti ti-clock text-white f-20"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0 text-muted">Today's Logins</h6>
                            <h3 class="mb-0">
                                {{ App\Models\Backend\Activity::where('category', 'auth')->where('action', 'login')->whereDate('created_at', today())->count() }}
                            </h3>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="trend-indicator bg-light-danger text-danger">
                            <i class="ti ti-arrow-down-right"></i> 3.2%
                        </span>
                        <span class="text-muted ms-2">vs yesterday</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card bg-light-info border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info">
                            <i class="ti ti-calendar text-white f-20"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0 text-muted">This Month</h6>
                            <h3 class="mb-0">
                                {{ App\Models\Backend\Activity::whereMonth('created_at', now()->month)->count() }}</h3>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="trend-indicator bg-light-success text-success">
                            <i class="ti ti-arrow-up-right"></i> 10.3%
                        </span>
                        <span class="text-muted ms-2">vs last month</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Row -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between py-3">
                    <h5 class="mb-0">Activity Overview</h5>
                    <div class="d-flex align-items-center">
                        <select class="form-select form-select-sm me-2" id="chart-period">
                            <option value="week">This Week</option>
                            <option value="month" selected>This Month</option>
                            <option value="year">This Year</option>
                        </select>
                        <div class="dropdown">
                            <a class="avtar avtar-xs btn-link-secondary dropdown-toggle arrow-none" href="#"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons-two-tone f-18">more_vert</i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="#">Download Report</a>
                                <a class="dropdown-item" href="#">Share</a>
                                <a class="dropdown-item" href="#">Print</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="user-activity-chart" style="height: 320px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between py-3">
                    <h5 class="mb-0">Usage Statistics</h5>
                    <div class="dropdown">
                        <a class="avtar avtar-xs btn-link-secondary dropdown-toggle arrow-none" href="#"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons-two-tone f-18">more_vert</i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="#">View Details</a>
                            <a class="dropdown-item" href="#">Export Data</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="user-type-pie-chart" style="height: 320px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity & Auth Activities Row -->
    <div class="row">
        <div class="col-md-12 col-xl-12">
            <div class="card table-card">
                <div class="card-header d-flex align-items-center justify-content-between py-3">
                    <h5 class="mb-0">Recent Activities</h5>
                    <div class="d-flex align-items-center gap-2">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light-primary dropdown-toggle" type="button"
                                id="activityFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ti ti-filter me-1"></i> Filter
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="activityFilterDropdown">
                                <li><a class="dropdown-item active" href="#" data-filter="all">All Activities</a>
                                </li>
                                <li><a class="dropdown-item" href="#" data-filter="login">Login</a></li>
                                <li><a class="dropdown-item" href="#" data-filter="update">Updates</a></li>
                                <li><a class="dropdown-item" href="#" data-filter="create">Creation</a></li>
                            </ul>
                        </div>
                    <div class="dropdown">
                        <a class="avtar avtar-xs btn-link-secondary dropdown-toggle arrow-none" href="#"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                class="material-icons-two-tone f-18">more_vert</i></a>
                        <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="#">View All</a>
                                <a class="dropdown-item" href="#">Export</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="activity-timeline">
                        <div id="activity-list">
                            @php
                                $activities = App\Models\Backend\Activity::with('user')
                                    ->orderBy('created_at', 'desc')
                                        ->take(5)
                                        ->get();
                                @endphp
                                @foreach ($activities as $activity)
                                @php
                                    // Determine icon class based on activity category/action
                                    $categoryIcon = 'bolt';
                                    $categoryBg = 'bg-light-primary';
                                    $categoryColor = 'text-primary';

                                    if (stripos($activity->note, 'login') !== false) {
                                        $categoryIcon = 'login';
                                        $categoryBg = 'bg-light-success';
                                        $categoryColor = 'text-success';
                                    } elseif (
                                        stripos($activity->note, 'update') !== false ||
                                        stripos($activity->note, 'edit') !== false
                                    ) {
                                        $categoryIcon = 'edit';
                                        $categoryBg = 'bg-light-warning';
                                        $categoryColor = 'text-warning';
                                    } elseif (
                                        stripos($activity->note, 'create') !== false ||
                                        stripos($activity->note, 'add') !== false
                                    ) {
                                        $categoryIcon = 'add_circle';
                                        $categoryBg = 'bg-light-info';
                                        $categoryColor = 'text-info';
                                    } elseif (
                                        stripos($activity->note, 'delete') !== false ||
                                        stripos($activity->note, 'remove') !== false
                                    ) {
                                        $categoryIcon = 'delete';
                                        $categoryBg = 'bg-light-danger';
                                        $categoryColor = 'text-danger';
                                    }

                                    // Format date in more readable way
                                    $activityDate = $activity->created_at->format('d M Y');
                                    $activityTime = $activity->created_at->format('H:i');
                                    $activityDay = $activity->created_at->diffForHumans();

                                    // Get the activity category for filtering
                                    $filterCategory = 'all';
                                    if (stripos($activity->note, 'login') !== false) {
                                        $filterCategory = 'login';
                                    } elseif (
                                        stripos($activity->note, 'update') !== false ||
                                        stripos($activity->note, 'edit') !== false
                                    ) {
                                        $filterCategory = 'update';
                                    } elseif (
                                        stripos($activity->note, 'create') !== false ||
                                        stripos($activity->note, 'add') !== false
                                    ) {
                                        $filterCategory = 'create';
                                    }
                                @endphp
                                <div class="d-flex mb-4 pb-3 border-bottom activity-item"
                                    data-category="{{ $filterCategory }}">
                                    <div class="timeline-dot me-3">
                                        <div class="avtar avtar-xs {{ $categoryBg }} rounded-circle">
                                            <i
                                                class="material-icons-two-tone {{ $categoryColor }} f-16">{{ $categoryIcon }}</i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <div>
                                                <h6 class="mb-0">
                                                    @if ($activity->user && !empty($activity->user->name))
                                                        {{ $activity->user->name }}
                                                        @else
                                                            <span class="text-danger">User deleted</span>
                                                        @endif
                                                    <span
                                                        class="badge {{ $categoryBg }} {{ $categoryColor }} ms-2 activity-badge">{{ ucfirst($filterCategory) }}</span>
                                                    </h6>
                                                </div>
                                            <div class="d-flex align-items-center">
                                                <span class="text-muted me-2 d-none d-md-inline"
                                                    title="{{ $activityDate }} at {{ $activityTime }}">{{ $activityDate }}</span>
                                                <span class="badge bg-light-secondary">{{ $activityDay }}</span>
                                            </div>
                                        </div>
                                        <p class="mb-0 text-muted">{{ $activity->note }}</p>
                                        <div class="mt-2 d-flex align-items-center activity-meta">
                                            <small class="text-muted me-2"><i
                                                    class="ti ti-clock-hour-4 me-1"></i>{{ $activityTime }}</small>
                                            @if ($activity->category)
                                                <small class="text-muted me-2"><i
                                                        class="ti ti-tag me-1"></i>{{ $activity->category }}</small>
                                            @endif
                                            @if ($activity->action)
                                                <small class="text-muted"><i
                                                        class="ti ti-activity me-1"></i>{{ $activity->action }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                        </div>
                        <div class="text-center mt-3">
                            <button id="load-more" class="btn btn-primary px-4 py-2" data-skip="5">
                                <i class="ti ti-reload me-2"></i>Load More Activities
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 col-xl-12 mt-4">
            <div class="card table-card">
                <div class="card-header d-flex align-items-center justify-content-between py-3">
                    <h5 class="mb-0">Login/Logout Activities</h5>
                    <div class="d-flex align-items-center gap-2">
                        <select class="form-select form-select-sm" id="auth-activity-period">
                            <option value="all">All Time</option>
                            <option value="today" selected>Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                        </select>
                    <div class="dropdown">
                        <a class="avtar avtar-xs btn-link-secondary dropdown-toggle arrow-none" href="#"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                class="material-icons-two-tone f-18">more_vert</i></a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="{{ route('activity') }}">View All</a>
                                <a class="dropdown-item" href="#">Export</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="auth-stats-summary mb-4">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="card border-0 bg-light-success">
                                    <div class="card-body p-3 text-center">
                                        <i class="material-icons-two-tone text-success f-24 mb-2">login</i>
                                        <h3 class="mb-1" id="login-count">
                                            {{ App\Models\Backend\Activity::where('category', 'auth')->where('action', 'login')->whereDate('created_at', today())->count() }}
                                        </h3>
                                        <p class="mb-0 text-muted">Logins</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card border-0 bg-light-danger">
                                    <div class="card-body p-3 text-center">
                                        <i class="material-icons-two-tone text-danger f-24 mb-2">logout</i>
                                        <h3 class="mb-1" id="logout-count">
                                            {{ App\Models\Backend\Activity::where('category', 'auth')->where('action', 'logout')->whereDate('created_at', today())->count() }}
                                        </h3>
                                        <p class="mb-0 text-muted">Logouts</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="auth-activity-list">
                        @php
                            $authActivities = App\Models\Backend\Activity::with('user')
                                ->where('category', 'auth')
                                        ->orderBy('created_at', 'desc')
                                        ->take(5)
                                        ->get();
                                @endphp
                                @foreach ($authActivities as $activity)
                            @php
                                    $iconClass = $activity->action == 'login' ? 'text-success' : 'text-danger';
                                    $icon = $activity->action == 'login' ? 'login' : 'logout';
                                $bgClass = $activity->action == 'login' ? 'bg-light-success' : 'bg-light-danger';

                                // Format date in more readable way
                                $activityDate = $activity->created_at->format('d M Y');
                                $activityTime = $activity->created_at->format('H:i');
                                $activityDay = $activity->created_at->diffForHumans();

                                // Get device and location info from note if available
                                $device = 'Unknown device';
                                $location = 'Unknown location';

                                if (stripos($activity->note, 'from') !== false) {
                                    $parts = explode('from', $activity->note);
                                    if (count($parts) > 1) {
                                        $locationPart = trim($parts[1]);
                                        $location = $locationPart;

                                        // Extract device info if format allows
                                        if (stripos($activity->note, 'using') !== false) {
                                            $deviceParts = explode('using', $activity->note);
                                            if (count($deviceParts) > 1) {
                                                $device = trim($deviceParts[1]);
                                            }
                                        }
                                    }
                                }
                            @endphp
                            <div class="d-flex mb-4 pb-3 border-bottom auth-activity-item"
                                data-date="{{ $activity->created_at->format('Y-m-d') }}">
                                <div class="avtar avtar-xs {{ $bgClass }} rounded-circle me-3">
                                    <i class="material-icons-two-tone {{ $iconClass }} f-16">{{ $icon }}</i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <h6 class="mb-0 d-flex align-items-center">
                                            @if ($activity->user && !empty($activity->user->name))
                                                {{ $activity->user->name }}
                                                        @else
                                                            <span class="text-danger">User deleted</span>
                                                        @endif
                                            <span
                                                class="badge {{ $bgClass }} {{ $iconClass }} ms-2 text-uppercase">{{ $activity->action }}</span>
                                                    </h6>
                                        <small class="text-muted badge bg-light">{{ $activityDay }}</small>
                                    </div>
                                    <p class="mb-0 text-muted">{{ $activity->note }}</p>
                                    <div class="mt-2 row auth-activity-details">
                                        <div class="col-6">
                                            <small class="d-flex align-items-center text-muted mb-1">
                                                <i class="ti ti-calendar-time me-1"></i> {{ $activityTime }}
                                            </small>
                                        </div>
                                        <div class="col-6">
                                            <small class="d-flex align-items-center text-muted mb-1">
                                                <i class="ti ti-calendar me-1"></i> {{ $activityDate }}
                                            </small>
                                        </div>
                                        <div class="col-12">
                                            <small class="d-flex align-items-center text-muted">
                                                <i class="ti ti-device-laptop me-1"></i> {{ $device }}
                                            </small>
                                        </div>
                                    </div>
                                                </div>
                                            </div>
                                @endforeach
                    </div>
                    <div class="text-center mt-3">
                        <button id="load-more-auth" class="btn btn-primary btn-sm px-3 py-2" data-skip="5">
                            <i class="ti ti-reload me-1"></i> Load More
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- [ Main Content ] end -->
@endsection

@section('scripts')
    <!-- [Page Specific JS] start -->
    <script src="{{ URL::asset('build/js/plugins/apexcharts.min.js') }}"></script>
    <!-- Map related plugins dihapus -->
    <!-- [Page Specific JS] end -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // User Activity Chart (Area Chart)
            var userActivityOptions = {
                series: [{
                        name: "Total Activities",
                        data: [45, 52, 38, 24, 33, 26, 21, 20, 6, 8, 15, 10]
                    },
                    {
                        name: "Login Activities",
                        data: [35, 41, 62, 42, 13, 18, 29, 37, 36, 51, 32, 35]
                    }
                ],
                chart: {
                    height: 320,
                    type: 'area',
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    }
                },
                colors: ['#4680ff', '#00acc1'],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.5,
                        opacityTo: 0.1,
                        stops: [0, 90, 100]
                    }
                },
                grid: {
                    strokeDashArray: 3
                },
                xaxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
                        'Dec'
                    ],
                },
                tooltip: {
                    x: {
                        format: 'dd/MM/yy HH:mm'
                    },
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right'
                }
            };

            var userActivityChart = new ApexCharts(
                document.querySelector("#user-activity-chart"),
                userActivityOptions
            );
            userActivityChart.render();

            // User Type Pie Chart
            var userPieOptions = {
                series: [44, 55, 13, 43],
                chart: {
                    width: '100%',
                    height: 320,
                    type: 'pie',
                },
                colors: ['#4680ff', '#00acc1', '#ffba57', '#ff5252'],
                labels: ['Administrators', 'Staff', 'Customers', 'Guests'],
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }],
                legend: {
                    position: 'bottom',
                }
            };

            var userPieChart = new ApexCharts(
                document.querySelector("#user-type-pie-chart"),
                userPieOptions
            );
            userPieChart.render();

            // Activity filtering
            const activityFilterLinks = document.querySelectorAll('[data-filter]');
            activityFilterLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Update active state
                    activityFilterLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');

                    const filterValue = this.getAttribute('data-filter');
                    const activities = document.querySelectorAll('.activity-item');

                    activities.forEach(item => {
                        if (filterValue === 'all' || item.getAttribute('data-category') ===
                            filterValue) {
                            item.style.display = 'flex';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            });

            // Auth activity period filter
            const authPeriodSelect = document.getElementById('auth-activity-period');
            if (authPeriodSelect) {
                authPeriodSelect.addEventListener('change', function() {
                    const period = this.value;
                    const authActivities = document.querySelectorAll('.auth-activity-item');
                    const today = new Date().toISOString().split('T')[0];
                    const weekAgo = new Date(Date.now() - 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[
                        0];
                    const monthAgo = new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split(
                        'T')[0];

                    let loginCount = 0;
                    let logoutCount = 0;

                    authActivities.forEach(item => {
                        const itemDate = item.getAttribute('data-date');
                        const isLogin = item.querySelector('.badge.text-uppercase').textContent
                            .toLowerCase() === 'login';
                        let shouldShow = false;

                        if (period === 'all') {
                            shouldShow = true;
                        } else if (period === 'today' && itemDate === today) {
                            shouldShow = true;
                        } else if (period === 'week' && itemDate >= weekAgo) {
                            shouldShow = true;
                        } else if (period === 'month' && itemDate >= monthAgo) {
                            shouldShow = true;
                        }

                        if (shouldShow) {
                            item.style.display = 'flex';
                            if (isLogin) {
                                loginCount++;
                            } else {
                                logoutCount++;
                            }
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    // Update counters without AJAX
                    document.getElementById('login-count').textContent = loginCount;
                    document.getElementById('logout-count').textContent = logoutCount;
                });
            }

            // Load more activities
            const loadMoreButton = document.getElementById('load-more');
            if (loadMoreButton) {
                loadMoreButton.addEventListener('click', function() {
                    let skip = this.dataset.skip;
                    $(this).html('<i class="spinner-border spinner-border-sm me-2"></i> Loading...');
                    $(this).prop('disabled', true);

                    $.ajax({
                        url: "{{ route('load.more.activities') }}",
                        type: 'GET',
                        data: {
                            skip: skip
                        },
                        success: function(response) {
                            if (response.length > 0) {
                                $.each(response, function(index, activity) {
                                    let user = activity.user;

                                    // Format dates with native JavaScript
                                    let createdDate = new Date(activity.created_at);
                                    let timeAgo = formatTimeAgo(createdDate);
                                    let activityDate = formatDate(createdDate,
                                        'D MMM YYYY');
                                    let activityTime = formatTime(createdDate);

                                    // Determine icon class based on activity note
                                    let categoryIcon = 'bolt';
                                    let categoryBg = 'bg-light-primary';
                                    let categoryColor = 'text-primary';
                                    let filterCategory = 'all';

                                    if (activity.note.toLowerCase().includes(
                                            'login')) {
                                        categoryIcon = 'login';
                                        categoryBg = 'bg-light-success';
                                        categoryColor = 'text-success';
                                        filterCategory = 'login';
                                    } else if (activity.note.toLowerCase().includes(
                                            'update') ||
                                        activity.note.toLowerCase().includes('edit')) {
                                        categoryIcon = 'edit';
                                        categoryBg = 'bg-light-warning';
                                        categoryColor = 'text-warning';
                                        filterCategory = 'update';
                                    } else if (activity.note.toLowerCase().includes(
                                            'create') ||
                                        activity.note.toLowerCase().includes('add')) {
                                        categoryIcon = 'add_circle';
                                        categoryBg = 'bg-light-info';
                                        categoryColor = 'text-info';
                                        filterCategory = 'create';
                                    } else if (activity.note.toLowerCase().includes(
                                            'delete') ||
                                        activity.note.toLowerCase().includes('remove')
                                    ) {
                                        categoryIcon = 'delete';
                                        categoryBg = 'bg-light-danger';
                                        categoryColor = 'text-danger';
                                    }

                                    let activityItem = `
                                    <div class="d-flex mb-4 pb-3 border-bottom activity-item" data-category="${filterCategory}">
                                        <div class="timeline-dot me-3">
                                            <div class="avtar avtar-xs ${categoryBg} rounded-circle">
                                                <i class="material-icons-two-tone ${categoryColor} f-16">${categoryIcon}</i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <div>
                                                    <h6 class="mb-0">
                                                        ${user ? user.name : 'Deleted User'}
                                                        <span class="badge ${categoryBg} ${categoryColor} ms-2 activity-badge">${filterCategory.charAt(0).toUpperCase() + filterCategory.slice(1)}</span>
                                                    </h6>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <span class="text-muted me-2 d-none d-md-inline" title="${activityDate} at ${activityTime}">${activityDate}</span>
                                                    <span class="badge bg-light-secondary">${timeAgo}</span>
                                                </div>
                                            </div>
                                            <p class="mb-0 text-muted">${activity.note}</p>
                                            <div class="mt-2 d-flex align-items-center activity-meta">
                                                <small class="text-muted me-2"><i class="ti ti-clock-hour-4 me-1"></i>${activityTime}</small>
                                                ${activity.category ? `<small class="text-muted me-2"><i class="ti ti-tag me-1"></i>${activity.category}</small>` : ''}
                                                ${activity.action ? `<small class="text-muted"><i class="ti ti-activity me-1"></i>${activity.action}</small>` : ''}
                                            </div>
                                        </div>
                                    </div>`;

                                    $('#activity-list').append(activityItem);
                                });

                                loadMoreButton.dataset.skip = parseInt(skip) + 5;
                                $(loadMoreButton).html(
                                    '<i class="ti ti-reload me-2"></i>Load More Activities');
                                $(loadMoreButton).prop('disabled', false);

                                // Re-apply current filter if any
                                const activeFilter = document.querySelector(
                                    '[data-filter].active');
                                if (activeFilter) {
                                    activeFilter.click();
                                }
                            } else {
                                $(loadMoreButton).html('No More Activities');
                                setTimeout(() => {
                                loadMoreButton.style.display = 'none';
                                }, 2000);
                            }
                        },
                        error: function() {
                            $(loadMoreButton).html(
                                '<i class="ti ti-reload me-2"></i>Load More Activities');
                            $(loadMoreButton).prop('disabled', false);
                        }
                    });
                });
            }

            // Load more auth activities
            const loadMoreAuthButton = document.getElementById('load-more-auth');
            if (loadMoreAuthButton) {
                loadMoreAuthButton.addEventListener('click', function() {
                    let skip = this.dataset.skip;
                    $(this).html('<i class="spinner-border spinner-border-sm me-2"></i> Loading...');
                    $(this).prop('disabled', true);

                    $.ajax({
                        url: "{{ route('auth.activities') }}",
                        type: 'GET',
                        data: {
                            skip: skip
                        },
                        success: function(response) {
                            if (response.length > 0) {
                                $.each(response, function(index, activity) {
                                    let user = activity.user;

                                    // Format dates with native JavaScript
                                    let createdDate = new Date(activity.created_at);
                                    let timeAgo = formatTimeAgo(createdDate);
                                    let activityDate = formatDate(createdDate,
                                        'D MMM YYYY');
                                    let activityTime = formatTime(createdDate);

                                    let iconClass = activity.action === 'login' ?
                                        'text-success' : 'text-danger';
                                    let icon = activity.action === 'login' ? 'login' :
                                        'logout';
                                    let bgClass = activity.action === 'login' ?
                                        'bg-light-success' : 'bg-light-danger';

                                    // Get device and location info from note if available
                                    let device = 'Unknown device';
                                    let location = 'Unknown location';

                                    if (activity.note.toLowerCase().includes(
                                            'from')) {
                                        let parts = activity.note.split(
                                            'from');
                                        if (parts.length > 1) {
                                            location = parts[1].trim();

                                            if (activity.note.toLowerCase().includes(
                                                    'using')) {
                                                let deviceParts = activity.note.split(
                                                    'using');
                                                if (deviceParts.length > 1) {
                                                    device = deviceParts[1].trim();
                                                }
                                            }
                                        }
                                    }

                                    let activityItem = `
                                    <div class="d-flex mb-4 pb-3 border-bottom auth-activity-item" data-date="${new Date(activity.created_at).toISOString().split('T')[0]}">
                                        <div class="avtar avtar-xs ${bgClass} rounded-circle me-3">
                                            <i class="material-icons-two-tone ${iconClass} f-16">${icon}</i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <h6 class="mb-0 d-flex align-items-center">
                                                    ${user ? user.name : 'Deleted User'}
                                                    <span class="badge ${bgClass} ${iconClass} ms-2 text-uppercase">${activity.action}</span>
                                                </h6>
                                                <small class="text-muted badge bg-light">${timeAgo}</small>
                                            </div>
                                            <p class="mb-0 text-muted">${activity.note}</p>
                                            <div class="mt-2 row auth-activity-details">
                                                <div class="col-6">
                                                    <small class="d-flex align-items-center text-muted mb-1">
                                                        <i class="ti ti-calendar-time me-1"></i> ${activityTime}
                                                    </small>
                                                </div>
                                                <div class="col-6">
                                                    <small class="d-flex align-items-center text-muted mb-1">
                                                        <i class="ti ti-calendar me-1"></i> ${activityDate}
                                                    </small>
                                                </div>
                                                <div class="col-12">
                                                    <small class="d-flex align-items-center text-muted">
                                                        <i class="ti ti-device-laptop me-1"></i> ${device}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;

                                    $('#auth-activity-list').append(activityItem);
                                });

                                loadMoreAuthButton.dataset.skip = parseInt(skip) + 5;
                                $(loadMoreAuthButton).html(
                                    '<i class="ti ti-reload me-1"></i> Load More');
                                $(loadMoreAuthButton).prop('disabled', false);

                                // Re-apply current period filter
                                const activePeriod = document.getElementById(
                                    'auth-activity-period').value;
                                if (activePeriod !== 'all') {
                                    const today = new Date().toISOString().split('T')[0];
                                    const weekAgo = new Date(Date.now() - 7 * 24 * 60 * 60 *
                                        1000).toISOString().split('T')[0];
                                    const monthAgo = new Date(Date.now() - 30 * 24 * 60 * 60 *
                                        1000).toISOString().split('T')[0];

                                    $('.auth-activity-item').each(function() {
                                        const itemDate = $(this).attr('data-date');

                                        if ((activePeriod === 'today' && itemDate !==
                                                today) ||
                                            (activePeriod === 'week' && itemDate <
                                                weekAgo) ||
                                            (activePeriod === 'month' && itemDate <
                                                monthAgo)) {
                                            $(this).hide();
                                        }
                                    });
                                }
                            } else {
                                $(loadMoreAuthButton).html('No More Activities');
                                setTimeout(() => {
                                loadMoreAuthButton.style.display = 'none';
                                }, 2000);
                            }
                        },
                        error: function() {
                            $(loadMoreAuthButton).html(
                                '<i class="ti ti-reload me-1"></i> Load More');
                            $(loadMoreAuthButton).prop('disabled', false);
                        }
                    });
                });
            }

            // Chart period change event
            $('#chart-period').on('change', function() {
                const period = $(this).val();
                let activityData = [];
                let loginData = [];

                // Simulate different data based on selected period
                if (period === 'week') {
                    activityData = [12, 19, 15, 26, 27, 12, 19];
                    loginData = [8, 12, 10, 17, 20, 9, 14];
                } else if (period === 'month') {
                    activityData = [45, 52, 38, 24, 33, 26, 21, 20, 6, 8, 15, 10];
                    loginData = [35, 41, 62, 42, 13, 18, 29, 37, 36, 51, 32, 35];
                } else if (period === 'year') {
                    activityData = [450, 520, 380, 240, 330, 260, 210, 200, 260, 280, 350, 400];
                    loginData = [350, 410, 320, 420, 290, 310, 340, 370, 360, 510, 390, 450];
                }

                userActivityChart.updateSeries([{
                        name: "Total Activities",
                        data: activityData
                    },
                    {
                        name: "Login Activities",
                        data: loginData
                    }
                ]);
            });

            // Show SweetAlert for login success notification
            @if (session('login_success'))
                // Create custom toast notification instead of using SweetAlert
                const toastContainer = document.createElement('div');
                toastContainer.style.position = 'fixed';
                toastContainer.style.top = '20px';
                toastContainer.style.right = '20px';
                toastContainer.style.zIndex = '9999';

                const toast = document.createElement('div');
                toast.className = 'alert alert-success alert-dismissible fade show';
                toast.innerHTML = `
                    <div class="d-flex align-items-center">
                        <i class="ti ti-check-circle me-2"></i>
                        <div>
                            <strong>Login Berhasil</strong><br>
                            <span>{{ session('login_success') }}</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;

                toastContainer.appendChild(toast);
                document.body.appendChild(toastContainer);

                // Auto dismiss after 2.5 seconds
                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => {
                        document.body.removeChild(toastContainer);
                    }, 150);
                }, 2500);
            @endif
        });

        // Helper functions for date formatting (replacement for Moment.js)
        function formatDate(date, format) {
            // Simplified version of D MMM YYYY format
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const day = date.getDate();
            const month = months[date.getMonth()];
            const year = date.getFullYear();

            return `${day} ${month} ${year}`;
        }

        function formatTime(date) {
            return date.getHours().toString().padStart(2, '0') + ':' +
                date.getMinutes().toString().padStart(2, '0');
        }

        function formatTimeAgo(date) {
            const now = new Date();
            const diffInSeconds = Math.floor((now - date) / 1000);

            if (diffInSeconds < 60) {
                return 'just now';
            }

            const diffInMinutes = Math.floor(diffInSeconds / 60);
            if (diffInMinutes < 60) {
                return `${diffInMinutes} minute${diffInMinutes > 1 ? 's' : ''} ago`;
            }

            const diffInHours = Math.floor(diffInMinutes / 60);
            if (diffInHours < 24) {
                return `${diffInHours} hour${diffInHours > 1 ? 's' : ''} ago`;
            }

            const diffInDays = Math.floor(diffInHours / 24);
            if (diffInDays < 30) {
                return `${diffInDays} day${diffInDays > 1 ? 's' : ''} ago`;
            }

            const diffInMonths = Math.floor(diffInDays / 30);
            if (diffInMonths < 12) {
                return `${diffInMonths} month${diffInMonths > 1 ? 's' : ''} ago`;
            }

            const diffInYears = Math.floor(diffInMonths / 12);
            return `${diffInYears} year${diffInYears > 1 ? 's' : ''} ago`;
        }
    </script>
@endsection
