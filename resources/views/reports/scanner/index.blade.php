@extends('layouts.main')

@section('title', 'Laporan Scanner')
@section('breadcrumb-item', 'Laporan Scanner')

@section('css')
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- [Page specific CSS] start -->
    <!-- data tables css -->
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/datatables/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/datatables/buttons.bootstrap5.min.css') }}">
    <!-- [Page specific CSS] end -->
    <style>
        .filter-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .summary-cards {
            margin-bottom: 20px;
        }

        .summary-card {
            background: #6c757d;
            border-radius: 8px;
            padding: 20px;
            color: white;
            margin-bottom: 15px;
            border: 1px solid #dee2e6;
        }

        .summary-card.success {
            background: #198754;
        }

        .summary-card.warning {
            background: #fd7e14;
        }

        .summary-card.info {
            background: #0dcaf0;
            color: #212529;
        }

        .summary-card .summary-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }

        .summary-card .summary-value {
            font-size: 1.8rem;
            font-weight: bold;
            margin: 10px 0 5px 0;
        }

        .summary-card .summary-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        @media (max-width: 768px) {
            .filter-section {
                padding: 15px 10px;
            }

            .summary-card {
                text-align: center;
                padding: 15px;
            }

            .summary-card .summary-value {
                font-size: 1.4rem;
            }
        }

        /* Unknown Products Warning Collapse Styling */
        #unknown-products-warning {
            border-left: 4px solid #ffc107;
        }

        #toggle-unknown-details {
            transition: all 0.3s ease;
            border: 1px solid #ffc107;
            color: #856404;
        }

        #toggle-unknown-details:hover {
            background-color: #ffc107;
            color: white;
            transform: translateY(-1px);
        }

        #toggle-icon {
            transition: transform 0.3s ease;
        }

        #unknown-products-details {
            border-left: 2px solid #ffc107;
            margin-left: 10px;
        }

        #unknown-skus-list .badge {
            margin: 2px;
            font-size: 0.8rem;
        }
    </style>
@endsection

@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">
        <!-- Summary Cards start -->
        <div class="col-12 summary-cards">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon">
                                <i class="fas fa-qrcode"></i>
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <div class="summary-value" id="total-scans">0</div>
                                <div class="summary-label">Total Scans</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="summary-card success">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon">
                                <i class="fas fa-shopping-bag"></i>
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <div class="summary-value" id="total-orders">0</div>
                                <div class="summary-label">Unique Orders</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="summary-card warning">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon">
                                <i class="fas fa-cubes"></i>
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <div class="summary-value" id="total-products-sold">0</div>
                                <div class="summary-label">Products Sold</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="summary-card info">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon">
                                <i class="fas fa-calculator"></i>
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <div class="summary-value" id="total-quantity">0</div>
                                <div class="summary-label">Total Quantity</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Summary Cards end -->

        <!-- Scanner Report Table start -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-6 mb-2 mb-md-0">
                            <h5 class="mb-0">Laporan Scanner</h5>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-md-end">
                                <button id="clear-filters" class="btn btn-secondary btn-sm">
                                    Clear Filters
                                </button>
                                <button id="export-excel" class="btn btn-success btn-sm">
                                    Export Excel
                                </button>
                                <button id="export-all" class="btn btn-success btn-sm"
                                    title="Export all data in the database">
                                    Export All
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="filter-section">
                        <div class="row">
                            <div class="col-md-4 col-sm-6 col-12 mb-3">
                                <label for="start-date" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="start-date" name="start_date">
                            </div>
                            <div class="col-md-4 col-sm-6 col-12 mb-3">
                                <label for="end-date" class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control" id="end-date" name="end_date">
                            </div>
                            <div class="col-md-4 col-sm-6 col-12 mb-3">
                                <label for="no-resi-filter" class="form-label">No Resi</label>
                                <input type="text" class="form-control" id="no-resi-filter"
                                    placeholder="Filter berdasarkan no resi">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button id="apply-filters" class="btn btn-primary">
                                    Apply Filters
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- End Filter Section -->

                    <!-- Export Status Indicator -->
                    <div class="export-status" id="exportStatus"
                        style="display: none; align-items: center; justify-content: center; padding: 10px; background-color: #fff3cd; border: 1px solid #ffeeba; color: #856404; border-radius: 4px; margin-bottom: 15px;">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                            style="width: 1rem; height: 1rem; margin-right: 8px;"></span>
                        <span id="exportStatusText">Preparing data for export...</span>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-primary" id="table-period">
                            Menampilkan semua data scanner
                        </h6>
                    </div>

                    <!-- Navigation Tabs -->
                    <ul class="nav nav-tabs mb-3" id="reportTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="data-tab" data-bs-toggle="tab"
                                data-bs-target="#data-pane" type="button" role="tab" aria-controls="data-pane"
                                aria-selected="true">
                                Data Transaksi
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="summary-tab" data-bs-toggle="tab"
                                data-bs-target="#summary-pane" type="button" role="tab"
                                aria-controls="summary-pane" aria-selected="false">
                                Ringkasan SKU
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="reportTabContent">
                        <!-- Data Transaksi Tab -->
                        <div class="tab-pane fade show active" id="data-pane" role="tabpanel"
                            aria-labelledby="data-tab">
                            <div class="dt-responsive table-responsive">
                                <table id="scanner-report-table" class="table table-striped table-bordered nowrap">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>No Resi</th>
                                            <th>Produk Terjual</th>
                                            <th>Quantity Terjual</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be loaded via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- Data Transaksi Tab End -->

                        <!-- Ringkasan SKU Tab -->
                        <div class="tab-pane fade" id="summary-pane" role="tabpanel" aria-labelledby="summary-tab">
                            <!-- Summary Header Info -->
                            <div class="alert alert-info mb-4">
                                <h6 class="mb-2">Panduan Membaca Ringkasan SKU</h6>
                                <p class="mb-1"><strong>SKU (Stock Keeping Unit):</strong> Kode unik untuk setiap produk
                                </p>
                                <p class="mb-1"><strong>Quantity:</strong> Jumlah produk yang terjual</p>
                                <p class="mb-0"><strong>Transactions:</strong> Berapa kali produk tersebut muncul dalam
                                    transaksi</p>
                            </div>

                            <!-- Unknown Products Warning - Collapsible -->
                            <div class="alert alert-warning mb-4" id="unknown-products-warning" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0" id="unknown-products-title">Peringatan: Produk Tidak Terdefinisi
                                    </h6>
                                    <button class="btn btn-sm btn-outline-warning" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#unknown-products-details"
                                        aria-expanded="false" aria-controls="unknown-products-details"
                                        id="toggle-unknown-details">
                                        <i class="fas fa-chevron-down" id="toggle-icon"></i>
                                        <span class="ms-1">Lihat Detail</span>
                                    </button>
                                </div>
                                <div class="collapse mt-3" id="unknown-products-details">
                                    <div class="border-top pt-3">
                                        <p class="mb-2">Ditemukan produk dengan SKU yang belum terdefinisi di tabel
                                            Product.
                                            Produk ini akan muncul sebagai "Unknown Product" dalam ringkasan.</p>
                                        <div class="mb-3">
                                            <strong>SKU yang belum terdefinisi:</strong>
                                            <div id="unknown-skus-list" class="mt-2"></div>
                                        </div>
                                        <div class="alert alert-info mb-0">
                                            <small>
                                                <strong><i class="fas fa-lightbulb me-1"></i>Solusi:</strong>
                                                Tambahkan SKU tersebut ke dalam tabel Product melalui menu manajemen produk
                                                untuk mendapatkan informasi kategori dan label yang akurat.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Summary Statistics -->
                                <div class="col-12 mb-4">
                                    <div class="row" id="summary-stats">
                                        <div class="col-lg-2 col-md-4 col-6 mb-3">
                                            <div class="card text-center border-primary">
                                                <div class="card-body p-3">
                                                    <div class="text-primary mb-2">
                                                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center"
                                                            style="width: 50px; height: 50px;">
                                                            <span class="fw-bold">QTY</span>
                                                        </div>
                                                    </div>
                                                    <h5 class="mt-2 mb-1" id="summary-total-qty">0</h5>
                                                    <p class="mb-0 text-muted small">Total Quantity</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-6 mb-3">
                                            <div class="card text-center border-success">
                                                <div class="card-body p-3">
                                                    <div class="text-success mb-2">
                                                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center"
                                                            style="width: 50px; height: 50px;">
                                                            <span class="fw-bold">SKU</span>
                                                        </div>
                                                    </div>
                                                    <h5 class="mt-2 mb-1" id="summary-unique-skus">0</h5>
                                                    <p class="mb-0 text-muted small">Unique SKUs</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-6 mb-3">
                                            <div class="card text-center border-warning">
                                                <div class="card-body p-3">
                                                    <div class="text-warning mb-2">
                                                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center"
                                                            style="width: 50px; height: 50px;">
                                                            <span class="fw-bold">CAT</span>
                                                        </div>
                                                    </div>
                                                    <h5 class="mt-2 mb-1" id="summary-unique-categories">0</h5>
                                                    <p class="mb-0 text-muted small">Categories</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-6 mb-3">
                                            <div class="card text-center border-info">
                                                <div class="card-body p-3">
                                                    <div class="text-info mb-2">
                                                        <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center"
                                                            style="width: 50px; height: 50px;">
                                                            <span class="fw-bold">LBL</span>
                                                        </div>
                                                    </div>
                                                    <h5 class="mt-2 mb-1" id="summary-unique-labels">0</h5>
                                                    <p class="mb-0 text-muted small">Labels</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-6 mb-3">
                                            <div class="card text-center border-secondary">
                                                <div class="card-body p-3">
                                                    <div class="text-secondary mb-2">
                                                        <div class="bg-secondary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center"
                                                            style="width: 50px; height: 50px;">
                                                            <span class="fw-bold">TXN</span>
                                                        </div>
                                                    </div>
                                                    <h5 class="mt-2 mb-1" id="summary-total-transactions">0</h5>
                                                    <p class="mb-0 text-muted small">Transactions</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-6 mb-3">
                                            <div class="card text-center">
                                                <div class="card-body p-3">
                                                    <button class="btn btn-outline-primary btn-sm w-100"
                                                        id="refresh-summary">
                                                        Refresh Data
                                                    </button>
                                                    <small class="text-muted d-block mt-2">Update ringkasan</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Summary Tables -->
                                <div class="col-12">
                                    <!-- Performance Metrics -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="card bg-light">
                                                <div class="card-header">
                                                    <h6 class="mb-0">Analisis Performa Produk</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-3 col-6 mb-3">
                                                            <div class="text-center">
                                                                <div class="bg-success text-white rounded p-2 mb-2">
                                                                    <span class="fw-bold" id="top-performer-qty">0</span>
                                                                </div>
                                                                <small class="text-muted">Produk Terlaris (Qty)</small>
                                                                <div class="fw-bold small" id="top-performer-name">-</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-6 mb-3">
                                                            <div class="text-center">
                                                                <div class="bg-info text-white rounded p-2 mb-2">
                                                                    <span class="fw-bold" id="most-frequent-txn">0</span>
                                                                </div>
                                                                <small class="text-muted">Paling Sering Dibeli</small>
                                                                <div class="fw-bold small" id="most-frequent-name">-</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-6 mb-3">
                                                            <div class="text-center">
                                                                <div class="bg-warning text-dark rounded p-2 mb-2">
                                                                    <span class="fw-bold" id="avg-qty-per-txn">0</span>
                                                                </div>
                                                                <small class="text-muted">Rata-rata Qty/Transaksi</small>
                                                                <div class="fw-bold small">Per Pembelian</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-6 mb-3">
                                                            <div class="text-center">
                                                                <div class="bg-secondary text-white rounded p-2 mb-2">
                                                                    <span class="fw-bold"
                                                                        id="category-diversity">0%</span>
                                                                </div>
                                                                <small class="text-muted">Keragaman Kategori</small>
                                                                <div class="fw-bold small">Distribusi</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- SKU Summary -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-header bg-primary text-white">
                                                    <h6 class="mb-0">Produk Terlaris (Top 10)</h6>
                                                    <small>Diurutkan berdasarkan quantity terjual</small>
                                                </div>
                                                <div class="card-body p-0">
                                                    <div style="max-height: 400px; overflow-y: auto;">
                                                        <table class="table table-sm mb-0" id="sku-summary-table">
                                                            <thead class="table-light sticky-top">
                                                                <tr>
                                                                    <th width="10%">Rank</th>
                                                                    <th width="50%">Produk</th>
                                                                    <th width="15%" class="text-center">Qty</th>
                                                                    <th width="15%" class="text-center">Txn</th>
                                                                    <th width="10%" class="text-center">Avg/Txn</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td colspan="5"
                                                                        class="text-center text-muted py-3">
                                                                        Loading data...
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Category and Label Summary -->
                                    <div class="row mb-4">
                                        <!-- Category Summary -->
                                        <div class="col-lg-6 col-md-12 mb-4">
                                            <div class="card h-100">
                                                <div class="card-header bg-success text-white">
                                                    <h6 class="mb-0">Ringkasan per Kategori</h6>
                                                    <small>Breakdown penjualan berdasarkan kategori</small>
                                                </div>
                                                <div class="card-body p-0">
                                                    <div style="max-height: 400px; overflow-y: auto;">
                                                        <table class="table table-sm mb-0" id="category-summary-table">
                                                            <thead class="table-light sticky-top">
                                                                <tr>
                                                                    <th width="40%">Kategori</th>
                                                                    <th width="20%" class="text-center">SKUs</th>
                                                                    <th width="20%" class="text-center">Qty</th>
                                                                    <th width="20%" class="text-center">%</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td colspan="4"
                                                                        class="text-center text-muted py-3">
                                                                        Loading data...
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Label Summary -->
                                        <div class="col-lg-6 col-md-12 mb-4">
                                            <div class="card h-100">
                                                <div class="card-header bg-info text-white">
                                                    <h6 class="mb-0">Ringkasan per Label</h6>
                                                    <small>Breakdown penjualan berdasarkan label</small>
                                                </div>
                                                <div class="card-body p-0">
                                                    <div style="max-height: 400px; overflow-y: auto;">
                                                        <table class="table table-sm mb-0" id="label-summary-table">
                                                            <thead class="table-light sticky-top">
                                                                <tr>
                                                                    <th width="40%">Label</th>
                                                                    <th width="20%" class="text-center">SKUs</th>
                                                                    <th width="20%" class="text-center">Qty</th>
                                                                    <th width="20%" class="text-center">%</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td colspan="4"
                                                                        class="text-center text-muted py-3">
                                                                        Loading data...
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Additional Insights -->
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h6 class="mb-0">Insight Tambahan</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h6 class="text-primary">Produk dengan Frekuensi Tinggi</h6>
                                                            <p class="small text-muted mb-2">Produk yang sering muncul
                                                                dalam transaksi (meski quantity kecil)</p>
                                                            <div id="high-frequency-products">
                                                                <div class="text-muted">Loading...</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6 class="text-success">Produk dengan Volume Tinggi</h6>
                                                            <p class="small text-muted mb-2">Produk dengan quantity besar
                                                                per transaksi</p>
                                                            <div id="high-volume-products">
                                                                <div class="text-muted">Loading...</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Ringkasan SKU Tab End -->
                    </div>
                    <!-- Tab Content End -->
                </div>
            </div>
        </div>
        <!-- Scanner Report Table end -->
    </div>
    <!-- [ Main Content ] end -->
@endsection

@section('scripts')
    <!-- Core JS files -->
    <script src="{{ URL::asset('build/js/plugins/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/bootstrap.min.js') }}"></script>

    <!-- DataTables Core -->
    <script src="{{ URL::asset('build/js/plugins/dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/dataTables.bootstrap5.min.js') }}"></script>

    <!-- DataTables Buttons and Extensions -->
    <script src="{{ URL::asset('build/js/plugins/dataTables.buttons.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/pdfmake.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/vfs_fonts.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/buttons.html5.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/buttons.print.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/buttons.colVis.min.js') }}"></script>

    <!-- SheetJS library for Excel export - menggunakan library yang sama seperti report.blade.php -->
    <!-- CATATAN: Pastikan file xlsx.full.min.js tersedia di public/build/js/plugins/ -->
    <!-- Download dari: https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js -->
    <script src="{{ URL::asset('build/js/plugins/xlsx.full.min.js') }}"></script>

    <!-- SweetAlert2 for confirmations - menggunakan library yang sama seperti report.blade.php -->
    <!-- CATATAN: Pastikan file sweetalert2.min.js tersedia di public/build/js/plugins/ -->
    <!-- Download dari: https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js -->
    <script src="{{ URL::asset('build/js/plugins/sweetalert2.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#scanner-report-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('reports.scanner.index') }}",
                    type: "GET",
                    data: function(d) {
                        d.start_date = $('#start-date').val();
                        d.end_date = $('#end-date').val();
                        d.no_resi = $('#no-resi-filter').val();
                        return d;
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'no_resi',
                        name: 'no_resi'
                    },
                    {
                        data: 'no_sku',
                        name: 'no_sku'
                    },
                    {
                        data: 'qty',
                        name: 'qty'
                    }
                ],
                order: [
                    [1, 'desc']
                ],
                pageLength: 25,
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'copy',
                        text: 'Copy',
                        className: 'btn btn-secondary'
                    },
                    {
                        extend: 'excel',
                        text: 'Excel',
                        className: 'btn btn-success'
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        className: 'btn btn-info'
                    }
                ],
                language: {
                    processing: '<div class="d-flex justify-content-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>',
                    emptyTable: 'Tidak ada data scanner',
                    zeroRecords: 'Tidak ditemukan data scanner yang sesuai',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                    infoEmpty: 'Menampilkan 0 sampai 0 dari 0 data',
                    infoFiltered: '(difilter dari _MAX_ total data)',
                    search: 'Cari:',
                    paginate: {
                        first: 'Pertama',
                        last: 'Terakhir',
                        next: 'Selanjutnya',
                        previous: 'Sebelumnya'
                    }
                },
                drawCallback: function(settings) {
                    // Update summary cards after table is drawn
                    updateSummaryCards();
                }
            });

            // Apply filters
            $('#apply-filters').on('click', function() {
                updateTablePeriod();
                table.ajax.reload();
            });

            // Clear filters
            $('#clear-filters').on('click', function() {
                $('#start-date').val('');
                $('#end-date').val('');
                $('#no-resi-filter').val('');
                updateTablePeriod();
                table.ajax.reload();
            });

            // Export Excel (filtered data)
            $('#export-excel').on('click', function() {
                const startDate = $('#start-date').val();
                const endDate = $('#end-date').val();
                const noResi = $('#no-resi-filter').val();

                // Check if any filters are applied
                if (!startDate && !endDate && !noResi) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Silakan terapkan filter terlebih dahulu atau gunakan "Export All" untuk mengekspor semua data.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    return;
                }

                // Show export status indicator
                $('#exportStatus').show();
                $('#exportStatusText').text('Mempersiapkan data terfilter untuk ekspor...');

                // Disable buttons during export
                const $buttons = $('#apply-filters, #clear-filters, #export-excel, #export-all');
                $buttons.prop('disabled', true);

                // Perform the AJAX request to get filtered data
                $.ajax({
                    url: "{{ route('reports.scanner.export') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        start_date: startDate,
                        end_date: endDate,
                        no_resi: noResi
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            // Show row count in status
                            $('#exportStatusText').text(
                                `Mengekspor ${response.count} baris data...`
                            );

                            // Export filtered data to Excel with a small delay to allow UI update
                            setTimeout(() => {
                                const dateRange = startDate && endDate ?
                                    `_${startDate.replace(/-/g, '')}_to_${endDate.replace(/-/g, '')}` :
                                    '_filtered';
                                exportToExcel(response.data,
                                    'Laporan_Scanner' + dateRange + '_' +
                                    getCurrentDate());
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: `${response.count} data berhasil diekspor ke Excel`,
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 2000,
                                    timerProgressBar: true
                                });
                                $('#exportStatus').hide();
                                $buttons.prop('disabled', false);
                            }, 500);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Perhatian',
                                text: response.message ||
                                    'Terjadi kesalahan saat mengekspor data',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                            $('#exportStatus').hide();
                            $buttons.prop('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        $('#exportStatus').hide();
                        $buttons.prop('disabled', false);
                        Swal.fire({
                            icon: 'error',
                            title: 'Perhatian',
                            text: 'Terjadi kesalahan: ' + (xhr.responseJSON &&
                                xhr.responseJSON.message ? xhr.responseJSON
                                .message : 'Tidak dapat menghubungi server'),
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    }
                });
            });

            // Function to update table period display
            function updateTablePeriod() {
                const startDate = $('#start-date').val();
                const endDate = $('#end-date').val();

                let periodText = 'Menampilkan semua data scanner';

                if (startDate || endDate) {
                    const formattedStartDate = startDate ? new Date(startDate).toLocaleDateString('id-ID') : 'Awal';
                    const formattedEndDate = endDate ? new Date(endDate).toLocaleDateString('id-ID') : 'Sekarang';

                    if (startDate && endDate && startDate === endDate) {
                        periodText = `Data scanner untuk tanggal: ${formattedStartDate}`;
                    } else if (startDate && endDate) {
                        periodText = `Data scanner periode: ${formattedStartDate} - ${formattedEndDate}`;
                    } else if (startDate) {
                        periodText = `Data scanner dari tanggal: ${formattedStartDate}`;
                    } else if (endDate) {
                        periodText = `Data scanner sampai tanggal: ${formattedEndDate}`;
                    }
                }

                $('#table-period').text(periodText);
            }

            // Function to update summary cards
            function updateSummaryCards() {
                // Get current table data
                const tableData = table.rows({
                    page: 'current'
                }).data();

                let totalScans = table.page.info().recordsTotal;
                let uniqueOrders = new Set();
                let totalProductsSold = 0;
                let totalQuantity = 0;

                // Calculate from current visible data
                tableData.each(function(row) {
                    // Count unique orders (no_resi)
                    if (row.no_resi) {
                        uniqueOrders.add(row.no_resi);
                    }

                    // Count products sold (assuming comma-separated values)
                    if (row.no_sku) {
                        const products = row.no_sku.split(',').length;
                        totalProductsSold += products;
                    }

                    // Count total quantity (assuming comma-separated values)
                    if (row.qty) {
                        if (typeof row.qty === 'string' && row.qty.includes(',')) {
                            const quantities = row.qty.split(',');
                            quantities.forEach(qty => {
                                totalQuantity += parseInt(qty.trim()) || 0;
                            });
                        } else {
                            totalQuantity += parseInt(row.qty) || 0;
                        }
                    }
                });

                // Update summary cards
                $('#total-scans').text(totalScans.toLocaleString('id-ID'));
                $('#total-orders').text(uniqueOrders.size.toLocaleString('id-ID'));
                $('#total-products-sold').text(totalProductsSold.toLocaleString('id-ID'));
                $('#total-quantity').text(totalQuantity.toLocaleString('id-ID'));
            }

            // Initialize table period display
            updateTablePeriod();

            // Summary Tab Functions
            let summaryData = null;

            // Load summary when tab is clicked
            $('#summary-tab').on('click', function() {
                loadSummaryData();
            });

            // Refresh summary button
            $('#refresh-summary').on('click', function() {
                loadSummaryData();
            });

            // Function to load summary data
            function loadSummaryData() {
                $.ajax({
                    url: "{{ route('reports.scanner.index') }}",
                    type: "GET",
                    data: {
                        action: 'get_summary',
                        start_date: $('#start-date').val(),
                        end_date: $('#end-date').val(),
                        no_resi: $('#no-resi-filter').val()
                    },
                    beforeSend: function() {
                        // Show loading state
                        showSummaryLoading();
                    },
                    success: function(response) {
                        if (response.success) {
                            summaryData = response.data;
                            updateSummaryDisplay();
                        } else {
                            console.error('Error loading summary:', response.message);
                            showSummaryError('Gagal memuat ringkasan data');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        showSummaryError('Terjadi kesalahan saat memuat ringkasan data');
                    }
                });
            }

            // Function to show loading state
            function showSummaryLoading() {
                const loadingHtml =
                    '<tr><td colspan="3" class="text-center text-muted py-3"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>';
                $('#sku-summary-table tbody').html(loadingHtml);
                $('#category-summary-table tbody').html(loadingHtml);
                $('#label-summary-table tbody').html(loadingHtml);
            }

            // Function to show error state
            function showSummaryError(message) {
                const errorHtml =
                    `<tr><td colspan="3" class="text-center text-danger py-3"><i class="fas fa-exclamation-triangle"></i> ${message}</td></tr>`;
                $('#sku-summary-table tbody').html(errorHtml);
                $('#category-summary-table tbody').html(errorHtml);
                $('#label-summary-table tbody').html(errorHtml);
            }

            // Function to update summary display
            function updateSummaryDisplay() {
                if (!summaryData) return;

                const totalQty = summaryData.total_stats.total_qty;
                const totalTxn = summaryData.total_stats.total_transactions;

                // Check for unknown products and show warning
                checkUnknownProducts();

                // Update summary statistics
                $('#summary-total-qty').text(totalQty.toLocaleString('id-ID'));
                $('#summary-unique-skus').text(summaryData.total_stats.unique_skus.toLocaleString('id-ID'));
                $('#summary-unique-categories').text(summaryData.total_stats.unique_categories.toLocaleString(
                    'id-ID'));
                $('#summary-unique-labels').text(summaryData.total_stats.unique_labels.toLocaleString('id-ID'));
                $('#summary-total-transactions').text(totalTxn.toLocaleString('id-ID'));

                // Update performance metrics
                if (summaryData.sku_summary.length > 0) {
                    const topPerformer = summaryData.sku_summary[0];
                    const mostFrequent = summaryData.sku_summary.reduce((prev, current) =>
                        (prev.transactions > current.transactions) ? prev : current
                    );

                    $('#top-performer-qty').text(topPerformer.qty.toLocaleString('id-ID'));
                    $('#top-performer-name').text(topPerformer.name_product.substring(0, 20) + '...');
                    $('#most-frequent-txn').text(mostFrequent.transactions.toLocaleString('id-ID'));
                    $('#most-frequent-name').text(mostFrequent.name_product.substring(0, 20) + '...');
                    $('#avg-qty-per-txn').text(Math.round(totalQty / totalTxn * 100) / 100);

                    // Calculate category diversity
                    const categoryCount = summaryData.total_stats.unique_categories;
                    const diversity = Math.min(100, Math.round((categoryCount / 10) *
                        100)); // Assume max 10 categories for 100%
                    $('#category-diversity').text(diversity + '%');
                }

                // Update SKU summary table with ranking
                let skuHtml = '';
                if (summaryData.sku_summary.length === 0) {
                    skuHtml =
                        '<tr><td colspan="5" class="text-center text-muted py-3">Tidak ada data SKU</td></tr>';
                } else {
                    summaryData.sku_summary.slice(0, 10).forEach(function(item, index) {
                        const rankBadge = index < 3 ?
                            `<span class="badge bg-warning text-dark">#${index + 1}</span>` :
                            `<span class="badge bg-light text-dark">#${index + 1}</span>`;

                        // Check if this is an unknown product
                        const isUnknown = item.category === 'Unknown Category' ||
                            item.category === 'Unknown' ||
                            item.category === '' ||
                            item.category === null ||
                            item.name_product === 'Unknown Product' ||
                            item.name_product === 'Unknown' ||
                            item.name_product === '' ||
                            item.name_product === null;

                        const rowClass = isUnknown ? 'table-warning' : '';
                        const unknownIndicator = isUnknown ?
                            '<span class="badge bg-danger text-white ms-1" title="SKU tidak terdefinisi di tabel Product">!</span>' :
                            '';

                        const avgPerTxn = Math.round((item.qty / item.transactions) * 100) / 100;

                        skuHtml += `
                            <tr class="${rowClass}">
                                <td class="text-center">${rankBadge}</td>
                                <td>
                                    <div class="fw-bold small">${item.name_product}${unknownIndicator}</div>
                                    <small class="text-muted">${item.sku} • ${item.category}</small>
                                    ${isUnknown ? '<div class="small text-danger">⚠️ Perlu ditambahkan ke tabel Product</div>' : ''}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary">${item.qty.toLocaleString('id-ID')}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">${item.transactions}</span>
                                </td>
                                <td class="text-center">
                                    <small class="text-muted">${avgPerTxn}</small>
                                </td>
                            </tr>
                        `;
                    });
                }
                $('#sku-summary-table tbody').html(skuHtml);

                // Update Category summary table with percentages
                let categoryHtml = '';
                if (summaryData.category_summary.length === 0) {
                    categoryHtml =
                        '<tr><td colspan="4" class="text-center text-muted py-3">Tidak ada data kategori</td></tr>';
                } else {
                    summaryData.category_summary.forEach(function(item) {
                        const percentage = Math.round((item.qty / totalQty) * 100);
                        categoryHtml += `
                            <tr>
                                <td>
                                    <div class="fw-bold">${item.category}</div>
                                    <small class="text-muted">${item.transactions} transaksi</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success">${item.unique_skus}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary">${item.qty.toLocaleString('id-ID')}</span>
                                </td>
                                <td class="text-center">
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-success" style="width: ${percentage}%">${percentage}%</div>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                }
                $('#category-summary-table tbody').html(categoryHtml);

                // Update Label summary table with percentages
                let labelHtml = '';
                if (summaryData.label_summary.length === 0) {
                    labelHtml =
                        '<tr><td colspan="4" class="text-center text-muted py-3">Tidak ada data label</td></tr>';
                } else {
                    summaryData.label_summary.forEach(function(item) {
                        const percentage = Math.round((item.qty / totalQty) * 100);
                        labelHtml += `
                            <tr>
                                <td>
                                    <div class="fw-bold">${item.label}</div>
                                    <small class="text-muted">${item.transactions} transaksi</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">${item.unique_skus}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary">${item.qty.toLocaleString('id-ID')}</span>
                                </td>
                                <td class="text-center">
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-info" style="width: ${percentage}%">${percentage}%</div>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                }
                $('#label-summary-table tbody').html(labelHtml);

                // Update additional insights
                updateAdditionalInsights();
            }

            // Function to update additional insights
            function updateAdditionalInsights() {
                if (!summaryData || summaryData.sku_summary.length === 0) return;

                // High frequency products (sorted by transaction count)
                const highFrequency = [...summaryData.sku_summary]
                    .sort((a, b) => b.transactions - a.transactions)
                    .slice(0, 5);

                let frequencyHtml = '';
                highFrequency.forEach(function(item, index) {
                    const avgQtyPerTxn = Math.round(item.qty / item.transactions * 100) / 100;
                    frequencyHtml += `
                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                            <div>
                                <div class="fw-bold small">${item.name_product.substring(0, 25)}...</div>
                                <small class="text-muted">${item.transactions} transaksi</small>
                            </div>
                            <div class="text-end">
                                <small class="text-primary">${avgQtyPerTxn} qty/txn</small>
                            </div>
                        </div>
                    `;
                });
                $('#high-frequency-products').html(frequencyHtml);

                // High volume products (sorted by average quantity per transaction)
                const highVolume = [...summaryData.sku_summary]
                    .map(item => ({
                        ...item,
                        avgQtyPerTxn: item.qty / item.transactions
                    }))
                    .sort((a, b) => b.avgQtyPerTxn - a.avgQtyPerTxn)
                    .slice(0, 5);

                let volumeHtml = '';
                highVolume.forEach(function(item, index) {
                    volumeHtml += `
                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                            <div>
                                <div class="fw-bold small">${item.name_product.substring(0, 25)}...</div>
                                <small class="text-muted">${item.qty} total qty</small>
                            </div>
                            <div class="text-end">
                                <small class="text-success">${Math.round(item.avgQtyPerTxn * 100) / 100} qty/txn</small>
                            </div>
                        </div>
                    `;
                });
                $('#high-volume-products').html(volumeHtml);
            }

            // Update summary when filters are applied
            $('#apply-filters').on('click', function() {
                // If summary tab is active, reload summary data
                if ($('#summary-tab').hasClass('active')) {
                    loadSummaryData();
                }
            });

            // Update summary when filters are cleared
            $('#clear-filters').on('click', function() {
                // If summary tab is active, reload summary data
                if ($('#summary-tab').hasClass('active')) {
                    loadSummaryData();
                }
            });

            // Export All button with confirmation
            $('#export-all').on('click', function() {
                Swal.fire({
                    title: 'Ekspor Semua Data',
                    html: `<div class="text-left">
                            <p>Anda akan mengekspor <strong>SEMUA DATA</strong> dari database.</p>
                            <p>Proses ini akan:</p>
                            <ul>
                                <li>Menyertakan semua data scanner</li>
                                <li>Mengabaikan filter yang sedang diterapkan</li>
                                <li>Mungkin memerlukan waktu yang lebih lama jika data sangat banyak</li>
                            </ul>
                            <p>Apakah Anda ingin melanjutkan?</p>
                           </div>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Ekspor Semua',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#28a745'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show export status indicator
                        $('#exportStatus').show();
                        $('#exportStatusText').text('Mempersiapkan semua data untuk ekspor...');

                        // Disable buttons during export
                        const $buttons = $(
                            '#apply-filters, #clear-filters, #export-excel, #export-all');
                        $buttons.prop('disabled', true);

                        // Perform the AJAX request to get all data
                        $.ajax({
                            url: "{{ route('reports.scanner.export') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                export_all: true
                                // No date filters for exporting all data
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    // Show row count in status
                                    $('#exportStatusText').text(
                                        `Mengekspor ${response.count} baris data...`
                                    );

                                    // Export all data to Excel with a small delay to allow UI update
                                    setTimeout(() => {
                                        exportToExcel(response.data,
                                            'Laporan_Scanner_Lengkap_' +
                                            getCurrentDate());
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil',
                                            text: `${response.count} data berhasil diekspor ke Excel`,
                                            toast: true,
                                            position: 'top-end',
                                            showConfirmButton: false,
                                            timer: 2000,
                                            timerProgressBar: true
                                        });
                                        $('#exportStatus').hide();
                                        $buttons.prop('disabled', false);
                                    }, 500);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Perhatian',
                                        text: response.message ||
                                            'Terjadi kesalahan saat mengekspor data',
                                        toast: true,
                                        position: 'top-end',
                                        showConfirmButton: false,
                                        timer: 3000,
                                        timerProgressBar: true
                                    });
                                    $('#exportStatus').hide();
                                    $buttons.prop('disabled', false);
                                }
                            },
                            error: function(xhr) {
                                $('#exportStatus').hide();
                                $buttons.prop('disabled', false);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Perhatian',
                                    text: 'Terjadi kesalahan: ' + (xhr
                                        .responseJSON &&
                                        xhr.responseJSON.message ? xhr
                                        .responseJSON
                                        .message :
                                        'Tidak dapat menghubungi server'),
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true
                                });
                            }
                        });
                    }
                });
            });

            // Function to check for unknown products
            function checkUnknownProducts() {
                if (!summaryData || !summaryData.sku_summary) return;

                // Find products with "Unknown" category or no category
                const unknownProducts = summaryData.sku_summary.filter(item =>
                    item.category === 'Unknown Category' ||
                    item.category === 'Unknown' ||
                    item.category === '' ||
                    item.category === null ||
                    item.name_product === 'Unknown Product' ||
                    item.name_product === 'Unknown' ||
                    item.name_product === '' ||
                    item.name_product === null
                );

                if (unknownProducts.length > 0) {
                    // Show warning alert
                    $('#unknown-products-warning').show();

                    // Create list of unknown SKUs
                    let unknownSkusHtml = '';
                    unknownProducts.forEach(function(item, index) {
                        const badgeClass = index % 2 === 0 ? 'bg-warning' : 'bg-secondary';
                        unknownSkusHtml += `
                            <span class="badge ${badgeClass} me-2 mb-1">
                                ${item.sku} (${item.qty.toLocaleString('id-ID')} qty, ${item.transactions} txn)
                            </span>
                        `;
                    });
                    $('#unknown-skus-list').html(unknownSkusHtml);

                    // Add summary count to warning with better formatting
                    const warningTitle = `Peringatan: ${unknownProducts.length} Produk Tidak Terdefinisi`;
                    $('#unknown-products-title').html(`
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        ${warningTitle}
                        <span class="badge bg-danger ms-2">${unknownProducts.length}</span>
                    `);
                } else {
                    // Hide warning if no unknown products
                    $('#unknown-products-warning').hide();
                }
            }

            // Helper function to get current date for filename
            function getCurrentDate() {
                const now = new Date();
                const year = now.getFullYear();
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const day = String(now.getDate()).padStart(2, '0');
                return `${year}${month}${day}`;
            }

            // Handle collapse toggle for unknown products warning
            $(document).on('show.bs.collapse', '#unknown-products-details', function() {
                $('#toggle-icon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
                $('#toggle-unknown-details span').text('Sembunyikan');
            });

            $(document).on('hide.bs.collapse', '#unknown-products-details', function() {
                $('#toggle-icon').removeClass('fa-chevron-up').addClass('fa-chevron-down');
                $('#toggle-unknown-details span').text('Lihat Detail');
            });

            // Function to export data to Excel
            function exportToExcel(data, fileName) {
                // Create a new workbook
                const wb = XLSX.utils.book_new();

                // Convert the data to a worksheet
                const ws = XLSX.utils.json_to_sheet(data);

                // Set column widths for better readability
                const colWidths = [{
                        wch: 8
                    }, // No
                    {
                        wch: 20
                    }, // Tanggal
                    {
                        wch: 25
                    }, // No Resi
                    {
                        wch: 60
                    }, // Produk Terjual - wide for multiple products
                    {
                        wch: 25
                    } // Quantity Terjual
                ];

                ws['!cols'] = colWidths;

                // Configure cell styles to ensure text wrapping for long content
                if (!ws['!rows']) ws['!rows'] = [];

                // Process each cell to ensure proper text wrapping and formatting
                for (let i = 0; i < data.length; i++) {
                    const rowIndex = i + 1; // +1 to skip header
                    const produkValue = data[i]['Produk Terjual'] || '';
                    const qtyValue = data[i]['Quantity Terjual'] || '';

                    // Estimate minimum needed height based on text length
                    const approximateLines = Math.max(
                        Math.ceil(produkValue.length / 40), // Estimate line breaks based on characters
                        Math.ceil(qtyValue.length / 15),
                        1 // Minimum 1 line
                    );

                    // Set row height for better readability
                    ws['!rows'][rowIndex] = {
                        hpt: Math.min(approximateLines * 18, 200) // Adjust height, cap at 200pt
                    };
                }

                // Add autofilter to enable Excel filtering
                const range = XLSX.utils.decode_range(ws['!ref']);
                ws['!autofilter'] = {
                    ref: XLSX.utils.encode_range({
                            r: 0,
                            c: 0
                        }, // Start at first row, first column (header)
                        {
                            r: 0,
                            c: range.e.c
                        } // End at first row, last column
                    )
                };

                // Add the worksheet to the workbook
                XLSX.utils.book_append_sheet(wb, ws, 'Laporan Scanner');

                // Generate Excel file and trigger download
                XLSX.writeFile(wb, fileName + '.xlsx');
            }
        });
    </script>
@endsection
