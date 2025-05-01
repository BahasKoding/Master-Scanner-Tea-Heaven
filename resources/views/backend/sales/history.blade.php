@extends('layouts.main')

@section('title', 'History Sales')
@section('breadcrumb-item', $item)

@section('css')
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- [Page specific CSS] start -->
    <!-- data tables css -->
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/datatables/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/datatables/buttons.bootstrap5.min.css') }}">
    <!-- [Page specific CSS] end -->
    <style>
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
        }

        .scanner-input:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
        }

        .sku-input-container {
            margin-bottom: 10px;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .sku-input {
            flex: 1;
        }

        .qty-input {
            width: 80px;
        }

        .filter-section {
            background-color: #fff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }

        .date-range-container {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .date-range-container .form-control {
            max-width: 150px;
        }

        .btn-export {
            margin-left: 10px;
        }

        .scanning-indicator {
            display: none;
            color: #4CAF50;
            margin-left: 10px;
            font-size: 0.9em;
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
    </style>
@endsection

@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">
        <!-- Add New History Sale start -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Add New History Sale</h5>
                    <small>Scan barcode for No Resi first, then No SKU.</small>
                </div>
                <div class="card-body">
                    <form id="historySaleForm" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="no_resi" class="form-label">No Resi <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control scanner-input scanner-active" id="no_resi"
                                        name="no_resi" required autofocus>
                                    <span class="scanning-indicator" id="resiScanningIndicator">
                                        <i class="fas fa-circle-notch fa-spin"></i> Scanning...
                                    </span>
                                </div>
                                <div class="error-feedback" id="resiError"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">No SKU & Quantity <span class="text-danger">*</span></label>
                                <div id="sku-container">
                                    <div class="sku-input-container">
                                        <input type="text" class="form-control scanner-input sku-input" name="no_sku[]"
                                            required disabled>
                                        <input type="number" class="form-control qty-input" name="qty[]" value="1"
                                            min="1">
                                        <span class="scanning-indicator" id="skuScanningIndicator">
                                            <i class="fas fa-circle-notch fa-spin"></i> Scanning...
                                        </span>
                                    </div>
                                </div>
                                <div class="error-feedback" id="skuError"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="button" id="resetScannerBtn" class="btn btn-warning">Reset Scanner</button>
                                <span id="autoSubmitTimer" class="text-muted ms-2" style="display: none;">
                                    Auto-submitting in <span id="submitCountdown">10</span>s...
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Add New History Sale end -->

        <!-- History Sales Table start -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>History Sales List</h5>
                </div>
                <div class="card-body">
                    <div class="dt-responsive table-responsive">
                        <table id="history-sales-table" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>No Resi</th>
                                    <th>SKU & Qty</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                    <th>Actions</th>
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
                    <input type="hidden" id="edit-history-sale-id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">No Resi</label>
                            <input type="text" class="form-control" id="edit-no_resi" name="no_resi" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">SKU & Quantity</label>
                            <div id="edit-sku-container">
                                <!-- SKU inputs will be added here -->
                            </div>
                            <button type="button" id="add-edit-sku-btn" class="btn btn-sm btn-outline-primary mt-2">
                                <i class="fas fa-plus"></i> Add SKU
                            </button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="updateHistorySaleBtn" class="btn btn-primary">Update</button>
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

    <!-- Sweet Alert -->
    <script src="{{ URL::asset('build/js/plugins/sweetalert2.all.min.js') }}"></script>

    <!-- Date picker -->
    <script src="{{ URL::asset('build/js/plugins/flatpickr.min.js') }}"></script>

    <script>
        // Tambahkan fungsi deleteHistorySale di luar $(document).ready
        function deleteHistorySale(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim request delete ke server
                    $.ajax({
                        url: `/history-sales/${id}`,
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire(
                                    'Deleted!',
                                    'History sale has been deleted.',
                                    'success'
                                );
                                // Reload DataTable
                                $('#history-sales-table').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire(
                                    'Error!',
                                    response.message || 'Failed to delete history sale.',
                                    'error'
                                );
                            }
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                xhr.responseJSON?.message || 'Failed to delete history sale.',
                                'error'
                            );
                        }
                    });
                }
            });
        }

        // Tambahkan fungsi editHistorySale di luar $(document).ready
        function editHistorySale(id) {
            // Ambil data history sale yang akan diedit
            $.ajax({
                url: `/history-sales/${id}/edit`,
                type: 'GET',
                success: function(response) {
                    const historySale = response.data;

                    // Set nilai form
                    $('#edit-history-sale-id').val(historySale.id);
                    $('#edit-no_resi').val(historySale.no_resi);

                    // Bersihkan container SKU yang lama
                    $('#edit-sku-container').empty();

                    // Tambahkan input SKU sesuai data
                    historySale.no_sku.forEach((sku, index) => {
                        const qty = historySale.qty[index];
                        const skuInput = `
                            <div class="sku-input-container">
                                <input type="text" class="form-control sku-input" name="no_sku[]" value="${sku}" required>
                                <input type="number" class="form-control qty-input" name="qty[]" value="${qty}" required min="1">
                                <button type="button" class="btn btn-outline-danger remove-edit-sku-btn">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        `;
                        $('#edit-sku-container').append(skuInput);
                    });

                    // Tampilkan modal
                    $('#editHistorySaleModal').modal('show');
                },
                error: function(xhr) {
                    Swal.fire(
                        'Error!',
                        'Failed to load history sale data.',
                        'error'
                    );
                }
            });
        }

        $(document).ready(function() {
            // Constants
            const SUBMIT_TIMEOUT = 10000; // 10 seconds for auto-submit
            const RESET_TIMEOUT = 3000; // 3 seconds for form reset
            const SCAN_DELAY = 200; // 200ms delay between scans
            const MIN_SKU_LENGTH = 10; // Minimum SKU length
            const NEW_FIELD_DELAY = 5000; // 5 seconds to wait for new SKU

            // State variables
            let submitTimer = null;
            let newFieldTimer = null;
            let lastScanTime = Date.now();
            let isProcessing = false;
            let hasValidResi = false;
            let countdownInterval = null;
            let countdownSeconds = 10;

            // Initialize DataTable
            initializeDataTable();

            // Setup AJAX CSRF
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Focus on no_resi initially
            resetForm();

            // Handle No Resi input
            $('#no_resi').on('input', function(e) {
                const noResi = $(this).val().trim();
                clearTimeout(submitTimer);
                $('#resiScanningIndicator').addClass('active');

                if (!noResi) {
                    hasValidResi = false;
                    return;
                }

                // Validate No Resi
                $.ajax({
                    url: '/validate-no-resi',
                    method: 'POST',
                    data: {
                        no_resi: noResi
                    },
                    success: function(response) {
                        if (response.valid) {
                            hasValidResi = true;
                            $('#resiError').hide();
                            // Enable first SKU input and focus after delay
                            setTimeout(() => {
                                $('.sku-input:first').prop('disabled', false).focus();
                                $('#resiScanningIndicator').removeClass('active');
                            }, SCAN_DELAY);
                        } else {
                            showError('resiError', 'No Resi already exists');
                            setTimeout(() => resetForm(), RESET_TIMEOUT);
                        }
                    },
                    error: function() {
                        showError('resiError', 'Error validating No Resi');
                        setTimeout(() => resetForm(), RESET_TIMEOUT);
                    }
                });
            });

            // Handle SKU inputs
            $(document).on('input', '.sku-input', function(e) {
                if (isProcessing || !hasValidResi) return;

                const input = $(this);
                const currentSku = input.val().trim();

                clearTimeout(submitTimer);
                clearTimeout(newFieldTimer);
                resetCountdown();

                if (!currentSku) return;

                $('#skuScanningIndicator').addClass('active');

                // Validate SKU length
                if (currentSku.length >= MIN_SKU_LENGTH) {
                    // Check for duplicates
                    if (isDuplicateSkuInForm(currentSku, input)) {
                        showError('skuError', 'Duplicate SKU detected');
                        setTimeout(() => resetForm(), RESET_TIMEOUT);
                        return;
                    }

                    // Start countdown for auto-submit
                    startCountdown();

                    // Set timer for possible new SKU
                    newFieldTimer = setTimeout(() => {
                        if (input.closest('.sku-input-container').is(':last-child')) {
                            addNewSkuField();
                        }
                    }, NEW_FIELD_DELAY);

                    // Set timer for auto-submit
                    submitTimer = setTimeout(() => {
                        submitForm();
                    }, SUBMIT_TIMEOUT);

                    $('#skuScanningIndicator').removeClass('active');
                }
            });

            // Reset button handler
            $('#resetScannerBtn').on('click', function() {
                resetForm();
            });

            // Prevent form submission on enter
            $('#historySaleForm').on('submit', function(e) {
                e.preventDefault();
            });

            // Helper Functions
            function showError(elementId, message) {
                $(`#${elementId}`).text(message).show();
            }

            function isDuplicateSkuInForm(sku, currentInput) {
                let duplicate = false;
                $('.sku-input').not(currentInput).each(function() {
                    if ($(this).val().trim() === sku) {
                        duplicate = true;
                        return false;
                    }
                });
                return duplicate;
            }

            function addNewSkuField() {
                const newSkuContainer = `
                    <div class="sku-input-container">
                        <input type="text" class="form-control scanner-input sku-input" name="no_sku[]" required>
                        <input type="number" class="form-control qty-input" name="qty[]" value="1" min="1">
                        <span class="scanning-indicator">
                            <i class="fas fa-circle-notch fa-spin"></i> Scanning...
                        </span>
                    </div>
                `;
                $('#sku-container').append(newSkuContainer);
                $('.sku-input:last').focus();
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
                countdownSeconds = 10;
                $('#submitCountdown').text(countdownSeconds);
                $('#autoSubmitTimer').show();

                countdownInterval = setInterval(() => {
                    countdownSeconds--;
                    $('#submitCountdown').text(countdownSeconds);
                    if (countdownSeconds <= 0) {
                        clearInterval(countdownInterval);
                    }
                }, 1000);
            }

            function resetCountdown() {
                clearInterval(countdownInterval);
                $('#autoSubmitTimer').hide();
            }

            async function submitForm() {
                if (isProcessing || !hasValidResi) return;

                // Validate if we have at least one valid SKU
                let hasValidData = false;
                $('.sku-input').each(function() {
                    const sku = $(this).val().trim();
                    if (sku && sku.length >= MIN_SKU_LENGTH) {
                        hasValidData = true;
                        return false;
                    }
                });

                if (!hasValidData) return;

                isProcessing = true;

                try {
                    const response = await $.ajax({
                        url: "{{ route('history-sales.store') }}",
                        type: "POST",
                        data: $('#historySaleForm').serialize(),
                        dataType: 'json'
                    });

                    if (response.status === 'success') {
                        // Show success message
                        Swal.fire({
                            title: 'Success!',
                            text: response.message,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        // Reload table and reset form
                        $('#history-sales-table').DataTable().ajax.reload(null, false);
                        setTimeout(() => resetForm(), 1500);
                    } else {
                        throw new Error(response.message);
                    }
                } catch (error) {
                    showError('skuError', error.message || 'Failed to save data');
                    setTimeout(() => resetForm(), RESET_TIMEOUT);
                } finally {
                    isProcessing = false;
                }
            }

            function initializeDataTable() {
                try {
                    $('#history-sales-table').DataTable({
                        processing: true,
                        serverSide: false,
                        ajax: {
                            url: "{{ route('history-sales.data') }}",
                            type: "POST"
                        },
                        columns: [{
                                data: 'no'
                            },
                            {
                                data: 'no_resi'
                            },
                            {
                                data: 'no_sku'
                            },
                            {
                                data: 'created_at'
                            },
                            {
                                data: 'updated_at'
                            },
                            {
                                data: 'actions'
                            }
                        ],
                        order: [
                            [3, 'desc']
                        ],
                        pageLength: 25
                    });
                } catch (e) {
                    console.error('Error initializing DataTable:', e);
                }
            }
        });
    </script>
@endsection
