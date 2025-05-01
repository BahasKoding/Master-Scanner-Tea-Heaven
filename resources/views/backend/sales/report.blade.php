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
        /* Base styles with reduced redundancy */
        :root {
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 2px 8px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 4px 12px rgba(0, 0, 0, 0.12);
            --border-color: #ced4da;
            --bg-light: #f8f9fa;
            --primary-color: #80bdff;
            --text-muted: #6c757d;
            --text-dark: #344767;
            --transition-fast: all 0.2s ease;
        }

        /* Consolidated and simplified filter section */
        .filter-section {
            background-color: var(--bg-light);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: var(--shadow-md);
            transition: var(--transition-fast);
        }

        .filter-section:hover {
            box-shadow: var(--shadow-lg);
        }

        /* Simplified form elements */
        .form-select,
        .form-control,
        .date-input {
            height: 38px;
            border: 1px solid var(--border-color);
            transition: var(--transition-fast);
        }

        .form-select:focus,
        .form-control:focus,
        .date-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        /* Simplified entries selector */
        .entries-select-container select {
            width: 100%;
            padding: 8px 12px;
            border-radius: 4px;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-position: right 0.75rem center;
            background-size: 16px 12px;
            background-repeat: no-repeat;
        }

        /* Labels and text - simplified */
        .form-label,
        .entries-select-container label {
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 8px;
            font-size: 0.875rem;
        }

        .help-text {
            margin-top: 5px;
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        /* Simplified button styles */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            min-height: 38px;
            transition: var(--transition-fast);
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

        /* Table styles - simplified */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 8px;
            box-shadow: var(--shadow-md);
        }

        #report-table th {
            background-color: var(--bg-light);
            font-weight: 600;
            border-bottom: 2px solid #e9ecef;
        }

        #report-table th,
        #report-table td {
            white-space: nowrap;
            vertical-align: middle;
            padding: 0.75rem 1rem;
        }

        /* Loading and status indicators */
        .loading-overlay {
            position: absolute;
            inset: 0;
            /* Shorthand for top/right/bottom/left */
            background: rgba(255, 255, 255, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            border-radius: 8px;
        }

        .export-status {
            display: none;
            align-items: center;
            justify-content: center;
            padding: 10px;
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .export-status .spinner-border {
            width: 1rem;
            height: 1rem;
            margin-right: 8px;
        }

        /* Card styling - simplified */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #f1f1f1;
            padding: 1.25rem 1.5rem;
        }

        .card-body {
            padding: 1.5rem;
            position: relative;
        }

        /* Responsive styles - combined and simplified */
        @media (max-width: 768px) {
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

            .card-body {
                padding: 15px;
            }

            #report-table {
                font-size: 14px;
            }

            .table-scroll-indicator {
                display: block;
            }
        }

        @media (max-width: 576px) {
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
    </style>
@endsection

@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">History Sales Report</h5>
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
                                    <input type="date" class="form-control date-input" id="start_date" name="start_date"
                                        max="{{ date('Y-m-d') }}">
                                    <div class="invalid-feedback" id="start_date_error">Mohon pilih tanggal mulai</div>
                                    <small class="help-text">First day of data range</small>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label">End Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control date-input" id="end_date" name="end_date"
                                        max="{{ date('Y-m-d') }}">
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
                                        <button type="button" class="btn btn-info" id="exportAllBtn"
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
                        <i class="fas fa-arrows-left-right"></i> Scroll horizontally to view more data
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
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i> Export Excel',
                    className: 'btn btn-success d-none', // Hidden button (we'll use our custom one)
                    title: function() {
                        const startDate = $startDate.val() || 'All';
                        const endDate = $endDate.val() || 'All';
                        return `History Sales Report (${startDate} to ${endDate})`;
                    },
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5],
                        modifier: {
                            search: 'applied',
                            order: 'applied',
                            page: 'all'
                        },
                        format: {
                            body: function(data, row, column, node) {
                                // Clean data for export
                                if (column === 2 || column === 3) {
                                    if (typeof data === 'string') {
                                        let cleanData = data
                                            .replace(/<br\s*\/?>/gi, ', ')
                                            .replace(/<[^>]*>/g, '')
                                            .replace(/\s*,\s*/g, ', ')
                                            .replace(/,\s*$/, '')
                                            .replace(/\u200B/g, '');
                                        return cleanData;
                                    }
                                }
                                return data;
                            },
                            header: function(data, columnIdx) {
                                if (typeof data === 'string') {
                                    return data.replace(/<[^>]*>/g, '');
                                }
                                return data;
                            }
                        }
                    },
                    action: function(e, dt, button, config) {
                        // Progress indication for large exports
                        const recordsTotal = dt.page.info().recordsTotal;
                        if (recordsTotal > 100) {
                            const percentStep = Math.ceil(recordsTotal / 10);
                            let currentProgress = 0;

                            $exportStatus.show();

                            // Update progress message periodically
                            const progressInterval = setInterval(() => {
                                currentProgress += percentStep;
                                if (currentProgress >= recordsTotal) {
                                    clearInterval(progressInterval);
                                    $exportStatusText.text('Finalizing export...');
                                } else {
                                    const percent = Math.min(Math.round((
                                            currentProgress / recordsTotal) *
                                        100), 95);
                                    $('#exportStatusText').text(
                                        `Preparing data: ${percent}% complete...`);
                                }
                            }, 300);

                            // Proceed with export
                            $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt,
                                button, config);

                            // Hide status after a delay
                            setTimeout(() => {
                                clearInterval(progressInterval);
                                $exportStatus.hide();
                            }, 1500);
                        } else {
                            // For smaller datasets, just do the export
                            $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt,
                                button, config);
                        }
                    },
                    customize: function(xlsx) {
                        const sheet = xlsx.xl.worksheets['sheet1.xml'];

                        // Set optimal column widths
                        const widths = [4, 15, 35, 10, 18, 18];

                        // Find or create cols element
                        let cols = $('cols', sheet);
                        if (cols.length === 0) {
                            const colsElement = $.parseXML('<cols></cols>').documentElement;
                            $('worksheet', sheet).prepend(colsElement);
                            cols = $('cols', sheet);
                        }

                        // Clear existing col elements
                        cols.empty();

                        // Add new col elements with specified widths
                        widths.forEach((width, i) => {
                            cols.append(
                                `<col min="${i + 1}" max="${i + 1}" width="${width}" customWidth="1"/>`
                            );
                        });

                        // Process each row to clean data and apply formatting
                        $('row', sheet).each(function(index) {
                            // Skip header row
                            if (index === 0) return;

                            const cells = $('c', this);

                            // Process all cells to ensure clean data
                            $(cells).each(function() {
                                const textNode = $('t', this);
                                if (textNode.length) {
                                    const text = textNode.text();
                                    if (text && text.includes('<')) {
                                        // If any HTML tag is found, clean it
                                        const cleanText = text
                                            .replace(/<br\s*\/?>/gi, ', ')
                                            .replace(/<[^>]*>/g, '')
                                            .replace(/\s*,\s*/g, ', ')
                                            .replace(/,\s*$/, '')
                                            .replace(/\u200B/g, '');
                                        textNode.text(cleanText);
                                    }
                                }

                                // Apply styling
                                $(this).attr('s', '55');
                            });

                            // Set compact row height
                            $(this).attr('ht', '22');
                            $(this).attr('customHeight', '1');
                        });

                        // Add custom style with improved readability
                        const styleSheet = xlsx.xl['styles.xml'];
                        const styles = $('styleSheet', styleSheet);

                        // Check if style 55 already exists
                        let cellXfs = $('cellXfs', styles);
                        if (cellXfs.length === 0) {
                            styles.append(
                                '<cellXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0" applyAlignment="1"><alignment vertical="center" horizontal="left" wrapText="1"/></xf></cellXfs>'
                            );
                        } else {
                            // Add new style for wrap text
                            const xfCount = parseInt(cellXfs.attr('count'));
                            cellXfs.attr('count', xfCount + 1);
                            cellXfs.append(
                                '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0" applyAlignment="1"><alignment vertical="center" horizontal="left" wrapText="1"/></xf>'
                            );
                        }
                    }
                }],
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
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Tidak ada data yang ditemukan",
                    info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                    infoEmpty: "Tidak ada data yang tersedia",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    search: "Cari:",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
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

                    if (endDate > today) {
                        $endDate.addClass('is-invalid');
                        $('#end_date_error').text('Tanggal akhir tidak boleh lebih dari hari ini').show();
                        showError('Tanggal akhir tidak boleh lebih dari hari ini');
                        return false;
                    }

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

                    // Confirm export for large datasets
                    if (recordsTotal > 1000) {
                        Swal.fire({
                            title: 'Ekspor Data Besar',
                            html: `Anda akan mengekspor <strong>${recordsTotal}</strong> data. Ini mungkin memerlukan waktu dan bisa memperlambat browser Anda.<br><br>Apakah Anda ingin melanjutkan?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, ekspor semua',
                            cancelButtonText: 'Batal',
                            confirmButtonColor: '#28a745'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                performExport();
                            }
                        });
                    } else {
                        performExport();
                    }
                }
                isInitialLoad = false;
            });

            // Optimized export function
            function performExport() {
                // Show export status indicator for better UX
                $exportStatus.show();
                $('#exportStatusText').text('Preparing data for export...');

                // Use setTimeout to allow UI to update before processing
                setTimeout(() => {
                    // Set all records to be exported
                    const currentPageLen = table.page.len();
                    table.page.len(-1).draw('page');

                    // Trigger the export
                    setTimeout(() => {
                        table.button('.buttons-excel').trigger();

                        // Reset pagination after a delay
                        setTimeout(() => {
                            table.page.len(currentPageLen).draw('page');
                            $exportStatus.hide();

                            showSuccess('Export completed successfully');
                        }, 1000);
                    }, 100);
                }, 100);
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
                table.ajax.reload();
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
                    confirmButtonColor: '#17a2b8'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show export status indicator
                        $exportStatus.show();
                        $('#exportStatusText').text('Preparing all data for export...');

                        // Clear date filters temporarily for this export
                        const currentStartDate = $startDate.val();
                        const currentEndDate = $endDate.val();

                        // Clear filters for the AJAX request
                        $startDate.val('');
                        $endDate.val('');

                        // Set a timeout to avoid UI blocking and show loading state
                        setTimeout(() => {
                            try {
                                // Disable all buttons during export
                                const $buttons = $(
                                    '#filterBtn, #exportBtn, #exportAllBtn, #clearBtn');
                                $buttons.prop('disabled', true);

                                // Update DataTable with no filters
                                table.ajax.reload(function() {
                                    // After data loaded without filters, export with retry mechanism
                                    setTimeout(() => {
                                        try {
                                            // Set page length to all for export
                                            table.page.len(-1).draw('page');

                                            setTimeout(() => {
                                                try {
                                                    // Trigger export
                                                    table.button(
                                                            '.buttons-excel'
                                                        )
                                                        .trigger();

                                                    // Restore filters and pagination after export
                                                    setTimeout(
                                                        () => {
                                                            try {
                                                                // Restore date filters
                                                                $startDate
                                                                    .val(
                                                                        currentStartDate
                                                                    );
                                                                $endDate
                                                                    .val(
                                                                        currentEndDate
                                                                    );

                                                                // Re-enable buttons
                                                                $buttons
                                                                    .prop(
                                                                        'disabled',
                                                                        false
                                                                    );

                                                                // Restore table state
                                                                table
                                                                    .page
                                                                    .len(
                                                                        parseInt(
                                                                            $pageLength
                                                                            .val()
                                                                        )
                                                                    )
                                                                    .draw(
                                                                        'page'
                                                                    );
                                                                $exportStatus
                                                                    .hide();

                                                                // Reload with original filters to restore view
                                                                table
                                                                    .ajax
                                                                    .reload();

                                                                showSuccess
                                                                    (
                                                                        'All data exported successfully'
                                                                    );
                                                            } catch (
                                                                e
                                                            ) {
                                                                handleExportError
                                                                    (e, $buttons,
                                                                        currentStartDate,
                                                                        currentEndDate
                                                                    );
                                                            }
                                                        }, 1500);
                                                } catch (e) {
                                                    handleExportError
                                                        (e, $buttons,
                                                            currentStartDate,
                                                            currentEndDate
                                                        );
                                                }
                                            }, 500);
                                        } catch (e) {
                                            handleExportError(e, $buttons,
                                                currentStartDate,
                                                currentEndDate);
                                        }
                                    }, 500);
                                });
                            } catch (e) {
                                handleExportError(e, $buttons, currentStartDate,
                                    currentEndDate);
                            }
                        }, 100);
                    }
                });
            });

            // Add a helper function to handle export errors
            function handleExportError(error, $buttons, originalStartDate, originalEndDate) {
                console.error('Export error:', error);

                // Re-enable all buttons
                $buttons.prop('disabled', false);

                // Restore original date values
                $startDate.val(originalStartDate);
                $endDate.val(originalEndDate);

                // Hide export status
                $exportStatus.hide();

                // Show error message
                Swal.fire({
                    icon: 'error',
                    title: 'Export Failed',
                    text: 'There was an error exporting the data. Please try again with a smaller date range or contact support.',
                    confirmButtonColor: '#dc3545'
                });

                // Reload table to restore view
                table.ajax.reload();
            }
        });
    </script>
@endsection
