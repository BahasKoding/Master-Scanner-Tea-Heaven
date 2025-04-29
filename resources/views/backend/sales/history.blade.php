@extends('layouts.main')

@section('title', 'History Sales')
@section('breadcrumb-item', $item)

@section('css')
    <!-- [Page specific CSS] start -->
    <!-- data tables css -->
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/select.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/autoFill.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/keyTable.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/buttons.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css">
    <!-- Date picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Sweet Alert -->
    <link href="{{ URL::asset('build/css/plugins/animate.min.css') }}" rel="stylesheet" type="text/css">
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
                                <input type="text" class="form-control scanner-input scanner-active" id="no_resi"
                                    name="no_resi" required autofocus>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No SKU <span class="text-danger">*</span></label>
                                <div id="sku-container">
                                    <div class="sku-input-container">
                                        <input type="text" class="form-control scanner-input sku-input" name="no_sku[]"
                                            required>
                                        <input type="hidden" class="qty-input" name="qty[]" value="1">
                                        <button type="button" class="btn btn-outline-secondary add-sku-btn"><i
                                                class="fas fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="button" id="saveHistorySaleBtn" class="btn btn-primary">Save History
                                    Sale</button>
                                <button type="button" id="resetScannerBtn" class="btn btn-warning">Reset Scanner</button>
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
    <!-- [Page Specific JS] start -->
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- jQuery Toast Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <!-- DataTables Buttons -->
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.colVis.min.js"></script>
    <!-- Date picker -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- Sweet Alert -->
    <script src="{{ URL::asset('build/js/plugins/sweetalert2.all.min.js') }}"></script>

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
            // Initialize date pickers
            const startDatePicker = flatpickr("#start-date", {
                dateFormat: "Y-m-d",
                maxDate: "today"
            });

            const endDatePicker = flatpickr("#end-date", {
                dateFormat: "Y-m-d",
                maxDate: "today"
            });

            // Initialize DataTable with try-catch
            try {
                var table = $('#history-sales-table').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: {
                        url: "{{ route('history-sales.data') }}",
                        type: "POST",
                        data: function(d) {
                            d._token = "{{ csrf_token() }}";
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
                            name: 'no_sku'
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
                        [3, 'desc']
                    ],
                    pageLength: 25,
                    dom: 'Blfrtip',
                    buttons: [{
                            extend: 'copyHtml5',
                            text: '<i class="fas fa-copy"></i> Copy',
                            className: 'btn btn-secondary',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4]
                            }
                        },
                        {
                            extend: 'csvHtml5',
                            text: '<i class="fas fa-file-csv"></i> CSV',
                            className: 'btn btn-primary',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4]
                            }
                        },
                        {
                            extend: 'excelHtml5',
                            text: '<i class="fas fa-file-excel"></i> Excel',
                            className: 'btn btn-success',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4]
                            }
                        },
                        {
                            extend: 'print',
                            text: '<i class="fas fa-print"></i> Print',
                            className: 'btn btn-info',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4]
                            }
                        }
                    ],
                    drawCallback: function() {
                        // Reinitialize event handlers jika diperlukan
                        $('[data-bs-toggle="tooltip"]').tooltip();
                    }
                });

                // Simpan referensi table ke window object agar bisa diakses global
                window.historySalesTable = table;

            } catch (e) {
                console.error('Error initializing DataTable:', e);
                $('#history-sales-table tbody').html(
                    '<tr><td colspan="6" class="text-center text-danger">Error initializing table. Please try refreshing the page.</td></tr>'
                );
            }

            // Setup AJAX untuk include CSRF token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Konstanta untuk timing
            const SUBMIT_TIMEOUT = 5000; // 5 detik untuk auto-submit
            const RESET_TIMEOUT = 3000; // 3 detik untuk reset form
            const SCAN_DELAY = 200; // 200ms delay antar scan
            const MIN_SKU_LENGTH = 10; // Minimal panjang SKU sebelum bisa tambah field baru
            const NEW_FIELD_DELAY = 1000; // 1 detik delay sebelum tambah field baru

            let submitTimer = null;
            let newFieldTimer = null;
            let lastScanTime = Date.now();
            let isProcessing = false;
            let lastSkuLength = 0; // Untuk track perubahan panjang SKU
            let hasValidSku = false; // Track apakah ada SKU valid

            // Inisialisasi awal: focus ke no_resi
            $(document).ready(function() {
                resetForm();
            });

            // Handler untuk no_resi input
            $('#no_resi').on('input', function(e) {
                const noResi = $(this).val().trim();

                clearTimeout(submitTimer);

                if (!noResi) return;

                // Setelah no_resi terisi, pindah ke SKU
                setTimeout(() => {
                    $('.sku-input:last').focus();
                }, SCAN_DELAY);
            });

            // Handler untuk SKU inputs
            $(document).on('input', '.sku-input', function(e) {
                if (isProcessing) return;

                const input = $(this);
                const currentSku = input.val().trim();

                clearTimeout(submitTimer);
                clearTimeout(newFieldTimer);

                if (!currentSku) {
                    // Jika field kosong, tetap bisa submit jika ada SKU valid lainnya
                    if (hasValidSkuInForm()) {
                        submitTimer = setTimeout(() => {
                            submitForm();
                        }, SUBMIT_TIMEOUT);
                    }
                    return;
                }

                // Cek jika panjang SKU bertambah (menandakan masih scanning)
                if (currentSku.length > lastSkuLength) {
                    lastSkuLength = currentSku.length;

                    // Jika SKU sudah cukup panjang
                    if (currentSku.length >= MIN_SKU_LENGTH) {
                        // Cek duplikasi
                        if (isDuplicateSkuInForm(currentSku, input)) {
                            showError('SKU sudah ada dalam form ini');
                            return;
                        }

                        hasValidSku = true;

                        // Tambah field baru jika ini adalah field terakhir
                        if (input.closest('.sku-input-container').is(':last-child')) {
                            addNewSkuField();
                        }

                        // Set timer untuk submit
                        submitTimer = setTimeout(() => {
                            submitForm();
                        }, SUBMIT_TIMEOUT);

                        lastScanTime = Date.now();
                    }
                }
            });

            // Fungsi untuk cek apakah ada SKU valid dalam form
            function hasValidSkuInForm() {
                let valid = false;
                $('.sku-input').each(function() {
                    const sku = $(this).val().trim();
                    if (sku && sku.length >= MIN_SKU_LENGTH) {
                        valid = true;
                        return false; // break loop
                    }
                });
                return valid;
            }

            // Fungsi untuk menampilkan error dan reset form
            function showError(message) {
                Swal.fire({
                    title: 'Error!',
                    text: message,
                    icon: 'error',
                    timer: 2000,
                    showConfirmButton: false
                });
                setTimeout(() => resetForm(), RESET_TIMEOUT);
            }

            // Fungsi untuk cek duplikasi SKU dalam form yang sama
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

            // Fungsi untuk submit form
            async function submitForm() {
                if (isProcessing) return;

                // Validasi apakah ada minimal satu SKU valid
                let hasValidData = false;
                $('.sku-input').each(function() {
                    const sku = $(this).val().trim();
                    if (sku && sku.length >= MIN_SKU_LENGTH) {
                        hasValidData = true;
                        return false; // break loop
                    }
                });

                if (!hasValidData) {
                    return; // Jangan submit jika tidak ada SKU valid
                }

                isProcessing = true;

                try {
                    // Validasi final
                    const noResi = $('#no_resi').val().trim();
                    if (!noResi) {
                        throw new Error('No Resi harus diisi');
                    }

                    // Submit via AJAX
                    const response = await $.ajax({
                        url: "{{ route('history-sales.store') }}",
                        type: "POST",
                        data: $('#historySaleForm').serialize(),
                        dataType: 'json'
                    });

                    if (response.status === 'success') {
                        Swal.fire({
                            title: 'Success!',
                            text: response.message,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        // Reload table dan reset form
                        $('#history-sales-table').DataTable().ajax.reload(null, false);
                        setTimeout(() => resetForm(), 1500);
                    } else {
                        throw new Error(response.message);
                    }
                } catch (error) {
                    console.error('Submit error:', error);
                    showError(error.message || 'Gagal menyimpan data');
                } finally {
                    isProcessing = false;
                }
            }

            // Fungsi untuk reset form
            function resetForm() {
                // Clear timer
                clearTimeout(submitTimer);
                clearTimeout(newFieldTimer);

                // Reset form fields
                $('#no_resi').val('');
                $('.sku-input-container:not(:first)').remove();
                $('.sku-input:first').val('');
                $('.qty-input:first').val('1');

                // Reset state
                isProcessing = false;
                lastScanTime = Date.now();
                lastSkuLength = 0;
                hasValidSku = false;

                // Set focus ke no_resi
                $('#no_resi').focus();
            }

            // Fungsi untuk menambah field SKU baru
            function addNewSkuField() {
                if (isProcessing) return;

                // Cek apakah field terakhir sudah terisi
                const lastField = $('.sku-input:last');
                const lastSku = lastField.val().trim();

                // Hanya tambah field baru jika field terakhir sudah terisi
                if (lastSku && lastSku.length >= MIN_SKU_LENGTH) {
                    const newSkuContainer = `
                        <div class="sku-input-container">
                            <input type="text" class="form-control scanner-input sku-input" name="no_sku[]" required>
                            <input type="hidden" class="qty-input" name="qty[]" value="1">
                        </div>
                    `;
                    $('#sku-container').append(newSkuContainer);

                    // Focus ke field baru setelah delay singkat
                    setTimeout(() => {
                        $('.sku-input:last').focus();
                    }, 100);
                }

                lastSkuLength = 0; // Reset tracking panjang SKU untuk field baru
            }

            // Reset button handler
            $('#resetScannerBtn').on('click', function() {
                resetForm();
            });

            // Prevent form submission on enter
            $('#historySaleForm').on('submit', function(e) {
                e.preventDefault();
            });

            // Tambah SKU baru di form edit
            $('#add-edit-sku-btn').on('click', function() {
                const newSkuInput = `
                    <div class="sku-input-container">
                        <input type="text" class="form-control sku-input" name="no_sku[]" required>
                        <input type="number" class="form-control qty-input" name="qty[]" value="1" required min="1">
                        <button type="button" class="btn btn-outline-danger remove-edit-sku-btn">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                `;
                $('#edit-sku-container').append(newSkuInput);
            });

            // Hapus SKU di form edit
            $(document).on('click', '.remove-edit-sku-btn', function() {
                if ($('#edit-sku-container .sku-input-container').length > 1) {
                    $(this).closest('.sku-input-container').remove();
                } else {
                    Swal.fire(
                        'Warning!',
                        'At least one SKU is required.',
                        'warning'
                    );
                }
            });

            // Modifikasi fungsi untuk submit form edit
            $('#updateHistorySaleBtn').on('click', function() {
                const id = $('#edit-history-sale-id').val();

                // Validasi form
                if (!$('#edit-no_resi').val()) {
                    Swal.fire('Error!', 'No Resi is required', 'error');
                    return;
                }

                // Validasi duplikasi SKU di form edit
                const skus = [];
                let hasDuplicate = false;
                let duplicateSku = '';

                $('#edit-sku-container .sku-input').each(function() {
                    const sku = $(this).val().trim();
                    if (sku !== '') {
                        if (skus.includes(sku)) {
                            hasDuplicate = true;
                            duplicateSku = sku;
                            return false;
                        }
                        skus.push(sku);
                    }
                });

                if (hasDuplicate) {
                    Swal.fire('Error!', 'Duplicate SKU detected: ' + duplicateSku, 'error');
                    return;
                }

                if (skus.length === 0) {
                    Swal.fire('Error!', 'At least one SKU is required', 'error');
                    return;
                }

                // Submit form via AJAX
                $.ajax({
                    url: `/history-sales/${id}`,
                    type: 'PUT',
                    data: $('#editHistorySaleForm').serialize(),
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire(
                                'Success!',
                                'History sale has been updated.',
                                'success'
                            );

                            // Tutup modal
                            $('#editHistorySaleModal').modal('hide');

                            // Reload DataTable
                            $('#history-sales-table').DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire(
                                'Error!',
                                response.message || 'Failed to update history sale.',
                                'error'
                            );
                        }
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error!',
                            xhr.responseJSON?.message || 'Failed to update history sale.',
                            'error'
                        );
                    }
                });
            });
        });
    </script>
    <!-- [Page Specific JS] end -->
