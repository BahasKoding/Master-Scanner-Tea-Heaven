@extends('layouts.main')

@section('title', 'Manajemen Catatan Produksi')
@section('breadcrumb-item', 'Catatan Produksi')

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

        .array-inputs .btn-remove {
            margin-top: 31px;
        }

        .array-container {
            padding: 10px;
            border: 1px solid #eee;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .pair-inputs {
            display: flex;
            gap: 10px;
        }
    </style>
@endsection

@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">
        <!-- Catatan Produksi Table start -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Daftar Catatan Produksi</h5>
                        <div>
                            <button id="clear-filters" class="btn btn btn-secondary">
                                <i class="fas fa-filter"></i> Hapus Filter
                            </button>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addProduksiModal">
                                <i class="fas fa-plus"></i> Tambah Catatan Produksi Baru
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="mb-3 p-3 border rounded bg-light">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="filter-sku" class="form-label small">SKU Produk</label>
                                <input type="text" id="filter-sku" class="form-control form-control-sm"
                                    placeholder="Filter berdasarkan SKU">
                            </div>
                            <div class="col-md-4">
                                <label for="filter-nama" class="form-label small">Nama Produk</label>
                                <input type="text" id="filter-nama" class="form-control form-control-sm"
                                    placeholder="Filter berdasarkan nama">
                            </div>
                            <div class="col-md-4">
                                <label for="filter-packaging" class="form-label small">Packaging</label>
                                <input type="text" id="filter-packaging" class="form-control form-control-sm"
                                    placeholder="Filter berdasarkan packaging">
                            </div>
                        </div>
                    </div>
                    <!-- End Filter Section -->

                    <div class="dt-responsive table-responsive">
                        <table id="produksi-table" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>SKU Produk</th>
                                    <th>Nama Produk</th>
                                    <th>Packaging</th>
                                    <th>Quantity</th>
                                    <th>SKU Induk</th>
                                    <th>Gramasi</th>
                                    <th>Total Terpakai</th>
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
        <!-- Catatan Produksi Table end -->
    </div>
    <!-- [ Main Content ] end -->

    <!-- Add Produksi Modal -->
    <div class="modal fade" id="addProduksiModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Catatan Produksi Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addProduksiForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">SKU Produk <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="sku_product" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama_product" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Packaging <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="packaging" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="quantity" required min="1">
                            </div>
                        </div>

                        <div class="form-section">
                            <h6>SKU Induk dan Gramasi</h6>
                            <div id="add-sku-gramasi-container">
                                <div class="array-container">
                                    <div class="row array-inputs">
                                        <div class="col-md-5 mb-3">
                                            <label class="form-label">SKU Induk <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control sku-induk-input" name="sku_induk[]"
                                                required>
                                        </div>
                                        <div class="col-md-5 mb-3">
                                            <label class="form-label">Gramasi <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control gramasi-input" name="gramasi[]"
                                                required min="1">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger btn-remove">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="add-more-sku" class="btn btn-secondary">
                                <i class="fas fa-plus"></i> Tambah SKU Induk
                            </button>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Total Terpakai <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="total_terpakai" required>
                            </div>
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

    <!-- Edit Produksi Modal -->
    <div class="modal fade" id="editProduksiModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Catatan Produksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editProduksiForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_produksi_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">SKU Produk <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="sku_product" id="edit_sku_product"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama_product" id="edit_nama_product"
                                    required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Packaging <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="packaging" id="edit_packaging"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="quantity" id="edit_quantity" required
                                    min="1">
                            </div>
                        </div>

                        <div class="form-section">
                            <h6>SKU Induk dan Gramasi</h6>
                            <div id="edit-sku-gramasi-container">
                                <!-- Akan diisi secara dinamis saat edit -->
                            </div>
                            <button type="button" id="edit-add-more-sku" class="btn btn-secondary">
                                <i class="fas fa-plus"></i> Tambah SKU Induk
                            </button>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Total Terpakai <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="total_terpakai"
                                    id="edit_total_terpakai" required>
                            </div>
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

            // Template function for SKU and Gramasi inputs
            function getSkuGramasiTemplate(isEdit = false) {
                const prefix = isEdit ? 'edit-' : '';
                return `
                    <div class="array-container">
                        <div class="row array-inputs">
                            <div class="col-md-5 mb-3">
                                <label class="form-label">SKU Induk <span class="text-danger">*</span></label>
                                <input type="text" class="form-control sku-induk-input" name="sku_induk[]" required>
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="form-label">Gramasi <span class="text-danger">*</span></label>
                                <input type="number" class="form-control gramasi-input" name="gramasi[]" required min="1">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-remove">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }

            // Add more SKU Induk fields
            $('#add-more-sku').on('click', function() {
                $('#add-sku-gramasi-container').append(getSkuGramasiTemplate());
            });

            $('#edit-add-more-sku').on('click', function() {
                $('#edit-sku-gramasi-container').append(getSkuGramasiTemplate(true));
            });

            // Remove SKU Induk field
            $(document).on('click', '.btn-remove', function() {
                if ($(this).closest('form').find('.array-container').length > 1) {
                    $(this).closest('.array-container').remove();
                } else {
                    Swal.fire({
                        title: 'Informasi',
                        text: 'Harus ada minimal satu SKU Induk dan Gramasi',
                        icon: 'info',
                        timer: 1500,
                        showConfirmButton: false,
                    });
                }
            });

            // Initialize DataTable
            var table = $('#produksi-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('catatan-produksi.index') }}",
                    type: "GET",
                    data: function(d) {
                        d.sku_product = $('#filter-sku').val();
                        d.nama_product = $('#filter-nama').val();
                        d.packaging = $('#filter-packaging').val();
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
                        data: 'sku_product',
                        name: 'sku_product'
                    },
                    {
                        data: 'nama_product',
                        name: 'nama_product'
                    },
                    {
                        data: 'packaging',
                        name: 'packaging'
                    },
                    {
                        data: 'quantity',
                        name: 'quantity'
                    },
                    {
                        data: 'sku_induk',
                        name: 'sku_induk'
                    },
                    {
                        data: 'gramasi',
                        name: 'gramasi'
                    },
                    {
                        data: 'total_terpakai',
                        name: 'total_terpakai'
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

            // Apply filters when text inputs change with debounce (300ms delay)
            $('#filter-sku, #filter-nama, #filter-packaging').on('keyup', debounce(function() {
                table.ajax.reload();
            }, 300));

            // Clear filters function
            $('#clear-filters').on('click', function() {
                $('#filter-sku, #filter-nama, #filter-packaging').val('');
                table.ajax.reload();
            });

            // Add Produksi Form Submit
            $('#addProduksiForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var submitButton = form.find('button[type="submit"]');

                // Disable submit button to prevent double submission
                submitButton.prop('disabled', true);

                var formData = new FormData(this);

                // Convert array inputs to JSON objects
                const skuIndukArray = [];
                const gramasiArray = [];

                form.find('.sku-induk-input').each(function() {
                    skuIndukArray.push($(this).val());
                });

                form.find('.gramasi-input').each(function() {
                    gramasiArray.push($(this).val());
                });

                // Remove the original array inputs
                formData.delete('sku_induk[]');
                formData.delete('gramasi[]');

                // Add as JSON
                formData.append('sku_induk', JSON.stringify(skuIndukArray));
                formData.append('gramasi', JSON.stringify(gramasiArray));

                $.ajax({
                    url: "{{ route('catatan-produksi.store') }}",
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (data.success) {
                            // Reset form
                            form[0].reset();
                            // Remove all SKU inputs except the first one
                            form.find('.array-container:not(:first)').remove();
                            // Clear the first SKU inputs
                            form.find('.sku-induk-input, .gramasi-input').val('');

                            submitButton.prop('disabled', false);

                            // Properly hide modal and remove backdrop
                            $('#addProduksiModal').modal('hide');
                            $('body').removeClass('modal-open');
                            $('.modal-backdrop').remove();

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
                    },
                    complete: function() {
                        // Always ensure the UI is restored
                        submitButton.prop('disabled', false);
                        $('body').removeClass('modal-open');
                        $('.modal-backdrop').remove();
                    }
                });
            });

            // Edit Button Click
            $(document).on('click', '.edit-btn', function() {
                var id = $(this).data('id');
                console.log('Edit button clicked for ID:', id);

                // Show loading state
                Swal.fire({
                    title: 'Memuat...',
                    text: 'Mengambil data catatan produksi',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: `/catatan-produksi/${id}/edit`,
                    method: 'GET',
                    success: function(response) {
                        console.log('Server response:', response);
                        Swal.close();

                        if (response.success) {
                            const data = response.data;
                            console.log('Catatan produksi data:', data);

                            // Set hidden ID
                            $('#edit_produksi_id').val(data.id);

                            // Set basic fields
                            $('#edit_sku_product').val(data.sku_product);
                            $('#edit_nama_product').val(data.nama_product);
                            $('#edit_packaging').val(data.packaging);
                            $('#edit_quantity').val(data.quantity);
                            $('#edit_total_terpakai').val(data.total_terpakai);

                            // Clear existing SKU Induk and Gramasi fields
                            $('#edit-sku-gramasi-container').empty();

                            // Parse JSON if needed
                            let skuIndukArray = data.sku_induk;
                            let gramasiArray = data.gramasi;

                            if (typeof skuIndukArray === 'string') {
                                try {
                                    skuIndukArray = JSON.parse(skuIndukArray);
                                } catch (e) {
                                    console.error('Error parsing SKU Induk:', e);
                                    skuIndukArray = [];
                                }
                            }

                            if (typeof gramasiArray === 'string') {
                                try {
                                    gramasiArray = JSON.parse(gramasiArray);
                                } catch (e) {
                                    console.error('Error parsing Gramasi:', e);
                                    gramasiArray = [];
                                }
                            }

                            // Add SKU Induk and Gramasi fields
                            for (let i = 0; i < skuIndukArray.length; i++) {
                                const template = getSkuGramasiTemplate(true);
                                const $template = $(template);

                                $('#edit-sku-gramasi-container').append($template);

                                // Set values for the newly added inputs
                                $template.find('.sku-induk-input').val(skuIndukArray[i]);
                                $template.find('.gramasi-input').val(gramasiArray[i]);
                            }

                            // If no SKU Induk was added (empty array), add one empty input
                            if (skuIndukArray.length === 0) {
                                $('#edit-sku-gramasi-container').append(getSkuGramasiTemplate(
                                    true));
                            }

                            // Show the modal
                            var editModal = new bootstrap.Modal(document.getElementById(
                                'editProduksiModal'));
                            editModal.show();
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.message ||
                                    'Gagal mengambil data catatan produksi',
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
                            text: 'Gagal mengambil data catatan produksi. Silahkan coba lagi.',
                            icon: 'error'
                        });
                    }
                });
            });

            // Edit Produksi Form Submit
            $('#editProduksiForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var submitButton = form.find('button[type="submit"]');
                var id = $('#edit_produksi_id').val();

                // Disable submit button to prevent double submission
                submitButton.prop('disabled', true);

                // Show loading state
                Swal.fire({
                    title: 'Memperbarui...',
                    text: 'Menyimpan data catatan produksi',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                var formData = new FormData(this);
                formData.append('_method', 'PUT');

                // Convert array inputs to JSON objects
                const skuIndukArray = [];
                const gramasiArray = [];

                form.find('.sku-induk-input').each(function() {
                    skuIndukArray.push($(this).val());
                });

                form.find('.gramasi-input').each(function() {
                    gramasiArray.push($(this).val());
                });

                // Remove the original array inputs
                formData.delete('sku_induk[]');
                formData.delete('gramasi[]');

                // Add as JSON
                formData.append('sku_induk', JSON.stringify(skuIndukArray));
                formData.append('gramasi', JSON.stringify(gramasiArray));

                $.ajax({
                    url: `/catatan-produksi/${id}`,
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
                            $('#editProduksiModal').modal('hide');

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
                                    'Tidak dapat memperbarui catatan produksi saat ini.',
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
                    text: "Apakah Anda yakin ingin menghapus catatan produksi ini? Tindakan ini tidak dapat dibatalkan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/catatan-produksi/${id}`,
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
                                        'Tidak dapat menghapus catatan produksi saat ini.',
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
            $('.modal').on('hidden.bs.modal', function() {
                console.log('Modal hidden - cleaning up');
                $(this).find('form')[0].reset();
            });
        });
    </script>
@endsection
