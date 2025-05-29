@extends('layouts.main')

@section('title', 'Manajemen Inventory Bahan Baku')
@section('breadcrumb-item', 'Inventory Bahan Baku')

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

        /* Alert for low stock */
        .low-stock {
            background-color: #f8d7da !important;
            color: #721c24;
        }

        .normal-stock {
            background-color: #d1edff !important;
            color: #0c5460;
        }
    </style>
@endsection

@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">
        <!-- Inventory Bahan Baku Table start -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <h5 class="mb-2 mb-sm-0">Daftar Inventory Bahan Baku - Semua Bahan Baku</h5>
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
                                <div class="col-lg-6 col-md-6 col-sm-12 mb-2">
                                    <label for="filter-bahan-baku" class="form-label small filter-label">Bahan Baku</label>
                                    <select class="form-control form-control-sm" name="filter-bahan-baku"
                                        id="filter-bahan-baku">
                                        <option value="">Semua Bahan Baku</option>
                                        @foreach ($bahanBakus as $bahanBaku)
                                            <option value="{{ $bahanBaku->id }}">{{ $bahanBaku->nama_barang }}
                                                ({{ $bahanBaku->sku_induk }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 mb-2">
                                    <label for="filter-category" class="form-label small filter-label">Kategori</label>
                                    <select class="form-control form-control-sm" name="filter-category"
                                        id="filter-category">
                                        <option value="">Semua Kategori</option>
                                        @foreach ($categories as $key => $category)
                                            <option value="{{ $key }}">{{ $category }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Filter Section -->

                    <div class="dt-responsive table-responsive">
                        <table id="inventoryBahanBaku-table" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>SKU</th>
                                    <th>Nama Bahan Baku</th>
                                    <th>Satuan</th>
                                    <th>Stok Awal</th>
                                    <th>Stok Masuk</th>
                                    <th>Terpakai</th>
                                    <th>Surplus</th>
                                    <th>Defect</th>
                                    <th>Terjual</th>
                                    <th>Live Stok</th>
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
        <!-- Inventory Bahan Baku Table end -->
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

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            // Initialize choices for select inputs
            var filterBahanBakuChoices = new Choices('#filter-bahan-baku', {
                searchEnabled: true,
                searchPlaceholderValue: "Cari bahan baku",
                itemSelectText: '',
                placeholder: true,
                placeholderValue: "Semua bahan baku"
            });

            // Initialize DataTable
            var table = $('#inventoryBahanBaku-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('inventory-bahan-baku.data') }}",
                    type: "POST",
                    data: function(d) {
                        d.bahan_baku_id = $('#filter-bahan-baku').val();
                        d.kategori = $('#filter-category').val();
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
                        data: 'stok_awal_display',
                        name: 'stok_awal_display',
                        render: function(data, type, row) {
                            return `<input type="number" class="form-control form-control-sm stock-input" 
                                    data-field="stok_awal" data-bahan-baku-id="${row.bahan_baku_id}" 
                                    value="${data}" min="0" style="width: 80px;">`;
                        }
                    },
                    {
                        data: 'stok_masuk_display',
                        name: 'stok_masuk_display',
                        render: function(data, type, row) {
                            return `<input type="number" class="form-control form-control-sm stock-input" 
                                    data-field="stok_masuk" data-bahan-baku-id="${row.bahan_baku_id}" 
                                    value="${data}" min="0" style="width: 80px;">`;
                        }
                    },
                    {
                        data: 'terpakai_display',
                        name: 'terpakai_display',
                        render: function(data, type, row) {
                            return `<input type="number" class="form-control form-control-sm stock-input" 
                                    data-field="terpakai" data-bahan-baku-id="${row.bahan_baku_id}" 
                                    value="${data}" min="0" style="width: 80px;">`;
                        }
                    },
                    {
                        data: 'surplus_stok_display',
                        name: 'surplus_stok_display',
                        render: function(data, type, row) {
                            return `<input type="number" class="form-control form-control-sm stock-input" 
                                    data-field="surplus_stok" data-bahan-baku-id="${row.bahan_baku_id}" 
                                    value="${data}" min="0" style="width: 80px;">`;
                        }
                    },
                    {
                        data: 'defect_display',
                        name: 'defect_display',
                        render: function(data, type, row) {
                            return `<input type="number" class="form-control form-control-sm stock-input" 
                                    data-field="defect" data-bahan-baku-id="${row.bahan_baku_id}" 
                                    value="${data}" min="0" style="width: 80px;">`;
                        }
                    },
                    {
                        data: 'terjual_display',
                        name: 'terjual_display',
                        render: function(data, type, row) {
                            return `<input type="number" class="form-control form-control-sm stock-input" 
                                    data-field="terjual" data-bahan-baku-id="${row.bahan_baku_id}" 
                                    value="${data}" min="0" style="width: 80px;">`;
                        }
                    },
                    {
                        data: 'live_stok_gudang_display',
                        name: 'live_stok_gudang_display',
                        render: function(data, type, row) {
                            const stockClass = data <= 10 ? 'bg-danger' : 'bg-primary';
                            return `<span class="badge ${stockClass} live-stock" data-bahan-baku-id="${row.bahan_baku_id}">${data}</span>`;
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <button type="button" class="btn btn-sm btn-success update-btn" data-id="${row.bahan_baku_id}">
                                    <i class="fas fa-save"></i> Update
                                </button>
                                <button type="button" class="btn btn-sm btn-secondary reset-btn" data-id="${row.bahan_baku_id}">
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
            $('#filter-bahan-baku, #filter-category').on('change', function() {
                table.ajax.reload();
            });

            // Clear filters function
            $('#clear-filters').on('click', function() {
                // Reset bahan baku filter
                filterBahanBakuChoices.setChoiceByValue('');

                // Reset category filter
                $('#filter-category').val('').trigger('change');

                // Reload table
                table.ajax.reload();
            });

            // Function to calculate live stock for a specific row
            function calculateRowLiveStock(bahanBakuId) {
                const stokAwal = parseInt($(`input[data-bahan-baku-id="${bahanBakuId}"][data-field="stok_awal"]`)
                    .val()) || 0;
                const stokMasuk = parseInt($(`input[data-bahan-baku-id="${bahanBakuId}"][data-field="stok_masuk"]`)
                    .val()) || 0;
                const terpakai = parseInt($(`input[data-bahan-baku-id="${bahanBakuId}"][data-field="terpakai"]`)
                    .val()) || 0;
                const surplusStok = parseInt($(
                        `input[data-bahan-baku-id="${bahanBakuId}"][data-field="surplus_stok"]`)
                    .val()) || 0;
                const defect = parseInt($(`input[data-bahan-baku-id="${bahanBakuId}"][data-field="defect"]`)
                    .val()) || 0;
                const terjual = parseInt($(`input[data-bahan-baku-id="${bahanBakuId}"][data-field="terjual"]`)
                    .val()) || 0;

                // Formula: stok_awal + stok_masuk - terpakai - defect - terjual
                const liveStock = stokAwal + stokMasuk - terpakai - defect - terjual;

                const $liveStockElement = $(`.live-stock[data-bahan-baku-id="${bahanBakuId}"]`);
                $liveStockElement.text(liveStock);

                // Update badge color based on stock level
                $liveStockElement.removeClass('bg-primary bg-danger');
                if (liveStock <= 10) {
                    $liveStockElement.addClass('bg-danger');
                } else {
                    $liveStockElement.addClass('bg-primary');
                }

                return liveStock;
            }

            // Live stock calculation when input changes
            $(document).on('input', '.stock-input', function() {
                const bahanBakuId = $(this).data('bahan-baku-id');
                calculateRowLiveStock(bahanBakuId);
            });

            // Update button click
            $(document).on('click', '.update-btn', function() {
                const bahanBakuId = $(this).data('id');
                const stokAwal = parseInt($(
                        `input[data-bahan-baku-id="${bahanBakuId}"][data-field="stok_awal"]`)
                    .val()) || 0;
                const stokMasuk = parseInt($(
                    `input[data-bahan-baku-id="${bahanBakuId}"][data-field="stok_masuk"]`).val()) || 0;
                const terpakai = parseInt($(
                    `input[data-bahan-baku-id="${bahanBakuId}"][data-field="terpakai"]`).val()) || 0;
                const surplusStok = parseInt($(
                    `input[data-bahan-baku-id="${bahanBakuId}"][data-field="surplus_stok"]`).val()) || 0;
                const defect = parseInt($(
                    `input[data-bahan-baku-id="${bahanBakuId}"][data-field="defect"]`).val()) || 0;
                const terjual = parseInt($(
                    `input[data-bahan-baku-id="${bahanBakuId}"][data-field="terjual"]`).val()) || 0;

                // Disable button during update
                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');

                // Prepare form data
                const formData = new FormData();
                formData.append('stok_awal', stokAwal);
                formData.append('stok_masuk', stokMasuk);
                formData.append('terpakai', terpakai);
                formData.append('surplus_stok', surplusStok);
                formData.append('defect', defect);
                formData.append('terjual', terjual);
                formData.append('_method', 'PUT');
                formData.append('_token', '{{ csrf_token() }}');

                $.ajax({
                    url: `/inventory-bahan-baku/${bahanBakuId}`,
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

                            // Update live stock display
                            calculateRowLiveStock(bahanBakuId);
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
                                    'Tidak dapat memperbarui inventory saat ini.',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#3085d6'
                            });
                        }
                    },
                    complete: function() {
                        // Re-enable button
                        $(`.update-btn[data-id="${bahanBakuId}"]`).prop('disabled', false).html(
                            '<i class="fas fa-save"></i> Update');
                    }
                });
            });

            // Reset button click
            $(document).on('click', '.reset-btn', function() {
                const bahanBakuId = $(this).data('id');

                Swal.fire({
                    title: 'Reset Data?',
                    text: "Apakah Anda yakin ingin mereset data inventory ke nilai awal?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Reset!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Reset all inputs to 0
                        $(`input[data-bahan-baku-id="${bahanBakuId}"]`).val(0);
                        calculateRowLiveStock(bahanBakuId);

                        Swal.fire({
                            title: 'Direset!',
                            text: 'Data inventory telah direset ke 0.',
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
    </script>
@endsection
