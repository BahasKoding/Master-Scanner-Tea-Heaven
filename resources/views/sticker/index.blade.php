@extends('layouts.main')

@section('title', 'Manajemen Sticker')
@section('breadcrumb-item', 'Sticker')

@section('css')
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- [Page specific CSS] start -->
    <!-- data tables css -->
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/buttons.bootstrap5.min.css') }}">
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

            /* Inline editing responsive */
            .stock-input {
                width: 60px !important;
                font-size: 12px !important;
            }

            .btn-sm {
                padding: 0.2rem 0.4rem !important;
                font-size: 0.75rem !important;
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

            .stock-input {
                width: 50px !important;
                font-size: 11px !important;
            }
        }

        /* Inline editing styles */
        .stock-input {
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: center;
            transition: border-color 0.3s ease;
        }

        .stock-input:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            outline: none;
        }

        .auto-field {
            background-color: #f8f9fa;
            color: #6c757d;
            font-weight: bold;
        }

        .sisa-display {
            font-weight: bold;
        }

        .update-btn {
            margin-right: 5px;
        }

        .table td {
            vertical-align: middle;
        }

        .badge.auto {
            font-size: 0.7em;
            margin-left: 5px;
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
                        <h5 class="mb-2 mb-sm-0">Manajemen Sticker - Input Langsung</h5>
                        <div class="d-flex flex-wrap">
                            <button id="clear-filters" class="btn btn btn-secondary me-2 mb-2 mb-sm-0">
                                <i class="fas fa-filter"></i> Hapus Filter
                            </button>
                            <button id="export-stickers" class="btn btn-success me-2 mb-2 mb-sm-0">
                                <i class="fas fa-file-excel"></i> Export Excel
                            </button>
                            @if (count($products) > 0)
                                <button type="button" class="btn btn-primary mb-2 mb-sm-0" data-bs-toggle="modal"
                                    data-bs-target="#addStickerModal">
                                    <i class="fas fa-plus"></i> Tambah Sticker
                                </button>
                            @else
                                <button type="button" class="btn btn-secondary mb-2 mb-sm-0" disabled
                                    title="Tidak ada produk yang tersedia untuk sticker">
                                    <i class="fas fa-plus"></i> Tambah Sticker
                                </button>
                            @endif
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
                                        @php
                                            $allEligibleProducts = \App\Models\Sticker::getEligibleProducts();
                                        @endphp
                                        @foreach ($allEligibleProducts as $product)
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

                    @if (count($products) == 0)
                        <!-- No Products Available Alert -->
                        <div class="alert alert-warning" role="alert">
                            <h6 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Tidak Ada Produk Tersedia
                            </h6>
                            <p class="mb-0">Semua produk yang memenuhi kriteria sudah memiliki data sticker. Anda hanya
                                dapat mengedit data sticker yang sudah ada.</p>
                        </div>
                    @endif

                    <!-- Info Box -->
                    <div class="alert alert-info" role="alert">
                        <h6 class="alert-heading"><i class="fas fa-info-circle"></i> Informasi Penggunaan</h6>
                        <p class="mb-1">• <strong>Stok Awal & Defect:</strong> Dapat diubah langsung di tabel</p>
                        <p class="mb-1">• <strong>Stok Masuk:</strong> Otomatis dari Purchase Sticker</p>
                        <p class="mb-1">• <strong>Produksi:</strong> Otomatis dari Catatan Produksi</p>
                        <p class="mb-0">• <strong>Sisa:</strong> Kalkulasi otomatis (Stok Awal + Stok Masuk - Produksi -
                            Defect)</p>
                    </div>

                    <div class="dt-responsive table-responsive">
                        <table id="sticker-table" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Produk</th>
                                    <th>SKU</th>
                                    <th>Ukuran</th>
                                    <th>Jumlah/A3</th>
                                    <th>Stok Awal</th>
                                    <th>Stok Masuk</th>
                                    <th>Produksi</th>
                                    <th>Defect</th>
                                    <th>Sisa</th>
                                    <th>Status</th>
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
                        @if (count($products) == 0)
                            <div class="alert alert-warning" role="alert">
                                <h6 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Tidak Ada Produk
                                    Tersedia</h6>
                                <p class="mb-0">Semua produk yang memenuhi kriteria sudah memiliki data sticker. Silakan
                                    refresh halaman jika Anda baru saja menghapus sticker.</p>
                            </div>
                        @endif
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
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>{{ count($products) }}</strong> produk tersedia
                                    (Hanya produk yang belum memiliki sticker)
                                </small>
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
                                <label for="add_stok_masuk" class="form-label">Stok Masuk</label>
                                <input type="number" class="form-control" id="add_stok_masuk" name="stok_masuk"
                                    min="0" value="0" readonly>
                                <small class="text-muted">Nilai otomatis dari Purchase Sticker</small>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="add_defect" class="form-label">Defect</label>
                                <input type="number" class="form-control" id="add_defect" name="defect"
                                    min="0" value="0">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="add_sisa" class="form-label">Sisa</label>
                                <input type="number" class="form-control" id="add_sisa" name="sisa" min="0"
                                    value="0" readonly>
                                <small class="text-muted">Kalkulasi otomatis</small>
                                <div class="invalid-feedback"></div>
                            </div>
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
                        @if (count($products) > 0)
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        @else
                            <button type="button" class="btn btn-secondary" disabled>Simpan</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
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

    <!-- XLSX library for Excel export -->
    <script src="{{ URL::asset('build/js/plugins/xlsx.full.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Setup CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initialize Choices for product filter
            var filterProductChoices = new Choices('#filter-product', {
                searchEnabled: true,
                searchPlaceholderValue: "Cari produk",
                itemSelectText: '',
                placeholder: true,
                placeholderValue: "Semua produk",
                allowHTML: false
            });

            // Initialize Choices for add product dropdown
            var addProductChoices = new Choices('#add_product_id', {
                searchEnabled: true,
                searchPlaceholderValue: "Cari produk...",
                itemSelectText: '',
                placeholder: true,
                placeholderValue: "Pilih Produk",
                noResultsText: 'Tidak ada produk yang ditemukan',
                noChoicesText: 'Tidak ada produk tersedia',
                allowHTML: false
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
                        name: 'stok_awal',
                        render: function(data, type, row) {
                            return `<input type="number" class="form-control form-control-sm stock-input" 
                                    data-field="stok_awal" data-sticker-id="${row.id}" 
                                    value="${data}" min="0" style="width: 80px;">`;
                        }
                    },
                    {
                        data: 'dynamic_stok_masuk',
                        name: 'stok_masuk',
                        render: function(data, type, row) {
                            return `<span class="auto-field">${data}</span>`;
                        }
                    },
                    {
                        data: 'dynamic_produksi',
                        name: 'produksi',
                        render: function(data, type, row) {
                            return `<span class="auto-field">${data}</span>`;
                        }
                    },
                    {
                        data: 'defect',
                        name: 'defect',
                        render: function(data, type, row) {
                            return `<input type="number" class="form-control form-control-sm stock-input" 
                                    data-field="defect" data-sticker-id="${row.id}" 
                                    value="${data}" min="0" style="width: 80px;">`;
                        }
                    },
                    {
                        data: 'dynamic_sisa',
                        name: 'sisa',
                        render: function(data, type, row) {
                            let badgeClass = data < 30 ? 'bg-danger' : 'bg-success';
                            return `<span class="badge ${badgeClass} sisa-display" data-sticker-id="${row.id}">${data}</span>`;
                        }
                    },
                    {
                        data: 'formatted_status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <div class="action-buttons">
                                    <button type="button" class="btn btn-sm btn-success update-btn" data-id="${data}">
                                        <i class="fas fa-save"></i> Update
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger delete-sticker" data-id="${data}" title="Hapus">
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
                    [1, 'asc']
                ]
            });

            // Filter functionality
            $('#filter-product, #filter-status').change(function() {
                table.draw();
            });

            // Clear filters
            $('#clear-filters').click(function() {
                filterProductChoices.setChoiceByValue('');
                $('#filter-status').val('');
                table.draw();
            });

            // Function to calculate sisa for a specific row
            function calculateRowSisa(stickerId) {
                const stokAwal = parseInt($(`input[data-sticker-id="${stickerId}"][data-field="stok_awal"]`)
                    .val()) || 0;
                const defect = parseInt($(`input[data-sticker-id="${stickerId}"][data-field="defect"]`).val()) || 0;

                // Get dynamic values from the row data (these are readonly)
                const rowData = table.row($(`input[data-sticker-id="${stickerId}"]`).closest('tr')).data();
                const stokMasuk = rowData ? rowData.dynamic_stok_masuk : 0;
                const produksi = rowData ? rowData.dynamic_produksi : 0;

                const sisa = stokAwal + stokMasuk - produksi - defect;

                // Update display
                const sisaElement = $(`.sisa-display[data-sticker-id="${stickerId}"]`);
                sisaElement.text(sisa);

                // Update badge class based on value
                sisaElement.removeClass('bg-success bg-danger');
                sisaElement.addClass(sisa < 30 ? 'bg-danger' : 'bg-success');

                return sisa;
            }

            // Live sisa calculation when input changes
            $(document).on('input', '.stock-input', function() {
                const stickerId = $(this).data('sticker-id');
                calculateRowSisa(stickerId);
            });

            // Update button click
            $(document).on('click', '.update-btn', function() {
                const stickerId = $(this).data('id');
                const stokAwal = parseInt($(`input[data-sticker-id="${stickerId}"][data-field="stok_awal"]`)
                    .val()) || 0;
                const defect = parseInt($(`input[data-sticker-id="${stickerId}"][data-field="defect"]`)
                    .val()) || 0;

                // Calculate sisa for validation
                const sisa = calculateRowSisa(stickerId);

                // Disable button during update
                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');

                // Prepare form data
                const formData = new FormData();
                formData.append('stok_awal', stokAwal);
                formData.append('defect', defect);
                formData.append('sisa', sisa);
                formData.append('status', 'active'); // Default status
                formData.append('_method', 'PUT');

                $.ajax({
                    url: "{{ route('stickers.update', ':id') }}".replace(':id', stickerId),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });

                            // Refresh the table to get updated dynamic values
                            table.draw(false);
                        }
                    },
                    error: function(xhr) {
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
                                    'Tidak dapat memperbarui sticker saat ini.',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#3085d6'
                            });
                        }
                    },
                    complete: function() {
                        // Re-enable button
                        $(`.update-btn[data-id="${stickerId}"]`).prop('disabled', false).html(
                            '<i class="fas fa-save"></i> Update');
                    }
                });
            });

            // Export Stickers
            $('#export-stickers').click(function() {
                var btn = $(this);
                var originalText = btn.html();

                // Show loading state
                btn.html('<i class="fas fa-spinner fa-spin"></i> Mengekspor...');
                btn.prop('disabled', true);

                // Prepare export data with current filters
                var exportData = {
                    product_id: $('#filter-product').val(),
                    status: $('#filter-status').val(),
                    ukuran: ''
                };

                $.ajax({
                    url: "{{ route('stickers.export') }}",
                    type: 'POST',
                    data: exportData,
                    success: function(response) {
                        if (response.status === 'success') {
                            // Create and download the Excel file
                            downloadExcel(response.data, 'Data_Sticker_' +
                                getCurrentDateString() + '.xlsx');
                            showAlert('success',
                                `Berhasil mengekspor ${response.count} data sticker ke Excel.`
                            );
                        } else {
                            showAlert('error', response.message ||
                                'Terjadi kesalahan saat mengekspor data.');
                        }
                    },
                    error: function(xhr) {
                        console.error('Export error:', xhr);
                        var errorMessage = xhr.responseJSON && xhr.responseJSON.message ?
                            xhr.responseJSON.message :
                            'Terjadi kesalahan saat mengekspor data.';
                        showAlert('error', errorMessage);
                    },
                    complete: function() {
                        // Restore button state
                        btn.html(originalText);
                        btn.prop('disabled', false);
                    }
                });
            });

            // Add Sticker Form Submit
            $('#addStickerForm').on('submit', function(e) {
                e.preventDefault();

                // Check if there are products available
                @if (count($products) == 0)
                    showAlert('warning', 'Tidak ada produk yang tersedia untuk membuat sticker baru.');
                    return;
                @endif

                var formData = new FormData(this);

                // Debug: Check if all required fields are filled
                var requiredFields = ['product_id', 'ukuran', 'jumlah', 'stok_awal', 'defect', 'status'];
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

                $.ajax({
                    url: "{{ route('stickers.store') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#addStickerModal').modal('hide');
                            $('#addStickerForm')[0].reset();
                            table.draw();
                            showAlert('success', response.message);

                            // Refresh the page to update available products list
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        } else {
                            showAlert('error', response.message ||
                                'Terjadi kesalahan yang tidak diketahui.');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON ? xhr.responseJSON.errors : {};
                            displayValidationErrors(errors, 'add');
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                showAlert('error', xhr.responseJSON.message);
                            }
                        } else {
                            showAlert('error', xhr.responseJSON && xhr.responseJSON.message ?
                                xhr.responseJSON.message :
                                'Terjadi kesalahan saat menyimpan data.');
                        }
                    }
                });
            });

            // Delete Sticker
            $(document).on('click', '.delete-sticker', function() {
                var id = $(this).data('id');
                var row = table.row($(this).parents('tr')).data();

                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    html: `Apakah Anda yakin ingin menghapus sticker ini?<br><strong>Produk:</strong> ${row.product_name}<br><strong>Ukuran:</strong> ${row.ukuran}`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('stickers.destroy', ':id') }}".replace(':id',
                                id),
                            type: 'DELETE',
                            success: function(response) {
                                if (response.success) {
                                    table.draw();
                                    showAlert('success', response.message);
                                }
                            },
                            error: function(xhr) {
                                showAlert('error', xhr.responseJSON.message ||
                                    'Terjadi kesalahan saat menghapus data.');
                            }
                        });
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
            $('#addStickerModal').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
                $(this).find('.is-invalid').removeClass('is-invalid');
                $(this).find('.invalid-feedback').text('');

                // Reset Choices.js dropdown
                addProductChoices.setChoiceByValue('');
            });

            // Helper function to download Excel file
            function downloadExcel(data, filename) {
                if (!data || data.length === 0) {
                    showAlert('warning', 'Tidak ada data untuk diekspor.');
                    return;
                }

                // Create workbook and worksheet
                var ws = XLSX.utils.json_to_sheet(data);
                var wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, "Data Sticker");

                // Auto-fit column widths
                var wscols = [{
                        wch: 5
                    }, {
                        wch: 12
                    }, {
                        wch: 25
                    }, {
                        wch: 8
                    }, {
                        wch: 12
                    },
                    {
                        wch: 15
                    }, {
                        wch: 15
                    }, {
                        wch: 10
                    }, {
                        wch: 10
                    }, {
                        wch: 10
                    },
                    {
                        wch: 8
                    }, {
                        wch: 8
                    }, {
                        wch: 10
                    }, {
                        wch: 20
                    }, {
                        wch: 20
                    }
                ];
                ws['!cols'] = wscols;

                // Write and save file
                XLSX.writeFile(wb, filename);
            }

            // Helper function to get current date string
            function getCurrentDateString() {
                var now = new Date();
                var year = now.getFullYear();
                var month = String(now.getMonth() + 1).padStart(2, '0');
                var day = String(now.getDate()).padStart(2, '0');
                var hours = String(now.getHours()).padStart(2, '0');
                var minutes = String(now.getMinutes()).padStart(2, '0');

                return `${year}${month}${day}_${hours}${minutes}`;
            }
        });
    </script>
@endsection
