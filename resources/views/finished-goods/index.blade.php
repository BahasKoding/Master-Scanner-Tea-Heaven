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

        /* Responsive improvements */
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

            .filter-toggle {
                display: block;
                width: 100%;
                margin-bottom: 10px;
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
                margin-bottom: 3px;
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

        .live-stock {
            font-size: 0.9rem;
            font-weight: bold;
        }

        .update-btn {
            margin-right: 5px;
        }

        .table td {
            vertical-align: middle;
        }

        .auto-field {
            background-color: #f8f9fa;
            color: #6c757d;
            font-weight: bold;
            cursor: not-allowed;
        }

        .auto-field:disabled {
            background-color: #e9ecef !important;
            border-color: #ced4da !important;
            opacity: 0.8;
        }

        .sisa-display {
            font-weight: bold;
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
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <h5 class="mb-2 mb-sm-0">Daftar Finished Goods - Semua Produk</h5>
                        <div class="d-flex flex-wrap">
                            <button id="clear-filters" class="btn btn btn-secondary me-2 mb-2 mb-sm-0">
                                <i class="fas fa-filter"></i> Hapus Filter
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="mb-3 p-3 border rounded bg-light filter-section">
                        <button id="filter-toggle-btn" class="btn btn-sm btn-outline-secondary mb-2 w-100 d-md-none"
                            type="button" data-bs-toggle="collapse" data-bs-target="#filterControls">
                            <i class="fas fa-filter"></i> Toggle Filters <i class="fas fa-chevron-down"></i>
                        </button>
                        <div id="filterControls" class="collapse show">
                            <div class="row">
                                <div class="col-lg-4 col-md-6 col-sm-12 mb-2">
                                    <label for="filter-product" class="form-label small filter-label">Produk</label>
                                    <select class="form-control form-control-sm" name="filter-product" id="filter-product">
                                        <option value="">Semua Produk</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name_product }}
                                                ({{ $product->sku }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-12 mb-2">
                                    <label for="filter-category" class="form-label small filter-label">Kategori</label>
                                    <select class="form-control form-control-sm" name="filter-category"
                                        id="filter-category">
                                        <option value="">Semua Kategori</option>
                                        @foreach ($categories as $key => $category)
                                            <option value="{{ $key }}">{{ $category }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-12 mb-2">
                                    <label for="filter-label" class="form-label small filter-label">Label</label>
                                    <select class="form-control form-control-sm" name="filter-label" id="filter-label">
                                        <option value="">Semua Label</option>
                                        @foreach ($labels as $key => $label)
                                            @if ($key > 0)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Filter Section -->

                    <!-- Info Box -->
                    <div class="alert alert-info" role="alert">
                        <h6 class="alert-heading"><i class="fas fa-info-circle"></i> Informasi Input Data</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong><i class="fas fa-edit text-primary"></i> Manual Input:</strong>
                                </p>
                                <ul class="mb-2">
                                    <li><strong>Stok Awal:</strong> Input manual oleh user</li>
                                    <li><strong>Defective:</strong> Input manual oleh user</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong><i class="fas fa-cog text-secondary"></i> Otomatis:</strong></p>
                                <ul class="mb-2">
                                    <li><strong>Stok Masuk:</strong> Dari Catatan Produksi</li>
                                    <li><strong>Stok Keluar:</strong> Dari History Sales Scanner</li>
                                    <li><strong>Live Stock:</strong> Kalkulasi otomatis</li>
                                </ul>
                            </div>
                        </div>
                        <small class="text-muted">💡 Field dengan latar abu-abu tidak dapat diedit karena nilainya dihitung
                            otomatis dari sistem</small>
                    </div>

                    <div class="dt-responsive table-responsive">
                        <table id="finishedGoods-table" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>SKU</th>
                                    <th>Produk</th>
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
            // Initialize choices for select inputs
            var filterProductChoices = new Choices('#filter-product', {
                searchEnabled: true,
                searchPlaceholderValue: "Cari produk",
                itemSelectText: '',
                placeholder: true,
                placeholderValue: "Semua produk",
                allowHTML: false
            });

            // Initialize DataTable
            var table = $('#finishedGoods-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('finished-goods.data') }}",
                    type: "POST",
                    data: function(d) {
                        d.product_id = $('#filter-product').val();
                        d.category_product = $('#filter-category').val();
                        d.label = $('#filter-label').val();
                        d._token = "{{ csrf_token() }}";
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
                        data: 'sku',
                        name: 'sku'
                    },
                    {
                        data: 'name_product',
                        name: 'name_product'
                    },
                    {
                        data: 'stok_awal_display',
                        name: 'stok_awal_display',
                        render: function(data, type, row) {
                            return `<input type="number" class="form-control form-control-sm stock-input" 
                                    data-field="stok_awal" data-product-id="${row.product_id}" 
                                    value="${data}" min="0" style="width: 80px;">`;
                        }
                    },
                    {
                        data: 'stok_masuk_display',
                        name: 'stok_masuk_display',
                        render: function(data, type, row) {
                            return `<input type="number" class="form-control form-control-sm stock-input auto-field" 
                                    data-field="stok_masuk" data-product-id="${row.product_id}" 
                                    value="${data}" min="0" style="width: 80px;" readonly disabled>
                                    <small class="text-muted d-block">Auto</small>`;
                        }
                    },
                    {
                        data: 'stok_keluar_display',
                        name: 'stok_keluar_display',
                        render: function(data, type, row) {
                            return `<input type="number" class="form-control form-control-sm stock-input auto-field" 
                                    data-field="stok_keluar" data-product-id="${row.product_id}" 
                                    value="${data}" min="0" style="width: 80px;" readonly disabled>
                                    <small class="text-muted d-block">Auto</small>`;
                        }
                    },
                    {
                        data: 'defective_display',
                        name: 'defective_display',
                        render: function(data, type, row) {
                            return `<input type="number" class="form-control form-control-sm stock-input" 
                                    data-field="defective" data-product-id="${row.product_id}" 
                                    value="${data}" min="0" style="width: 80px;">`;
                        }
                    },
                    {
                        data: 'live_stock_display',
                        name: 'live_stock_display',
                        render: function(data, type, row) {
                            return `<span class="badge bg-primary live-stock" data-product-id="${row.product_id}">${data}</span>
                                    <small class="text-muted d-block">Auto</small>`;
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <button type="button" class="btn btn-sm btn-success update-btn" data-id="${row.product_id}">
                                    <i class="fas fa-save"></i> Update
                                </button>
                                <button type="button" class="btn btn-sm btn-secondary reset-btn" data-id="${row.product_id}">
                                    <i class="fas fa-undo"></i> Reset
                                </button>
                            `;
                        }
                    }
                ],
                order: [
                    [2, 'asc']
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
            $('#filter-product, #filter-category, #filter-label').on('change', function() {
                table.ajax.reload();
            });

            // Clear filters function
            $('#clear-filters').on('click', function() {
                // Reset product filter
                filterProductChoices.setChoiceByValue('');

                // Reset category and label filters
                $('#filter-category').val('').trigger('change');
                $('#filter-label').val('').trigger('change');

                // Reload table
                table.ajax.reload();
            });

            // Function to calculate live stock for a specific row
            function calculateRowLiveStock(productId) {
                // Only get values from enabled inputs (manual inputs)
                const stokAwal = parseInt($(`input[data-product-id="${productId}"][data-field="stok_awal"]`)
                    .val()) || 0;
                const defective = parseInt($(`input[data-product-id="${productId}"][data-field="defective"]`)
                    .val()) || 0;

                // Get auto values from disabled inputs (just for display, but actual calculation will be server-side)
                const stokMasuk = parseInt($(`input[data-product-id="${productId}"][data-field="stok_masuk"]`)
                    .val()) || 0;
                const stokKeluar = parseInt($(`input[data-product-id="${productId}"][data-field="stok_keluar"]`)
                    .val()) || 0;

                const liveStock = stokAwal + stokMasuk - stokKeluar - defective;
                $(`.live-stock[data-product-id="${productId}"]`).text(Math.max(0, liveStock));

                return liveStock;
            }

            // Live stock calculation when input changes (only for enabled inputs)
            $(document).on('input', '.stock-input:not([disabled])', function() {
                const productId = $(this).data('product-id');
                calculateRowLiveStock(productId);
            });

            // Update button click - only send manual input fields
            $(document).on('click', '.update-btn', function() {
                const productId = $(this).data('id');

                // Only get values from manual input fields (enabled inputs)
                const stokAwal = parseInt($(`input[data-product-id="${productId}"][data-field="stok_awal"]`)
                    .val()) || 0;
                const defective = parseInt($(
                        `input[data-product-id="${productId}"][data-field="defective"]`)
                    .val()) || 0;

                // Disable button during update
                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');

                // Prepare form data - only send manual fields
                const formData = new FormData();
                formData.append('stok_awal', stokAwal);
                formData.append('defective', defective);
                formData.append('_method', 'PUT');
                formData.append('_token', '{{ csrf_token() }}');

                $.ajax({
                    url: `/finished-goods/${productId}`,
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

                            // Reload table to get updated dynamic values
                            table.ajax.reload(null, false);
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
                                    'Tidak dapat memperbarui stok saat ini.',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#3085d6'
                            });
                        }
                    },
                    complete: function() {
                        // Re-enable button
                        $(`.update-btn[data-id="${productId}"]`).prop('disabled', false).html(
                            '<i class="fas fa-save"></i> Update');
                    }
                });
            });

            // Reset button click - only reset manual fields
            $(document).on('click', '.reset-btn', function() {
                const productId = $(this).data('id');

                Swal.fire({
                    title: 'Reset Data Manual?',
                    text: "Apakah Anda yakin ingin mereset data stok manual (Stok Awal & Defective) ke 0?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Reset!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Reset only manual input fields to 0
                        $(`input[data-product-id="${productId}"][data-field="stok_awal"]`).val(0);
                        $(`input[data-product-id="${productId}"][data-field="defective"]`).val(0);
                        calculateRowLiveStock(productId);

                        Swal.fire({
                            title: 'Direset!',
                            text: 'Data stok manual telah direset ke 0.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    }
                });
            });
        });

        // Load the enhanced auto-refresh script
        $.getScript('{{ asset('js/finished-goods/auto-refresh.js') }}', function() {
            console.log('Auto-refresh script loaded successfully');
        }).fail(function(jqxhr, settings, exception) {
            console.error('Error loading auto-refresh script:', exception);

            // Fallback to basic auto-refresh if script fails to load
            setInterval(function() {
                if (table) {
                    table.ajax.reload(null, false); // false = tidak reset pagination
                }
            }, 30000); // 30 detik

            // Optional: Refresh saat tab menjadi aktif kembali
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden && table) {
                    table.ajax.reload(null, false);
                }
            });
        });
    </script>

@endsection
