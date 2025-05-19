@extends('layouts.main')

@section('title', 'Manajemen Finished Goods')
@section('breadcrumb-item', 'Finished Goods')

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
    </style>
@endsection

@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">
        <!-- Finished Goods Table start -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Daftar Finished Goods</h5>
                        <div>
                            <button id="clear-filters" class="btn btn btn-secondary">
                                <i class="fas fa-filter"></i> Hapus Filter
                            </button>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addFinishedGoodsModal">
                                <i class="fas fa-plus"></i> Tambah Finished Goods Baru
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="mb-3 p-3 border rounded bg-light">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="filter-product" class="form-label small">Produk</label>
                                <select class="form-control form-control-sm" name="filter-product" id="filter-product">
                                    <option value="">Semua Produk</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name_product }}
                                            ({{ $product->sku }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- End Filter Section -->

                    <div class="dt-responsive table-responsive">
                        <table id="finishedGoods-table" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Produk</th>
                                    <th>SKU</th>
                                    <th>Packaging</th>
                                    <th>Stok Awal</th>
                                    <th>Stok Masuk</th>
                                    <th>Stok Keluar</th>
                                    <th>Defective</th>
                                    <th>Live Stock</th>
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
        <!-- Finished Goods Table end -->
    </div>
    <!-- [ Main Content ] end -->

    <!-- Add Finished Goods Modal -->
    <div class="modal fade" id="addFinishedGoodsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Finished Goods Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addFinishedGoodsForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Produk <span class="text-danger">*</span></label>
                            <select class="form-control" name="id_product" id="add-product" required>
                                <option value="">Pilih Produk</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name_product }} ({{ $product->sku }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stok Awal <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="stok_awal" required min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stok Masuk <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="stok_masuk" required min="0">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stok Keluar <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="stok_keluar" required min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Defective <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="defective" required min="0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Live Stock (Terhitung Otomatis)</label>
                            <input type="text" class="form-control" id="live_stock_preview" disabled>
                            <small class="text-muted">Live Stock = Stok Awal + Stok Masuk - Stok Keluar - Defective</small>
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

    <!-- Edit Finished Goods Modal -->
    <div class="modal fade" id="editFinishedGoodsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Finished Goods</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editFinishedGoodsForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_finished_goods_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Produk <span class="text-danger">*</span></label>
                            <select class="form-control" name="id_product" id="edit-product" required>
                                <option value="">Pilih Produk</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name_product }}
                                        ({{ $product->sku }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stok Awal <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="stok_awal" id="edit_stok_awal" required
                                    min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stok Masuk <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="stok_masuk" id="edit_stok_masuk"
                                    required min="0">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stok Keluar <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="stok_keluar" id="edit_stok_keluar"
                                    required min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Defective <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="defective" id="edit_defective" required
                                    min="0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Live Stock (Terhitung Otomatis)</label>
                            <input type="text" class="form-control" id="edit_live_stock_preview" disabled>
                            <small class="text-muted">Live Stock = Stok Awal + Stok Masuk - Stok Keluar - Defective</small>
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

            // Function to calculate live stock
            function calculateLiveStock() {
                const stokAwal = parseInt($('input[name="stok_awal"]').val()) || 0;
                const stokMasuk = parseInt($('input[name="stok_masuk"]').val()) || 0;
                const stokKeluar = parseInt($('input[name="stok_keluar"]').val()) || 0;
                const defective = parseInt($('input[name="defective"]').val()) || 0;

                const liveStock = stokAwal + stokMasuk - stokKeluar - defective;
                $('#live_stock_preview').val(liveStock);
            }

            // Function to calculate live stock for edit modal
            function calculateEditLiveStock() {
                const stokAwal = parseInt($('#edit_stok_awal').val()) || 0;
                const stokMasuk = parseInt($('#edit_stok_masuk').val()) || 0;
                const stokKeluar = parseInt($('#edit_stok_keluar').val()) || 0;
                const defective = parseInt($('#edit_defective').val()) || 0;

                const liveStock = stokAwal + stokMasuk - stokKeluar - defective;
                $('#edit_live_stock_preview').val(liveStock);
            }

            // Initialize choices for select inputs
            var addProductChoices = new Choices('#add-product', {
                searchEnabled: true,
                searchPlaceholderValue: "Cari produk",
                itemSelectText: '',
                placeholder: true,
                placeholderValue: "Pilih produk"
            });

            var editProductChoices = new Choices('#edit-product', {
                searchEnabled: true,
                searchPlaceholderValue: "Cari produk",
                itemSelectText: '',
                placeholder: true,
                placeholderValue: "Pilih produk"
            });

            var filterProductChoices = new Choices('#filter-product', {
                searchEnabled: true,
                searchPlaceholderValue: "Cari produk",
                itemSelectText: '',
                placeholder: true,
                placeholderValue: "Semua produk"
            });

            // Calculate live stock when input values change
            $('input[name="stok_awal"], input[name="stok_masuk"], input[name="stok_keluar"], input[name="defective"]')
                .on('input', calculateLiveStock);
            $('#edit_stok_awal, #edit_stok_masuk, #edit_stok_keluar, #edit_defective').on('input',
                calculateEditLiveStock);

            // Initialize DataTable
            var table = $('#finishedGoods-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('finished-goods.index') }}",
                    type: "GET",
                    data: function(d) {
                        d.id_product = $('#filter-product').val();
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
                        data: 'product_name',
                        name: 'product_name'
                    },
                    {
                        data: 'product_sku',
                        name: 'product_sku'
                    },
                    {
                        data: 'product_packaging',
                        name: 'product_packaging'
                    },
                    {
                        data: 'stok_awal',
                        name: 'stok_awal'
                    },
                    {
                        data: 'stok_masuk',
                        name: 'stok_masuk'
                    },
                    {
                        data: 'stok_keluar',
                        name: 'stok_keluar'
                    },
                    {
                        data: 'defective',
                        name: 'defective'
                    },
                    {
                        data: 'live_stock',
                        name: 'live_stock'
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
            $('#filter-product').on('change', function() {
                table.ajax.reload();
            });

            // Clear filters function
            $('#clear-filters').on('click', function() {
                filterProductChoices.setChoiceByValue('');
                table.ajax.reload();
            });

            // Add Finished Goods Form Submit
            $('#addFinishedGoodsForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var submitButton = form.find('button[type="submit"]');

                // Disable submit button to prevent double submission
                submitButton.prop('disabled', true);

                var formData = new FormData(this);

                $.ajax({
                    url: "{{ route('finished-goods.store') }}",
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (data.success) {
                            // Reset form and choices
                            form[0].reset();
                            addProductChoices.setChoiceByValue('');
                            $('#live_stock_preview').val('');
                            submitButton.prop('disabled', false);

                            // Properly hide modal and remove backdrop
                            $('#addFinishedGoodsModal').modal('hide');
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
                    text: 'Mengambil data finished goods',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: `/finished-goods/${id}/edit`,
                    method: 'GET',
                    success: function(response) {
                        console.log('Server response:', response);
                        Swal.close();

                        if (response.success) {
                            const data = response.data;
                            console.log('Finished goods data:', data);

                            // Set hidden ID
                            $('#edit_finished_goods_id').val(data.id);

                            // Set form values
                            editProductChoices.setChoiceByValue(data.id_product.toString());
                            $('#edit_stok_awal').val(data.stok_awal);
                            $('#edit_stok_masuk').val(data.stok_masuk);
                            $('#edit_stok_keluar').val(data.stok_keluar);
                            $('#edit_defective').val(data.defective);
                            $('#edit_live_stock_preview').val(data.live_stock);

                            // Show the modal
                            var editModal = new bootstrap.Modal(document.getElementById(
                                'editFinishedGoodsModal'));
                            editModal.show();
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.message ||
                                    'Gagal mengambil data finished goods',
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
                            text: 'Gagal mengambil data finished goods. Silahkan coba lagi.',
                            icon: 'error'
                        });
                    }
                });
            });

            // Edit Finished Goods Form Submit
            $('#editFinishedGoodsForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var submitButton = form.find('button[type="submit"]');
                var id = $('#edit_finished_goods_id').val();

                // Disable submit button to prevent double submission
                submitButton.prop('disabled', true);

                // Show loading state
                Swal.fire({
                    title: 'Memperbarui...',
                    text: 'Menyimpan data finished goods',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                var formData = new FormData(this);
                formData.append('_method', 'PUT');

                $.ajax({
                    url: `/finished-goods/${id}`,
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
                            editProductChoices.setChoiceByValue('');
                            $('#edit_live_stock_preview').val('');
                            $('#editFinishedGoodsModal').modal('hide');

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
                                    'Tidak dapat memperbarui finished goods saat ini.',
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
                    text: "Apakah Anda yakin ingin menghapus finished goods ini? Tindakan ini tidak dapat dibatalkan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/finished-goods/${id}`,
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
                                        'Tidak dapat menghapus finished goods saat ini.',
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
                if ($(this).attr('id') === 'addFinishedGoodsModal') {
                    addProductChoices.setChoiceByValue('');
                    $('#live_stock_preview').val('');
                } else if ($(this).attr('id') === 'editFinishedGoodsModal') {
                    editProductChoices.setChoiceByValue('');
                    $('#edit_live_stock_preview').val('');
                }
            });
        });
    </script>
@endsection
