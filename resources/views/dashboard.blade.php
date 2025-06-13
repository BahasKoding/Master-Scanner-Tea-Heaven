@extends('layouts.main')

@section('title', 'Dashboard')
@section('breadcrumb-item', 'Dashboard')
@section('breadcrumb-item-active', 'Overview')

@section('content')
    <!-- [ Main Content ] start -->

    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="avatar bg-primary text-white me-3">
                                <i class="ti ti-user"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Selamat Datang, {{ Auth::user()->name }}!</h5>
                                <small class="text-muted">
                                    Role: <span class="badge bg-primary">{{ Auth::user()->getRoleNames()->first() }}</span>
                                    | Login Terakhir:
                                    {{ $userStats['last_login'] ? $userStats['last_login']->format('d/m/Y H:i') : 'Belum pernah login sebelumnya' }}
                                    <br>
                                    <i class="ti ti-activity"></i> {{ $userStats['total_logins'] }} kali login
                                    | <i class="ti ti-calendar-event"></i> {{ $userStats['activities_today'] }} aktivitas
                                    hari ini
                                </small>
                            </div>
                        </div>

                        <!-- Date Range Filter -->
                        <div class="d-flex align-items-center gap-2">
                            <form method="GET" action="{{ route('dashboard') }}" class="d-flex align-items-center gap-2">
                                <select name="range" class="form-select form-select-sm" style="width: auto;"
                                    onchange="toggleDateInputs(this.value)">
                                    <option value="month" {{ $dateRange['range'] == 'month' ? 'selected' : '' }}>Bulanan
                                    </option>
                                    <option value="year" {{ $dateRange['range'] == 'year' ? 'selected' : '' }}>Tahunan
                                    </option>
                                    <option value="all" {{ $dateRange['range'] == 'all' ? 'selected' : '' }}>Semua Waktu
                                    </option>
                                </select>

                                <div id="monthInput" style="{{ $dateRange['range'] != 'month' ? 'display: none;' : '' }}">
                                    <select name="month" class="form-select form-select-sm" style="width: auto;">
                                        @foreach ($availableMonths as $num => $name)
                                            <option value="{{ $num }}"
                                                {{ $dateRange['month'] == $num ? 'selected' : '' }}>{{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div id="yearInput" style="{{ $dateRange['range'] == 'all' ? 'display: none;' : '' }}">
                                    <select name="year" class="form-select form-select-sm" style="width: auto;">
                                        @foreach ($availableYears as $year)
                                            <option value="{{ $year }}"
                                                {{ $dateRange['year'] == $year ? 'selected' : '' }}>{{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="ti ti-filter"></i> Filter
                                </button>
                            </form>
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
                <span class="ms-2 text-muted">
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

        <div class="col-lg-3 col-md-6 mb-3">
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

        <div class="col-lg-3 col-md-6 mb-3">
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

        <div class="col-lg-3 col-md-6 mb-3">
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

        <div class="col-lg-3 col-md-6 mb-3">
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

        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
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

        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
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

        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
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

        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
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

        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
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

        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
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
        <div class="col-lg-6">
            <div class="card border">
                <div class="card-header d-flex justify-content-between align-items-center">
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
                                                    <strong>{{ $sku['sku'] }}</strong>
                                                    <br><small
                                                        class="text-muted">{{ Str::limit($sku['name'], 30) }}</small>
                                                </div>
                                            </td>
                                            <td class="py-2 text-end">
                                                <span
                                                    class="badge bg-primary">{{ number_format($sku['total_sold']) }}</span>
                                                <br><small class="text-muted">{{ $sku['transactions'] }} order</small>
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

        <div class="col-lg-6">
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
        <div class="col-md-6">
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
                                {{ $systemHealth['last_activity'] ? $systemHealth['last_activity']->format('d/m/Y H:i') : 'Belum ada aktivitas terbaru' }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border">
                <div class="card-header">
                    <h6 class="mb-0">Akses Cepat</h6>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-6 mb-2">
                            <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-primary w-100">
                                <i class="ti ti-box me-1"></i>Produk
                            </a>
                        </div>
                        <div class="col-6 mb-2">
                            <a href="{{ route('scanner.index') }}" class="btn btn-sm btn-outline-success w-100">
                                <i class="ti ti-scan me-1"></i>Scanner
                            </a>
                        </div>
                        <div class="col-6 mb-2">
                            <a href="{{ route('catatan-produksi.index') }}" class="btn btn-sm btn-outline-info w-100">
                                <i class="ph-duotone ph-factory me-1"></i>Produksi
                            </a>
                        </div>
                        <div class="col-6 mb-2">
                            <a href="{{ route('stickers.index') }}" class="btn btn-sm btn-outline-warning w-100">
                                <i class="ti ti-sticker me-1"></i>Stiker
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
