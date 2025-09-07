@extends('layouts.main')

@section('title', 'Manajemen Inventory Bahan Baku')
@section('breadcrumb-item', 'Inventory Bahan Baku')

@section('css')
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- [Page specific CSS] start -->
    <!-- data tables css -->
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/buttons.bootstrap5.min.css') }}">
    <!-- Choices css is already included in main style.css -->
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

        /* Auto field styling to match finished goods */
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
                        <h5 class="mb-2 mb-sm-0">Daftar Inventory Bahan Baku - Semua Item</h5>
                        <div class="d-flex flex-wrap">
                            <button id="bulk-update-all" class="btn btn-primary me-2 mb-2 mb-sm-0">
                                <i class="fas fa-save"></i> Update Semua
                            </button>
                            <button id="sync-inventory" class="btn btn-success me-2 mb-2 mb-sm-0">
                                <i class="fas fa-sync-alt"></i> Sync Data
                            </button>
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
                                <div class="col-lg-3 col-md-6 col-sm-12 mb-2">
                                    <label for="filter-sku-induk" class="form-label small filter-label">SKU Induk</label>
                                    <input type="text" class="form-control form-control-sm" name="filter-sku-induk"
                                        id="filter-sku-induk" placeholder="Cari SKU...">
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12 mb-2">
                                    <label for="filter-nama-barang" class="form-label small filter-label">Nama
                                        Barang</label>
                                    <input type="text" class="form-control form-control-sm" name="filter-nama-barang"
                                        id="filter-nama-barang" placeholder="Cari nama barang...">
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12 mb-2">
                                    <label for="filter-category" class="form-label small filter-label">Kategori</label>
                                    <select class="form-control form-control-sm" name="filter-category"
                                        id="filter-category">
                                        <option value="">Semua Kategori</option>
                                        @foreach ($categories as $key => $category)
                                            <option value="{{ $key }}">{{ $category }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12 mb-2">
                                    <label for="filter-month-year" class="form-label small filter-label">
                                        <i class="fas fa-calendar-alt"></i> Filter Bulan/Tahun
                                    </label>
                                    <input type="month" class="form-control form-control-sm" name="filter-month-year"
                                        id="filter-month-year" value="{{ $filterMonthYear ?? date('Y-m') }}"
                                        title="Filter stok masuk/terpakai berdasarkan bulan dan tahun">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Filter Section -->

                    <!-- Search Information -->
                    <div class="alert alert-light border-primary" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-search text-primary me-2"></i>
                            <div>
                                <small class="text-muted mb-0">
                                    <strong>Info Pencarian:</strong> Kolom yang dapat dicari menggunakan "Search" adalah:
                                    <span class="badge bg-primary mx-1">SKU Induk</span>
                                    <span class="badge bg-success mx-1">Nama Barang</span>
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Filter Information -->
                    <div class="alert alert-info" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calendar-alt text-info me-2"></i>
                            <div>
                                <small class="mb-0">
                                    <strong>Filter Bulan Aktif:</strong>
                                    <span id="current-filter-display" class="badge bg-info mx-1">
                                        {{ date('F Y', strtotime(($filterMonthYear ?? date('Y-m')) . '-01')) }}
                                    </span>
                                    <br>
                                    <span class="text-muted">Stok masuk dan terpakai dihitung berdasarkan transaksi pada
                                        bulan yang dipilih.</span>
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="alert alert-info" role="alert">
                        <h6 class="alert-heading"><i class="fas fa-info-circle"></i> Informasi Input Data</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong><i class="fas fa-edit text-primary"></i> Manual Input:</strong>
                                </p>
                                <ul class="mb-2">
                                    <li><strong>Stok Awal:</strong> Input manual oleh user</li>
                                    <li><strong>Defect:</strong> Input manual oleh user</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong><i class="fas fa-cog text-secondary"></i> Otomatis:</strong></p>
                                <ul class="mb-2">
                                    <li><strong>Stok Masuk:</strong> Dari Purchase</li>
                                    <li><strong>Terpakai:</strong> Dari Catatan Produksi</li>
                                    <li><strong>Live Stock:</strong> Kalkulasi otomatis</li>
                                </ul>
                            </div>
                        </div>
                        <small class="text-muted">💡 Field dengan latar abu-abu tidak dapat diedit karena nilainya dihitung
                            otomatis dari sistem</small>
                    </div>

                    <div class="dt-responsive table-responsive">
                        <table id="inventoryBahanBaku-table" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>SKU</th>
                                    <th>Nama Bahan Baku</th>
                                    <th>Kategori</th>
                                    <th>Satuan</th>
                                    <th>Stok Awal</th>
                                    <th>Stok Masuk <small class="text-muted d-block">Monthly</small></th>
                                    <th>Terpakai <small class="text-muted d-block">Monthly</small></th>
                                    <th>Defect</th>
                                    <th>Stok Sisa</th>
                                    <th>Live Stock <small class="text-muted d-block">Calculated</small></th>
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
    <script src="{{ URL::asset('build/js/plugins/sweetalert2.all.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            console.log('Initializing inventory bahan baku DataTable...');

            // Initialize DataTable with optimized configuration
            let table = $('#inventoryBahanBaku-table').DataTable({
                processing: true,
                serverSide: true,
                deferRender: true, // Improve performance for large datasets
                searchDelay: 500, // Add delay to reduce server requests
                responsive: true,
                ajax: {
                    url: '{{ route('inventory-bahan-baku.data') }}',
                    type: 'POST',
                    data: function(d) {
                        d._token = $('meta[name="csrf-token"]').attr('content');
                        d.sku_induk = $('#filter-sku-induk').val();
                        d.nama_barang = $('#filter-nama-barang').val();
                        d.kategori = $('#filter-category').val();
                        d.filter_month_year = $('#filter-month-year').val();
                        return d;
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTables Error:', xhr.responseText);
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Error Memuat Data',
                                text: 'Terjadi kesalahan saat memuat data inventory.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            alert('Error memuat data: ' + error);
                        }
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        width: '5%'
                    },
                    {
                        data: 'sku_induk',
                        name: 'sku_induk',
                        searchable: true,
                        width: '10%'
                    },
                    {
                        data: 'nama_barang',
                        name: 'nama_barang',
                        searchable: true,
                        width: '20%'
                    },
                    {
                        data: 'kategori_name',
                        name: 'kategori_name',
                        searchable: false,
                        width: '10%'
                    },
                    {
                        data: 'satuan',
                        name: 'satuan',
                        searchable: false,
                        width: '8%'
                    },
                    {
                        data: 'stok_awal',
                        name: 'stok_awal',
                        orderable: false,
                        searchable: false,
                        width: '10%',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return `<input type="number" class="form-control form-control-sm stock-input" 
                                        data-field="stok_awal" data-bahan-baku-id="${row.bahan_baku_id}" 
                                        value="${data || 0}" min="0" style="width: 80px;">`;
                            }
                            return data;
                        }
                    },
                    {
                        data: 'stok_masuk_display',
                        name: 'stok_masuk_display',
                        orderable: false,
                        searchable: false,
                        width: '10%',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return `<span class="badge bg-secondary">${data || 0}</span>
                                        <small class="text-muted d-block">Auto</small>`;
                            }
                            return data;
                        }
                    },
                    {
                        data: 'terpakai_display',
                        name: 'terpakai_display',
                        orderable: false,
                        searchable: false,
                        width: '10%',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return `<span class="badge bg-warning">${data || 0}</span>
                                        <small class="text-muted d-block">Auto</small>`;
                            }
                            return data;
                        }
                    },
                    {
                        data: 'defect',
                        name: 'defect',
                        orderable: false,
                        searchable: false,
                        width: '8%',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return `<input type="number" class="form-control form-control-sm stock-input" 
                                        data-field="defect" data-bahan-baku-id="${row.bahan_baku_id}" 
                                        value="${data || 0}" min="0" style="width: 80px;">`;
                            }
                            return data;
                        }
                    },
                    {
                        data: 'stok_sisa',
                        name: 'stok_sisa',
                        orderable: false,
                        searchable: false,
                        width: '8%',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return `<span class="badge bg-info">${data || 0}</span>
                                        <small class="text-muted d-block">Auto</small>`;
                            }
                            return data;
                        }
                    },
                    {
                        data: 'live_stok_display',
                        name: 'live_stok_display',
                        orderable: false,
                        searchable: false,
                        width: '10%',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return `<span class="badge bg-primary live-stock" data-bahan-baku-id="${row.bahan_baku_id}">${data || 0}</span>
                                        <small class="text-muted d-block">Auto</small>`;
                            }
                            return data;
                        }
                    },
                ],
                order: [
                    [2, 'asc']
                ],
                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                dom: 'Blfrtip',
                buttons: [{
                        extend: 'copy',
                        text: '<i class="fas fa-copy"></i> Salin',
                        className: 'btn btn-secondary btn-sm'
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-success btn-sm'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Cetak',
                        className: 'btn btn-info btn-sm'
                    }
                ],
                // Performance optimizations
                stateSave: false, // Disable state saving for better search performance
                autoWidth: false,
                // Search optimization
                search: {
                    smart: false, // Disable smart search for better performance
                    regex: false,
                    caseInsensitive: true
                }
            });

            console.log('DataTable initialized successfully');

            // Initialize the current filter display on page load
            const initialMonthValue = $('#filter-month-year').val() || '{{ date('Y-m') }}';
            updateCurrentFilterDisplay(initialMonthValue);

            // Apply filters when input/dropdown values change with debouncing
            let filterTimeout;
            $('#filter-sku-induk, #filter-nama-barang, #filter-category, #filter-month-year').on('input change',
                function() {
                    const filterId = $(this).attr('id');
                    const filterValue = $(this).val();

                    // Special handling for month filter - update display dynamically
                    if (filterId === 'filter-month-year') {
                        updateCurrentFilterDisplay(filterValue);
                    }

                    // Clear existing timeout
                    clearTimeout(filterTimeout);

                    // Set new timeout to debounce filter changes
                    filterTimeout = setTimeout(function() {
                        // Clear search before applying filters to prevent conflicts
                        table.search('').draw();

                        // Reload with new filters
                        table.ajax.reload(null, false); // Don't reset paging
                    }, 300);
                });

            // Clear filters function with search reset
            $('#clear-filters').on('click', function() {
                // Clear DataTable search first
                table.search('').draw();

                $('#filter-sku-induk').val('');
                $('#filter-nama-barang').val('');
                $('#filter-category').val('');

                // Reset month filter to current month
                $('#filter-month-year').val('{{ date('Y-m') }}');

                // Update display to current month when clearing filters
                updateCurrentFilterDisplay('{{ date('Y-m') }}');

                table.ajax.reload(null, false);
            });

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Update last sync time
            $('#last-sync').text(new Date().toLocaleTimeString('id-ID'));

            // Function to calculate live stock for a specific row
            function calculateRowLiveStock(bahanBakuId) {
                // Only get values from enabled inputs (manual inputs)
                const stokAwal = parseInt($(`input[data-bahan-baku-id="${bahanBakuId}"][data-field="stok_awal"]`)
                    .val()) || 0;
                const defect = parseInt($(`input[data-bahan-baku-id="${bahanBakuId}"][data-field="defect"]`)
                    .val()) || 0;

                // Get auto values from disabled inputs (just for display, but actual calculation will be server-side)
                const stokMasuk = parseInt($(`input[data-bahan-baku-id="${bahanBakuId}"][data-field="stok_masuk"]`)
                    .val()) || 0;
                const terpakai = parseInt($(`input[data-bahan-baku-id="${bahanBakuId}"][data-field="terpakai"]`)
                    .val()) || 0;

                const liveStock = stokAwal + stokMasuk - terpakai - defect;
                $(`.live-stock[data-bahan-baku-id="${bahanBakuId}"]`).text(liveStock);

                return liveStock;
            }

            // Live stock calculation when input changes (only for enabled inputs)
            $(document).on('input', '.stock-input:not([disabled])', function() {
                const bahanBakuId = $(this).data('bahan-baku-id');
                calculateRowLiveStock(bahanBakuId);
            });

            // Track modified fields
            let modifiedFields = new Set();

            // Track field changes
            $(document).on('input', '.stock-input:not([disabled])', function() {
                const bahanBakuId = $(this).data('bahan-baku-id');
                const field = $(this).data('field');
                const key = `${bahanBakuId}_${field}`;

                modifiedFields.add(key);

                // Add visual indicator for modified fields
                $(this).addClass('border-warning');

                // Update bulk update button state
                updateBulkUpdateButton();

                // Calculate live stock for this row
                calculateRowLiveStock(bahanBakuId);
            });

            // Function to update bulk update button state
            function updateBulkUpdateButton() {
                const btn = $('#bulk-update-all');
                if (modifiedFields.size > 0) {
                    btn.removeClass('btn-secondary').addClass('btn-primary');
                    btn.html(`<i class="fas fa-save"></i> Update Semua (${modifiedFields.size} perubahan)`);
                    btn.prop('disabled', false);
                } else {
                    btn.removeClass('btn-primary').addClass('btn-secondary');
                    btn.html('<i class="fas fa-save"></i> Update Semua');
                    btn.prop('disabled', true);
                }
            }

            // Initialize bulk update button state
            updateBulkUpdateButton();

            // Bulk Update All functionality
            $('#bulk-update-all').on('click', function() {
                if (modifiedFields.size === 0) {
                    Swal.fire({
                        title: 'Tidak Ada Perubahan',
                        text: 'Tidak ada field yang dimodifikasi untuk diupdate.',
                        icon: 'info',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                const btn = $(this);
                const originalText = btn.html();

                // Collect all modified data
                const updates = [];
                const processedIds = new Set();
                const filterMonthYear = $('#filter-month-year').val();

                modifiedFields.forEach(key => {
                    const [bahanBakuId, field] = key.split('_');

                    if (!processedIds.has(bahanBakuId)) {
                        const stokAwal = parseInt($(
                            `input[data-bahan-baku-id="${bahanBakuId}"][data-field="stok_awal"]`
                        ).val()) || 0;
                        const defect = parseInt($(
                            `input[data-bahan-baku-id="${bahanBakuId}"][data-field="defect"]`
                        ).val()) || 0;

                        updates.push({
                            bahan_baku_id: parseInt(bahanBakuId),
                            stok_awal: stokAwal,
                            defect: defect
                        });

                        processedIds.add(bahanBakuId);
                    }
                });

                // Show confirmation dialog
                Swal.fire({
                    title: 'Konfirmasi Bulk Update',
                    html: `Apakah Anda yakin ingin memperbarui <strong>${updates.length} item</strong> inventory bahan baku?<br><br><small class="text-muted">Operasi ini akan memperbarui semua field yang telah dimodifikasi.</small>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Update Semua!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        performBulkUpdate(updates, btn, originalText);
                    }
                });
            });

            $(document).on('change', '.stock-input[data-field="stok_awal"], .stock-input[data-field="defect"]',
                function() {
                    console.log('Manual input field changed, updating inventory...');

                    const bahanBakuId = $(this).data('bahan-baku-id');
                    const updateButton = $(`.update-btn[data-id="${bahanBakuId}"]`);

                    // Get values from the input fields for the specific item
                    const stokAwal = parseInt($(
                        `input[data-bahan-baku-id="${bahanBakuId}"][data-field="stok_awal"]`).val()) || 0;
                    const defect = parseInt($(`input[data-bahan-baku-id="${bahanBakuId}"][data-field="defect"]`)
                        .val()) || 0;

                    // Temporarily disable the update button to prevent multiple submissions
                    updateButton.prop('disabled', true).html(
                        '<i class="fas fa-spinner fa-spin"></i> Updating...');

                    // Prepare form data
                    const formData = new FormData();
                    formData.append('stok_awal', stokAwal);
                    formData.append('defect', defect);
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

                                // Reload table to get updated dynamic values
                                // This is optional if you're not using DataTables or similar
                                // table.ajax.reload(null, false);
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
                            // Re-enable the button regardless of success or error
                            updateButton.prop('disabled', false).html(
                                '<i class="fas fa-save"></i> Update');
                        }
                    });
                });

            // Function to perform bulk update
            function performBulkUpdate(updates, btn, originalText) {
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');

                $.ajax({
                    url: '{{ route('inventory-bahan-baku.bulk-update') }}',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        updates: updates,
                        filter_month_year: filterMonthYear
                    },
                    success: function(response) {
                        if (response.success) {
                            const data = response.data;
                            let message = response.message;

                            if (data.error_count > 0) {
                                message +=
                                    `<br><small class="text-muted">Detail: ${data.success_count} berhasil, ${data.error_count} gagal</small>`;
                            }

                            Swal.fire({
                                title: 'Bulk Update Selesai!',
                                html: message,
                                icon: data.error_count > 0 ? 'warning' : 'success',
                                timer: 3000,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });

                            // Clear modified fields tracking
                            modifiedFields.clear();
                            $('.stock-input').removeClass('border-warning');
                            updateBulkUpdateButton();

                            // Reload table to get updated data
                            table.ajax.reload(null, false);
                        } else {
                            Swal.fire({
                                title: 'Bulk Update Gagal!',
                                text: response.message || 'Terjadi kesalahan saat bulk update',
                                icon: 'error'
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error('Bulk Update Error:', xhr);
                        let errorMessage = 'Terjadi kesalahan saat bulk update';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error'
                        });
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(originalText);
                    }
                });
            }

            // Function to update current filter display
            function updateCurrentFilterDisplay(monthValue) {
                if (monthValue) {
                    const date = new Date(monthValue + '-01');
                    const monthName = date.toLocaleDateString('id-ID', {
                        month: 'long',
                        year: 'numeric'
                    });
                    $('#current-filter-display').text(monthName);
                } else {
                    $('#current-filter-display').text('Semua Bulan');
                }
            }

            // Sync Inventory Data
            $('#sync-inventory').on('click', function() {
                const btn = $(this);
                const originalText = btn.html();
                const filterMonthYear = $('#filter-month-year').val();

                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Syncing...');

                $.ajax({
                    url: '{{ route('inventory-bahan-baku.sync-all') }}',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        filter_month_year: filterMonthYear
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Sync Berhasil!',
                                text: response.message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });
                            table.ajax.reload(null, false);
                        } else {
                            Swal.fire({
                                title: 'Sync Gagal!',
                                text: response.message ||
                                    'Terjadi kesalahan saat sync data',
                                icon: 'error'
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error('Sync Error:', xhr);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat sync data',
                            icon: 'error'
                        });
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(originalText);
                    }
                });
            });
        });
    </script>
@endsection
