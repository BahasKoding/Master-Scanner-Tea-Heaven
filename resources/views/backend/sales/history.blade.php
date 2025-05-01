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
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/datatables/buttons.bootstrap5.min.css') }}">
    <!-- [Page specific CSS] end -->
    <style>
        /* Mobile-first responsive styles */
        @media (max-width: 768px) {
            .card-body {
                padding: 15px;
            }

            .scanner-input,
            .sku-input,
            .qty-input {
                font-size: 16px;
                /* Prevent iOS zoom on focus */
            }

            .sku-input-container {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .qty-input {
                width: 100%;
            }

            .scanning-indicator {
                width: 100%;
                margin: 5px 0;
            }

            #resetScannerBtn {
                width: 100%;
                margin-top: 15px;
            }

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

            /* Adjust modal for mobile */
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

            /* Make table more readable on mobile */
            #history-sales-table {
                font-size: 14px;
            }

            #history-sales-table th,
            #history-sales-table td {
                white-space: normal;
                min-width: 100px;
            }

            /* Stack action buttons on mobile */
            #history-sales-table .btn {
                display: block;
                width: 100%;
                margin: 5px 0;
            }
        }

        /* Base styles for all screen sizes */
        .scanner-active {
            border: 2px solid #4CAF50 !important;
            box-shadow: 0 0 5px #4CAF50;
        }

        .form-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .scanner-input {
            transition: all 0.3s ease;
            height: 45px;
        }

        .scanner-input:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
        }

        .sku-input-container {
            margin-bottom: 15px;
            display: grid;
            grid-template-columns: 1fr 120px 120px;
            gap: 15px;
            align-items: center;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .sku-input {
            width: 100%;
            height: 45px;
        }

        .qty-input {
            width: 120px;
            height: 45px;
            text-align: center;
            font-weight: bold;
        }

        .scanning-indicator {
            display: none;
            color: #4CAF50;
            margin-left: 10px;
            text-align: center;
        }

        .scanning-indicator.active {
            display: inline-block;
        }

        .error-feedback {
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 0.25rem;
            display: none;
        }

        .countdown-timer {
            color: #6c757d;
            font-size: 1em;
            margin-left: 10px;
            background-color: #e9ecef;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 500;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }

        .scanner-section {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }

        .card {
            border: none;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #e3e6f0;
            padding: 15px;
        }

        /* Responsive header for all screen sizes */
        .card-header .d-flex {
            flex-wrap: wrap;
            gap: 10px;
        }

        /* Improve table responsiveness */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Improve button touch targets */
        .btn {
            min-height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* Improve form field touch targets */
        input[type="text"],
        input[type="number"] {
            min-height: 44px;
        }

        /* Add smooth transitions */
        .btn,
        input,
        .card {
            transition: all 0.3s ease;
        }

        /* Improve modal responsiveness */
        .modal-dialog {
            max-width: 95%;
            margin: 20px auto;
        }

        @media (min-width: 768px) {
            .modal-dialog {
                max-width: 700px;
            }
        }

        /* Mobile and Tablet Responsive Table Styles */
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
            /* Ensure minimum width for content */
            width: 100%;
        }

        #history-sales-table th,
        #history-sales-table td {
            white-space: nowrap;
            vertical-align: middle;
            padding: 12px 8px;
        }

        /* Responsive SKU Input Form */
        .sku-form-wrapper {
            max-width: 100%;
            overflow-x: hidden;
        }

        .sku-input-container {
            display: grid;
            gap: 10px;
            margin-bottom: 15px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        /* Mobile styles */
        @media (max-width: 576px) {
            .sku-input-container {
                grid-template-columns: 1fr;
            }

            .sku-input {
                width: 100%;
            }

            .qty-input {
                width: 100%;
            }

            .scanning-indicator {
                width: 100%;
                text-align: center;
                margin: 5px 0;
            }

            #history-sales-table td:nth-child(3),
            /* SKU column */
            #history-sales-table td:nth-child(4) {
                /* Qty column */
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
        }

        /* Tablet styles */
        @media (min-width: 577px) and (max-width: 991px) {
            .sku-input-container {
                grid-template-columns: 2fr 1fr;
            }

            .scanning-indicator {
                grid-column: span 2;
                text-align: center;
            }

            #history-sales-table td:nth-child(3),
            /* SKU column */
            #history-sales-table td:nth-child(4) {
                /* Qty column */
                max-width: 200px;
                white-space: normal;
                word-break: break-word;
            }
        }

        /* Desktop styles */
        @media (min-width: 992px) {
            .sku-input-container {
                grid-template-columns: 1fr 120px 120px;
            }

            .scanning-indicator {
                text-align: center;
            }
        }

        /* Common styles for all devices */
        .sku-input,
        .qty-input {
            height: 44px;
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 16px;
        }

        .sku-input:focus,
        .qty-input:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
            outline: none;
        }

        /* Table action buttons */
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

        /* Horizontal scroll indicator */
        .table-scroll-indicator {
            display: none;
            margin-bottom: 10px;
        }

        @media (max-width: 991px) {
            .table-scroll-indicator {
                display: block;
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
                        <h5 class="mb-2 mb-md-0">History Sales List</h5>
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
                            <thead>
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th style="width: 120px;">No Resi</th>
                                    <th>SKU</th>
                                    <th style="width: 80px;">Qty</th>
                                    <th style="width: 150px;">Created At</th>
                                    <th style="width: 150px;">Updated At</th>
                                    <th style="width: 120px;">Actions</th>
                                </tr>
                            </thead>
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

    <!-- DataTables Buttons and Extensions -->
    <script src="{{ URL::asset('build/js/plugins/dataTables.buttons.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/pdfmake.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/vfs_fonts.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/buttons.html5.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/buttons.print.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/buttons.colVis.min.js') }}"></script>

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

            // Initialize DataTable with standard configuration
            var table = $('#history-sales-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                scrollX: true,
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
                }
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
        });
    </script>
@endsection
