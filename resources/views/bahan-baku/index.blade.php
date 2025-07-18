@extends('layouts.main')

@section('title', 'Manajemen Bahan Baku')
@section('breadcrumb-item', 'Bahan Baku')

@section('css')
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- [Page specific CSS] start -->
    <!-- data tables css -->
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/datatables/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/datatables/buttons.bootstrap5.min.css') }}">
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

        /* SKU Induk Warning styling */
        #sku-induk-warning,
        #edit-sku-induk-warning {
            font-weight: 500;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 6px;
            padding: 12px 15px;
            margin-top: 8px;
            animation: fadeIn 0.3s ease-in;
            color: #721c24;
            font-size: 0.9rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #dc3545;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Disabled button styling */
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
@endsection

@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">
        <!-- Bahan Baku Table start -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Daftar Bahan Baku</h5>
                        <div>
                            <button id="clear-filters" class="btn btn btn-secondary">
                                <i class="fas fa-filter"></i> Hapus Filter
                            </button>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addBahanBakuModal">
                                <i class="fas fa-plus"></i> Tambah Bahan Baku Baru
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="mb-3 p-3 border rounded bg-light">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="filter-kategori" class="form-label small">Kategori</label>
                                <select class="form-control form-control-sm" name="filter-kategori" id="filter-kategori">
                                    <option value="">Semua Kategori</option>
                                    @foreach ($kategoriOptions as $key => $kategori)
                                        <option value="{{ $key }}">{{ $kategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filter-sku-induk" class="form-label small">SKU Induk</label>
                                <input type="text" id="filter-sku-induk" class="form-control form-control-sm"
                                    placeholder="Filter berdasarkan SKU Induk">
                            </div>
                            <div class="col-md-3">
                                <label for="filter-nama-barang" class="form-label small">Nama Barang</label>
                                <input type="text" id="filter-nama-barang" class="form-control form-control-sm"
                                    placeholder="Filter berdasarkan nama">
                            </div>
                            <div class="col-md-3">
                                <label for="filter-satuan" class="form-label small">Satuan</label>
                                <select id="filter-satuan" class="form-select form-select-sm">
                                    <option value="">Semua Satuan</option>
                                    <option value="PCS">PCS</option>
                                    <option value="GRAM">GRAM</option>
                                    <option value="KG">KG</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- End Filter Section -->

                    <div class="dt-responsive table-responsive">
                        <table id="bahan-baku-table" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kategori</th>
                                    <th>SKU Induk</th>
                                    <th>Nama Barang</th>
                                    <th>Satuan</th>
                                    <th>Aksi</th>
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
        <!-- Bahan Baku Table end -->
    </div>
    <!-- [ Main Content ] end -->

    <!-- Add Bahan Baku Modal -->
    <div class="modal fade" id="addBahanBakuModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Bahan Baku Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addBahanBakuForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select class="form-control" name="kategori" id="add-kategori" required>
                                <option value="">Pilih Kategori</option>
                                @foreach ($kategoriOptions as $key => $kategori)
                                    <option value="{{ $key }}">{{ $kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">SKU Induk <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="sku_induk" id="add_sku_induk" required>
                            <div class="invalid-feedback" id="error-sku_induk"></div>
                            <div class="text-danger mt-1" id="sku-induk-warning" style="display: none;"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_barang" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Satuan <span class="text-danger">*</span></label>
                            <select class="form-select" name="satuan" id="add-satuan" required>
                                <option value="">Pilih Satuan</option>
                                <option value="PCS">PCS</option>
                                <option value="GRAM">GRAM</option>
                                <option value="KG">KG</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Bahan Baku Modal -->
    <div class="modal fade" id="editBahanBakuModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Bahan Baku</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editBahanBakuForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_bahan_baku_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select class="form-control" name="kategori" id="edit-kategori" required>
                                <option value="">Pilih Kategori</option>
                                @foreach ($kategoriOptions as $key => $kategori)
                                    <option value="{{ $key }}">{{ $kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">SKU Induk <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="sku_induk" id="edit_sku_induk" required>
                            <div class="invalid-feedback" id="error-edit-sku_induk"></div>
                            <div class="text-danger mt-1" id="edit-sku-induk-warning" style="display: none;"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_barang" id="edit_nama_barang"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Satuan <span class="text-danger">*</span></label>
                            <select class="form-select" name="satuan" id="edit_satuan" required>
                                <option value="">Pilih Satuan</option>
                                <option value="PCS">PCS</option>
                                <option value="GRAM">GRAM</option>
                                <option value="KG">KG</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Perbarui</button>
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

    <!-- Choices JS -->
    <script src="{{ URL::asset('build/js/plugins/choices.min.js') }}"></script>

    <!-- SweetAlert2 -->
    <script src="{{ URL::asset('build/js/plugins/sweetalert2.all.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            // Debounce function to limit how often a function can trigger
            function debounce(func, wait) {
                let timeout;
                return function() {
                    const context = this;
                    const args = arguments;
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(context, args), wait);
                };
            }

            // Function to check SKU Induk availability
            function checkSkuIndukAvailability(skuInduk, bahanBakuId = null, warningElementId, inputElementId,
                formType) {
                if (!skuInduk || skuInduk.length < 2) {
                    $(warningElementId).hide();
                    enableSaveButtons(formType);
                    return;
                }

                $.ajax({
                    url: "{{ route('bahan-baku.check-sku-induk') }}",
                    method: 'POST',
                    data: {
                        sku_induk: skuInduk,
                        bahan_baku_id: bahanBakuId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.exists) {
                            // Show warning message
                            $(warningElementId).text(
                                `⚠️ SKU Induk "${skuInduk}" sudah digunakan. Silahkan gunakan SKU Induk yang berbeda.`
                            ).show();

                            // Disable save buttons
                            disableSaveButtons(formType);

                            // Show toast notification
                            Swal.fire({
                                title: 'SKU Induk Sudah Digunakan!',
                                text: `SKU Induk "${skuInduk}" sudah ada di database. Silahkan gunakan SKU Induk lain.`,
                                icon: 'warning',
                                timer: 4000,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });
                        } else {
                            $(warningElementId).hide();
                            enableSaveButtons(formType);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error checking SKU Induk:', error);
                        $(warningElementId).hide();
                        enableSaveButtons(formType);
                    }
                });
            }

            // Function to disable save buttons
            function disableSaveButtons(formType) {
                if (formType === 'add') {
                    $('#addBahanBakuForm button[type="submit"]').prop('disabled', true);
                } else if (formType === 'edit') {
                    $('#editBahanBakuForm button[type="submit"]').prop('disabled', true);
                }
            }

            // Function to enable save buttons
            function enableSaveButtons(formType) {
                if (formType === 'add') {
                    $('#addBahanBakuForm button[type="submit"]').prop('disabled', false);
                } else if (formType === 'edit') {
                    $('#editBahanBakuForm button[type="submit"]').prop('disabled', false);
                }
            }

            // Debounced function for add form
            const debouncedCheckSkuIndukAdd = debounce(function(skuInduk) {
                checkSkuIndukAvailability(skuInduk, null, '#sku-induk-warning', '#add_sku_induk', 'add');
            }, 500);

            // Debounced function for edit form
            const debouncedCheckSkuIndukEdit = debounce(function(skuInduk, bahanBakuId) {
                checkSkuIndukAvailability(skuInduk, bahanBakuId, '#edit-sku-induk-warning',
                    '#edit_sku_induk', 'edit');
            }, 500);

            // Initialize Choices.js for add form
            var addKategoriChoices = new Choices('#add-kategori', {
                searchEnabled: true,
                searchPlaceholderValue: "Cari kategori",
                itemSelectText: '',
                placeholder: true,
                placeholderValue: "Pilih kategori",
                allowHTML: false
            });

            var addSatuanChoices = new Choices('#add-satuan', {
                searchEnabled: true,
                searchPlaceholderValue: "Cari satuan",
                itemSelectText: '',
                placeholder: true,
                placeholderValue: "Pilih satuan",
                allowHTML: false
            });

            // Initialize Choices.js for edit form
            var editKategoriChoices = new Choices('#edit-kategori', {
                searchEnabled: true,
                searchPlaceholderValue: "Cari kategori",
                itemSelectText: '',
                placeholder: true,
                placeholderValue: "Pilih kategori",
                allowHTML: false
            });

            var editSatuanChoices = new Choices('#edit_satuan', {
                searchEnabled: true,
                searchPlaceholderValue: "Cari satuan",
                itemSelectText: '',
                placeholder: true,
                placeholderValue: "Pilih satuan",
                allowHTML: false
            });

            // Initialize DataTable
            var table = $('#bahan-baku-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('bahan-baku.index') }}",
                    type: "GET",
                    data: function(d) {
                        d.kategori = $('#filter-kategori').val();
                        d.sku_induk = $('#filter-sku-induk').val();
                        d.nama_barang = $('#filter-nama-barang').val();
                        d.satuan = $('#filter-satuan').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'kategori',
                        name: 'kategori'
                    },
                    {
                        data: 'sku_induk',
                        name: 'sku_induk'
                    },
                    {
                        data: 'nama_barang',
                        name: 'nama_barang'
                    },
                    {
                        data: 'satuan',
                        name: 'satuan'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <button type="button" class="btn btn-sm btn-warning edit-btn" data-id="${row.id}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="${row.id}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            `;
                        }
                    }
                ],
                order: [
                    [1, 'asc']
                ],
                pageLength: 25,
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'copy',
                        text: '<i class="fas fa-copy"></i> Salin',
                        className: 'btn btn-secondary'
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-success'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Cetak',
                        className: 'btn btn-info'
                    }
                ]
            });

            // Apply filters when dropdown values change
            $('#filter-kategori, #filter-satuan').on('change', function() {
                table.draw();
            });

            // Apply filters when text inputs change with debounce (300ms delay)
            $('#filter-sku-induk, #filter-nama-barang').on('keyup', debounce(function() {
                table.draw();
            }, 300));

            // Clear filters button
            $('#clear-filters').on('click', function() {
                $('#filter-kategori, #filter-satuan').val('');
                $('#filter-sku-induk, #filter-nama-barang').val('');
                table.draw();
            });

            // SKU Induk checking for Add form
            $('#add_sku_induk').on('input', function() {
                const skuInduk = $(this).val().trim();

                // Clear warning if user clears the input
                if (skuInduk === '') {
                    $('#sku-induk-warning').hide();
                    enableSaveButtons('add');
                }

                debouncedCheckSkuIndukAdd(skuInduk);
            });

            // SKU Induk checking for Edit form
            $('#edit_sku_induk').on('input', function() {
                const skuInduk = $(this).val().trim();
                const bahanBakuId = $('#edit_bahan_baku_id').val();

                // Clear warning if user clears the input
                if (skuInduk === '') {
                    $('#edit-sku-induk-warning').hide();
                    enableSaveButtons('edit');
                }

                debouncedCheckSkuIndukEdit(skuInduk, bahanBakuId);
            });

            // Add Bahan Baku Form Submit
            $('#addBahanBakuForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var submitButton = form.find('button[type="submit"]');

                // Check if SKU Induk warning is visible (means duplicate SKU Induk)
                if ($('#sku-induk-warning').is(':visible')) {
                    Swal.fire({
                        title: 'SKU Induk Tidak Valid!',
                        text: 'Tidak dapat menyimpan karena SKU Induk sudah digunakan. Silahkan ganti SKU Induk terlebih dahulu.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                // Disable submit button to prevent double submission
                submitButton.prop('disabled', true);

                var formData = new FormData(this);

                $.ajax({
                    url: "{{ route('bahan-baku.store') }}",
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (data.success) {
                            // Reset form and choices
                            form[0].reset();
                            addKategoriChoices.setChoiceByValue('');
                            addSatuanChoices.setChoiceByValue('');
                            submitButton.prop('disabled', false);

                            // Hide modal properly
                            $('#addBahanBakuModal').modal('hide');

                            // Reload table
                            table.ajax.reload(null, false);

                            // Show success message
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });
                        }
                    },
                    error: function(xhr) {
                        // Re-enable submit button on error
                        submitButton.prop('disabled', false);

                        if (xhr.status === 422) {
                            // Validation errors
                            const errors = xhr.responseJSON.errors;
                            const errorMessages = Object.values(errors).flat();

                            Swal.fire({
                                title: 'Mohon Periksa Input Anda',
                                html: errorMessages.join('<br>'),
                                icon: 'warning',
                                confirmButtonText: 'Saya Mengerti',
                                confirmButtonColor: '#3085d6'
                            });
                        } else {
                            // Other errors
                            Swal.fire({
                                title: 'Oops...',
                                text: xhr.responseJSON?.message ||
                                    'Terjadi kesalahan pada permintaan.',
                                icon: 'error',
                                confirmButtonText: 'Coba Lagi',
                                confirmButtonColor: '#3085d6'
                            });
                        }
                    }
                });
            });

            // Edit Button Click
            $(document).on('click', '.edit-btn', function() {
                var id = $(this).data('id');
                // Debug log removed for production

                // Show loading state
                Swal.fire({
                    title: 'Memuat...',
                    text: 'Mengambil data bahan baku',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: "{{ url('bahan-baku') }}/" + id + "/edit",
                    method: 'GET',
                    success: function(response) {
                        console.log('Server response:', response);
                        Swal.close();

                        if (response.success) {
                            const data = response.data;
                            console.log('Bahan baku data:', data);

                            // Set hidden ID
                            $('#edit_bahan_baku_id').val(data.id);

                            // Set kategori with Choices.js
                            editKategoriChoices.setChoiceByValue(data.kategori.toString());

                            // Set SKU induk
                            $('#edit_sku_induk').val(data.sku_induk);

                            // Set nama barang
                            $('#edit_nama_barang').val(data.nama_barang);

                            // Set satuan with Choices.js
                            editSatuanChoices.setChoiceByValue(data.satuan);

                            // Log the current values
                            console.log('Form values after setting:', {
                                id: $('#edit_bahan_baku_id').val(),
                                kategori: $('#edit-kategori').val(),
                                sku_induk: $('#edit_sku_induk').val(),
                                nama_barang: $('#edit_nama_barang').val(),
                                satuan: $('#edit_satuan').val()
                            });

                            // Show the modal
                            var editModal = new bootstrap.Modal(document.getElementById(
                                'editBahanBakuModal'));
                            editModal.show();

                            // Double check values after modal is shown
                            setTimeout(() => {
                                console.log('Values after modal shown:', {
                                    id: $('#edit_bahan_baku_id').val(),
                                    kategori: $('#edit-kategori').val(),
                                    sku_induk: $('#edit_sku_induk').val(),
                                    nama_barang: $('#edit_nama_barang').val(),
                                    satuan: $('#edit_satuan').val()
                                });
                            }, 500);
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.message ||
                                    'Gagal mengambil data bahan baku',
                                icon: 'error'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', {
                            xhr: xhr,
                            status: status,
                            error: error
                        });
                        Swal.fire({
                            title: 'Error',
                            text: 'Gagal mengambil data bahan baku. Silahkan coba lagi.',
                            icon: 'error'
                        });
                    }
                });
            });

            // Edit Bahan Baku Form Submit
            $('#editBahanBakuForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var submitButton = form.find('button[type="submit"]');
                var id = $('#edit_bahan_baku_id').val();

                // Check if SKU Induk warning is visible (means duplicate SKU Induk)
                if ($('#edit-sku-induk-warning').is(':visible')) {
                    Swal.fire({
                        title: 'SKU Induk Tidak Valid!',
                        text: 'Tidak dapat menyimpan karena SKU Induk sudah digunakan. Silahkan ganti SKU Induk terlebih dahulu.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                // Disable submit button to prevent double submission
                submitButton.prop('disabled', true);

                // Show loading state
                Swal.fire({
                    title: 'Memperbarui...',
                    text: 'Menyimpan data bahan baku',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                var formData = new FormData(this);
                formData.append('_method', 'PUT');

                $.ajax({
                    url: `/bahan-baku/${id}`,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        if (data.success) {
                            // Reset form and close modal
                            form[0].reset();
                            editKategoriChoices.setChoiceByValue('');
                            editSatuanChoices.setChoiceByValue('');
                            $('#editBahanBakuModal').modal('hide');

                            // Reload table
                            table.ajax.reload(null, false);

                            // Show success message
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });
                        }
                    },
                    error: function(xhr) {
                        submitButton.prop('disabled', false);

                        if (xhr.status === 422) {
                            // Validation errors
                            const errors = xhr.responseJSON.errors;
                            const errorMessages = Object.values(errors).flat();

                            Swal.fire({
                                title: 'Mohon Periksa Input Anda',
                                html: errorMessages.join('<br>'),
                                icon: 'warning',
                                confirmButtonText: 'Saya Mengerti',
                                confirmButtonColor: '#3085d6'
                            });
                        } else {
                            // Other errors
                            Swal.fire({
                                title: 'Gagal Memperbarui',
                                text: xhr.responseJSON?.message ||
                                    'Tidak dapat memperbarui bahan baku saat ini.',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#3085d6'
                            });
                        }
                    },
                    complete: function() {
                        submitButton.prop('disabled', false);
                    }
                });
            });

            // Delete Button Click
            $(document).on('click', '.delete-btn', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: "Apakah Anda yakin ingin menghapus bahan baku ini? Tindakan ini tidak dapat dibatalkan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/bahan-baku/${id}`,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            success: function(data) {
                                if (data.success) {
                                    table.ajax.reload();
                                    Swal.fire({
                                        title: 'Terhapus!',
                                        text: data.message,
                                        icon: 'success',
                                        timer: 1500,
                                        showConfirmButton: false,
                                        toast: true,
                                        position: 'top-end'
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: 'Gagal Menghapus',
                                    text: xhr.responseJSON?.message ||
                                        'Tidak dapat menghapus bahan baku saat ini.',
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#3085d6'
                                });
                            }
                        });
                    }
                });
            });

            // Clean up modal when it's hidden
            $('#editBahanBakuModal').on('hidden.bs.modal', function() {
                console.log('Modal hidden - cleaning up');
                $(this).find('form')[0].reset();
                editKategoriChoices.setChoiceByValue('');
                editSatuanChoices.setChoiceByValue('');

                // Clear SKU Induk warnings
                $('#edit-sku-induk-warning').hide();

                // Re-enable buttons
                $(this).find('button').prop('disabled', false);
            });

            // Clean up add modal when it's hidden
            $('#addBahanBakuModal').on('hidden.bs.modal', function() {
                console.log('Add modal hidden - cleaning up');
                $(this).find('form')[0].reset();
                addKategoriChoices.setChoiceByValue('');
                addSatuanChoices.setChoiceByValue('');

                // Clear SKU Induk warnings
                $('#sku-induk-warning').hide();

                // Re-enable buttons
                $(this).find('button').prop('disabled', false);
            });

            // Prevent form reset when modal is shown
            $('#editBahanBakuModal').on('show.bs.modal', function() {
                console.log('Modal showing - preventing auto reset');
                return true;
            });
        });
    </script>
@endsection
