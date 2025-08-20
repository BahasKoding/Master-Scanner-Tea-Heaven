@extends('layouts.main')

@section('title', 'Ringkasan Penjualan Scanner')
@section('breadcrumb-item', 'Ringkasan Scanner')

@section('css')
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- [Page specific CSS] start -->
    <!-- Choices css -->
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/choices.min.css') }}">
    <!-- [Page specific CSS] end -->
    <style>
        .form-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        /* Responsive improvements */
        .filter-section {
            padding: 15px;
            margin-bottom: 15px;
        }

        .filter-label {
            font-size: 12px;
            margin-bottom: 4px;
        }

        /* Table responsiveness */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* DataTables buttons responsiveness */
        .dt-buttons {
            margin-bottom: 10px;
        }

        .dt-button {
            margin-right: 5px;
            margin-bottom: 5px;
        }

        /* Modal responsiveness */
        @media (max-width: 768px) {
            .modal-dialog {
                max-width: 95%;
                margin: 10px auto;
            }

            .filter-section {
                padding: 10px;
            }

            .table {
                font-size: 14px;
            }

            .dt-buttons {
                display: flex;
                flex-wrap: wrap;
                gap: 5px;
            }

            .dt-button {
                flex: 1;
                white-space: nowrap;
                min-width: auto !important;
                padding: 0.25rem 0.5rem !important;
                font-size: 0.875rem !important;
            }

            .filter-toggle {
                display: block;
                width: 100%;
                margin-bottom: 10px;
            }

            .paginate_button {
                padding: 0.3rem 0.5rem !important;
            }

            /* Inline editing responsive */
            .stock-input {
                width: 60px !important;
                font-size: 12px !important;
            }

            .btn-sm {
                padding: 0.2rem 0.4rem !important;
                font-size: 0.75rem !important;
            }
        }

        @media (max-width: 576px) {
            .action-buttons .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.875rem;
                margin-bottom: 3px;
            }

            .paginate_button {
                padding: 0.2rem 0.3rem !important;
                font-size: 0.8rem;
            }

            .stock-input {
                width: 50px !important;
                font-size: 11px !important;
            }
        }

        /* Inline editing styles */
        .stock-input {
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: center;
            transition: border-color 0.3s ease;
        }

        .stock-input:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            outline: none;
        }

        .live-stock {
            font-size: 0.9rem;
            font-weight: bold;
        }

        .update-btn {
            margin-right: 5px;
        }

        .table td {
            vertical-align: middle;
        }

        .auto-field {
            background-color: #f8f9fa;
            color: #6c757d;
            font-weight: bold;
            cursor: not-allowed;
        }

        .auto-field:disabled {
            background-color: #e9ecef !important;
            border-color: #ced4da !important;
            opacity: 0.8;
        }

        .sisa-display {
            font-weight: bold;
        }
    </style>
@endsection

@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">
        <!-- Monthly Sales Summary start -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <h5 class="mb-2 mb-sm-0">Ringkasan Penjualan Scanner Bulanan</h5>
                        <div class="d-flex flex-wrap">
                            <button id="load-summary" class="btn btn-primary me-2 mb-2 mb-sm-0">
                                <i class="fas fa-chart-bar"></i> Muat Ringkasan
                            </button>
                            <button id="export-summary" class="btn btn-success me-2 mb-2 mb-sm-0">
                                <i class="fas fa-download"></i> Export
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="mb-3 p-3 border rounded bg-light filter-section">
                        <button id="filter-toggle-btn" class="btn btn-sm btn-outline-secondary mb-2 w-100 d-md-none"
                            type="button" data-bs-toggle="collapse" data-bs-target="#filterControls">
                            <i class="fas fa-filter"></i> Toggle Filters <i class="fas fa-chevron-down"></i>
                        </button>
                        <div id="filterControls" class="collapse show">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12 mb-2">
                                    <label for="filter-month" class="form-label small filter-label">Bulan</label>
                                    <select class="form-control form-control-sm" name="filter-month" id="filter-month">
                                        @foreach ($months as $key => $month)
                                            <option value="{{ $key }}" {{ $key == date('m') ? 'selected' : '' }}>
                                                {{ $month }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 mb-2">
                                    <label for="filter-year" class="form-label small filter-label">Tahun</label>
                                    <select class="form-control form-control-sm" name="filter-year" id="filter-year">
                                        @foreach ($years as $key => $year)
                                            <option value="{{ $key }}" {{ $key == date('Y') ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Filter Section -->

                    <!-- Summary Statistics Cards -->
                    <div id="summary-stats" class="row mb-4 d-none">
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title mb-0">Total Qty Terjual</h6>
                                            <small class="text-white-50">Dari history sales</small>
                                            <h4 class="mb-0" id="total-qty">0</h4>
                                        </div>
                                        <i class="fas fa-boxes fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title mb-0">Total Transaksi</h6>
                                            <small class="text-white-50">Dari history sales</small>
                                            <h4 class="mb-0" id="total-transactions">0</h4>
                                        </div>
                                        <i class="fas fa-receipt fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title mb-0">Total Produk</h6>
                                            <small class="text-white-50">Semua produk di database</small>
                                            <h4 class="mb-0" id="unique-skus">0</h4>
                                        </div>
                                        <i class="fas fa-barcode fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title mb-0">Rata-rata/Hari</h6>
                                            <small class="text-white-50">Qty terjual per hari</small>
                                            <h4 class="mb-0" id="avg-per-day">0</h4>
                                        </div>
                                        <i class="fas fa-chart-line fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Tables -->
                    <div id="summary-tables" class="d-none">
                        <!-- SKU Summary -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-list"></i> Ringkasan per SKU</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="sku-summary-table" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>SKU</th>
                                                <th>Nama Produk</th>
                                                <th>Kategori</th>
                                                <th>Label</th>
                                                <th>Quantity Terjual</th>
                                                <th>Jumlah Transaksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Data will be loaded via AJAX -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Category Summary -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-tags"></i> Ringkasan per Kategori</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="category-summary-table" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Kategori</th>
                                                <th>Quantity Terjual</th>
                                                <th>Unique SKUs</th>
                                                <th>Jumlah Transaksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Data will be loaded via AJAX -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Daily Summary -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-calendar-day"></i> Ringkasan Harian</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="daily-summary-table" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tanggal</th>
                                                <th>Hari</th>
                                                <th>Quantity Terjual</th>
                                                <th>Unique SKUs</th>
                                                <th>Jumlah Transaksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Data will be loaded via AJAX -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- No Data Message -->
                    <div id="no-data-message" class="alert alert-info text-center">
                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                        <h5>Pilih Bulan dan Tahun</h5>
                        <p class="mb-0">Silakan pilih bulan dan tahun, kemudian klik "Muat Ringkasan" untuk melihat data penjualan.</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Finished Goods Table end -->
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

    <!-- Choices JS -->
    <script src="{{ URL::asset('build/js/plugins/choices.min.js') }}"></script>
    
    <!-- SweetAlert2 -->
    <script src="{{ URL::asset('build/js/plugins/sweetalert2.all.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            // Initialize DataTables for summary tables
            let skuSummaryTable, categorySummaryTable, dailySummaryTable;

            // Initialize summary tables
            function initializeSummaryTables() {
                // SKU Summary Table
                skuSummaryTable = $('#sku-summary-table').DataTable({
                    data: [],
                    columns: [
                        { data: null, render: function(data, type, row, meta) { return meta.row + 1; } },
                        { data: 'sku' },
                        { data: 'name_product' },
                        { data: 'category' },
                        { data: 'label' },
                        { data: 'qty', render: function(data) { return parseInt(data).toLocaleString(); } },
                        { data: 'transactions', render: function(data) { return parseInt(data).toLocaleString(); } }
                    ],
                    pageLength: 10,
                    order: [[5, 'desc']], // Order by quantity descending
                    dom: 'Bfrtip',
                    buttons: [
                        { 
                            extend: 'copy', 
                            text: '<i class="fas fa-copy"></i> Salin',
                            title: function() {
                                return getExportTitle('SKU Summary');
                            }
                        },
                        { 
                            extend: 'excel', 
                            text: '<i class="fas fa-file-excel"></i> Excel',
                            title: function() {
                                return getExportTitle('SKU Summary');
                            },
                            filename: function() {
                                return getExportFilename('Ringkasan_SKU');
                            }
                        },
                        { 
                            extend: 'print', 
                            text: '<i class="fas fa-print"></i> Cetak',
                            title: function() {
                                return getExportTitle('SKU Summary');
                            }
                        }
                    ]
                });

                // Category Summary Table
                categorySummaryTable = $('#category-summary-table').DataTable({
                    data: [],
                    columns: [
                        { data: null, render: function(data, type, row, meta) { return meta.row + 1; } },
                        { data: 'category' },
                        { data: 'qty', render: function(data) { return parseInt(data).toLocaleString(); } },
                        { data: 'unique_skus', render: function(data) { return parseInt(data).toLocaleString(); } },
                        { data: 'transactions', render: function(data) { return parseInt(data).toLocaleString(); } }
                    ],
                    pageLength: 10,
                    order: [[2, 'desc']], // Order by quantity descending
                    dom: 'Bfrtip',
                    buttons: [
                        { 
                            extend: 'copy', 
                            text: '<i class="fas fa-copy"></i> Salin',
                            title: function() {
                                return getExportTitle('Category Summary');
                            }
                        },
                        { 
                            extend: 'excel', 
                            text: '<i class="fas fa-file-excel"></i> Excel',
                            title: function() {
                                return getExportTitle('Category Summary');
                            },
                            filename: function() {
                                return getExportFilename('Ringkasan_Kategori');
                            }
                        },
                        { 
                            extend: 'print', 
                            text: '<i class="fas fa-print"></i> Cetak',
                            title: function() {
                                return getExportTitle('Category Summary');
                            }
                        }
                    ]
                });

                // Daily Summary Table
                dailySummaryTable = $('#daily-summary-table').DataTable({
                    data: [],
                    columns: [
                        { data: null, render: function(data, type, row, meta) { return meta.row + 1; } },
                        { data: 'formatted_date' },
                        { data: 'day_name' },
                        { data: 'qty', render: function(data) { return parseInt(data).toLocaleString(); } },
                        { data: 'unique_skus_count', render: function(data) { return parseInt(data).toLocaleString(); } },
                        { data: 'transactions', render: function(data) { return parseInt(data).toLocaleString(); } }
                    ],
                    pageLength: 10,
                    order: [[1, 'asc']], // Order by date ascending
                    dom: 'Bfrtip',
                    buttons: [
                        { 
                            extend: 'copy', 
                            text: '<i class="fas fa-copy"></i> Salin',
                            title: function() {
                                return getExportTitle('Daily Summary');
                            }
                        },
                        { 
                            extend: 'excel', 
                            text: '<i class="fas fa-file-excel"></i> Excel',
                            title: function() {
                                return getExportTitle('Daily Summary');
                            },
                            filename: function() {
                                return getExportFilename('Ringkasan_Harian');
                            }
                        },
                        { 
                            extend: 'print', 
                            text: '<i class="fas fa-print"></i> Cetak',
                            title: function() {
                                return getExportTitle('Daily Summary');
                            }
                        }
                    ]
                });
            }

            // Global variables to store current period info
            let currentPeriodInfo = null;

            // Function to generate export title with date range
            function getExportTitle(summaryType) {
                if (currentPeriodInfo && currentPeriodInfo.is_filtered) {
                    return `${summaryType} - ${currentPeriodInfo.period_display}`;
                }
                return `${summaryType} - Semua Data`;
            }

            // Function to generate export filename with date range
            function getExportFilename(baseFilename) {
                if (currentPeriodInfo && currentPeriodInfo.is_filtered) {
                    const monthNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    const monthName = monthNames[parseInt(currentPeriodInfo.month)];
                    return `${baseFilename}_${monthName}_${currentPeriodInfo.year}`;
                }
                return `${baseFilename}_Semua_Data`;
            }

            // Load monthly summary data
            function loadMonthlySummary() {
                const month = $('#filter-month').val();
                const year = $('#filter-year').val();
                const loadBtn = $('#load-summary');

                // Show loading state
                loadBtn.prop('disabled', true)
                       .html('<i class="fas fa-spinner fa-spin"></i> Memuat...');

                $.ajax({
                    url: "{{ route('reports.scanner.summary') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        action: 'get_monthly_summary',
                        month: month,
                        year: year
                    },
                    success: function(response) {
                        if (response.success && response.data) {
                            // Store period information for export functionality
                            currentPeriodInfo = response.data.period;

                            // Update statistics cards
                            $('#total-qty').text(parseInt(response.data.total_stats.total_qty).toLocaleString());
                            $('#total-transactions').text(parseInt(response.data.total_stats.total_transactions).toLocaleString());
                            $('#unique-skus').text(parseInt(response.data.total_stats.unique_skus).toLocaleString());
                            $('#avg-per-day').text(parseFloat(response.data.total_stats.avg_qty_per_day).toLocaleString());

                            // Update table data
                            skuSummaryTable.clear().rows.add(response.data.sku_summary).draw();
                            categorySummaryTable.clear().rows.add(response.data.category_summary).draw();
                            dailySummaryTable.clear().rows.add(response.data.daily_summary).draw();

                            // Show summary sections
                            $('#summary-stats').removeClass('d-none');
                            $('#summary-tables').removeClass('d-none');
                            $('#no-data-message').addClass('d-none');

                            // Update page title with period
                            $('.card-header h5').first().html(`Ringkasan Penjualan Scanner - ${response.data.period.period_display}`);

                            // Show success message
                            Swal.fire({
                                title: 'Berhasil!',
                                text: `Data ringkasan untuk ${response.data.period.period_display} berhasil dimuat.`,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });
                        } else {
                            // Show error message
                            Swal.fire({
                                title: 'Tidak Ada Data',
                                text: response.message || 'Tidak ada data penjualan untuk periode yang dipilih.',
                                icon: 'info',
                                confirmButtonText: 'OK'
                            });

                            // Hide summary sections
                            $('#summary-stats').addClass('d-none');
                            $('#summary-tables').addClass('d-none');
                            $('#no-data-message').removeClass('d-none');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan saat memuat data ringkasan.';
                        
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });

                        // Hide summary sections
                        $('#summary-stats').addClass('d-none');
                        $('#summary-tables').addClass('d-none');
                        $('#no-data-message').removeClass('d-none');
                    },
                    complete: function() {
                        // Restore button state
                        loadBtn.prop('disabled', false)
                               .html('<i class="fas fa-chart-bar"></i> Muat Ringkasan');
                    }
                });
            }

            // Export summary functionality
            function exportSummary() {
                const month = $('#filter-month').val();
                const year = $('#filter-year').val();
                
                // Check if data is loaded
                if ($('#summary-stats').hasClass('d-none')) {
                    Swal.fire({
                        title: 'Tidak Ada Data',
                        text: 'Silakan muat ringkasan terlebih dahulu sebelum mengekspor.',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                // Get current month name for filename
                const monthNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                  'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                const filename = `Ringkasan_Penjualan_${monthNames[parseInt(month)]}_${year}`;

                // Export all tables
                Swal.fire({
                    title: 'Export Data',
                    text: 'Pilih format export yang diinginkan:',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-file-excel"></i> Excel',
                    cancelButtonText: '<i class="fas fa-file-pdf"></i> PDF',
                    showDenyButton: true,
                    denyButtonText: '<i class="fas fa-copy"></i> Copy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Export to Excel
                        skuSummaryTable.button('.buttons-excel').trigger();
                    } else if (result.isDenied) {
                        // Copy to clipboard
                        skuSummaryTable.button('.buttons-copy').trigger();
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        // Export to PDF (print)
                        skuSummaryTable.button('.buttons-print').trigger();
                    }
                });
            }

            // Initialize tables on page load
            initializeSummaryTables();

            // Event handlers
            $('#load-summary').on('click', function() {
                loadMonthlySummary();
            });

            $('#export-summary').on('click', function() {
                exportSummary();
            });

            // Auto-load summary for current month on page load
            // loadMonthlySummary();
        });
    </script>

@endsection