@extends('layouts.main')

@section('title', 'Manajemen Label')
@section('breadcrumb-item', 'Label')

@section('css')
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- [Page specific CSS] start -->
    <!-- data tables css -->
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/datatables/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/datatables/buttons.bootstrap5.min.css') }}">
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
        <!-- Label Table start -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Daftar Label</h5>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#addLabelModal">
                            <i class="fas fa-plus"></i> Tambah Label Baru
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="dt-responsive table-responsive">
                        <table id="label-table" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
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
        <!-- Label Table end -->
    </div>
    <!-- [ Main Content ] end -->

    <!-- Add Label Modal -->
    <div class="modal fade" id="addLabelModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Label Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addLabelForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-info" id="saveAndAddMore">Simpan & Tambah Lagi</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Label Modal -->
    <div class="modal fade" id="editLabelModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Label</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editLabelForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_label_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" id="edit_name" required>
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

    <!-- View Products Modal -->
    <div class="modal fade" id="viewProductsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Daftar Produk - Label: <span id="productLabelName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Berikut adalah daftar produk yang menggunakan label ini.
                        Untuk menghapus label ini, Anda harus mengubah label atau menghapus produk-produk ini terlebih
                        dahulu melalui menu Produk.
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Produk</th>
                                    <th>Tanggal Dibuat</th>
                                </tr>
                            </thead>
                            <tbody id="productsList">
                                <!-- Products will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                    <div id="paginationLinks" class="mt-3">
                        <!-- Pagination links will be placed here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('category-products.index') }}" class="btn btn-primary">Ke Halaman Produk</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
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
            // Initialize DataTable
            var table = $('#label-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('labels.index') }}",
                    type: "GET"
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
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
                                <button type="button" class="btn btn-sm btn-info view-products-btn" data-id="${row.id}" data-name="${row.name}">
                                    <i class="fas fa-list"></i>
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

            // Save and Add More Button Click
            $('#saveAndAddMore').on('click', function(e) {
                e.preventDefault();
                var form = $('#addLabelForm');
                var submitButton = $(this);

                // Disable submit button to prevent double submission
                submitButton.prop('disabled', true);

                var formData = new FormData(form[0]);

                $.ajax({
                    url: "{{ route('labels.store') }}",
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (data.success) {
                            // Reset form but keep the modal open
                            form[0].reset();
                            submitButton.prop('disabled', false);

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

                            // Focus on the name input for next entry
                            form.find('input[name="name"]').focus();
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
                        // Re-enable the button
                        submitButton.prop('disabled', false);
                    }
                });
            });

            // Add Label Form Submit
            $('#addLabelForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var submitButton = form.find('button[type="submit"]');

                // Disable submit button to prevent double submission
                submitButton.prop('disabled', true);

                var formData = new FormData(this);

                $.ajax({
                    url: "{{ route('labels.store') }}",
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (data.success) {
                            // Reset form first
                            form[0].reset();
                            submitButton.prop('disabled', false);

                            // Properly hide modal
                            $('#addLabelModal').modal('hide');

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
                        // Always ensure the submit button is re-enabled
                        submitButton.prop('disabled', false);
                    }
                });
            });

            // Fix for modal backdrop issue
            $(document).on('click', '[data-bs-toggle="modal"][data-bs-target="#addLabelModal"]',
                function() {
                    // Remove any existing backdrop before showing new modal
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open');
                    $('body').css('padding-right', '');

                    // Reset the form
                    $('#addLabelForm')[0].reset();

                    // Show the modal properly
                    setTimeout(function() {
                        $('#addLabelModal').modal('show');
                    }, 100);

                    return false;
                });

            // Edit Button Click
            $(document).on('click', '.edit-btn', function() {
                var id = $(this).data('id');
                console.log('Edit button clicked for ID:', id);

                // Show loading state
                Swal.fire({
                    title: 'Memuat...',
                    text: 'Mengambil data label',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: "{{ url('labels') }}/" + id + "/edit",
                    method: 'GET',
                    success: function(response) {
                        console.log('Server response:', response);
                        Swal.close();

                        if (response.success) {
                            const data = response.data;
                            console.log('Label data:', data);

                            // Set hidden ID
                            $('#edit_label_id').val(data.id);

                            // Set name
                            $('#edit_name').val(data.name);

                            // Clean up any existing modal backdrop
                            $('.modal-backdrop').remove();
                            $('body').removeClass('modal-open');
                            $('body').css('padding-right', '');

                            // Show the modal
                            setTimeout(function() {
                                var editModal = new bootstrap.Modal(document
                                    .getElementById('editLabelModal'));
                                editModal.show();
                            }, 100);
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.message ||
                                    'Gagal mengambil data label',
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
                            text: 'Gagal mengambil data label. Silahkan coba lagi.',
                            icon: 'error'
                        });
                    }
                });
            });

            // Edit Label Form Submit
            $('#editLabelForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var submitButton = form.find('button[type="submit"]');
                var id = $('#edit_label_id').val();

                // Disable submit button to prevent double submission
                submitButton.prop('disabled', true);

                // Show loading state
                Swal.fire({
                    title: 'Memperbarui...',
                    text: 'Menyimpan data label',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                var formData = new FormData(this);
                formData.append('_method', 'PUT');

                $.ajax({
                    url: `/labels/${id}`,
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
                            $('#editLabelModal').modal('hide');

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
                                    'Tidak dapat memperbarui label',
                                icon: 'error'
                            });
                        }
                    }
                });
            });

            // Delete Button Click
            $(document).on('click', '.delete-btn', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: "Apakah Anda yakin ingin menghapus label ini? Tindakan ini tidak dapat dibatalkan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/labels/${id}`,
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
                                if (xhr.status === 422 && xhr.responseJSON
                                    ?.category_products_count > 0) {
                                    // Special handling for label in use
                                    Swal.fire({
                                        title: 'Tidak Dapat Menghapus Label',
                                        html: xhr.responseJSON.message +
                                            '<br><br>Silahkan pergi ke menu Produk dan ubah label produk tersebut terlebih dahulu.',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#3085d6',
                                        cancelButtonColor: '#d33',
                                        confirmButtonText: 'Ke Halaman Produk',
                                        cancelButtonText: 'Tutup'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.href =
                                                "{{ route('category-products.index') }}";
                                        }
                                    });
                                } else {
                                    // General error handling
                                    Swal.fire({
                                        title: 'Gagal Menghapus',
                                        text: xhr.responseJSON?.message ||
                                            'Tidak dapat menghapus label saat ini.',
                                        icon: 'error',
                                        confirmButtonText: 'OK',
                                        confirmButtonColor: '#3085d6'
                                    });
                                }
                            }
                        });
                    }
                });
            });

            // View Products Button Click
            $(document).on('click', '.view-products-btn', function() {
                var id = $(this).data('id');
                var labelName = $(this).data('name');

                // Set label name in modal
                $('#productLabelName').text(labelName);

                // Show loading state
                $('#productsList').html(
                    '<tr><td colspan="3" class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat data produk...</td></tr>'
                );
                $('#paginationLinks').html('');

                // Show the modal
                $('#viewProductsModal').modal('show');

                // Fetch products data
                loadProducts(id);
            });

            // Function to load products for a label
            function loadProducts(labelId, page = 1) {
                $.ajax({
                    url: `/labels/${labelId}/products`,
                    method: 'GET',
                    data: {
                        page: page
                    },
                    success: function(response) {
                        if (response.success) {
                            const data = response.data;
                            const products = data.products.data;

                            // Clear the products list
                            $('#productsList').empty();

                            if (products.length > 0) {
                                // Populate the table with products
                                products.forEach(function(product) {
                                    $('#productsList').append(`
                                        <tr>
                                            <td>${product.id}</td>
                                            <td>${product.name}</td>
                                            <td>${new Date(product.created_at).toLocaleString()}</td>
                                        </tr>
                                    `);
                                });

                                // Create pagination links
                                renderPagination(data.products, labelId);
                            } else {
                                $('#productsList').html(
                                    '<tr><td colspan="3" class="text-center">Tidak ada produk dengan label ini.</td></tr>'
                                );
                            }
                        } else {
                            $('#productsList').html(
                                '<tr><td colspan="3" class="text-center text-danger">Error saat memuat data produk. Silahkan coba lagi.</td></tr>'
                            );
                        }
                    },
                    error: function() {
                        $('#productsList').html(
                            '<tr><td colspan="3" class="text-center text-danger">Gagal memuat data produk. Silahkan coba lagi nanti.</td></tr>'
                        );
                    }
                });
            }

            // Function to render pagination links
            function renderPagination(paginationData, labelId) {
                if (!paginationData.last_page || paginationData.last_page <= 1) {
                    $('#paginationLinks').html('');
                    return;
                }

                let links = '<ul class="pagination justify-content-center">';

                // Previous page link
                if (paginationData.current_page > 1) {
                    links += `<li class="page-item">
                        <a class="page-link" href="#" data-page="${paginationData.current_page - 1}" data-label="${labelId}">Sebelumnya</a>
                    </li>`;
                } else {
                    links += '<li class="page-item disabled"><span class="page-link">Sebelumnya</span></li>';
                }

                // Page number links
                for (let i = 1; i <= paginationData.last_page; i++) {
                    if (i === paginationData.current_page) {
                        links += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
                    } else {
                        links += `<li class="page-item">
                            <a class="page-link" href="#" data-page="${i}" data-label="${labelId}">${i}</a>
                        </li>`;
                    }
                }

                // Next page link
                if (paginationData.current_page < paginationData.last_page) {
                    links += `<li class="page-item">
                        <a class="page-link" href="#" data-page="${paginationData.current_page + 1}" data-label="${labelId}">Selanjutnya</a>
                    </li>`;
                } else {
                    links += '<li class="page-item disabled"><span class="page-link">Selanjutnya</span></li>';
                }

                links += '</ul>';

                $('#paginationLinks').html(links);

                // Add click handlers for pagination links
                $('#paginationLinks').on('click', '.page-link', function(e) {
                    e.preventDefault();
                    const page = $(this).data('page');
                    const labelId = $(this).data('label');
                    loadProducts(labelId, page);
                });
            }

            // Clean up modal when it's hidden
            $('#editLabelModal').on('hidden.bs.modal', function() {
                console.log('Modal hidden - cleaning up');
                $(this).find('form')[0].reset();
                // Clean up any lingering modal artifacts
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                $('body').css('padding-right', '');
            });

            // Clean up add modal when it's hidden
            $('#addLabelModal').on('hidden.bs.modal', function() {
                console.log('Add modal hidden - cleaning up');
                // Clean up any lingering modal artifacts
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                $('body').css('padding-right', '');
            });

            // Prevent form reset when modal is shown
            $('#editLabelModal').on('show.bs.modal', function() {
                console.log('Modal showing - preventing auto reset');
                return true;
            });
        });
    </script>
@endsection
