@extends('layouts.main')

@section('title', 'Manajemen Sticker')
@section('breadcrumb-item', 'Sticker')

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

        /* Responsive styling */
        .filter-section {
            padding: 15px;
            margin-bottom: 15px;
        }

        .filter-label {
            font-size: 12px;
            margin-bottom: 4px;
        }

        /* Table responsiveness */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Action button styling */
        .action-buttons .btn {
            margin-right: 5px;
            margin-bottom: 5px;
        }

        /* DataTables buttons responsiveness */
        .dt-buttons {
            margin-bottom: 10px;
        }

        .dt-button {
            margin-right: 5px;
            margin-bottom: 5px;
        }

        /* Modal responsiveness */
        @media (max-width: 768px) {
            .modal-dialog {
                max-width: 95%;
                margin: 10px auto;
            }

            .filter-section {
                padding: 10px;
            }

            .table {
                font-size: 14px;
            }

            .dt-buttons {
                display: flex;
                flex-wrap: wrap;
                gap: 5px;
            }

            .dt-button {
                flex: 1;
                white-space: nowrap;
                min-width: auto !important;
                padding: 0.25rem 0.5rem !important;
                font-size: 0.875rem !important;
            }

            .paginate_button {
                padding: 0.3rem 0.5rem !important;
            }
        }

        @media (max-width: 576px) {
            .action-buttons .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.875rem;
            }

            .card-header {
                flex-direction: column;
                align-items: flex-start !important;
            }

            .card-header .btn-group {
                margin-top: 10px;
                width: 100%;
            }

            .paginate_button {
                padding: 0.2rem 0.3rem !important;
                font-size: 0.8rem;
            }
        }
    </style>
@endsection

@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">
        @if (isset($error_message))
            <div class="col-sm-12 mb-3">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> {{ $error_message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif
        @if (session('success'))
            <div class="col-sm-12 mb-3">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="col-sm-12 mb-3">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif
        <!-- Sticker Table start -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <h5 class="mb-2 mb-sm-0">Daftar Sticker</h5>
                        <div class="d-flex flex-wrap">
                            <button id="clear-filters" class="btn btn btn-secondary me-2 mb-2 mb-sm-0">
                                <i class="fas fa-filter"></i> Hapus Filter
                            </button>
                            <button type="button" class="btn btn-primary mb-2 mb-sm-0" data-bs-toggle="modal"
                                data-bs-target="#addStickerModal">
                                <i class="fas fa-plus"></i> Tambah Sticker
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3 filter-section p-3 border rounded bg-light">
                        <button id="filter-toggle-btn" class="btn btn-sm btn-outline-secondary mb-2 w-100 d-md-none"
                            type="button" data-bs-toggle="collapse" data-bs-target="#filterControls">
                            <i class="fas fa-filter"></i> Toggle Filters <i class="fas fa-chevron-down"></i>
                        </button>
                        <div id="filterControls" class="collapse show">
                            <div class="row">
                                <div class="col-md-4 col-sm-12 mb-2">
                                    <label for="filter-product" class="form-label filter-label">Filter Produk</label>
                                    <select id="filter-product" class="form-select form-select-sm">
                                        <option value="">Semua Produk</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name_product }}
                                                ({{ $product->sku }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 col-sm-12 mb-2">
                                    <label for="filter-status" class="form-label filter-label">Filter Status</label>
                                    <select id="filter-status" class="form-select form-select-sm">
                                        <option value="">Semua Status</option>
                                        @foreach ($statuses as $key => $status)
                                            <option value="{{ $key }}">{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="dt-responsive table-responsive">
                        <table id="sticker-table" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Produk</th>
                                    <th>SKU</th>
                                    <th>Ukuran</th>
                                    <th>Jumlah</th>
                                    <th>Stok Awal</th>
                                    <th>Stok Masuk</th>
                                    <th>Produksi</th>
                                    <th>Defect</th>
                                    <th>Sisa</th>
                                    <th>Status</th>
                                    <th>Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Sticker Table end -->
    </div>
    <!-- [ Main Content ] end -->

    <!-- Add Sticker Modal -->
    <div class="modal fade" id="addStickerModal" tabindex="-1" aria-labelledby="addStickerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addStickerModalLabel">Tambah Sticker Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addStickerForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="add_product_id" class="form-label">Produk <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="add_product_id" name="product_id" required>
                                    <option value="">Pilih Produk</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name_product }}
                                            ({{ $product->sku }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="add_ukuran" class="form-label">Ukuran <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="add_ukuran" name="ukuran" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="add_jumlah" class="form-label">Jumlah <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="add_jumlah" name="jumlah" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="add_stok_awal" class="form-label">Stok Awal <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="add_stok_awal" name="stok_awal"
                                    min="0" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="add_stok_masuk" class="form-label">Stok Masuk <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="add_stok_masuk" name="stok_masuk"
                                    min="0" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="add_produksi" class="form-label">Produksi <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="add_produksi" name="produksi"
                                    min="0" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="add_defect" class="form-label">Defect <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="add_defect" name="defect"
                                    min="0" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="add_sisa" class="form-label">Sisa <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="add_sisa" name="sisa" min="0"
                                    required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="add_status" class="form-label">Status <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="add_status" name="status" required>
                                    <option value="">Pilih Status</option>
                                    @foreach ($statuses as $key => $status)
                                        <option value="{{ $key }}">{{ $status }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Sticker Modal -->
    <div class="modal fade" id="editStickerModal" tabindex="-1" aria-labelledby="editStickerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editStickerModalLabel">Edit Sticker</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editStickerForm">
                    <div class="modal-body">
                        <input type="hidden" id="edit_sticker_id" name="sticker_id">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_product_id" class="form-label">Produk <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="edit_product_id" name="product_id" required>
                                    <option value="">Pilih Produk</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name_product }}
                                            ({{ $product->sku }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_ukuran" class="form-label">Ukuran <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_ukuran" name="ukuran" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_jumlah" class="form-label">Jumlah <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_jumlah" name="jumlah" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_stok_awal" class="form-label">Stok Awal <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="edit_stok_awal" name="stok_awal"
                                    min="0" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_stok_masuk" class="form-label">Stok Masuk <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="edit_stok_masuk" name="stok_masuk"
                                    min="0" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_produksi" class="form-label">Produksi <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="edit_produksi" name="produksi"
                                    min="0" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_defect" class="form-label">Defect <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="edit_defect" name="defect"
                                    min="0" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_sisa" class="form-label">Sisa <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="edit_sisa" name="sisa" min="0"
                                    required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_status" class="form-label">Status <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="edit_status" name="status" required>
                                    <option value="">Pilih Status</option>
                                    @foreach ($statuses as $key => $status)
                                        <option value="{{ $key }}">{{ $status }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteStickerModal" tabindex="-1" aria-labelledby="deleteStickerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteStickerModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus sticker ini?</p>
                    <p><strong>Produk:</strong> <span id="delete-sticker-product"></span></p>
                    <p><strong>Ukuran:</strong> <span id="delete-sticker-ukuran"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteSticker">Hapus</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- [Page Specific JS] start -->
    <!-- Core JS files - jQuery HARUS dimuat pertama -->
    <script src="{{ URL::asset('build/js/plugins/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/bootstrap.min.js') }}"></script>

    <!-- datatable Js -->
    <script src="{{ URL::asset('build/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/datatables/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/datatables/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/datatables/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/datatables/pdfmake.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/datatables/vfs_fonts.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/datatables/buttons.print.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/datatables/buttons.colVis.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Setup CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initialize DataTable
            var table = $('#sticker-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('stickers.index') }}",
                    data: function(d) {
                        d.product_id = $('#filter-product').val();
                        d.status = $('#filter-status').val();
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
                        data: 'ukuran',
                        name: 'ukuran'
                    },
                    {
                        data: 'jumlah',
                        name: 'jumlah'
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
                        data: 'produksi',
                        name: 'produksi'
                    },
                    {
                        data: 'defect',
                        name: 'defect'
                    },
                    {
                        data: 'sisa',
                        name: 'sisa'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-info view-sticker" data-id="${data}" title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning edit-sticker" data-id="${data}" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-sticker" data-id="${data}" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ],
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                responsive: true,
                order: [
                    [11, 'desc']
                ]
            });

            // Filter functionality
            $('#filter-product, #filter-status').change(function() {
                table.draw();
            });

            // Clear filters
            $('#clear-filters').click(function() {
                $('#filter-product').val('');
                $('#filter-status').val('');
                table.draw();
            });

            // Add Sticker Form Submit
            $('#addStickerForm').on('submit', function(e) {
                e.preventDefault();

                console.log('Form submission started');

                var formData = new FormData(this);

                // Debug: Log form data
                console.log('Form data entries:');
                for (let [key, value] of formData.entries()) {
                    console.log(key + ': ' + value);
                }

                // Debug: Check if all required fields are filled
                var requiredFields = ['product_id', 'ukuran', 'jumlah', 'stok_awal', 'stok_masuk',
                    'produksi', 'defect', 'sisa', 'status'
                ];
                var missingFields = [];

                requiredFields.forEach(function(field) {
                    var value = formData.get(field);
                    if (!value || value.trim() === '') {
                        missingFields.push(field);
                    }
                });

                if (missingFields.length > 0) {
                    console.error('Missing required fields:', missingFields);
                    showAlert('error', 'Field yang wajib diisi: ' + missingFields.join(', '));
                    return;
                }

                console.log('All required fields are filled, sending AJAX request...');

                $.ajax({
                    url: "{{ route('stickers.store') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function(xhr) {
                        console.log('AJAX request about to be sent');
                        console.log('URL:', "{{ route('stickers.store') }}");
                        console.log('CSRF Token:', $('meta[name="csrf-token"]').attr(
                            'content'));
                    },
                    success: function(response) {
                        console.log('AJAX Success Response:', response);

                        if (response.success) {
                            $('#addStickerModal').modal('hide');
                            $('#addStickerForm')[0].reset();
                            table.draw();

                            // Show success message
                            showAlert('success', response.message);
                        } else {
                            console.error('Response indicates failure:', response);
                            showAlert('error', response.message ||
                                'Terjadi kesalahan yang tidak diketahui.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error Details:');
                        console.error('Status:', status);
                        console.error('Error:', error);
                        console.error('Response Status:', xhr.status);
                        console.error('Response Text:', xhr.responseText);

                        try {
                            var responseJson = JSON.parse(xhr.responseText);
                            console.error('Parsed Response:', responseJson);
                        } catch (e) {
                            console.error('Could not parse response as JSON');
                        }

                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON ? xhr.responseJSON.errors : {};
                            console.error('Validation Errors:', errors);
                            displayValidationErrors(errors, 'add');

                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                showAlert('error', xhr.responseJSON.message);
                            }
                        } else if (xhr.status === 419) {
                            console.error('CSRF Token Mismatch');
                            showAlert('error',
                                'CSRF Token tidak valid. Silakan refresh halaman dan coba lagi.'
                            );
                        } else if (xhr.status === 500) {
                            console.error('Server Error');
                            var errorMessage = xhr.responseJSON && xhr.responseJSON.message ?
                                xhr.responseJSON.message : 'Terjadi kesalahan server.';
                            showAlert('error', errorMessage);
                        } else {
                            console.error('Unknown Error');
                            showAlert('error', xhr.responseJSON && xhr.responseJSON.message ?
                                xhr.responseJSON.message :
                                'Terjadi kesalahan saat menyimpan data.');
                        }
                    },
                    complete: function(xhr, status) {
                        console.log('AJAX Request completed with status:', status);
                    }
                });
            });

            // Edit Sticker
            $(document).on('click', '.edit-sticker', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ route('stickers.edit', ':id') }}".replace(':id', id),
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            var sticker = response.data;

                            $('#edit_sticker_id').val(sticker.id);
                            $('#edit_product_id').val(sticker.product_id);
                            $('#edit_ukuran').val(sticker.ukuran);
                            $('#edit_jumlah').val(sticker.jumlah);
                            $('#edit_stok_awal').val(sticker.stok_awal);
                            $('#edit_stok_masuk').val(sticker.stok_masuk);
                            $('#edit_produksi').val(sticker.produksi);
                            $('#edit_defect').val(sticker.defect);
                            $('#edit_sisa').val(sticker.sisa);
                            $('#edit_status').val(sticker.status);

                            $('#editStickerModal').modal('show');
                        }
                    },
                    error: function(xhr) {
                        showAlert('error', xhr.responseJSON.message ||
                            'Terjadi kesalahan saat mengambil data.');
                    }
                });
            });

            // Update Sticker Form Submit
            $('#editStickerForm').on('submit', function(e) {
                e.preventDefault();

                var id = $('#edit_sticker_id').val();
                var formData = new FormData(this);
                formData.append('_method', 'PUT');

                $.ajax({
                    url: "{{ route('stickers.update', ':id') }}".replace(':id', id),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#editStickerModal').modal('hide');
                            table.draw();

                            // Show success message
                            showAlert('success', response.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            displayValidationErrors(errors, 'edit');
                        } else {
                            showAlert('error', xhr.responseJSON.message ||
                                'Terjadi kesalahan saat memperbarui data.');
                        }
                    }
                });
            });

            // Delete Sticker
            $(document).on('click', '.delete-sticker', function() {
                var id = $(this).data('id');
                var row = table.row($(this).parents('tr')).data();

                $('#delete-sticker-product').text(row.product_name);
                $('#delete-sticker-ukuran').text(row.ukuran);
                $('#confirmDeleteSticker').data('id', id);
                $('#deleteStickerModal').modal('show');
            });

            // Confirm Delete
            $('#confirmDeleteSticker').on('click', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ route('stickers.destroy', ':id') }}".replace(':id', id),
                    type: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            $('#deleteStickerModal').modal('hide');
                            table.draw();

                            // Show success message
                            showAlert('success', response.message);
                        }
                    },
                    error: function(xhr) {
                        showAlert('error', xhr.responseJSON.message ||
                            'Terjadi kesalahan saat menghapus data.');
                    }
                });
            });

            // View Sticker
            $(document).on('click', '.view-sticker', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ route('stickers.show', ':id') }}".replace(':id', id),
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            var sticker = response.data;
                            var content = `
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Produk:</strong> ${sticker.product ? sticker.product.name_product : '-'}</p>
                                        <p><strong>SKU:</strong> ${sticker.product ? sticker.product.sku : '-'}</p>
                                        <p><strong>Ukuran:</strong> ${sticker.ukuran}</p>
                                        <p><strong>Jumlah:</strong> ${sticker.jumlah}</p>
                                        <p><strong>Status:</strong> ${sticker.status}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Stok Awal:</strong> ${sticker.stok_awal}</p>
                                        <p><strong>Stok Masuk:</strong> ${sticker.stok_masuk}</p>
                                        <p><strong>Produksi:</strong> ${sticker.produksi}</p>
                                        <p><strong>Defect:</strong> ${sticker.defect}</p>
                                        <p><strong>Sisa:</strong> ${sticker.sisa}</p>
                                    </div>
                                </div>
                            `;

                            showAlert('info', content, 'Detail Sticker');
                        }
                    },
                    error: function(xhr) {
                        showAlert('error', xhr.responseJSON.message ||
                            'Terjadi kesalahan saat mengambil data.');
                    }
                });
            });

            // Helper functions
            function displayValidationErrors(errors, prefix) {
                // Clear previous errors
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                // Display new errors
                $.each(errors, function(field, messages) {
                    var input = $(`#${prefix}_${field}`);
                    input.addClass('is-invalid');
                    input.siblings('.invalid-feedback').text(messages[0]);
                });
            }

            function showAlert(type, message, title = null) {
                var alertClass = type === 'success' ? 'alert-success' :
                    type === 'error' ? 'alert-danger' : 'alert-info';

                var alertHtml = `
                    <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                        ${title ? '<strong>' + title + '</strong><br>' : ''}
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;

                $('.row').first().prepend(`<div class="col-sm-12 mb-3">${alertHtml}</div>`);

                // Auto dismiss after 5 seconds
                setTimeout(function() {
                    $('.alert').alert('close');
                }, 5000);
            }

            // Reset modal forms when closed
            $('#addStickerModal, #editStickerModal').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
                $(this).find('.is-invalid').removeClass('is-invalid');
                $(this).find('.invalid-feedback').text('');
            });
        });
    </script>
@endsection
