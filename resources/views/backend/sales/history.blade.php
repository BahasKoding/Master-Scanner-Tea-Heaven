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

        /* Help text styling */
        .form-help-text {
            font-size: 0.85rem;
            color: var(--secondary-color);
            margin-top: 0.25rem;
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

        /* Button Group Styling */
        .btn-group {
            display: inline-flex;
            flex-wrap: wrap;
            gap: 10px;
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

        /* Remove SKU button styling */
        .btn-remove-sku {
            width: 40px;
            height: var(--form-element-height);
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--border-radius-normal);
            transition: all 0.2s ease;
        }

        .btn-remove-sku:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 5px rgba(0, 0, 0, 0.2);
        }

        .btn-remove-sku:disabled {
            opacity: 0.5;
            cursor: not-allowed;
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

        .sku-input {
            min-width: 300px;
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

        /* Scanner toggle styling */
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .form-check-input:focus {
            border-color: var(--primary-color);
            box-shadow: var(--focus-shadow);
        }

        .form-check.form-switch .form-check-input {
            width: 3em;
            height: 1.5em;
            cursor: pointer;
        }

        .form-check-label {
            cursor: pointer;
            user-select: none;
        }

        #scannerStatus {
            font-weight: 500;
            transition: var(--transition-normal);
        }

        .scanner-inactive {
            color: var(--secondary-color);
            text-decoration: line-through;
        }

        .scanner-active {
            color: var(--primary-color);
            font-weight: 600;
        }

        .scanner-mode-hint {
            font-size: 0.75rem;
            opacity: 0.8;
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
            padding: 8px;
            background-color: #fff3cd;
            border-radius: var(--border-radius-normal);
            text-align: center;
            font-size: 0.9rem;
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

            /* Scanner toggle display on mobile */
            .card-header .d-flex {
                flex-direction: column;
                align-items: flex-start;
            }

            .d-flex.align-items-center {
                width: 100%;
                margin-top: 10px;
                justify-content: space-between;
                flex-wrap: wrap;
            }

            .form-check.form-switch {
                margin-bottom: 10px;
                width: 100%;
            }

            .countdown-timer {
                margin-left: 0;
                width: 100%;
                text-align: center;
                margin-top: 10px;
                font-size: 0.9rem;
            }

            .scanner-mode-hint {
                display: inline-block !important;
                margin-left: 5px;
            }

            .sku-input-container {
                grid-template-columns: 1fr 80px 40px;
                padding: 10px;
            }

            .sku-input {
                min-width: 100%;
            }

            .sku-input,
            .qty-input {
                width: 100%;
            }

            .scanning-indicator {
                width: 100%;
                grid-column: span 3;
                text-align: center;
                margin: 5px 0;
            }

            /* Button responsiveness on mobile */
            .btn-group {
                width: 100%;
                flex-direction: column;
            }

            #submitManualBtn,
            #resetScannerBtn {
                width: 100%;
                margin: 5px 0;
            }

            /* Table styles for mobile */
            .table-scroll-indicator {
                display: block;
                font-weight: bold;
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
        @media (min-width: 577px) and (max-width: 991px) {
            .card-header .d-flex {
                justify-content: space-between;
            }

            .form-check.form-switch {
                margin-right: 15px;
            }

            .countdown-timer {
                font-size: 0.9rem;
                padding: 5px 10px;
            }

            .sku-input-container {
                grid-template-columns: 2fr 80px 40px;
            }

            .sku-input {
                min-width: 100%;
            }

            .scanning-indicator {
                grid-column: span 3;
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

        /* Desktop styles */
        @media (min-width: 992px) {
            .sku-input-container {
                grid-template-columns: 1fr 120px 40px;
            }

            .scanning-indicator {
                grid-column: span 3;
                text-align: center;
            }

            .modal-dialog {
                max-width: 700px;
            }
        }

        /* Input validation styling */
        .is-validating {
            background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24px" height="24px"><path fill="%23aaa" d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z"/><path fill="%23aaa" d="M12,4a8,8,0,0,0-8,8H6a6,6,0,1,1,6,6v2A8,8,0,0,0,12,4Z"><animateTransform attributeName="transform" type="rotate" from="0 12 12" to="360 12 12" dur="1s" repeatCount="indefinite"/></path></svg>');
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 20px;
            transition: background 0.3s;
        }

        /* Table loading state */
        .table-loading {
            position: relative;
        }

        .table-loading:after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.7) url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 44 44" width="44px" height="44px"><circle fill="none" stroke="%234CAF50" stroke-width="4" cx="22" cy="22" r="14"><animate attributeName="stroke-dasharray" dur="1.5s" calcMode="spline" values="0 100;100 100;0 100" keyTimes="0;0.5;1" keySplines="0.42,0,0.58,1;0.42,0,0.58,1" repeatCount="indefinite"/><animate attributeName="stroke-dashoffset" dur="1.5s" calcMode="spline" values="0;-100;-200" keyTimes="0;0.5;1" keySplines="0.42,0,0.58,1;0.42,0,0.58,1" repeatCount="indefinite"/></circle></svg>') center center no-repeat;
            background-size: 50px;
            z-index: 10;
            border-radius: var(--border-radius-large);
            animation: fadeIn 0.2s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
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
                            <small class="text-muted">Pindai No Resi terlebih dahulu, kemudian pindai SKU barang</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="form-check form-switch me-3">
                                <input class="form-check-input" type="checkbox" id="scannerToggle" checked>
                                <label class="form-check-label" for="scannerToggle">
                                    <span id="scannerStatus">Resi Auto-Scan</span>
                                    <small class="d-block text-muted scanner-mode-hint">Mode pindai otomatis</small>
                                </label>
                            </div>
                            <div class="countdown-timer" id="autoSubmitTimer" style="display: none;">
                                <i class="fas fa-clock me-1"></i> Menyimpan dalam <span id="submitCountdown">10</span> detik
                                <br><small class="text-muted">(atau klik "Simpan Data" untuk menyimpan sekarang)</small>
                            </div>
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
                                            id="no_resi" name="no_resi" required autofocus
                                            placeholder="Ketik atau pindai nomor resi di sini...">
                                        <span class="scanning-indicator" id="resiScanningIndicator">
                                            <i class="fas fa-circle-notch fa-spin"></i> Memindai...
                                        </span>
                                    </div>
                                    <div class="error-feedback" id="resiError"></div>
                                    <small class="text-muted d-block mt-1">
                                        <i class="fas fa-info-circle"></i> Nomor resi harus unik dan belum pernah digunakan
                                        sebelumnya
                                    </small>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">
                                        <i class="fas fa-boxes me-1"></i> No SKU & Jumlah
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div id="sku-container">
                                        <div class="sku-input-container">
                                            <input type="text" class="form-control scanner-input sku-input"
                                                name="no_sku[]" required disabled
                                                placeholder="Ketik atau pindai nomor SKU di sini...">
                                            <input type="number" class="form-control qty-input" name="qty[]"
                                                value="1" min="1" title="Jumlah barang">
                                            <button type="button" class="btn btn-danger btn-remove-sku"
                                                title="Hapus SKU" disabled>
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <span class="scanning-indicator" id="skuScanningIndicator">
                                                <i class="fas fa-circle-notch fa-spin"></i> Memindai...
                                            </span>
                                        </div>
                                    </div>
                                    <div class="error-feedback" id="skuError"></div>
                                    <small class="text-muted d-block mt-1">
                                        <i class="fas fa-info-circle"></i> Masukkan nomor SKU produk dan jumlahnya. Isian
                                        baru akan muncul otomatis.
                                    </small>
                                </div>

                                <div class="text-center mt-4">
                                    <div class="btn-group">
                                        <button type="button" id="submitManualBtn" class="btn btn-primary btn-lg me-2">
                                            <i class="fas fa-save me-1"></i> Simpan Data
                                        </button>
                                        <button type="button" id="resetScannerBtn" class="btn btn-warning btn-lg">
                                            <i class="fas fa-redo me-1"></i> Reset Form
                                        </button>
                                    </div>
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
                            <h5 class="mb-0">Daftar Riwayat Penjualan</h5>
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
                        <i class="fas fa-arrows-left-right me-1"></i> Geser kanan-kiri untuk melihat data lengkap
                    </div>
                    <div class="table-wrapper">
                        <table id="history-sales-table" class="table table-striped table-bordered">
                            <thead id="main-table-header">
                                <tr>
                                    <th style="width:.50px;">NO</th>
                                    <th style="width: 120px;">NO RESI</th>
                                    <th>NO SKU</th>
                                    <th style="width: 80px;">JUMLAH</th>
                                    <th style="width: 150px;">DIBUAT</th>
                                    <th style="width: 150px;">DIPERBARUI</th>
                                    <th style="width: 100px;">AKSI</th>
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
         *    - Validates uniqueness of No Resi only when auto-scan is enabled
         *    - On valid: enables SKU input
         *    - On invalid: shows error, resets after 3s
         * 
         * 2. SKU Scanning Process:
         *    - Shows "Scanning..." indicator
         *    - Validates minimum length (3 chars)
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
        // Declare table variable globally
        var table;

        $(document).ready(function() {
            $(document).on('keydown', function(e) {
                // Check if Enter key was pressed (key code 13)
                if (e.keyCode === 13) {
                    // Prevent default form submission
                    e.preventDefault();
                    
                    // Check if we're not inside a modal (to avoid conflicts with modal forms)
                    if ($('.modal.show').length === 0) {
                        // Check if at least one SKU field has a value
                        let hasValidSku = false;
                        $('.sku-input').each(function() {
                            if ($(this).val().trim() !== '') {
                                hasValidSku = true;
                                return false; // Break the loop
                            }
                        });
                        
                        // If we have at least one valid SKU, trigger the submit button click
                        if (hasValidSku) {
                            $('#submitManualBtn').click();
                        }
                    }
                }
            });
            // Prevent DataTables from showing error messages in console
            $.fn.dataTable.ext.errMode = 'none';

            // Constants for timing and validation - consolidated in one place
            const CONFIG = {
                SUBMIT_TIMEOUT: 10000, // 10s auto-submit delay
                RESET_TIMEOUT: 3000, // 3s reset delay
                SCAN_DELAY: 100, // 100ms between scans
                MIN_SKU_LENGTH: 3, // Minimum SKU length
                NEW_FIELD_DELAY: 100, // 100ms wait before adding new SKU field
                COUNTDOWN_SECONDS: 10, // Initial countdown value
                TABLE_CHUNK_SIZE: 500, // Chunk size for loading large datasets
                EXPORT_LIMIT: 10000 // Maximum number of records for export
            };

            // State tracking variables - consolidated and organized
            const STATE = {
                submitTimer: null,
                newFieldTimer: null,
                countdownInterval: null,
                countdownSeconds: CONFIG.COUNTDOWN_SECONDS,
                isProcessing: false,
                hasValidResi: false,
                isScannerActive: true,
                isAddingNewField: false,
                resiValidationTimer: null
            };

            // Initialize scanner toggle
            function initScannerToggle() {
                $('#scannerToggle').on('change', function() {
                    STATE.isScannerActive = $(this).is(':checked');

                    if (STATE.isScannerActive) {
                        $('#scannerStatus').text('Resi Auto-Scan').removeClass('scanner-inactive').addClass(
                            'scanner-active');
                        $('.scanner-mode-hint').text('Mode pindai otomatis');
                        $('#no_resi').addClass('scanner-active');
                    } else {
                        $('#scannerStatus').text('Resi Manual').removeClass('scanner-active').addClass(
                            'scanner-inactive');
                        $('.scanner-mode-hint').text('Mode input manual');
                        $('#no_resi').removeClass('scanner-active');
                    }
                }).trigger('change'); // Initialize the state
            }

            /**
             * No Resi Input Handler - refactored for clarity
             */
            function initNoResiHandler() {
                $('#no_resi').on('input', function() {
                    const noResi = $(this).val().trim();
                    clearTimeout(STATE.submitTimer);

                    // Always show scanning indicator
                    $('#resiScanningIndicator').addClass('active');

                    if (!noResi) {
                        STATE.hasValidResi = false;
                        return;
                    }

                    // Only auto-validate if scanner is active, otherwise wait for blur
                    if (STATE.isScannerActive) {
                        validateNoResi(noResi, false);
                    }
                });

                // Manual validation for No Resi when scanner is disabled
                $('#no_resi').on('blur', function() {
                    const noResi = $(this).val().trim();

                    if (!STATE.isScannerActive && noResi) {
                        validateNoResi(noResi, true);
                    }
                });
            }

            /**
             * SKU Input Handler - refactored for clarity
             */
            function initSkuHandler() {
                $(document).on('input', '.sku-input', function() {
                    if (STATE.isProcessing || !STATE.hasValidResi) return;

                    const input = $(this);
                    const currentSku = input.val().trim();

                    // Check if the SKU input matches the No Resi value
                    const noResi = $('#no_resi').val().trim();
                    if (currentSku === noResi && noResi !== '') {
                        input.val(''); // Clear the input
                        showAlert('warning', 'Perhatian!', 'Nilai No Resi tidak boleh sama dengan No SKU');
                        return; // Stop processing
                    }

                    handleSkuInput(input, currentSku);
                });

                // Quantity Input Handler
                $(document).on('input', '.qty-input', function() {
                    if (STATE.isProcessing || !STATE.hasValidResi) return;

                    if ($(this).closest('.sku-input-container').find('.sku-input').val().trim().length >=
                        CONFIG.MIN_SKU_LENGTH) {
                        startCountdown();
                        setupAutoSubmit();
                    }
                });

                // Remove SKU Button Handler
                $(document).on('click', '.btn-remove-sku', function() {
                    const container = $(this).closest('.sku-input-container');
                    // Don't remove the last container, just clear it
                    if ($('.sku-input-container').length > 1) {
                        container.fadeOut(200, function() {
                            $(this).remove();
                            // If at least one SKU has content, restart countdown
                            if (hasSKUsWithContent()) {
                                startCountdown();
                                setupAutoSubmit();
                            }
                        });
                    } else {
                        container.find('.sku-input').val('');
                        container.find('.qty-input').val('1');
                    }
                });

                // Add paste event handler to prevent pasting No Resi into SKU
                $(document).on('paste', '.sku-input', function(e) {
                    const noResi = $('#no_resi').val().trim();

                    // Get pasted content
                    let pastedText;
                    if (window.clipboardData && window.clipboardData.getData) {
                        pastedText = window.clipboardData.getData('Text');
                    } else if (e.originalEvent && e.originalEvent.clipboardData && e.originalEvent
                        .clipboardData.getData) {
                        pastedText = e.originalEvent.clipboardData.getData('text/plain');
                    }

                    // Check if pasted content is No Resi
                    if (pastedText && pastedText.trim() === noResi && noResi !== '') {
                        e.preventDefault();
                        showAlert('warning', 'Perhatian!', 'Nilai No Resi tidak boleh sama dengan No SKU');
                    }
                });

                // Add scan detection for better handling of barcode scanner input
                $(document).on('keypress', '.sku-input', function(e) {
                    // If Enter key is pressed very shortly after input (typical for scanners)
                    if (e.which === 13) {
                        e.preventDefault(); // Prevent form submission
                        const input = $(this);
                        const currentSku = input.val().trim();
                        const noResi = $('#no_resi').val().trim();

                        if (currentSku === noResi && noResi !== '') {
                            input.val(''); // Clear the input
                            showAlert('warning', 'Perhatian!',
                                'Nilai No Resi tidak boleh sama dengan No SKU');
                            return; // Stop processing
                        }
                    }
                });
            }

            // Check if any SKU inputs have content
            function hasSKUsWithContent() {
                let hasContent = false;
                $('.sku-input').each(function() {
                    if ($(this).val().trim().length >= CONFIG.MIN_SKU_LENGTH) {
                        hasContent = true;
                        return false; // break the loop
                    }
                });
                return hasContent;
            }

            /**
             * Initialize form buttons
             */
            function initFormButtons() {
                // Reset scanner button
                $('#resetScannerBtn').on('click', resetForm);

                // Submit manual button
                $('#submitManualBtn').on('click', function() {
                    // Only submit if we have a valid resi and at least one SKU field has content
                    if (STATE.hasValidResi) {
                        let hasContent = hasSKUsWithContent();

                        if (hasContent) {
                            clearInterval(STATE.countdownInterval);
                            clearTimeout(STATE.submitTimer);
                            $('#autoSubmitTimer').hide();
                            submitForm();
                        } else {
                            showAlert('warning', 'Perhatian!',
                                'Anda harus mengisi minimal satu SKU untuk melanjutkan');
                        }
                    } else {
                        showAlert('warning', 'Perhatian!', 'No Resi harus diisi dan valid terlebih dahulu');
                        $('#no_resi').focus();
                    }
                });

                // Prevent default form submission
                $('#historySaleForm').on('submit', function(e) {
                    e.preventDefault();
                });
            }

            /**
             * Initialize filter buttons
             */
            function initFilterButtons() {
                // Filter buttons handler
                $('#showActive').on('click', function() {
                    $(this).addClass('active').siblings().removeClass('active');
                    table.ajax.reload();
                });

                $('#showArchived').on('click', function() {
                    $(this).addClass('active').siblings().removeClass('active');
                    table.ajax.reload();
                });
            }

            /**
             * Helper Functions - Refactored for better organization
             */
            function validateNoResi(noResi, allowDuplicates) {
                // Tambahkan indikator loading pada input
                const $noResiInput = $('#no_resi');
                $noResiInput.addClass('is-validating');

                // Batasi permintaan dengan debounce sederhana
                if (STATE.resiValidationTimer) {
                    clearTimeout(STATE.resiValidationTimer);
                }

                STATE.resiValidationTimer = setTimeout(() => {
                    $.ajax({
                        url: "{{ route('history-sales.validate-no-resi') }}",
                        method: 'POST',
                        data: {
                            no_resi: noResi,
                            allow_duplicates: allowDuplicates
                        },
                        cache: false,
                        timeout: 3000, // 3 detik timeout
                        success: function(response) {
                            $noResiInput.removeClass('is-validating');
                            if (response.valid) {
                                handleValidResi();
                            } else {
                                handleInvalidResi(response.message);
                            }
                        },
                        error: function(xhr) {
                            $noResiInput.removeClass('is-validating');
                            handleResiError(xhr.responseJSON?.message ||
                                'Error validasi Nomor Resi');
                        }
                    });
                }, 300); // 300ms debounce
            }

            function handleValidResi() {
                STATE.hasValidResi = true;
                $('#resiError').hide();

                // Enable the SKU input and remove button
                $('.sku-input:first').prop('disabled', false);
                $('.btn-remove-sku:first').prop('disabled', false);

                // Add a visual indicator showing scan direction
                $('#no_resi').removeClass('scanner-active');
                $('.sku-input:first').addClass('scanner-active');

                // Add a visual hint that we're now scanning SKUs
                showAlert('success', 'No Resi Valid', 'Silakan pindai No SKU', 1000);

                // Ensure focus moves to SKU input field after validation
                // Clear any existing timeouts to prevent race conditions
                if (window.focusTimeout) {
                    clearTimeout(window.focusTimeout);
                }
                
                // Use a slightly longer delay to ensure the alert doesn't steal focus
                window.focusTimeout = setTimeout(() => {
                    // Force blur on current element first
                    document.activeElement.blur();
                    // Then focus on the SKU input with a small delay
                    setTimeout(() => {
                        $('.sku-input:first').focus();
                        // Verify focus was set correctly
                        if (document.activeElement !== $('.sku-input:first')[0]) {
                            $('.sku-input:first').trigger('focus');
                        }
                        $('#resiScanningIndicator').removeClass('active');
                    }, 50);
                }, CONFIG.SCAN_DELAY + 100);
            }

            function handleInvalidResi(message) {
                showError('resiError', message || 'Nomor Resi sudah ada dalam sistem');
                setTimeout(() => resetForm(), CONFIG.RESET_TIMEOUT);
            }

            function handleResiError(message) {
                showError('resiError', message || 'Error validasi Nomor Resi');
                setTimeout(() => resetForm(), CONFIG.RESET_TIMEOUT);
            }

            function handleSkuInput(input, currentSku) {
                if (!currentSku) return;

                clearTimeout(STATE.submitTimer);
                clearTimeout(STATE.newFieldTimer);

                $('#skuScanningIndicator').addClass('active');

                if (currentSku.length >= CONFIG.MIN_SKU_LENGTH) {
                    // Check for duplicates
                    if (isDuplicateSkuInForm(currentSku, input)) {
                        showError('skuError', 'SKU duplikat terdeteksi: ' + currentSku);
                        setTimeout(() => resetSkuForm(), CONFIG.RESET_TIMEOUT);
                        return;
                    }

                    // Only add new field if we're not already in the process
                    if (!STATE.isAddingNewField && input.closest('.sku-input-container').is(':last-child')) {
                        STATE.isAddingNewField = true;
                        setTimeout(() => {
                            addNewSkuField();
                            STATE.isAddingNewField = false;
                        }, CONFIG.NEW_FIELD_DELAY);
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
                clearTimeout(STATE.submitTimer);
                STATE.submitTimer = setTimeout(() => {
                    submitForm();
                }, CONFIG.SUBMIT_TIMEOUT);
            }

            function showAlert(icon, title, message, timer = 2000) {
                const titles = {
                    'success': 'Berhasil!',
                    'error': 'Terjadi Kesalahan!',
                    'warning': 'Perhatian!',
                    'info': 'Informasi'
                };

                Swal.fire({
                    title: title || titles[icon] || '',
                    text: message,
                    icon: icon,
                    timer: timer,
                    showConfirmButton: false
                });
            }

            function showError(elementId, message) {
                $(`#${elementId}`).text(message).show();
                showAlert('error', 'Terjadi Kesalahan!', message);
            }

            function addNewSkuField() {
                // Only add new field if the last field has content
                const lastSkuInput = $('.sku-input:last');
                if (lastSkuInput.val().trim().length >= CONFIG.MIN_SKU_LENGTH) {
                    const newSkuContainer = `
                    <div class="sku-input-container">
                        <input type="text" class="form-control scanner-input sku-input" 
                            name="no_sku[]" required placeholder="Scan SKU...">
                        <input type="number" class="form-control qty-input" 
                            name="qty[]" value="1" min="1">
                        <button type="button" class="btn btn-danger btn-remove-sku" title="Hapus SKU">
                            <i class="fas fa-minus"></i>
                        </button>
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
                clearTimeout(STATE.submitTimer);
                clearTimeout(STATE.newFieldTimer);
                clearInterval(STATE.countdownInterval);

                // Only reset SKU-related elements
                $('.sku-input-container:not(:first)').remove();
                $('.sku-input:first').val('').focus();
                $('.qty-input:first').val('1');
                $('#skuError').hide();
                $('#skuScanningIndicator').removeClass('active');
                $('#autoSubmitTimer').hide();
            }

            function resetForm() {
                clearTimeout(STATE.submitTimer);
                clearTimeout(STATE.newFieldTimer);
                clearInterval(STATE.countdownInterval);

                $('#no_resi').val('').prop('disabled', false).focus();
                // Reset scanner active indicator
                $('#no_resi').addClass('scanner-active');
                $('.sku-input').removeClass('scanner-active');

                $('.sku-input-container:not(:first)').remove();
                $('.sku-input:first').val('').prop('disabled', true);
                $('.qty-input:first').val('1');
                $('.btn-remove-sku:first').prop('disabled', true);

                $('.error-feedback').hide();
                $('.scanning-indicator').removeClass('active');
                $('#autoSubmitTimer').hide();

                STATE.isProcessing = false;
                STATE.hasValidResi = false;
            }

            function startCountdown() {
                clearInterval(STATE.countdownInterval);
                clearTimeout(STATE.submitTimer);
                STATE.countdownSeconds = CONFIG.COUNTDOWN_SECONDS;
                $('#submitCountdown').text(STATE.countdownSeconds);
                $('#autoSubmitTimer').show().css({
                    'background-color': '#f8f9fa',
                    'border-radius': '5px',
                    'padding': '8px 15px',
                    'margin-top': '10px',
                    'border': '1px solid #dee2e6'
                });

                STATE.countdownInterval = setInterval(() => {
                    STATE.countdownSeconds--;
                    $('#submitCountdown').text(STATE.countdownSeconds);

                    // Add visual indication as time gets lower
                    if (STATE.countdownSeconds <= 3) {
                        $('#autoSubmitTimer').css('background-color', '#fff3cd');
                    }

                    if (STATE.countdownSeconds <= 0) {
                        clearInterval(STATE.countdownInterval);
                        submitForm(); // Auto-submit when countdown reaches 0
                    }
                }, 1000);
            }

            async function submitForm() {
                if (STATE.isProcessing || !STATE.hasValidResi) return;

                STATE.isProcessing = true;

                // Tampilkan indikator loading
                const loadingIndicator = Swal.fire({
                    title: 'Menyimpan Data',
                    html: 'Mohon tunggu sebentar...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Dapatkan data form dan jangan kirim data kosong
                const formData = new FormData(document.getElementById('historySaleForm'));
                const formDataObj = {};

                // Kumpulkan SKUs dan quantities valid
                const skus = [];
                const quantities = [];

                // Get No Resi value to check for duplicates
                const noResi = $('#no_resi').val().trim();

                // Check for duplicate values between No Resi and SKUs
                let hasDuplicateWithResi = false;

                $('.sku-input').each(function(index) {
                    const sku = $(this).val().trim();
                    if (sku.length >= CONFIG.MIN_SKU_LENGTH) {
                        // Check if SKU matches No Resi
                        if (sku === noResi) {
                            hasDuplicateWithResi = true;
                            return false; // break the loop
                        }

                        skus.push(sku);
                        quantities.push($('.qty-input').eq(index).val() || 1);
                    }
                });

                // If duplicate found between No Resi and No SKU, show warning and stop submission
                if (hasDuplicateWithResi) {
                    loadingIndicator.close();
                    showAlert('warning', 'Perhatian!',
                        'Nilai No Resi tidak boleh sama dengan No SKU. Silakan periksa kembali.');
                    STATE.isProcessing = false;
                    return;
                }

                // Buat data yang akan dikirim
                formDataObj.no_resi = noResi;
                formDataObj.no_sku = skus;
                formDataObj.qty = quantities;
                formDataObj._token = $('meta[name="csrf-token"]').attr('content');
                formDataObj.allow_duplicates = !STATE.isScannerActive;

                try {
                    // Eksekusi request AJAX dengan waktu timeout yang lebih pendek
                    const response = await $.ajax({
                        url: "{{ route('history-sales.store') }}",
                        type: "POST",
                        data: formDataObj,
                        dataType: 'json',
                        timeout: 5000, // 5 detik timeout
                        cache: false
                    });

                    loadingIndicator.close();
                    handleSubmitResponse(response);
                } catch (error) {
                    console.error('Error submitting form:', error);
                    loadingIndicator.close();
                    handleSubmitError(error);
                } finally {
                    STATE.isProcessing = false;
                }
            }

            function handleSubmitResponse(response) {
                if (response.status === 'success') {
                    showAlert('success', 'Berhasil!', response.message, 1000);

                    // Perbarui tabel dan reset form setelah alert
                    setTimeout(() => {
                        resetForm();
                        // Refresh tabel dengan cara yang lebih ringan
                        $('#history-sales-table').DataTable().ajax.reload(null, false);
                    }, 1100);
                } else {
                    throw new Error(response.message || 'Terjadi kesalahan pada server');
                }
            }

            function handleSubmitError(error) {
                showError('skuError', error.responseJSON?.message || error.message || 'Failed to save data');
                setTimeout(() => resetForm(), CONFIG.RESET_TIMEOUT);
            }

            /**
             * Update current date info display
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

            // Initialize all components
            $(document).ready(function() {
                // Prevent DataTables from showing error messages in console
                $.fn.dataTable.ext.errMode = 'none';

                // Setup AJAX CSRF token
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // Check and remove duplicate thead elements that might already exist
                if ($('#history-sales-table thead').length > 1) {
                    $('#history-sales-table thead:gt(0)').remove();
                }

                // Initialize DataTable with optimized configuration
                table = $('#history-sales-table').DataTable({
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
                    deferRender: true, // Improves rendering performance
                    pageLength: 10, // Reduced from 25 for faster loading
                    lengthMenu: [
                        [10, 25, 50],
                        [10, 25, 50]
                    ],
                    searchDelay: 500, // Delay search requests
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

                            // Tambahkan indikator loading
                            $('.table-wrapper').addClass('table-loading');
                        },
                        dataSrc: function(json) {
                            updateCurrentDateInfo();
                            // Hapus indikator loading setelah data dimuat
                            $('.table-wrapper').removeClass('table-loading');
                            return json.data;
                        },
                        error: function(xhr, error, thrown) {
                            console.warn('AJAX error:', error);
                            // Hapus indikator loading ketika terjadi error
                            $('.table-wrapper').removeClass('table-loading');
                            showAlert('error', 'Terjadi Kesalahan!',
                                'Gagal memuat data. Silakan coba lagi.');
                        }
                    },
                    columns: [{
                            data: 'no',
                            name: 'no',
                            orderable: false,
                            searchable: false
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
                            className: 'align-top',
                            searchable: false
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
                        },
                        {
                            data: 'updated_at',
                            name: 'updated_at',
                            visible: false,
                            searchable: false
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
                        },
                        emptyTable: "Tidak ada data dalam tabel",
                        infoPostFix: "",
                        thousands: ".",
                        loadingRecords: "Memuat...",
                        aria: {
                            sortAscending: ": aktifkan untuk mengurutkan kolom naik",
                            sortDescending: ": aktifkan untuk mengurutkan kolom turun"
                        }
                    },
                    drawCallback: function() {
                        // Ensure no duplicate headers
                        if ($('#history-sales-table thead').length > 1) {
                            $('#history-sales-table thead:not(#main-table-header)').remove();
                        }

                        // Remove any DataTables generated header
                        $('.dataTable > thead:gt(0)').remove();

                        // Make sure tooltip works for action buttons
                        $('[title]').tooltip();
                    },
                    initComplete: function() {
                        // Update date info on first load
                        updateCurrentDateInfo();

                        // Fix header issues
                        if ($('#history-sales-table thead').length > 1) {
                            $('#history-sales-table thead:not(#main-table-header)').remove();
                        }

                        // Improve search performance by adding debounce
                        const searchInput = $('div.dataTables_filter input');
                        searchInput.unbind();
                        searchInput.bind('input', debounce(function(e) {
                            table.search(this.value).draw();
                        }, 500));

                        // Add observer to detect DOM changes only when necessary
                        try {
                            const tableElement = document.getElementById('history-sales-table');
                            if (tableElement) {
                                const observer = new MutationObserver(function(mutations) {
                                    if ($('#history-sales-table thead').length > 1) {
                                        $('#history-sales-table thead:not(#main-table-header)')
                                            .remove();
                                    }
                                });

                                observer.observe(tableElement, {
                                    childList: true,
                                    subtree: true
                                });
                            }
                        } catch (e) {
                            console.warn('MutationObserver error:', e);
                        }
                    }
                });

                // Debounce function to improve performance
                function debounce(func, wait) {
                    let timeout;
                    return function() {
                        const context = this,
                            args = arguments;
                        clearTimeout(timeout);
                        timeout = setTimeout(function() {
                            func.apply(context, args);
                        }, wait);
                    };
                }

                // Add SKU button click handler in edit modal
                $('#add-edit-sku-btn').on('click', function() {
                    const skuHtml = `
                    <div class="edit-sku-container d-flex mb-2">
                        <input type="text" class="form-control me-2" name="edit_no_sku[]" placeholder="SKU">
                        <input type="number" class="form-control me-2" name="edit_qty[]" value="1" min="1" style="width: 120px;">
                        <button type="button" class="btn btn-danger remove-edit-sku">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>`;
                    $('#edit-sku-container').append(skuHtml);
                });

                // Remove SKU button click handler in edit modal
                $(document).on('click', '.remove-edit-sku', function() {
                    // Don't remove the last container, just clear it
                    if ($('.edit-sku-container').length > 1) {
                        $(this).closest('.edit-sku-container').remove();
                    } else {
                        $(this).closest('.edit-sku-container').find('input[name="edit_no_sku[]"]')
                            .val('');
                        $(this).closest('.edit-sku-container').find('input[name="edit_qty[]"]').val(
                            '1');
                    }
                });

                // Initialize edit functionality
                try {
                    initializeHistoryEdit(table);
                } catch (e) {
                    console.warn('Edit functionality initialization error:', e);
                }

                // Initialize UI components
                initScannerToggle();
                initNoResiHandler();
                initSkuHandler();
                initFormButtons();
                initFilterButtons();

                // Initialize focus on No Resi input
                $('#no_resi').focus();

                // Add touch scroll indicator behavior
                const tableWrapper = $('.table-wrapper');
                if (tableWrapper.length && tableWrapper[0].scrollWidth > tableWrapper[0].clientWidth) {
                    $('.table-scroll-indicator').show();
                }

                // Hide scroll indicator after user has scrolled
                tableWrapper.on('scroll', function() {
                    $('.table-scroll-indicator').fadeOut();
                });
            });
        });
    </script>
@endsection
