@extends('layouts.main')

@section('title', 'History Sales')
@section('breadcrumb-item', $item)

@section('css')
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Ensure proper mobile viewport -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <!-- [Page specific CSS] start -->
    <!-- data tables css -->
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/datatables/dataTables.bootstrap5.min.css') }}">
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
        }

        /* ========== UTILITY CLASSES ========== */
        .transition {
            transition: var(--transition-normal);
        }

        /* ========== COMMON ELEMENTS ========== */
        .card {
            border: none;
            box-shadow: var(--card-shadow);
            margin-bottom: 30px;
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid var(--border-color);
            padding: 15px;
        }

        .card-header .d-flex {
            flex-wrap: wrap;
            gap: 10px;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
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
            transition: var(--transition-normal);
        }

        /* Button icon styling */
        .btn-icon {
            width: 36px;
            height: 36px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 3px;
            transition: all 0.2s ease;
        }

        .btn-icon i {
            font-size: 1rem;
        }

        .btn-icon:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 5px rgba(0, 0, 0, 0.2);
        }

        /* ========== FORM ELEMENTS ========== */
        input[type="text"],
        input[type="number"] {
            min-height: var(--form-element-height);
            transition: var(--transition-normal);
        }

        .scanner-active {
            border: 2px solid var(--primary-color) !important;
            box-shadow: 0 0 5px var(--primary-color);
        }

        .scanner-input:focus,
        .sku-input:focus,
        .qty-input:focus {
            border-color: var(--primary-color);
            box-shadow: var(--focus-shadow);
            outline: none;
        }

        .scanner-input {
            height: 45px;
        }

        .sku-input,
        .qty-input {
            height: var(--form-element-height);
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-normal);
            font-size: 16px;
        }

        .sku-input-container {
            display: grid;
            gap: 10px;
            margin-bottom: 15px;
            padding: 15px;
            background-color: var(--light-bg);
            border-radius: var(--border-radius-large);
            border: 1px solid var(--border-color);
        }

        .qty-input {
            text-align: center;
            font-weight: bold;
        }

        /* ========== INDICATORS & HELPERS ========== */
        .scanning-indicator {
            display: none;
            color: var(--primary-color);
            margin-left: 10px;
            text-align: center;
        }

        .scanning-indicator.active {
            display: inline-block;
        }

        .countdown-timer {
            color: var(--secondary-color);
            font-size: 1em;
            margin-left: 10px;
            background-color: var(--light-bg);
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 500;
        }

        .table-scroll-indicator {
            display: none;
            margin-bottom: 10px;
        }

        .current-date-info {
            display: block;
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .current-date-info strong {
            color: var(--primary-color);
        }

        /* ========== SECTIONS & CONTAINERS ========== */
        .scanner-section {
            background-color: #fff;
            padding: 20px;
            border-radius: var(--border-radius-large);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }

        .form-section {
            background-color: var(--light-bg);
            padding: 20px;
            border-radius: var(--border-radius-normal);
            margin-bottom: 20px;
        }

        .sku-form-wrapper {
            max-width: 100%;
            overflow-x: hidden;
        }

        /* ========== TABLE STYLES ========== */
        .table-wrapper {
            position: relative;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin: 0 -15px;
            padding: 0 15px;
            width: calc(100% + 30px);
        }

        .table-wrapper::-webkit-scrollbar {
            height: 6px;
        }

        .table-wrapper::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .table-wrapper::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        #history-sales-table {
            min-width: 800px;
            width: 100%;
        }

        #history-sales-table th,
        #history-sales-table td {
            white-space: nowrap;
            vertical-align: middle;
            padding: 12px 8px;
        }

        /* Action buttons in table */
        .action-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .action-buttons .btn {
            flex: 1;
            min-width: 80px;
            margin: 2px;
            padding: 6px 12px;
            white-space: nowrap;
        }

        /* ========== MODAL STYLES ========== */
        .modal-dialog {
            max-width: 95%;
            margin: 20px auto;
        }

        /* ========== RESPONSIVE STYLES ========== */
        /* Mobile styles */
        @media (max-width: 576px) {
            .card-body {
                padding: 15px;
            }

            .sku-input-container {
                grid-template-columns: 1fr;
            }

            .sku-input,
            .qty-input {
                width: 100%;
            }

            .scanning-indicator {
                width: 100%;
                text-align: center;
                margin: 5px 0;
            }

            #resetScannerBtn {
                width: 100%;
                margin-top: 15px;
            }

            #history-sales-table {
                font-size: 14px;
            }

            #history-sales-table td:nth-child(3),
            #history-sales-table td:nth-child(4) {
                max-width: 150px;
                white-space: normal;
                word-break: break-word;
            }

            .table-scroll-indicator {
                display: block;
                text-align: center;
                padding: 10px;
                color: #666;
                font-size: 0.9em;
            }

            .btn-icon {
                width: 32px;
                height: 32px;
            }

            .btn-icon i {
                font-size: 0.875rem;
            }

            #history-sales-table .btn {
                display: inline-flex;
                width: auto;
                margin: 0 3px;
            }

            .d-flex.justify-content-center {
                display: flex !important;
                justify-content: center !important;
                gap: 8px;
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

            .modal-dialog {
                margin: 10px;
                padding: 0;
            }

            .modal-content {
                border-radius: 10px;
            }

            .modal-body {
                padding: 15px;
            }
        }

        @media (min-width: 577px) and (max-width: 991px) {
            .sku-input-container {
                grid-template-columns: 2fr 1fr;
            }

            .scanning-indicator {
                grid-column: span 2;
                text-align: center;
            }

            #history-sales-table td:nth-child(3),
            #history-sales-table td:nth-child(4) {
                max-width: 200px;
                white-space: normal;
                word-break: break-word;
            }

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

        @media (min-width: 992px) {
            .sku-input-container {
                grid-template-columns: 1fr 120px 120px;
            }

            .scanning-indicator {
                text-align: center;
            }
        }
    </style>
@endsection

@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">
        <!-- Scanner Form start -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="mb-2 mb-md-0">
                            <h5 class="mb-0">Scan Barcode</h5>
                            <small class="text-muted">Scan No Resi first, then scan SKU(s)</small>
                        </div>
                        <div class="countdown-timer" id="autoSubmitTimer" style="display: none;">
                            <i class="fas fa-clock me-1"></i> Auto-submitting in <span id="submitCountdown">10</span>s
                        </div>
                    </div>
                </div>
                <div class="card-body scanner-section">
                    <form id="historySaleForm" method="POST">
                        @csrf
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="mb-4">
                                    <label for="no_resi" class="form-label">
                                        <i class="fas fa-barcode me-1"></i> No Resi
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="text" class="form-control scanner-input scanner-active"
                                            id="no_resi" name="no_resi" required autofocus placeholder="Scan No Resi...">
                                        <span class="scanning-indicator" id="resiScanningIndicator">
                                            <i class="fas fa-circle-notch fa-spin"></i> Scanning...
                                        </span>
                                    </div>
                                    <div class="error-feedback" id="resiError"></div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">
                                        <i class="fas fa-boxes me-1"></i> No SKU & Quantity
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div id="sku-container">
                                        <div class="sku-input-container">
                                            <input type="text" class="form-control scanner-input sku-input"
                                                name="no_sku[]" required disabled placeholder="Scan SKU..."
                                                style="min-width: 300px;">
                                            <input type="number" class="form-control qty-input" name="qty[]"
                                                value="1" min="1">
                                            <span class="scanning-indicator" id="skuScanningIndicator">
                                                <i class="fas fa-circle-notch fa-spin"></i> Scanning...
                                            </span>
                                        </div>
                                    </div>
                                    <div class="error-feedback" id="skuError"></div>
                                </div>

                                <div class="text-center mt-4">
                                    <button type="button" id="resetScannerBtn" class="btn btn-warning btn-lg">
                                        <i class="fas fa-redo me-1"></i> Reset Scanner
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Scanner Form end -->

        <!-- History Sales Table start -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h5 class="mb-0">History Sales List</h5>
                            <span class="text-muted current-date-info">Menampilkan data:
                                <strong>{{ date('d F Y') }}</strong></span>
                        </div>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary active" id="showActive">
                                <i class="fas fa-list me-1 d-none d-sm-inline"></i>Data Aktif
                            </button>
                            <button type="button" class="btn btn-outline-warning" id="showArchived">
                                <i class="fas fa-archive me-1 d-none d-sm-inline"></i>Data Terarsip
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-scroll-indicator">
                        <i class="fas fa-arrows-left-right"></i> Geser kanan-kiri untuk melihat data lengkap
                    </div>
                    <div class="table-wrapper">
                        <table id="history-sales-table" class="table table-striped table-bordered">
                            <thead id="main-table-header">
                                <tr>
                                    <th style="width: 50px;">NO</th>
                                    <th style="width: 120px;">NO RESI</th>
                                    <th>SKU</th>
                                    <th style="width: 80px;">QTY</th>
                                    <th style="width: 150px;">CREATED AT</th>
                                    <th style="width: 150px;">UPDATED AT</th>
                                    <th style="width: 100px;">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data akan diisi oleh DataTables -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- History Sales Table end -->
    </div>
    <!-- [ Main Content ] end -->

    <!-- Edit History Sale Modal -->
    <div class="modal fade" id="editHistorySaleModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit History Sale</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editHistorySaleForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_history_sale_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">No Resi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="no_resi" id="edit_no_resi" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">SKU & Quantity</label>
                            <div id="edit-sku-container">
                                <!-- SKU inputs will be added here -->
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-edit-sku-btn">
                                <i class="fas fa-plus"></i> Add SKU
                            </button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Core JS files -->
    <script src="{{ URL::asset('build/js/plugins/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/bootstrap.min.js') }}"></script>

    <!-- DataTables Core -->
    <script src="{{ URL::asset('build/js/plugins/dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/dataTables.bootstrap5.min.js') }}"></script>

    <!-- Custom JS -->
    <script src="{{ URL::asset('js/history-sales-edit.js') }}"></script>

    <script>
        /**
         * History Sales Scanner Implementation
         * ===================================
         * This implementation follows a specific workflow for scanning No Resi and SKUs:
         * 
         * 1. No Resi Scanning Process:
         *    - Input field focused by default
         *    - Shows "Scanning..." indicator
         *    - Immediate validation via AJAX
         *    - Validates uniqueness of No Resi only
         *    - On valid: enables SKU input
         *    - On invalid: shows error, resets after 3s
         * 
         * 2. SKU Scanning Process:
         *    - Shows "Scanning..." indicator
         *    - Validates minimum length (10 chars)
         *    - Checks for duplicate SKUs within form
         *    - On duplicate: shows error, resets form after 3s
         *    - On valid: adds ONE new input field
         *    - Starts 10s countdown for auto-submit
         * 
         * 3. Auto-Submit Process:
         *    - Triggers after 10s countdown
         *    - Or when reset button clicked
         *    - Only non-empty SKUs will be processed
         *    - Shows success/error message
         *    - Resets form on completion
         */
        $(document).ready(function() {
            // Prevent DataTables from showing error messages in console
            $.fn.dataTable.ext.errMode = 'none';

            // Constants for timing and validation
            const SUBMIT_TIMEOUT = 10000; // 10s auto-submit delay
            const RESET_TIMEOUT = 3000; // 3s reset delay
            const SCAN_DELAY = 100; // 100ms between scans
            const MIN_SKU_LENGTH = 3; // Minimum SKU length
            const NEW_FIELD_DELAY = 100; // 100ms wait before adding new SKU field

            // State tracking variables
            let submitTimer = null;
            let newFieldTimer = null;
            let lastScanTime = Date.now();
            let isProcessing = false;
            let hasValidResi = false;
            let countdownInterval = null;
            let countdownSeconds = 10;
            let isAddingNewField = false; // New flag to prevent multiple field additions

            // Check and remove duplicate thead elements that might already exist
            if ($('#history-sales-table thead').length > 1) {
                $('#history-sales-table thead:gt(0)').remove();
            }

            // Initialize DataTable with standard configuration
            var table = $('#history-sales-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                scrollX: true,
                dom: 'frtip',
                bAutoWidth: false,
                ordering: true,
                searching: true,
                stateSave: false,
                paging: true,
                fixedHeader: false,
                headerCallback: function() {
                    // Hapus header yang duplikat segera setelah header dibuat
                    if ($('#history-sales-table thead').length > 1) {
                        $('#history-sales-table thead:not(#main-table-header)').remove();
                    }
                },
                initComplete: function() {
                    // Check for duplicate headers after initialization
                    if ($('#history-sales-table thead').length > 1) {
                        $('#history-sales-table thead:not(#main-table-header)').remove();
                    }

                    // Tambahkan observer untuk mendeteksi perubahan DOM
                    const observer = new MutationObserver(function(mutations) {
                        if ($('#history-sales-table thead').length > 1) {
                            $('#history-sales-table thead:not(#main-table-header)').remove();
                        }
                    });

                    observer.observe(document.getElementById('history-sales-table'), {
                        childList: true,
                        subtree: true
                    });

                    // Update tanggal saat pertama kali load
                    updateCurrentDateInfo();
                },
                ajax: {
                    url: "{{ route('history-sales.data') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}";
                        if ($('#showArchived').hasClass('active')) {
                            d.only_trashed = true;
                        } else if ($('#showActive').hasClass('active')) {
                            d.only_trashed = false;
                        }
                    },
                    // Tambahkan handler untuk ajax complete untuk memperbarui info tanggal
                    dataSrc: function(json) {
                        updateCurrentDateInfo();
                        return json.data;
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
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [4, 'desc']
                ],
                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                language: {
                    processing: '<i class="fas fa-spinner fa-spin fa-2x"></i><span class="ms-2">Memuat data...</span>',
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
                drawCallback: function(settings) {
                    // Ensure no duplicate headers by removing any extra thead elements
                    if ($('#history-sales-table thead').length > 1) {
                        $('#history-sales-table thead:not(#main-table-header)').remove();
                    }

                    // Remove any DataTables generated header
                    $('.dataTable > thead:gt(0)').remove();

                    // Make sure tooltip works for action buttons
                    $('[title]').tooltip();
                }
            });

            // Clean up any duplicate headers after Ajax calls
            table.on('xhr.dt', function() {
                setTimeout(function() {
                    if ($('#history-sales-table thead').length > 1) {
                        $('#history-sales-table thead:not(#main-table-header)').remove();
                    }
                }, 100);
            });

            // Initialize edit functionality
            initializeHistoryEdit(table);

            // Setup AJAX CSRF token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            /**
             * No Resi Input Handler
             * - Validates input immediately
             * - Shows scanning indicator
             * - Enables SKU input on success
             */
            $('#no_resi').on('input', function(e) {
                const noResi = $(this).val().trim();
                clearTimeout(submitTimer);
                $('#resiScanningIndicator').addClass('active');

                if (!noResi) {
                    hasValidResi = false;
                    return;
                }

                validateNoResi(noResi);
            });

            /**
             * SKU Input Handler
             * - Validates length and duplicates
             * - Starts countdown for auto-submit
             * - Prepares new field if needed
             */
            $(document).on('input', '.sku-input', function(e) {
                if (isProcessing || !hasValidResi) return;
                const input = $(this);
                const currentSku = input.val().trim();
                handleSkuInput(input, currentSku);
            });

            /**
             * Quantity Input Handler
             * - Resets countdown timer when quantity is changed manually
             */
            $(document).on('input', '.qty-input', function(e) {
                if (isProcessing || !hasValidResi) return;
                if ($(this).closest('.sku-input-container').find('.sku-input').val().trim().length >=
                    MIN_SKU_LENGTH) {
                    startCountdown();
                    setupAutoSubmit();
                }
            });

            /**
             * Helper Functions
             */
            function validateNoResi(noResi) {
                $.ajax({
                    url: "{{ route('history-sales.validate-no-resi') }}",
                    method: 'POST',
                    data: {
                        no_resi: noResi
                    },
                    success: function(response) {
                        if (response.valid) {
                            handleValidResi();
                        } else {
                            handleInvalidResi();
                        }
                    },
                    error: function() {
                        handleResiError();
                    }
                });
            }

            function handleValidResi() {
                hasValidResi = true;
                $('#resiError').hide();
                setTimeout(() => {
                    $('.sku-input:first').prop('disabled', false).focus();
                    $('#resiScanningIndicator').removeClass('active');
                }, SCAN_DELAY);
            }

            function handleInvalidResi() {
                showError('resiError', 'No Resi already exists');
                setTimeout(() => resetForm(), RESET_TIMEOUT);
            }

            function handleResiError() {
                showError('resiError', 'Error validating No Resi');
                setTimeout(() => resetForm(), RESET_TIMEOUT);
            }

            function handleSkuInput(input, currentSku) {
                if (!currentSku) return;

                clearTimeout(submitTimer);
                clearTimeout(newFieldTimer);

                $('#skuScanningIndicator').addClass('active');

                if (currentSku.length >= MIN_SKU_LENGTH) {
                    // Check for duplicates
                    if (isDuplicateSkuInForm(currentSku, input)) {
                        showError('skuError', 'Duplicate SKU detected');
                        setTimeout(() => resetSkuForm(), RESET_TIMEOUT);
                        return;
                    }

                    // Only add new field if we're not already in the process
                    if (!isAddingNewField && input.closest('.sku-input-container').is(':last-child')) {
                        isAddingNewField = true;
                        setTimeout(() => {
                            addNewSkuField();
                            isAddingNewField = false;
                        }, NEW_FIELD_DELAY);
                    }

                    // Reset and restart countdown for each valid SKU input
                    startCountdown();
                    setupAutoSubmit();
                    $('#skuScanningIndicator').removeClass('active');
                }
            }

            function isDuplicateSkuInForm(sku, currentInput) {
                let duplicate = false;
                $('.sku-input').not(currentInput).each(function() {
                    const existingSku = $(this).val().trim();
                    if (existingSku && existingSku === sku) {
                        duplicate = true;
                        return false;
                    }
                });
                return duplicate;
            }

            function setupAutoSubmit() {
                clearTimeout(submitTimer);
                submitTimer = setTimeout(() => {
                    submitForm();
                }, SUBMIT_TIMEOUT);
            }

            function showError(elementId, message) {
                $(`#${elementId}`).text(message).show();
                Swal.fire({
                    title: 'Error!',
                    text: message,
                    icon: 'error',
                    timer: 2000,
                    showConfirmButton: false
                });
            }

            function addNewSkuField() {
                // Only add new field if the last field has content
                const lastSkuInput = $('.sku-input:last');
                if (lastSkuInput.val().trim().length >= MIN_SKU_LENGTH) {
                    const newSkuContainer = `
                    <div class="sku-input-container">
                            <input type="text" class="form-control scanner-input sku-input" 
                                name="no_sku[]" required placeholder="Scan SKU...">
                            <input type="number" class="form-control qty-input" 
                                name="qty[]" value="1" min="1">
                        <span class="scanning-indicator">
                            <i class="fas fa-circle-notch fa-spin"></i> Scanning...
                        </span>
                    </div>
                `;
                    $('#sku-container').append(newSkuContainer);
                    $('.sku-input:last').focus();
                }
            }

            function resetSkuForm() {
                clearTimeout(submitTimer);
                clearTimeout(newFieldTimer);
                clearInterval(countdownInterval);

                // Only reset SKU-related elements
                $('.sku-input-container:not(:first)').remove();
                $('.sku-input:first').val('').focus();
                $('.qty-input:first').val('1');
                $('#skuError').hide();
                $('#skuScanningIndicator').removeClass('active');
                $('#autoSubmitTimer').hide();
            }

            function resetForm() {
                clearTimeout(submitTimer);
                clearTimeout(newFieldTimer);
                clearInterval(countdownInterval);

                $('#no_resi').val('').prop('disabled', false).focus();
                $('.sku-input-container:not(:first)').remove();
                $('.sku-input:first').val('').prop('disabled', true);
                $('.qty-input:first').val('1');

                $('.error-feedback').hide();
                $('.scanning-indicator').removeClass('active');
                $('#autoSubmitTimer').hide();

                isProcessing = false;
                hasValidResi = false;
                lastScanTime = Date.now();
            }

            function startCountdown() {
                clearInterval(countdownInterval);
                clearTimeout(submitTimer);
                countdownSeconds = 10;
                $('#submitCountdown').text(countdownSeconds);
                $('#autoSubmitTimer').show();

                countdownInterval = setInterval(() => {
                    countdownSeconds--;
                    $('#submitCountdown').text(countdownSeconds);
                    if (countdownSeconds <= 0) {
                        clearInterval(countdownInterval);
                        submitForm(); // Auto-submit when countdown reaches 0
                    }
                }, 1000);
            }

            async function submitForm() {
                if (isProcessing || !hasValidResi) return;

                isProcessing = true;

                try {
                    const response = await $.ajax({
                        url: "{{ route('history-sales.store') }}",
                        type: "POST",
                        data: $('#historySaleForm').serialize(),
                        dataType: 'json'
                    });

                    handleSubmitResponse(response);
                } catch (error) {
                    handleSubmitError(error);
                } finally {
                    isProcessing = false;
                }
            }

            function handleSubmitResponse(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.message,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        // Pastikan table di-refresh setelah alert tertutup
                        table.ajax.reload();
                        resetForm();
                    });
                } else {
                    throw new Error(response.message);
                }
            }

            function handleSubmitError(error) {
                showError('skuError', error.message || 'Failed to save data');
                setTimeout(() => resetForm(), RESET_TIMEOUT);
            }

            // Event Handlers
            $('#resetScannerBtn').on('click', resetForm);
            $('#historySaleForm').on('submit', function(e) {
                e.preventDefault();
            });

            // Initialize focus on No Resi input
            $('#no_resi').focus();

            // Filter buttons handler
            $('#showActive').on('click', function() {
                $(this).addClass('active').siblings().removeClass('active');
                table.ajax.reload();
            });

            $('#showArchived').on('click', function() {
                $(this).addClass('active').siblings().removeClass('active');
                table.ajax.reload();
            });

            // Add touch scroll indicator behavior
            const tableWrapper = $('.table-wrapper');
            if (tableWrapper[0].scrollWidth > tableWrapper[0].clientWidth) {
                $('.table-scroll-indicator').show();
            }

            // Hide scroll indicator after user has scrolled
            tableWrapper.on('scroll', function() {
                $('.table-scroll-indicator').fadeOut();
            });

            /**
             * Fungsi untuk memperbarui informasi tanggal aktif yang ditampilkan
             */
            function updateCurrentDateInfo() {
                const today = new Date();
                const options = {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric',
                    timeZone: 'Asia/Jakarta'
                };

                // Format tanggal ke Bahasa Indonesia
                const indonesianDate = today.toLocaleDateString('id-ID', options);

                // Update text pada elemen
                $('.current-date-info strong').text(indonesianDate);
            }
        });
    </script>
@endsection
