@extends('layouts.main')

@section('title', 'History Sales Report')
@section('breadcrumb-item', $item)

@section('css')
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- [Page specific CSS] start -->
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/datatables/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/datatables/buttons.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/flatpickr/flatpickr.min.css') }}">
    <!-- [Page specific CSS] end -->
    <style>
        /* ========== VARIABEL & ROOT ========== */
        :root {
            --primary-color: #4CAF50;
            --primary-light: rgba(76, 175, 80, 0.25);
            --danger-color: #dc3545;
            --secondary-color: #6c757d;
            --light-bg: #f8f9fa;
            --border-color: #dee2e6;
            --focus-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
            --card-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
            --transition-normal: all 0.3s ease;
            --border-radius-normal: 4px;
            --border-radius-large: 8px;
            --form-element-height: 44px;
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 2px 8px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 4px 12px rgba(0, 0, 0, 0.12);
            --text-muted: #6c757d;
            --text-dark: #344767;
        }

        /* ========== UTILITY CLASSES ========== */
        .transition {
            transition: var(--transition-normal);
        }

        /* ========== COMMON ELEMENTS ========== */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            margin-bottom: 30px;
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid var(--border-color);
            padding: 1.25rem 1.5rem;
        }

        .card-header .d-flex {
            flex-wrap: wrap;
            gap: 10px;
        }

        .card-body {
            padding: 1.5rem;
            position: relative;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .error-feedback {
            color: var(--danger-color);
            font-size: 0.875em;
            margin-top: 0.25rem;
            display: none;
        }

        /* ========== BUTTONS ========== */
        .btn {
            min-height: var(--form-element-height);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            transition: var(--transition-normal);
            box-shadow: var(--shadow-sm);
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn:active {
            transform: translateY(1px);
            box-shadow: none;
        }

        /* ========== FORM ELEMENTS ========== */
        .form-select,
        .form-control,
        .date-input {
            height: var(--form-element-height);
            border: 1px solid var(--border-color);
            transition: var(--transition-normal);
            border-radius: var(--border-radius-normal);
        }

        .form-select:focus,
        .form-control:focus,
        .date-input:focus {
            border-color: var(--primary-color);
            box-shadow: var(--focus-shadow);
            outline: none;
        }

        .entries-select-container select {
            width: 100%;
            padding: 8px 12px;
            border-radius: var(--border-radius-normal);
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-position: right 0.75rem center;
            background-size: 16px 12px;
            background-repeat: no-repeat;
        }

        /* ========== FILTER SECTION ========== */
        .filter-section {
            background-color: var(--light-bg);
            padding: 20px;
            border-radius: var(--border-radius-large);
            margin-bottom: 20px;
            box-shadow: var(--shadow-md);
            transition: var(--transition-normal);
        }

        .filter-section:hover {
            box-shadow: var(--shadow-lg);
        }

        /* ========== INDICATORS & HELPERS ========== */
        .help-text {
            margin-top: 5px;
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .loading-overlay {
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            border-radius: var(--border-radius-large);
        }

        .export-status {
            display: none;
            align-items: center;
            justify-content: center;
            padding: 10px;
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            border-radius: var(--border-radius-normal);
            margin-bottom: 15px;
        }

        .export-status .spinner-border {
            width: 1rem;
            height: 1rem;
            margin-right: 8px;
        }

        .table-scroll-indicator {
            display: none;
            margin-bottom: 10px;
            text-align: center;
            padding: 10px;
            color: var(--text-muted);
            font-size: 0.9em;
        }

        .current-date-info {
            display: block;
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .current-date-info strong {
            color: var(--primary-color);
        }

        /* ========== TABLE STYLES ========== */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: var(--border-radius-large);
            box-shadow: var(--shadow-md);
        }

        .table-responsive::-webkit-scrollbar {
            height: 6px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        #report-table {
            min-width: 800px;
            width: 100%;
        }

        #report-table th {
            background-color: var(--light-bg);
            font-weight: 600;
            border-bottom: 2px solid #e9ecef;
        }

        #report-table th,
        #report-table td {
            white-space: nowrap;
            vertical-align: middle;
            padding: 0.75rem 1rem;
        }

        /* ========== RESPONSIVE STYLES ========== */
        /* Mobile styles */
        @media (max-width: 576px) {
            .card-body {
                padding: 15px;
            }

            .filter-section {
                padding: 15px;
            }

            .btn-group {
                flex-direction: column;
                width: 100%;
            }

            .btn {
                width: 100%;
                margin-bottom: 6px;
            }

            #report-table {
                font-size: 14px;
            }

            .table-scroll-indicator {
                display: block;
            }

            .d-flex.flex-wrap.gap-2 {
                justify-content: space-between;
                margin-top: 10px;
            }

            .d-flex.flex-wrap.gap-2 .btn {
                flex: 1 1 calc(50% - 5px);
                margin-bottom: 5px;
                padding: 8px;
                min-width: calc(50% - 5px);
            }

            .table-responsive {
                margin: 0 -15px;
            }
        }

        /* Tablet styles */
        @media (max-width: 768px) {
            .btn-group {
                width: 100%;
                margin-top: 10px;
            }

            .btn-group .btn {
                flex: 1;
                white-space: nowrap;
                padding: 8px;
                font-size: 14px;
            }

            .table-responsive {
                margin: 0 -15px;
            }
        }

        @media (min-width: 577px) and (max-width: 991px) {
            .table-scroll-indicator {
                display: block;
            }
        }

        /* Desktop styles */
        @media (min-width: 768px) {
            .modal-dialog {
                max-width: 700px;
            }
        }
    </style>
@endsection

@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">History Sales Report</h5>
                            <span class="text-muted current-date-info" id="date-filter-info">
                                <strong>Pilih tanggal</strong> untuk melihat data
                            </span>
                        </div>
                        <div id="report-summary" class="text-muted small">
                            <span id="total-records">0</span> records found
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="filter-section">
                        <form id="reportForm" onsubmit="return false;">
                            <div class="row g-3">
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control date-input" id="start_date" name="start_date">
                                    <div class="invalid-feedback" id="start_date_error">Mohon pilih tanggal mulai</div>
                                    <small class="help-text">First day of data range</small>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label">End Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control date-input" id="end_date" name="end_date">
                                    <div class="invalid-feedback" id="end_date_error">Mohon pilih tanggal akhir</div>
                                    <small class="help-text">Last day of data range</small>
                                </div>
                                <div class="col-lg-2 col-md-6">
                                    <div class="entries-select-container">
                                        <label class="form-label">Show Entries</label>
                                        <select class="form-select" id="pageLength">
                                            <option value="10">10 entries</option>
                                            <option value="25" selected>25 entries</option>
                                            <option value="50">50 entries</option>
                                            <option value="100">100 entries</option>
                                            <option value="-1">All entries</option>
                                        </select>
                                        <small class="help-text">Records per page</small>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <label class="form-label d-none d-md-block">&nbsp;</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        <button type="button" class="btn btn-primary" id="filterBtn">
                                            <i class="fas fa-filter"></i>
                                            <span class="d-none d-sm-inline">Filter</span>
                                        </button>
                                        <button type="button" class="btn btn-success" id="exportBtn">
                                            <i class="fas fa-file-excel"></i>
                                            <span class="d-none d-sm-inline">Export Filtered</span>
                                        </button>
                                        <button type="button" class="btn btn-success" id="exportAllBtn"
                                            title="Export all data in the database">
                                            <i class="fas fa-download"></i>
                                            <span class="d-none d-sm-inline">Export All</span>
                                        </button>
                                        <button type="button" class="btn btn-danger" id="clearBtn">
                                            <i class="fas fa-times"></i>
                                            <span class="d-none d-sm-inline">Clear</span>
                                        </button>
                                    </div>
                                    <small class="help-text d-md-none">Apply filters or export data</small>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Export Status Indicator -->
                    <div class="export-status" id="exportStatus">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <span id="exportStatusText">Preparing data for export...</span>
                    </div>

                    <!-- Table Scroll Indicator -->
                    <div class="table-scroll-indicator">
                        <i class="fas fa-arrows-left-right"></i> Geser kanan-kiri untuk melihat data lengkap
                    </div>

                    <!-- Data Table -->
                    <div class="table-responsive position-relative">
                        <div class="loading-overlay" id="tableLoading" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <table id="report-table" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>No Resi</th>
                                    <th>SKU</th>
                                    <th style="width: 80px;">Qty</th>
                                    <th style="width: 150px;">Created At</th>
                                    <th style="width: 150px;">Updated At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Table data will be populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
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
    <script src="{{ URL::asset('build/js/plugins/dataTables.buttons.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/pdfmake.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/vfs_fonts.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/buttons.html5.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/buttons.print.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/buttons.colVis.min.js') }}"></script>

    <!-- SheetJS library for Excel export -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <!-- Date picker enhancement -->
    <script src="{{ URL::asset('build/js/plugins/flatpickr.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Cache frequently used selectors
            const $startDate = $('#start_date');
            const $endDate = $('#end_date');
            const $pageLength = $('#pageLength');
            const $filterBtn = $('#filterBtn');
            const $exportBtn = $('#exportBtn');
            const $clearBtn = $('#clearBtn');
            const $tableLoading = $('#tableLoading');
            const $exportStatus = $('#exportStatus');
            const $totalRecords = $('#total-records');

            // Initialize DataTable with optimized settings
            const table = $('#report-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                dom: 'Bfrtip',
                deferRender: true,
                buttons: [{
                        extend: 'copy',
                        text: '<i class="fas fa-copy"></i> Salin',
                        className: 'btn btn-secondary',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-success',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn btn-danger',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Cetak',
                        className: 'btn btn-info',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    }
                ],
                ajax: {
                    url: "{{ route('history-sales.data') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}";
                        d.start_date = $startDate.val();
                        d.end_date = $endDate.val();
                    },
                    beforeSend: function() {
                        $tableLoading.show();
                    },
                    complete: function() {
                        $tableLoading.hide();

                        // Update record count
                        const info = table.page.info();
                        $totalRecords.text(info.recordsTotal);

                        // Update date filter info when data is loaded
                        updateDateFilterInfo($startDate.val(), $endDate.val());
                    }
                },
                columns: [{
                        data: 'no',
                        name: 'no'
                    },
                    {
                        data: 'no_resi',
                        name: 'no_resi'
                    },
                    {
                        data: 'no_sku',
                        name: 'no_sku',
                        className: 'align-top'
                    },
                    {
                        data: 'qty',
                        name: 'qty',
                        className: 'align-top'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at'
                    }
                ],
                order: [
                    [4, 'desc']
                ],
                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    ['10', '25', '50', '100', 'All']
                ],
                language: {
                    lengthMenu: "Tampilkan _MENU_ entri per halaman",
                    zeroRecords: "Tidak ada data yang ditemukan",
                    info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                    infoEmpty: "Tidak ada data tersedia",
                    infoFiltered: "(difilter dari _MAX_ total entri)",
                    search: "Cari:",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    },
                    loadingRecords: "Memuat...",
                    processing: "Memproses...",
                },
                drawCallback: function() {
                    // Additional functionality after table is drawn
                    if ($(window).width() < 768) {
                        $('.table-scroll-indicator').show();
                    }
                }
            });

            // Add these variables at the top of your document.ready function
            let isInitialLoad = true;
            let isFilterAttempted = false;

            // Date validation with improved error handling
            function validateDates() {
                // Only validate if user has clicked filter/export - not on initial load
                if (!isInitialLoad) {
                    if (!$startDate.val() || !$endDate.val()) {
                        if ($startDate.val() && !$endDate.val()) {
                            // Only end date is missing
                            $endDate.addClass('is-invalid');
                            $('#end_date_error').show();
                            showError('Mohon pilih tanggal akhir untuk melengkapi filter');
                        } else if (!$startDate.val() && $endDate.val()) {
                            // Only start date is missing
                            $startDate.addClass('is-invalid');
                            $('#start_date_error').show();
                            showError('Mohon pilih tanggal mulai untuk melengkapi filter');
                        } else {
                            // Both dates are missing
                            if (isFilterAttempted) {
                                $startDate.addClass('is-invalid');
                                $endDate.addClass('is-invalid');
                                $('#start_date_error').show();
                                $('#end_date_error').show();
                                showError('Mohon isi tanggal mulai dan tanggal akhir');
                            }
                        }
                        return false;
                    }

                    const startDate = new Date($startDate.val());
                    const endDate = new Date($endDate.val());
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    // Reset validation states
                    $('.date-input').removeClass('is-invalid');
                    $('.invalid-feedback').hide();

                    // Validate date range
                    if (startDate > today) {
                        $startDate.addClass('is-invalid');
                        $('#start_date_error').text('Tanggal mulai tidak boleh lebih dari hari ini').show();
                        showError('Tanggal mulai tidak boleh lebih dari hari ini');
                        return false;
                    }

                    // Menghapus validasi endDate > today agar bisa memfilter data hingga hari ini
                    // Komentar: Validasi ini dihapus karena menyebabkan masalah saat pengguna ingin melihat
                    // data hingga hari ini (misalnya rentang 14-21 pada tanggal 21)

                    if (startDate > endDate) {
                        $endDate.addClass('is-invalid');
                        $('#end_date_error').text('Tanggal akhir harus setelah tanggal mulai').show();
                        showError('Tanggal akhir harus setelah tanggal mulai');
                        return false;
                    }

                    const diffTime = Math.abs(endDate - startDate);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    if (diffDays > 365) {
                        $endDate.addClass('is-invalid');
                        $('#end_date_error').text('Rentang tanggal tidak boleh lebih dari 1 tahun').show();
                        showError('Rentang tanggal tidak boleh lebih dari 1 tahun');
                        return false;
                    }
                }

                return true;
            }

            // Improved error messaging with SweetAlert
            function showError(message) {
                Swal.fire({
                    icon: 'error',
                    title: 'Perhatian',
                    text: message,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            }

            // Success notification
            function showSuccess(message) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: message,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            }

            // Event Handlers with debouncing/throttling for better performance

            // Reset validation on input
            $('.date-input').on('change', function() {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').hide();
            });

            // Filter button with validation
            $filterBtn.on('click', function() {
                isFilterAttempted = true;
                if (validateDates()) {
                    table.ajax.reload();
                    updateDateFilterInfo($startDate.val(), $endDate.val());
                    showSuccess('Data berhasil difilter');
                }
                isInitialLoad = false;
            });

            // Export button with validation and enhanced UX
            $exportBtn.on('click', function() {
                isFilterAttempted = true;
                if (validateDates()) {
                    const recordsTotal = table.page.info().recordsTotal;

                    if (recordsTotal === 0) {
                        showError('Tidak ada data untuk diekspor. Silakan ubah kriteria filter Anda.');
                        return;
                    }

                    // Update export status
                    $exportStatus.show();
                    $('#exportStatusText').text('Mempersiapkan data untuk ekspor...');

                    // Perform the AJAX request to get data
                    $.ajax({
                        url: "{{ route('history-sales.export') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            start_date: $startDate.val(),
                            end_date: $endDate.val()
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                // Show row count in status
                                $('#exportStatusText').text(
                                    `Mengekspor ${response.count} baris data...`);

                                // Export data to Excel
                                setTimeout(() => {
                                    exportToExcel(response.data, 'Laporan_Penjualan_' +
                                        formatDateForFileName($startDate.val()) +
                                        '_to_' + formatDateForFileName($endDate
                                            .val()));
                                    showSuccess(
                                        `${response.count} data berhasil diekspor ke Excel dengan format satu baris per No Resi dan nilai SKU/Quantity yang dipisahkan dengan koma`
                                    );
                                    $exportStatus.hide();
                                }, 500);
                            } else {
                                showError(response.message ||
                                    'Terjadi kesalahan saat mengekspor data');
                                $exportStatus.hide();
                            }
                        },
                        error: function(xhr) {
                            $exportStatus.hide();
                            showError('Terjadi kesalahan: ' + (xhr.responseJSON && xhr
                                .responseJSON.message ? xhr.responseJSON.message :
                                'Tidak dapat menghubungi server'));
                        }
                    });
                }
                isInitialLoad = false;
            });

            // Helper function to format date for filename
            function formatDateForFileName(dateString) {
                if (!dateString) return 'all';
                return dateString.replace(/-/g, '');
            }

            // Function to export data to Excel
            function exportToExcel(data, fileName) {
                // Create a new workbook
                const wb = XLSX.utils.book_new();

                // Convert the data to a worksheet
                const ws = XLSX.utils.json_to_sheet(data);

                // Set column widths for better readability - increase the width for SKU and Quantity columns
                const colWidths = [{
                        wch: 8
                    }, // ID
                    {
                        wch: 25
                    }, // No Resi
                    {
                        wch: 60
                    }, // SKU - significantly increased width for multiple comma-separated SKUs
                    {
                        wch: 25
                    }, // Jumlah - increased width for comma-separated quantities
                    {
                        wch: 20
                    }, // Dibuat Pada
                    {
                        wch: 20
                    } // Diperbarui Pada
                ];

                ws['!cols'] = colWidths;

                // Configure cell styles to ensure text wrapping for long content
                if (!ws['!rows']) ws['!rows'] = [];

                // Process each cell to ensure proper text wrapping and formatting
                for (let i = 0; i < data.length; i++) {
                    const rowIndex = i + 1; // +1 to skip header
                    const skuValue = data[i]['SKU'] || '';
                    const qtyValue = data[i]['Jumlah'] || '';

                    // Estimate minimum needed height based on text length and typical width
                    const approximateLines = Math.max(
                        Math.ceil(skuValue.length / 40), // Estimate line breaks based on characters
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
                XLSX.utils.book_append_sheet(wb, ws, 'Laporan Penjualan');

                // Generate Excel file and trigger download
                XLSX.writeFile(wb, fileName + '.xlsx');
            }

            // Clear button with confirmation for better UX
            $clearBtn.on('click', function() {
                $startDate.val('');
                $endDate.val('');
                $('.date-input').removeClass('is-invalid');
                $('.invalid-feedback').hide();
                $pageLength.val(25);
                table.page.len(25).draw();
                table.ajax.reload();
                updateDateFilterInfo('', '');
                showSuccess('Filter telah dihapus');
                isFilterAttempted = false;
            });

            // Page length selector
            $pageLength.on('change', function() {
                const length = parseInt($(this).val());
                table.page.len(length).draw();
            });

            // Responsive behavior for table scroll indicator
            $('.table-responsive').on('scroll', function() {
                $('.table-scroll-indicator').fadeOut();
            });

            // Window resize handler
            $(window).on('resize', function() {
                if ($(window).width() < 768) {
                    $('.table-scroll-indicator').show();
                } else {
                    $('.table-scroll-indicator').hide();
                }
            });

            // Initialize with a data load but without validation
            if (isInitialLoad) {
                // Set default date to today
                const today = new Date();
                const yyyy = today.getFullYear();
                const mm = String(today.getMonth() + 1).padStart(2, '0');
                const dd = String(today.getDate()).padStart(2, '0');
                const formattedDate = `${yyyy}-${mm}-${dd}`;

                // Set both dates to today as default
                $startDate.val(formattedDate);
                $endDate.val(formattedDate);

                // Load data with today's filter
                table.ajax.reload();

                // Update date filter info
                updateDateFilterInfo(formattedDate, formattedDate);
            }

            // Export All button with confirmation
            $('#exportAllBtn').on('click', function() {
                Swal.fire({
                    title: 'Ekspor Semua Data',
                    html: `<div class="text-left">
                            <p>Anda akan mengekspor <strong>SEMUA DATA</strong> dari database.</p>
                            <p>Proses ini akan:</p>
                            <ul>
                                <li>Menyertakan semua data riwayat penjualan</li>
                                <li>Mengabaikan filter tanggal yang sedang diterapkan</li>
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
                        $exportStatus.show();
                        $('#exportStatusText').text('Mempersiapkan semua data untuk ekspor...');

                        // Disable buttons during export
                        const $buttons = $('#filterBtn, #exportBtn, #exportAllBtn, #clearBtn');
                        $buttons.prop('disabled', true);

                        // Perform the AJAX request to get all data
                        $.ajax({
                            url: "{{ route('history-sales.export') }}",
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
                                            'Laporan_Penjualan_Lengkap_' +
                                            getCurrentDate());
                                        showSuccess(
                                            `${response.count} data berhasil diekspor ke Excel dengan format satu baris per No Resi dan nilai SKU/Quantity yang dipisahkan dengan koma`
                                        );
                                        $exportStatus.hide();
                                        $buttons.prop('disabled', false);
                                    }, 500);
                                } else {
                                    showError(response.message ||
                                        'Terjadi kesalahan saat mengekspor data');
                                    $exportStatus.hide();
                                    $buttons.prop('disabled', false);
                                }
                            },
                            error: function(xhr) {
                                $exportStatus.hide();
                                $buttons.prop('disabled', false);
                                showError('Terjadi kesalahan: ' + (xhr.responseJSON &&
                                    xhr.responseJSON.message ? xhr.responseJSON
                                    .message : 'Tidak dapat menghubungi server'
                                ));
                            }
                        });
                    }
                });
            });

            // Helper function to get current date for filename
            function getCurrentDate() {
                const now = new Date();
                const year = now.getFullYear();
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const day = String(now.getDate()).padStart(2, '0');
                return `${year}${month}${day}`;
            }

            // Function to update the date filter info display
            function updateDateFilterInfo(startDate, endDate) {
                const $dateFilterInfo = $('#date-filter-info');

                if (!startDate || !endDate) {
                    $dateFilterInfo.html('<strong>Pilih tanggal</strong> untuk melihat data');
                    return;
                }

                // Format the dates to Indonesian format
                const formatDate = (dateString) => {
                    const date = new Date(dateString);
                    const options = {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric',
                        timeZone: 'Asia/Jakarta'
                    };
                    return date.toLocaleDateString('id-ID', options);
                };

                const formattedStartDate = formatDate(startDate);
                const formattedEndDate = formatDate(endDate);

                $dateFilterInfo.html(
                    `Menampilkan data: <strong>${formattedStartDate}</strong> s/d <strong>${formattedEndDate}</strong>`
                );
            }
        });
    </script>
@endsection
