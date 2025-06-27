@extends('layouts.main')

@section('title', 'Dashboard')
@section('breadcrumb-item', 'Dashboard')
@section('breadcrumb-item-active', 'Overview')

@section('content')
    <!-- [ Main Content ] start -->
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card welcome-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <!-- User Info Section -->
                        <div class="col-lg-8 col-md-7 col-12 mb-3 mb-md-0">
                            <div class="d-flex align-items-center">
                                <div class="welcome-avatar me-3">
                                    <i class="ti ti-user"></i>
                                </div>
                                <div class="welcome-content">
                                    <h4 class="welcome-title mb-2">Selamat Datang, {{ Auth::user()->name }}!</h4>
                                    <div class="welcome-info">
                                        <div class="info-item">
                                            <i class="ti ti-shield-check"></i>
                                            <span>Role: {{ Auth::user()->getRoleNames()->first() }}</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="ti ti-clock"></i>
                                            <span>Login Terakhir:
                                                {{ $userStats['last_login'] ? $userStats['last_login']->format('d/m/Y H:i') : 'Belum pernah login sebelumnya' }}</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="ti ti-activity"></i>
                                            <span>{{ $userStats['total_logins'] }} kali login</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="ti ti-calendar-event"></i>
                                            <span>{{ $userStats['activities_today'] }} aktivitas hari ini</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Date Range Filter Section -->
                        <div class="col-lg-4 col-md-5 col-12">
                            <div class="filter-section">
                                <h6 class="filter-title mb-2">Filter Periode</h6>
                                <form method="GET" action="{{ route('dashboard') }}" class="filter-form">
                                    <div class="filter-row">
                                        <select name="range" class="form-select form-select-sm"
                                            onchange="toggleDateInputs(this.value)">
                                            <option value="month" {{ $dateRange['range'] == 'month' ? 'selected' : '' }}>
                                                Bulanan</option>
                                            <option value="year" {{ $dateRange['range'] == 'year' ? 'selected' : '' }}>
                                                Tahunan</option>
                                            <option value="all" {{ $dateRange['range'] == 'all' ? 'selected' : '' }}>
                                                Semua Waktu</option>
                                        </select>
                                    </div>

                                    <div class="filter-row" id="monthInput"
                                        style="{{ $dateRange['range'] != 'month' ? 'display: none;' : '' }}">
                                        <select name="month" class="form-select form-select-sm">
                                            @foreach ($availableMonths as $num => $name)
                                                <option value="{{ $num }}"
                                                    {{ $dateRange['month'] == $num ? 'selected' : '' }}>
                                                    {{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="filter-row" id="yearInput"
                                        style="{{ $dateRange['range'] == 'all' ? 'display: none;' : '' }}">
                                        <select name="year" class="form-select form-select-sm">
                                            @foreach ($availableYears as $year)
                                                <option value="{{ $year }}"
                                                    {{ $dateRange['year'] == $year ? 'selected' : '' }}>
                                                    {{ $year }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="filter-row">
                                        <button type="submit" class="btn btn-dark btn-sm w-100">
                                            <i class="ti ti-filter me-1"></i>Terapkan Filter
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Filter Display -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-info py-2 mb-0">
                <i class="ti ti-calendar"></i>
                <strong>Menampilkan data untuk:</strong> {{ $dateRange['label'] }}
                <br class="d-block d-md-none">
                <span class="ms-0 ms-md-2 text-muted">
                    ({{ $dateRange['start']->format('d M Y') }} - {{ $dateRange['end']->format('d M Y') }})
                </span>
            </div>
        </div>
    </div>

    <!-- Master Data Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <h6 class="mb-3">Ringkasan Sistem</h6>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 mb-3">
            <div class="card border">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Produk</p>
                            <h4 class="mb-0">{{ number_format($masterData['total_products']) }}</h4>
                            @if ($masterData['products_low_stock'] > 0)
                                <small class="text-warning">{{ $masterData['products_low_stock'] }} stok menipis</small>
                            @else
                                <small class="text-muted">Stok aman semua</small>
                            @endif
                        </div>
                        <div class="text-primary">
                            <i class="ti ti-box fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 mb-3">
            <div class="card border">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Bahan Baku</p>
                            <h4 class="mb-0">{{ number_format($masterData['total_bahan_baku']) }}</h4>
                            <small class="text-muted">Bahan mentah</small>
                        </div>
                        <div class="text-success">
                            <i class="ti ti-package fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 mb-3">
            <div class="card border">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Total User</p>
                            <h4 class="mb-0">{{ number_format($masterData['total_users']) }}</h4>
                            <small class="text-muted">Pengguna sistem</small>
                        </div>
                        <div class="text-info">
                            <i class="ti ti-users fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 mb-3">
            <div class="card border">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Stiker Perlu Order</p>
                            <h4 class="mb-0 {{ $masterData['stickers_need_order'] > 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format($masterData['stickers_need_order']) }}
                            </h4>
                            @if ($masterData['stickers_need_order'] > 0)
                                <small class="text-danger">
                                    <i class="ti ti-alert-triangle"></i> Sisa < 30 </small>
                                    @else
                                        <small class="text-success">Stiker masih aman</small>
                            @endif
                        </div>
                        <div class="text-{{ $masterData['stickers_need_order'] > 0 ? 'danger' : 'success' }}">
                            <i class="ti ti-sticker fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Statistics -->
    <div class="row mb-4">
        <div class="col-12">
            <h6 class="mb-3">Statistik Aktivitas
                <small class="text-muted">- {{ $dateRange['label'] }}</small>
            </h6>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 col-12 mb-3">
            <div class="card border" data-bs-toggle="tooltip" data-bs-placement="top"
                title="Total transaksi penjualan yang tercatat di sistem">
                <div class="card-body p-3 text-center">
                    <i class="ti ti-scan text-primary fs-2 mb-2"></i>
                    <h5 class="mb-1">{{ number_format($activityData['sales_count']) }}</h5>
                    <p class="text-muted mb-0 small">Penjualan</p>
                    <small class="text-muted d-block" style="font-size: 0.7rem;">
                        <i class="ti ti-info-circle"></i> Total transaksi
                    </small>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 col-12 mb-3">
            <div class="card border" data-bs-toggle="tooltip" data-bs-placement="top"
                title="Jumlah batch produksi yang sudah selesai">
                <div class="card-body p-3 text-center">
                    <i class="ph-duotone ph-factory text-success fs-2 mb-2"></i>
                    <h5 class="mb-1">{{ number_format($activityData['production_count']) }}</h5>
                    <p class="text-muted mb-0 small">Produksi</p>
                    <small class="text-muted d-block" style="font-size: 0.7rem;">
                        <i class="ti ti-info-circle"></i> Batch selesai
                    </small>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 col-12 mb-3">
            <div class="card border" data-bs-toggle="tooltip" data-bs-placement="top"
                title="Total order pembelian bahan baku dan supplies">
                <div class="card-body p-3 text-center">
                    <i class="ti ti-shopping-cart text-info fs-2 mb-2"></i>
                    <h5 class="mb-1">{{ number_format($activityData['purchases_count']) }}</h5>
                    <p class="text-muted mb-0 small">Pembelian</p>
                    <small class="text-muted d-block" style="font-size: 0.7rem;">
                        <i class="ti ti-info-circle"></i> Order pembelian
                    </small>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 col-12 mb-3">
            <div class="card border" data-bs-toggle="tooltip" data-bs-placement="top"
                title="Total jumlah produk yang terjual ke customer">
                <div class="card-body p-3 text-center">
                    <i class="ti ti-package text-warning fs-2 mb-2"></i>
                    <h5 class="mb-1">{{ number_format($activityData['total_qty_sold']) }}</h5>
                    <p class="text-muted mb-0 small">Qty Terjual</p>
                    <small class="text-muted d-block" style="font-size: 0.7rem;">
                        <i class="ti ti-info-circle"></i> Unit terjual
                    </small>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 col-12 mb-3">
            <div class="card border" data-bs-toggle="tooltip" data-bs-placement="top"
                title="Total jumlah produk yang diproduksi">
                <div class="card-body p-3 text-center">
                    <i class="ti ti-tools text-secondary fs-2 mb-2"></i>
                    <h5 class="mb-1">{{ number_format($activityData['total_production_qty']) }}</h5>
                    <p class="text-muted mb-0 small">Diproduksi</p>
                    <small class="text-muted d-block" style="font-size: 0.7rem;">
                        <i class="ti ti-info-circle"></i> Unit diproduksi
                    </small>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 col-12 mb-3">
            <div class="card border" data-bs-toggle="tooltip" data-bs-placement="top"
                title="Total jumlah bahan dan supplies yang dibeli">
                <div class="card-body p-3 text-center">
                    <i class="ti ti-truck text-dark fs-2 mb-2"></i>
                    <h5 class="mb-1">{{ number_format($activityData['total_purchase_qty']) }}</h5>
                    <p class="text-muted mb-0 small">Dibeli</p>
                    <small class="text-muted d-block" style="font-size: 0.7rem;">
                        <i class="ti ti-info-circle"></i> Unit dibeli
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance & Recent Activity -->
    <div class="row mb-4">
        <div class="col-lg-6 col-12 mb-4 mb-lg-0">
            <div class="card border">
                <div class="card-header d-flex justify-content-between align-items-center flex-column flex-sm-row">
                    <h6 class="mb-0">Produk Terlaris</h6>
                    <small class="text-muted">{{ $dateRange['label'] }}</small>
                </div>
                <div class="card-body p-0">
                    @if (count($performanceData['top_selling_skus']) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <tbody>
                                    @foreach ($performanceData['top_selling_skus'] as $index => $sku)
                                        <tr>
                                            <td class="py-2">
                                                <span class="badge bg-secondary">#{{ $index + 1 }}</span>
                                            </td>
                                            <td class="py-2">
                                                <div>
                                                    <strong class="d-block">{{ $sku['sku'] }}</strong>
                                                    <small class="text-muted">{{ Str::limit($sku['name'], 30) }}</small>
                                                </div>
                                            </td>
                                            <td class="py-2 text-end">
                                                <span
                                                    class="badge bg-primary d-block mb-1">{{ number_format($sku['total_sold']) }}</span>
                                                <small class="text-muted">{{ $sku['transactions'] }} order</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="ti ti-chart-bar text-muted fs-3"></i>
                            <p class="text-muted mb-0">Belum ada data penjualan untuk periode ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-12">
            <div class="card border">
                <div class="card-header">
                    <h6 class="mb-0">Aktivitas Terbaru</h6>
                </div>
                <div class="card-body p-0">
                    <div class="activity-timeline" style="max-height: 300px; overflow-y: auto;">
                        @forelse ($performanceData['recent_sales'] as $sale)
                            <div class="d-flex align-items-center p-3 border-bottom">
                                <div class="flex-shrink-0">
                                    <i class="ti ti-scan text-primary"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">{{ $sale->no_resi }}</h6>
                                    <small class="text-muted">{{ $sale->created_at->diffForHumans() }}</small>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="badge bg-light text-dark">
                                        @if (is_array($sale->no_sku))
                                            {{ count($sale->no_sku) }} item
                                        @else
                                            1 item
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="ti ti-activity text-muted fs-3"></i>
                                <p class="text-muted mb-0">Belum ada aktivitas terbaru</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Health & Quick Access -->
    <div class="row mb-4">
        <div class="col-md-6 col-12 mb-4 mb-md-0">
            <div class="card border">
                <div class="card-header">
                    <h6 class="mb-0">Kesehatan Sistem</h6>
                </div>
                <div class="card-body p-3">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <h5 class="text-primary mb-1">{{ number_format($systemHealth['total_transactions']) }}</h5>
                            <p class="text-muted mb-0 small">Total Transaksi</p>
                        </div>
                        <div class="col-6 mb-3">
                            <span
                                class="badge 
                                @if ($systemHealth['data_integrity'] === 'Good') bg-success
                                @elseif($systemHealth['data_integrity'] === 'Fair') bg-warning  
                                @else bg-danger @endif">
                                @if ($systemHealth['data_integrity'] === 'Good')
                                    Bagus
                                @elseif($systemHealth['data_integrity'] === 'Fair')
                                    Cukup
                                @else
                                    Perlu Perhatian
                                @endif
                            </span>
                            <p class="text-muted mb-0 small">Integritas Data</p>
                        </div>
                        <div class="col-12">
                            <small class="text-muted">
                                Aktivitas Terakhir:
                                <br class="d-block d-sm-none">
                                {{ $systemHealth['last_activity'] ? $systemHealth['last_activity']->format('d/m/Y H:i') : 'Belum ada aktivitas terbaru' }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-12">
            <div class="card border">
                <div class="card-header">
                    <h6 class="mb-0">Akses Cepat</h6>
                </div>
                <div class="card-body p-3">
                    <div class="row g-2">
                        <div class="col-6 mb-2">
                            <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-primary w-100">
                                <i class="ti ti-box me-1"></i><span class="d-none d-sm-inline">Produk</span>
                            </a>
                        </div>
                        <div class="col-6 mb-2">
                            <a href="{{ route('scanner.index') }}" class="btn btn-sm btn-outline-success w-100">
                                <i class="ti ti-scan me-1"></i><span class="d-none d-sm-inline">Scanner</span>
                            </a>
                        </div>
                        <div class="col-6 mb-2">
                            <a href="{{ route('catatan-produksi.index') }}" class="btn btn-sm btn-outline-info w-100">
                                <i class="ph-duotone ph-factory me-1"></i><span class="d-none d-sm-inline">Produksi</span>
                            </a>
                        </div>
                        <div class="col-6 mb-2">
                            <a href="{{ route('stickers.index') }}" class="btn btn-sm btn-outline-warning w-100">
                                <i class="ti ti-sticker me-1"></i><span class="d-none d-sm-inline">Stiker</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- [ Main Content ] end -->

    <script>
        function toggleDateInputs(range) {
            const monthInput = document.getElementById('monthInput');
            const yearInput = document.getElementById('yearInput');

            if (range === 'month') {
                monthInput.style.display = 'block';
                yearInput.style.display = 'block';
            } else if (range === 'year') {
                monthInput.style.display = 'none';
                yearInput.style.display = 'block';
            } else { // all
                monthInput.style.display = 'none';
                yearInput.style.display = 'none';
            }
        }

        // Initialize Bootstrap tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>

    <style>
        /* Welcome Card Styles */
        .welcome-card {
            border: 1px solid #e9ecef;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .welcome-avatar {
            width: 50px;
            height: 50px;
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .welcome-avatar i {
            font-size: 1.25rem;
            color: #6c757d;
        }

        .welcome-title {
            font-weight: 600;
            color: #212529;
            margin-bottom: 0.75rem;
        }

        .welcome-info {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            font-size: 0.875rem;
            color: #6c757d;
            gap: 0.5rem;
        }

        .info-item i {
            width: 16px;
            flex-shrink: 0;
            color: #6c757d;
        }

        .filter-section {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1rem;
        }

        .filter-title {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.75rem;
        }

        .filter-form {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .filter-row {
            width: 100%;
        }

        .filter-row .form-select {
            border: 1px solid #ced4da;
        }

        .filter-row .btn {
            font-weight: 500;
        }

        .card {
            transition: box-shadow 0.2s ease, transform 0.2s ease;
            border: 1px solid #dee2e6;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        /* Ensure cards have proper minimum height */
        .card .card-body {
            min-height: 80px;
        }

        /* Fix specific card body layouts */
        .card .card-body .d-flex.justify-content-between {
            width: 100%;
            align-items: center;
        }

        /* Ensure consistent text sizing */
        .card h4 {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .card .text-muted {
            font-size: 0.875rem;
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

        /* Mobile specific styles */
        @media (max-width: 575.98px) {
            .card-body {
                padding: 1rem !important;
            }

            .fs-2 {
                font-size: 1.5rem !important;
            }

            .h5,
            h5 {
                font-size: 1.1rem;
            }

            .small {
                font-size: 0.75rem !important;
            }

            .btn-sm {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }

            .form-select-sm {
                padding: 0.25rem 1.75rem 0.25rem 0.5rem;
                font-size: 0.75rem;
            }

            .alert {
                padding: 0.5rem 0.75rem;
                font-size: 0.875rem;
            }

            .badge {
                font-size: 0.65em;
            }

            /* Welcome section mobile adjustments */
            .welcome-avatar {
                width: 45px;
                height: 45px;
            }

            .welcome-avatar i {
                font-size: 1.1rem;
            }

            .welcome-title {
                font-size: 1.25rem;
            }

            .info-item {
                font-size: 0.8rem;
            }

            .filter-section {
                margin-top: 1rem;
                padding: 0.75rem;
            }

            .filter-title {
                font-size: 0.9rem;
            }

            .welcome-info {
                gap: 0.4rem;
            }
        }

        /* Tablet specific styles */
        @media (min-width: 576px) and (max-width: 991.98px) {
            .card-header {
                padding: 0.75rem 1rem;
            }

            .activity-timeline {
                max-height: 250px;
            }
        }

        /* Desktop/Laptop specific styles */
        @media (min-width: 992px) {
            .card-body {
                padding: 1.5rem !important;
            }

            .card-header {
                padding: 1rem 1.5rem;
            }

            .btn-sm {
                padding: 0.375rem 0.75rem;
                font-size: 0.875rem;
            }

            .form-select-sm {
                padding: 0.375rem 2rem 0.375rem 0.75rem;
                font-size: 0.875rem;
            }

            .fs-2 {
                font-size: 2rem !important;
            }

            .h5,
            h5 {
                font-size: 1.25rem;
            }

            .small {
                font-size: 0.875rem !important;
            }

            .activity-timeline {
                max-height: 350px;
            }

            /* Better spacing for desktop */
            .mb-3 {
                margin-bottom: 1.5rem !important;
            }

            .mb-4 {
                margin-bottom: 2rem !important;
            }

            /* Ensure proper text display on desktop */
            .d-none.d-sm-inline {
                display: inline !important;
            }

            /* Better button sizing on desktop */
            .btn {
                min-height: 38px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            /* Proper gap spacing */
            .gap-2 {
                gap: 0.75rem !important;
            }

            /* Fix form layout on desktop */
            .form-select-sm {
                min-width: 120px;
            }

            /* Ensure proper flex behavior */
            .d-flex.flex-column.flex-sm-row {
                flex-direction: row !important;
            }

            /* Better card grid alignment */
            .row .col-xl-3,
            .row .col-lg-6,
            .row .col-md-6 {
                display: flex;
                flex-direction: column;
            }

            .row .col-xl-3 .card,
            .row .col-lg-6 .card,
            .row .col-md-6 .card {
                flex: 1;
                height: 100%;
            }

            /* Ensure consistent button sizes */
            .btn-outline-primary,
            .btn-outline-success,
            .btn-outline-info,
            .btn-outline-warning {
                min-height: 42px;
                font-weight: 500;
            }

            /* Welcome section desktop enhancements */
            .welcome-avatar {
                width: 55px;
                height: 55px;
            }

            .welcome-avatar i {
                font-size: 1.35rem;
            }

            .welcome-title {
                font-size: 1.5rem;
            }

            .info-item {
                font-size: 0.9rem;
            }

            .filter-section {
                padding: 1.25rem;
            }

            .welcome-info {
                gap: 0.6rem;
            }
        }

        /* Large desktop specific styles */
        @media (min-width: 1200px) {
            .card-body {
                padding: 2rem !important;
            }

            .card-header {
                padding: 1.25rem 2rem;
            }

            .activity-timeline {
                max-height: 400px;
            }

            .mb-4 {
                margin-bottom: 2.5rem !important;
            }

            /* Better typography on large screens */
            .h5,
            h5 {
                font-size: 1.5rem;
            }

            .h6,
            h6 {
                font-size: 1.125rem;
            }

            /* Enhanced spacing for large screens */
            .gap-2 {
                gap: 1rem !important;
            }

            /* Welcome section large desktop */
            .welcome-avatar {
                width: 60px;
                height: 60px;
            }

            .welcome-avatar i {
                font-size: 1.5rem;
            }

            .welcome-title {
                font-size: 1.75rem;
            }

            .info-item {
                font-size: 1rem;
            }

            .filter-section {
                padding: 1.5rem;
            }

            .welcome-info {
                gap: 0.75rem;
            }
        }

        /* Ultra-wide desktop optimization */
        @media (min-width: 1400px) {
            .container-fluid {
                max-width: 1320px;
                margin: 0 auto;
            }

            .card-body {
                padding: 2.5rem !important;
            }
        }

        /* Improve button spacing on mobile */
        @media (max-width: 575.98px) {
            .btn {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .d-flex.gap-2 {
                gap: 0.5rem !important;
            }
        }
    </style>
@endsection
