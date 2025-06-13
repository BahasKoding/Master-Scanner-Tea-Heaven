@extends('layouts.main')

@section('title', 'Purchase Items')
@section('breadcrumb-item', 'Purchase Items')

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

        /* Choices.js custom styling */
        .choices {
            margin-bottom: 0;
        }

        .choices__inner {
            min-height: 38px;
            padding: 4px 8px;
            background-color: #fff;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
        }

        .choices__list--dropdown .choices__item {
            padding: 8px 12px;
        }

        .choices__item--choice {
            word-break: break-word;
        }

        .choices[data-type*="select-one"] .choices__inner {
            cursor: pointer;
        }

        .choices__list--dropdown .choices__item--highlighted {
            background-color: #0d6efd;
            color: #fff;
        }

        .is-open .choices__inner {
            border-radius: 0.375rem 0.375rem 0 0;
        }



        /* Reset button styling */
        .btn-outline-secondary:hover {
            background-color: #6c757d;
            border-color: #6c757d;
            color: #fff;
        }

        /* Disabled select styling */
        select:disabled {
            background-color: #f8f9fa;
            color: #6c757d;
            cursor: not-allowed;
        }

        .choices.is-disabled .choices__inner {
            background-color: #f8f9fa;
            color: #6c757d;
            cursor: not-allowed;
        }
    </style>
@endsection

@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">
        <!-- Purchase Table start -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <h5 class="mb-2 mb-sm-0">{{ $item ?? 'Purchase Items' }}</h5>
                        <div class="d-flex flex-wrap">
                            <button type="button" class="btn btn-primary mb-2 mb-sm-0" onclick="showCreateModal()">
                                <i class="fas fa-plus"></i> Tambah Purchase
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
                                <div class="col-md-2 col-sm-12 mb-2">
                                    <label for="filter_kategori" class="form-label filter-label">Kategori</label>
                                    <select class="form-select form-select-sm" id="filter_kategori"
                                        onchange="updateItemFilter()">
                                        <option value="">-- Semua Kategori --</option>
                                        <option value="bahan_baku">Bahan Baku</option>
                                        <option value="finished_goods">Finished Goods</option>
                                    </select>
                                </div>
                                <div class="col-md-4 col-sm-12 mb-2">
                                    <label for="filter_item" class="form-label filter-label">Filter Item</label>
                                    <select class="form-select form-select-sm" id="filter_item">
                                        <option value="">-- Semua Item --</option>
                                        @foreach ($bahanBakus as $bahanBaku)
                                            <option value="{{ $bahanBaku->id }}" data-kategori="bahan_baku">
                                                {{ $bahanBaku->full_name }}</option>
                                        @endforeach
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" data-kategori="finished_goods">
                                                {{ $product->name_product }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 col-sm-12 mb-2">
                                    <label for="filter_start_date" class="form-label filter-label">Tanggal Mulai</label>
                                    <input type="date" class="form-control form-control-sm" id="filter_start_date">
                                </div>
                                <div class="col-md-3 col-sm-12 mb-2">
                                    <label for="filter_end_date" class="form-label filter-label">Tanggal Akhir</label>
                                    <input type="date" class="form-control form-control-sm" id="filter_end_date">
                                </div>
                                <div class="col-md-2 col-sm-12 d-flex align-items-end">
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
                        <table id="purchase-table" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kategori</th>
                                    <th>Item</th>
                                    <th>SKU</th>
                                    <th>Satuan</th>
                                    <th>Qty Pembelian</th>
                                    <th>Tanggal Kedatangan</th>
                                    <th>Qty Masuk</th>
                                    <th>Defect</th>
                                    <th>Retur</th>
                                    <th>Total Masuk</th>
                                    <th>Penerima</th>
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
        <!-- Purchase Table end -->
    </div>
    <!-- [ Main Content ] end -->

    <!-- Create/Edit Modal -->
    <div class="modal fade" id="purchaseModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="purchaseModalLabel">Tambah Purchase Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="purchaseForm">
                    @csrf
                    <input type="hidden" id="purchase_id" name="purchase_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="kategori" class="form-label">Kategori <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="kategori" name="kategori" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        <option value="bahan_baku">Bahan Baku</option>
                                        <option value="finished_goods">Finished Goods</option>
                                    </select>
                                    <div class="invalid-feedback" id="error-kategori"></div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="bahan_baku_id" class="form-label">Item <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select item-select" id="bahan_baku_id" name="bahan_baku_id"
                                        required disabled>
                                        <option value="">-- Pilih Kategori Terlebih Dahulu --</option>
                                    </select>
                                    <div class="invalid-feedback" id="error-bahan_baku_id"></div>
                                    <small class="text-muted">Pilih kategori terlebih dahulu untuk memilih item</small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-grid">
                                        <button type="button" class="btn btn-outline-secondary"
                                            onclick="resetKategoriAndItem(true)" title="Reset Kategori dan Item">
                                            <i class="fas fa-undo"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="qty_pembelian" class="form-label">Quantity Pembelian <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="qty_pembelian" name="qty_pembelian"
                                        min="1" required>
                                    <div class="invalid-feedback" id="error-qty_pembelian"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tanggal_kedatangan_barang" class="form-label">Tanggal Kedatangan</label>
                                    <input type="date" class="form-control" id="tanggal_kedatangan_barang"
                                        name="tanggal_kedatangan_barang">
                                    <div class="invalid-feedback" id="error-tanggal_kedatangan_barang"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="checker_penerima_barang" class="form-label">Penerima Barang</label>
                                    <input type="text" class="form-control" id="checker_penerima_barang"
                                        name="checker_penerima_barang" placeholder="Nama penerima barang">
                                    <div class="invalid-feedback" id="error-checker_penerima_barang"></div>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h6>Detail Penerimaan Barang</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="qty_barang_masuk" class="form-label">Qty Barang Masuk</label>
                                    <input type="number" class="form-control" id="qty_barang_masuk"
                                        name="qty_barang_masuk" min="0" value="0">
                                    <div class="invalid-feedback" id="error-qty_barang_masuk"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="barang_defect_tanpa_retur" class="form-label">Barang Defect</label>
                                    <input type="number" class="form-control" id="barang_defect_tanpa_retur"
                                        name="barang_defect_tanpa_retur" min="0" value="0">
                                    <div class="invalid-feedback" id="error-barang_defect_tanpa_retur"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="barang_diretur_ke_supplier" class="form-label">Barang Retur</label>
                                    <input type="number" class="form-control" id="barang_diretur_ke_supplier"
                                        name="barang_diretur_ke_supplier" min="0" value="0">
                                    <div class="invalid-feedback" id="error-barang_diretur_ke_supplier"></div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <strong>Total Stok Masuk:</strong> <span id="total_stok_display">0</span> <span
                                id="satuan_display"></span>
                            <br><small>Formula: Qty Masuk - Defect + Retur</small>
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

    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Purchase</h5>
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
    <script src="{{ URL::asset('build/js/plugins/sweetalert2.all.min.js') }}"></script>

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
            // Variables to hold Choices instances
            window.itemChoices = null;

            // Initialize Choices.js for item selection
            function initItemChoices() {
                const itemSelect = document.getElementById('bahan_baku_id');
                if (itemSelect && !itemSelect.disabled) {
                    // Destroy existing instance if it exists
                    if (window.itemChoices) {
                        try {
                            window.itemChoices.destroy();
                            window.itemChoices = null;
                        } catch (e) {
                            // Ignore destroy errors
                        }
                    }

                    window.itemChoices = new Choices(itemSelect, {
                        searchEnabled: true,
                        searchPlaceholderValue: "Cari item berdasarkan SKU atau nama...",
                        itemSelectText: '',
                        placeholder: true,
                        placeholderValue: "-- Pilih Item --",
                        removeItemButton: false,
                        allowHTML: false,
                        classNames: {
                            containerOuter: 'choices',
                        }
                    });
                }
            }

            // Don't initialize item choices on page load since it starts disabled
            // initItemChoices(); // This will be called when kategori is selected

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

            // Responsive adjustments for mobile
            function adjustForMobile() {
                if (window.innerWidth < 768) {
                    // Modify table for better mobile view
                    $('.dt-responsive table').addClass('table-sm');

                    // Ensure filters are collapsed by default on mobile
                    if (!$('#filterControls').hasClass('show')) {
                        $('#filterControls').collapse('hide');
                    }
                } else {
                    // Remove mobile-specific modifications
                    $('.dt-responsive table').removeClass('table-sm');
                    $('#filterControls').addClass('show');
                }
            }

            // Call adjustments on page load and resize
            adjustForMobile();
            $(window).resize(function() {
                adjustForMobile();
            });

            // Initialize DataTable
            try {
                var table = $('#purchase-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('purchase.index') }}",
                        data: function(d) {
                            d.kategori = $('#filter_kategori').val();
                            d.bahan_baku_id = $('#filter_item').val();
                            d.start_date = $('#filter_start_date').val();
                            d.end_date = $('#filter_end_date').val();
                            return d;
                        },
                        error: function(xhr, error, code) {
                            console.error('DataTables Ajax Error:', xhr.responseText);
                            console.error('Error details:', error, code);
                            console.error('Status:', xhr.status);
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
                            data: 'kategori_display',
                            name: 'kategori_display',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'item_name',
                            name: 'item_name'
                        },
                        {
                            data: 'item_sku',
                            name: 'item_sku'
                        },
                        {
                            data: 'satuan',
                            name: 'satuan',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'qty_pembelian',
                            name: 'qty_pembelian'
                        },
                        {
                            data: 'tanggal_kedatangan_barang',
                            name: 'tanggal_kedatangan_barang'
                        },
                        {
                            data: 'qty_barang_masuk',
                            name: 'qty_barang_masuk'
                        },
                        {
                            data: 'barang_defect_tanpa_retur',
                            name: 'barang_defect_tanpa_retur'
                        },
                        {
                            data: 'barang_diretur_ke_supplier',
                            name: 'barang_diretur_ke_supplier'
                        },
                        {
                            data: 'total_stok_masuk',
                            name: 'total_stok_masuk'
                        },
                        {
                            data: 'checker_penerima_barang',
                            name: 'checker_penerima_barang'
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
                                        <button type="button" class="btn btn-sm btn-warning" onclick="editPurchase(${data})" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="deletePurchase(${data})" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                `;
                            }
                        }
                    ],
                    order: [
                        [6, 'desc']
                    ], // Order by tanggal_kedatangan_barang desc
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
                        emptyTable: 'Tidak ada data purchase',
                        zeroRecords: 'Tidak ditemukan purchase yang sesuai',
                        info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ purchase',
                        infoEmpty: 'Menampilkan 0 sampai 0 dari 0 purchase',
                        infoFiltered: '(difilter dari _MAX_ total purchase)',
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

                // Store table reference globally
                window.purchaseTable = table;

                // Apply kategori filter
                $('#filter_kategori').on('change', function() {
                    table.draw();
                });

                // Apply item filter
                $('#filter_item').on('change', function() {
                    table.draw();
                });

                // Apply date filters
                $('#filter_start_date, #filter_end_date').on('change', function() {
                    table.draw();
                });

                // Calculate total stock on input change
                $('#qty_barang_masuk, #barang_defect_tanpa_retur, #barang_diretur_ke_supplier').on('input',
                    function() {
                        calculateTotalStock();
                    });

                // Handle item selection change - attach to document to handle dynamically recreated elements
                $(document).on('change', '#bahan_baku_id', function() {
                    var selectedOption = $(this).find('option:selected');



                    // Update satuan display
                    if (selectedOption.val()) {
                        updateSatuanDisplay(selectedOption[0]);
                    } else {
                        $('#satuan_display').text('');
                    }
                });

                // Handle choices:change event from Choices.js for item selection
                $(document).on('choice', '#bahan_baku_id', function(event) {
                    const selectedValue = this.value;
                    const selectedOption = $(this).find('option:selected')[0];

                    if (selectedValue && selectedOption) {
                        updateSatuanDisplay(selectedOption);
                    } else {
                        $('#satuan_display').text('');
                    }
                });

                // Form submission
                $('#purchaseForm').on('submit', function(e) {
                    e.preventDefault();

                    var formData = new FormData(this);
                    var purchaseId = $('#purchase_id').val();
                    var url = purchaseId ? `/purchase/${purchaseId}` : "{{ route('purchase.store') }}";

                    if (purchaseId) {
                        formData.append('_method', 'PUT');
                    }



                    // Show loading state
                    Swal.fire({
                        title: purchaseId ? 'Memperbarui...' : 'Menyimpan...',
                        text: 'Sedang memproses data purchase',
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
                                $('#purchaseModal').modal('hide');
                                if (window.purchaseTable) {
                                    window.purchaseTable.ajax.reload(null, false);
                                }

                                // Show success message with SweetAlert
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
                                // Validation errors
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
                            $('#saveBtn').prop('disabled', false).html(
                                '<i class="fas fa-save me-1"></i>Simpan');
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
            $('#purchaseModal, #detailModal').on('hidden.bs.modal', function() {
                $(this).find('form')[0]?.reset();
                $(this).find('.is-invalid').removeClass('is-invalid');

                // Reset to initial state
                resetKategoriAndItem();

                // Clean up any lingering modal artifacts
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                $('body').css('padding-right', '');
            });

            // Modal is shown - no need to initialize choices since they start disabled

            // Handle kategori change
            $(document).on('change', '#kategori', function() {
                updateItemOptions();
            });
        });

        // Global functions
        window.showCreateModal = function() {
            try {
                // Initialize itemChoices if not exists
                if (!window.itemChoices) {
                    window.itemChoices = null;
                }

                $('#purchaseModalLabel').text('Tambah Purchase Item');
                $('#purchaseModal').modal('show');

                // Reset form after modal is shown to avoid race conditions
                setTimeout(() => {
                    resetForm();
                }, 100);
            } catch (e) {
                console.error('Error opening create modal:', e);
                Swal.fire({
                    title: 'Error',
                    text: 'Gagal membuka form tambah purchase',
                    icon: 'error'
                });
            }
        };

        // Reset kategori and item function - silent version (no notification)
        window.resetKategoriAndItem = function(showNotification = false) {
            // Reset kategori
            $('#kategori').val('');

            // Destroy choices instance if exists
            if (window.itemChoices) {
                try {
                    window.itemChoices.destroy();
                    window.itemChoices = null;
                } catch (e) {
                    // Ignore destroy errors
                }
            }

            // Reset item select to disabled state
            const selectElement = document.getElementById('bahan_baku_id');
            if (selectElement) {
                selectElement.innerHTML = '<option value="">-- Pilih Kategori Terlebih Dahulu --</option>';
                selectElement.disabled = true;
                selectElement.value = '';
            }

            // Clear satuan display
            $('#satuan_display').text('');

            // Show toast message only if requested
            if (showNotification) {
                Swal.fire({
                    title: 'Reset Berhasil',
                    text: 'Kategori dan item telah direset',
                    icon: 'info',
                    timer: 1500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }
        };

        // Update item filter based on kategori
        window.updateItemFilter = function() {
            var selectedKategori = $('#filter_kategori').val();
            var itemSelect = $('#filter_item');

            // Show all options first
            itemSelect.find('option').show();

            if (selectedKategori) {
                // Hide options that don't match selected kategori
                itemSelect.find('option[data-kategori]').each(function() {
                    if ($(this).data('kategori') !== selectedKategori) {
                        $(this).hide();
                    }
                });
            }

            // Reset selection if current selection is now hidden
            if (itemSelect.find('option:selected').is(':hidden')) {
                itemSelect.val('');
            }
        };

        // Update item options in modal based on kategori
        window.updateItemOptions = function() {
            var selectedKategori = $('#kategori').val();
            const selectElement = document.getElementById('bahan_baku_id');

            // Destroy existing Choices instance if exists
            if (window.itemChoices) {
                try {
                    window.itemChoices.destroy();
                    window.itemChoices = null;
                } catch (e) {
                    // Ignore destroy errors
                }
            }

            // Clear the select element
            selectElement.innerHTML = '';

            if (!selectedKategori) {
                // No kategori selected - disable item select
                selectElement.innerHTML = '<option value="">-- Pilih Kategori Terlebih Dahulu --</option>';
                selectElement.disabled = true;
                hideItemDetail();
                return;
            }

            // Kategori selected - enable item select and populate options
            selectElement.disabled = false;
            selectElement.innerHTML = '<option value="">-- Pilih Item --</option>';

            if (selectedKategori === 'bahan_baku') {
                // Only show bahan baku options
                @foreach ($bahanBakus as $bahanBaku)
                    selectElement.innerHTML += `<option value="{{ $bahanBaku->id }}" 
                        data-satuan="{{ $bahanBaku->satuan }}" 
                        data-kategori="bahan_baku" 
                        data-sku="{{ $bahanBaku->sku_induk }}" 
                        data-name="{{ $bahanBaku->nama_barang }}">
                        [{{ $bahanBaku->sku_induk }}] {{ $bahanBaku->nama_barang }}
                    </option>`;
                @endforeach
            } else if (selectedKategori === 'finished_goods') {
                // Only show finished goods options
                @foreach ($products as $product)
                    selectElement.innerHTML += `<option value="{{ $product->id }}" 
                        data-satuan="{{ $product->satuan ?? 'pcs' }}" 
                        data-kategori="finished_goods" 
                        data-sku="{{ $product->sku }}" 
                        data-name="{{ $product->name_product }}">
                        [{{ $product->sku }}] {{ $product->name_product }}
                    </option>`;
                @endforeach
            }

            // Create new Choices instance only if item select is enabled
            if (!selectElement.disabled) {
                window.itemChoices = new Choices(selectElement, {
                    searchEnabled: true,
                    searchPlaceholderValue: "Cari item berdasarkan SKU atau nama...",
                    itemSelectText: '',
                    placeholder: true,
                    placeholderValue: "-- Pilih Item --",
                    removeItemButton: false,
                    allowHTML: false,
                    classNames: {
                        containerOuter: 'choices',
                    }
                });
            }

            // Clear satuan display when kategori changes
            $('#satuan_display').text('');
        };

        // Function to update satuan display when item is selected
        function updateSatuanDisplay(option) {
            if (option && option.dataset && option.dataset.satuan) {
                $('#satuan_display').text(option.dataset.satuan);
            } else {
                $('#satuan_display').text('');
            }
        }

        window.editPurchase = function(id) {
            // Show loading state
            Swal.fire({
                title: 'Memuat...',
                text: 'Mengambil data purchase',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            $.get(`/purchase/${id}/edit`, function(response) {
                Swal.close();

                if (response.success) {
                    var data = response.data;
                    $('#purchase_id').val(data.id);

                    // Set kategori first to filter items
                    $('#kategori').val(data.kategori);
                    updateItemOptions();

                    // Wait a bit for Choices to be recreated, then set item value
                    setTimeout(() => {
                        if (window.itemChoices) {
                            try {
                                window.itemChoices.setChoiceByValue(data.bahan_baku_id.toString());
                            } catch (e) {
                                // Fallback to regular select
                                $('#bahan_baku_id').val(data.bahan_baku_id);
                            }
                        } else {
                            $('#bahan_baku_id').val(data.bahan_baku_id);
                        }

                        // Trigger change to update satuan
                        $('#bahan_baku_id').trigger('change');
                    }, 200);
                    $('#qty_pembelian').val(data.qty_pembelian);
                    $('#tanggal_kedatangan_barang').val(data.tanggal_kedatangan_barang);
                    $('#qty_barang_masuk').val(data.qty_barang_masuk);
                    $('#barang_defect_tanpa_retur').val(data.barang_defect_tanpa_retur);
                    $('#barang_diretur_ke_supplier').val(data.barang_diretur_ke_supplier);
                    $('#checker_penerima_barang').val(data.checker_penerima_barang);

                    calculateTotalStock();

                    $('#purchaseModalLabel').text('Edit Purchase Item');
                    $('#purchaseModal').modal('show');
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: response.message || 'Gagal mengambil data purchase',
                        icon: 'error'
                    });
                }
            }).fail(function(xhr) {
                Swal.fire({
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Gagal memuat data purchase',
                    icon: 'error'
                });
            });
        };

        window.deletePurchase = function(id) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: "Apakah Anda yakin ingin menghapus purchase ini? Tindakan ini tidak dapat dibatalkan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Menghapus...',
                        text: 'Sedang menghapus data purchase',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: `/purchase/${id}`,
                        method: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                if (window.purchaseTable) {
                                    window.purchaseTable.ajax.reload(null, false);
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
                                    'Tidak dapat menghapus purchase saat ini.',
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
            // Show loading state
            Swal.fire({
                title: 'Memuat...',
                text: 'Mengambil detail purchase',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            $.get(`/purchase/${id}`, function(response) {
                Swal.close();
                $('#detailContent').html(response);
                $('#detailModal').modal('show');
            }).fail(function(xhr) {
                Swal.fire({
                    title: 'Error',
                    text: 'Gagal memuat detail purchase',
                    icon: 'error'
                });
            });
        };

        window.applyFilters = function() {
            if (window.purchaseTable) {
                window.purchaseTable.ajax.reload();
            }
        };

        window.clearFilters = function() {
            $('#filter_kategori').val('');
            $('#filter_item').val('');
            $('#filter_start_date').val('');
            $('#filter_end_date').val('');
            updateItemFilter(); // Reset item filter options
            if (window.purchaseTable) {
                window.purchaseTable.ajax.reload();
            }
        };

        function calculateTotalStock() {
            var masuk = parseInt($('#qty_barang_masuk').val()) || 0;
            var defect = parseInt($('#barang_defect_tanpa_retur').val()) || 0;
            var retur = parseInt($('#barang_diretur_ke_supplier').val()) || 0;
            var total = masuk - defect + retur;
            $('#total_stok_display').text(total);
        }

        function resetForm() {
            $('#purchaseForm')[0].reset();
            $('#purchase_id').val('');
            $('#total_stok_display').text('0');
            $('#satuan_display').text('');
            clearValidationErrors();

            // Reset to initial state manually to avoid calling resetKategoriAndItem again
            $('#kategori').val('');

            // Destroy choices instance if exists
            if (window.itemChoices) {
                try {
                    window.itemChoices.destroy();
                    window.itemChoices = null;
                } catch (e) {
                    // Ignore destroy errors
                }
            }

            // Reset item select to disabled state
            const selectElement = document.getElementById('bahan_baku_id');
            if (selectElement) {
                selectElement.innerHTML = '<option value="">-- Pilih Kategori Terlebih Dahulu --</option>';
                selectElement.disabled = true;
                selectElement.value = '';
            }

            // Clear satuan display (item detail removed)
            $('#satuan_display').text('');
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
