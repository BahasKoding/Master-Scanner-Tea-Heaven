@extends('layouts.main')

@section('title', 'Purchase Stiker')
@section('breadcrumb-item', 'Purchase Stiker')

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
        <!-- Purchase Sticker Table start -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <h5 class="mb-2 mb-sm-0">{{ $item ?? 'Purchase Stiker' }}</h5>
                        <div class="d-flex flex-wrap">
                            <button type="button" class="btn btn-primary mb-2 mb-sm-0" onclick="showCreateModal()">
                                <i class="fas fa-plus"></i> Tambah Purchase Stiker
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Show error message if any -->
                    @if (isset($error_message))
                        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                            <strong>Error!</strong> {{ $error_message }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="mb-3 filter-section p-3 border rounded bg-light">
                        <button id="filter-toggle-btn" class="btn btn-sm btn-outline-secondary mb-2 w-100 d-md-none"
                            type="button" data-bs-toggle="collapse" data-bs-target="#filterControls">
                            <i class="fas fa-filter"></i> Toggle Filters <i class="fas fa-chevron-down"></i>
                        </button>
                        <div id="filterControls" class="collapse show">
                            <div class="row">
                                <div class="col-md-4 col-sm-12 mb-2">
                                    <label for="filter_product" class="form-label filter-label">Filter Produk</label>
                                    <select class="form-select form-select-sm" id="filter_product">
                                        <option value="">-- Semua Produk --</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name_product }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 col-sm-12 mb-2">
                                    <label for="filter_ukuran" class="form-label filter-label">Filter Ukuran</label>
                                    <input type="text" class="form-control form-control-sm" id="filter_ukuran"
                                        placeholder="Masukkan ukuran stiker">
                                </div>
                                <div class="col-md-4 col-sm-12 d-flex align-items-end">
                                    <button type="button" class="btn btn-secondary btn-sm me-2 mb-2"
                                        onclick="applyFilters()">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm mb-2"
                                        onclick="clearFilters()">
                                        <i class="fas fa-times"></i> Clear
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dt-responsive table-responsive">
                        <table id="purchase-sticker-table" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Produk</th>
                                    <th>SKU</th>
                                    <th>Ukuran Stiker</th>
                                    <th>Jumlah Stiker</th>
                                    <th>Jumlah Order</th>
                                    <th>Stok Masuk</th>
                                    <th>Total Order</th>
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
        <!-- Purchase Sticker Table end -->
    </div>
    <!-- [ Main Content ] end -->

    <!-- Create/Edit Modal -->
    <div class="modal fade" id="purchaseStickerModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="purchaseStickerModalLabel">Tambah Purchase Stiker</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="purchaseStickerForm">
                    @csrf
                    <input type="hidden" id="purchase_sticker_id" name="purchase_sticker_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="product_id" class="form-label">Produk <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="product_id" name="product_id" required>
                                        <option value="">-- Pilih Produk --</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">
                                                {{ $product->name_product }} ({{ $product->sku }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="error-product_id"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ukuran_stiker" class="form-label">Ukuran Stiker <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="ukuran_stiker" name="ukuran_stiker"
                                        placeholder="Contoh: 10x15 cm" required>
                                    <div class="invalid-feedback" id="error-ukuran_stiker"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="jumlah_stiker" class="form-label">Jumlah Stiker <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="jumlah_stiker" name="jumlah_stiker"
                                        min="1" required>
                                    <div class="invalid-feedback" id="error-jumlah_stiker"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="jumlah_order" class="form-label">Jumlah Order <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="jumlah_order" name="jumlah_order"
                                        min="0" required>
                                    <div class="invalid-feedback" id="error-jumlah_order"></div>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h6>Detail Penerimaan Stiker</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="stok_masuk" class="form-label">Stok Masuk</label>
                                    <input type="number" class="form-control" id="stok_masuk" name="stok_masuk"
                                        min="0" value="0">
                                    <div class="invalid-feedback" id="error-stok_masuk"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="total_order" class="form-label">Total Order</label>
                                    <input type="number" class="form-control" id="total_order" name="total_order"
                                        min="0" value="0">
                                    <div class="invalid-feedback" id="error-total_order"></div>
                                    <small class="text-muted">Kosongkan untuk sama dengan jumlah order</small>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <strong>Informasi:</strong>
                            <ul class="mb-0">
                                <li>Jika Total Order dikosongkan, akan otomatis sama dengan Jumlah Order</li>
                                <li>Produk yang bisa dibuat stiker: Tea Bag, Drip Bag, dan Box Tea</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="saveBtn">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Purchase Stiker</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailContent">
                    <!-- Detail content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
            // Show session flash messages with SweetAlert
            @if (session('success'))
                Swal.fire({
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    title: 'Error!',
                    text: '{{ session('error') }}',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            @endif

            // Responsive adjustments for mobile
            function adjustForMobile() {
                if (window.innerWidth < 768) {
                    $('.dt-responsive table').addClass('table-sm');
                    if (!$('#filterControls').hasClass('show')) {
                        $('#filterControls').collapse('hide');
                    }
                } else {
                    $('.dt-responsive table').removeClass('table-sm');
                    $('#filterControls').addClass('show');
                }
            }

            adjustForMobile();
            $(window).resize(function() {
                adjustForMobile();
            });

            // Initialize DataTable
            try {
                var table = $('#purchase-sticker-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('purchase-sticker.index') }}",
                        data: function(d) {
                            d.product_id = $('#filter_product').val();
                            d.ukuran_stiker = $('#filter_ukuran').val();
                            return d;
                        },
                        error: function(xhr, error, code) {
                            console.error('DataTables Ajax Error:', xhr.responseText);
                            Swal.fire({
                                title: 'Error',
                                text: 'Gagal memuat data. Silakan refresh halaman.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
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
                            name: 'product_sku',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'ukuran_stiker',
                            name: 'ukuran_stiker'
                        },
                        {
                            data: 'jumlah_stiker',
                            name: 'jumlah_stiker'
                        },
                        {
                            data: 'jumlah_order',
                            name: 'jumlah_order'
                        },
                        {
                            data: 'stok_masuk',
                            name: 'stok_masuk'
                        },
                        {
                            data: 'total_order',
                            name: 'total_order'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                return `
                                    <div class="action-buttons">
                                        <button type="button" class="btn btn-sm btn-info" onclick="showDetail(${data})" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-warning" onclick="editPurchaseSticker(${data})" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="deletePurchaseSticker(${data})" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                `;
                            }
                        }
                    ],
                    order: [
                        [0, 'desc']
                    ],
                    pageLength: 25,
                    responsive: true,
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
                    ],
                    language: {
                        processing: '<div class="d-flex justify-content-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>',
                        emptyTable: 'Tidak ada data purchase stiker',
                        zeroRecords: 'Tidak ditemukan purchase stiker yang sesuai',
                        info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ purchase stiker',
                        infoEmpty: 'Menampilkan 0 sampai 0 dari 0 purchase stiker',
                        infoFiltered: '(difilter dari _MAX_ total purchase stiker)',
                        search: 'Cari:',
                        lengthMenu: "Tampilkan _MENU_ data per halaman",
                        loadingRecords: "Memuat data...",
                        zeroRecords: "Tidak ada data yang ditemukan",
                        paginate: {
                            first: "Pertama",
                            previous: "Sebelumnya",
                            next: "Selanjutnya",
                            last: "Terakhir"
                        }
                    }
                });

                window.purchaseStickerTable = table;

                // Apply filters
                $('#filter_product, #filter_ukuran').on('change keyup', function() {
                    table.draw();
                });

                // Form submission
                $('#purchaseStickerForm').on('submit', function(e) {
                    e.preventDefault();

                    var formData = new FormData(this);
                    var purchaseStickerId = $('#purchase_sticker_id').val();
                    var url = purchaseStickerId ? `/purchase-sticker/${purchaseStickerId}` :
                        "{{ route('purchase-sticker.store') }}";

                    if (purchaseStickerId) {
                        formData.append('_method', 'PUT');
                    }

                    Swal.fire({
                        title: purchaseStickerId ? 'Memperbarui...' : 'Menyimpan...',
                        text: 'Sedang memproses data purchase stiker',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        beforeSend: function() {
                            $('#saveBtn').prop('disabled', true).html(
                                '<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...'
                            );
                            clearValidationErrors();
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#purchaseStickerModal').modal('hide');
                                if (window.purchaseStickerTable) {
                                    window.purchaseStickerTable.ajax.reload(null, false);
                                }

                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: response.message,
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false,
                                    toast: true,
                                    position: 'top-end'
                                });

                                resetForm();
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                const errors = xhr.responseJSON.errors;
                                showValidationErrors(errors);

                                Swal.fire({
                                    title: 'Mohon Periksa Input Anda',
                                    text: 'Ada beberapa kesalahan pada form yang Anda isi.',
                                    icon: 'warning',
                                    confirmButtonText: 'Saya Mengerti',
                                    confirmButtonColor: '#3085d6'
                                });
                            } else {
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
                            $('#saveBtn').prop('disabled', false).html('Simpan');
                        }
                    });
                });

            } catch (error) {
                console.error('Error initializing DataTable:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Gagal menginisialisasi tabel data.',
                    icon: 'error'
                });
            }

            // Clean up modal when it's hidden
            $('#purchaseStickerModal, #detailModal').on('hidden.bs.modal', function() {
                $(this).find('form')[0]?.reset();
                $(this).find('.is-invalid').removeClass('is-invalid');
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                $('body').css('padding-right', '');
            });
        });

        // Global functions
        window.showCreateModal = function() {
            resetForm();
            $('#purchaseStickerModalLabel').text('Tambah Purchase Stiker');
            $('#purchaseStickerModal').modal('show');
        };

        window.editPurchaseSticker = function(id) {
            Swal.fire({
                title: 'Memuat...',
                text: 'Mengambil data purchase stiker',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            $.get(`/purchase-sticker/${id}/edit`, function(response) {
                Swal.close();

                if (response.success) {
                    var data = response.data;
                    $('#purchase_sticker_id').val(data.id);
                    $('#product_id').val(data.product_id);
                    $('#ukuran_stiker').val(data.ukuran_stiker);
                    $('#jumlah_stiker').val(data.jumlah_stiker);
                    $('#jumlah_order').val(data.jumlah_order);
                    $('#stok_masuk').val(data.stok_masuk);
                    $('#total_order').val(data.total_order);

                    $('#purchaseStickerModalLabel').text('Edit Purchase Stiker');
                    $('#purchaseStickerModal').modal('show');
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: response.message || 'Gagal mengambil data purchase stiker',
                        icon: 'error'
                    });
                }
            }).fail(function(xhr) {
                Swal.fire({
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Gagal memuat data purchase stiker',
                    icon: 'error'
                });
            });
        };

        window.deletePurchaseSticker = function(id) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: "Apakah Anda yakin ingin menghapus purchase stiker ini? Tindakan ini tidak dapat dibatalkan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menghapus...',
                        text: 'Sedang menghapus data purchase stiker',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: `/purchase-sticker/${id}`,
                        method: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                if (window.purchaseStickerTable) {
                                    window.purchaseStickerTable.ajax.reload(null, false);
                                }

                                Swal.fire({
                                    title: 'Terhapus!',
                                    text: response.message,
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
                                    'Tidak dapat menghapus purchase stiker saat ini.',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#3085d6'
                            });
                        }
                    });
                }
            });
        };

        window.showDetail = function(id) {
            Swal.fire({
                title: 'Memuat...',
                text: 'Mengambil detail purchase stiker',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            $.get(`/purchase-sticker/${id}`, function(response) {
                Swal.close();
                $('#detailContent').html(response);
                $('#detailModal').modal('show');
            }).fail(function(xhr) {
                Swal.fire({
                    title: 'Error',
                    text: 'Gagal memuat detail purchase stiker',
                    icon: 'error'
                });
            });
        };

        window.applyFilters = function() {
            if (window.purchaseStickerTable) {
                window.purchaseStickerTable.ajax.reload();
            }
        };

        window.clearFilters = function() {
            $('#filter_product').val('');
            $('#filter_ukuran').val('');
            if (window.purchaseStickerTable) {
                window.purchaseStickerTable.ajax.reload();
            }
        };

        function resetForm() {
            $('#purchaseStickerForm')[0].reset();
            $('#purchase_sticker_id').val('');
            clearValidationErrors();
        }

        function showValidationErrors(errors) {
            $.each(errors, function(field, messages) {
                const input = $(`[name="${field}"]`);
                input.addClass('is-invalid');
                $(`#error-${field}`).text(messages[0]);
            });
        }

        function clearValidationErrors() {
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
        }
    </script>
@endsection
