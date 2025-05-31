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
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .form-section h6 {
            margin-bottom: 15px;
            font-weight: 600;
        }

        .array-container {
            padding: 15px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            background-color: white;
        }

        .array-container:hover {
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Responsive styles for modals */
        .modal-xl {
            max-width: 1140px;
            /* Default bootstrap xl size */
        }

        /* Small screens (tablets in landscape and small desktops) */
        @media (min-width: 768px) and (max-width: 1199px) {
            .modal-xl {
                max-width: 95% !important;
                width: 95% !important;
            }
        }

        /* Extra large screens */
        @media (min-width: 1200px) {
            .modal-xl {
                max-width: 1140px !important;
                width: auto !important;
            }
        }

        /* Responsive styling for select2 */
        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single {
            border-color: #dee2e6;
            height: 38px;
            padding: 6px 12px;
            border-radius: 4px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }

        .btn-action-group {
            display: flex;
            justify-content: flex-end;
            margin-top: 8px;
        }

        .btn-remove {
            border-radius: 50%;
            width: 38px;
            height: 38px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Improve mobile experience */
        @media (max-width: 768px) {
            .array-container {
                padding: 10px;
            }

            .form-section {
                padding: 15px 10px;
            }

            .modal-footer,
            .modal-header {
                padding: 0.75rem;
            }

            .select2-container--default .select2-selection--single {
                height: 35px;
            }

            .filter-section {
                margin-bottom: 10px;
            }

            .filter-label {
                margin-bottom: 3px;
                font-size: 12px;
            }

            .filter-controls {
                margin-bottom: 10px;
            }

            .table-responsive {
                font-size: 14px;
            }

            .dt-buttons {
                display: flex;
                flex-wrap: wrap;
                gap: 5px;
                margin-bottom: 10px;
            }

            .dt-button {
                flex: 1;
                white-space: nowrap;
                min-width: auto !important;
                padding: 0.25rem 0.5rem !important;
                font-size: 0.875rem !important;
            }
        }

        /* Choices.js custom styling */
        .choices__inner {
            min-height: 38px;
            padding: 4px 8px;
        }

        .choices__list--dropdown .choices__item {
            padding: 6px 10px;
        }

        .is-open .choices__inner {
            border-radius: 4px 4px 0 0;
        }

        /* Table responsive improvements */
        .dataTables_wrapper .row {
            margin-left: 0;
            margin-right: 0;
        }

        /* Button responsiveness */
        .action-buttons .btn {
            margin-bottom: 5px;
        }

        /* Pagination responsiveness */
        .dataTables_paginate .paginate_button {
            padding: 0.3rem 0.5rem !important;
        }

        @media (max-width: 576px) {
            .dataTables_paginate .paginate_button {
                padding: 0.2rem 0.4rem !important;
                font-size: 0.8rem;
            }
        }

        /* Style untuk field readonly agar terlihat seperti disabled */
        input[readonly],
        select[readonly] {
            background-color: #e9ecef !important;
            opacity: 0.65;
            cursor: not-allowed;
        }

        /* Style untuk container product select */
        .product-select-container {
            width: 100%;
        }

        /* Mobile responsive adjustments */
        @media (max-width: 768px) {
            .dt-responsive table {
                font-size: 0.85rem;
            }

            .filter-section .row {
                margin-bottom: 0.5rem;
            }

            .filter-label {
                font-size: 0.8rem;
            }

            /* Mobile button adjustments */
            .btn {
                font-size: 0.875rem !important;
                padding: 0.375rem 0.75rem !important;
            }

            .btn-sm {
                font-size: 0.8rem !important;
                padding: 0.25rem 0.5rem !important;
            }

            /* Mobile modal adjustments */
            .modal-dialog {
                margin: 0.5rem;
            }

            .modal-xl {
                max-width: calc(100% - 1rem);
            }

            /* Mobile card header */
            .card-header .col-12 {
                text-align: center;
            }

            .card-header h5 {
                font-size: 1.1rem;
            }

            /* Mobile form adjustments */
            .form-label {
                font-size: 0.9rem;
                margin-bottom: 0.25rem;
            }

            .form-control,
            .form-select {
                font-size: 0.9rem;
            }

            .text-muted {
                font-size: 0.8rem;
            }

            /* Mobile gap adjustments */
            .gap-2 {
                gap: 0.5rem !important;
            }
        }

        /* Tablet adjustments */
        @media (min-width: 769px) and (max-width: 991px) {
            .btn {
                font-size: 0.9rem;
                padding: 0.4rem 0.8rem;
            }

            /* Tablet modal adjustments - lebih besar untuk tablet */
            .modal-xl {
                max-width: 95% !important;
                width: 95% !important;
            }

            .modal-dialog {
                margin: 1rem auto;
            }

            /* Tablet form spacing */
            .mb-3 {
                margin-bottom: 1rem !important;
            }

            /* Tablet specific modal body padding */
            .modal-body {
                padding: 1.5rem;
            }
        }

        /* Desktop adjustments */
        @media (min-width: 992px) {
            .btn {
                font-size: 0.95rem;
                padding: 0.5rem 1rem;
            }
        }

        /* Button responsiveness improvements */
        .btn-action-group {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        @media (max-width: 576px) {
            .btn-action-group {
                gap: 0.25rem;
            }

            .btn-action-group .btn {
                flex: 1;
                min-width: auto;
            }
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
                    <div class="row align-items-center">
                        <div class="col-12 col-md-6 mb-2 mb-md-0">
                            <h5 class="mb-0">Daftar Catatan Produksi</h5>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-md-end">
                                <button id="clear-filters" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-filter"></i>
                                    <span class="d-none d-sm-inline">Hapus Filter</span>
                                    <span class="d-inline d-sm-none">Filter</span>
                                </button>
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#addProduksiModal">
                                    <i class="fas fa-plus"></i>
                                    <span class="d-none d-sm-inline">Tambah Catatan Produksi Baru</span>
                                    <span class="d-inline d-sm-none">Tambah</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="mb-3 p-3 border rounded bg-light filter-section">
                        <div class="row">
                            <div class="col-md-3 col-sm-6 col-12 mb-2">
                                <label for="filter-sku" class="form-label small filter-label">SKU Produk</label>
                                <input type="text" id="filter-sku" class="form-control form-control-sm"
                                    placeholder="Filter berdasarkan SKU">
                            </div>
                            <div class="col-md-3 col-sm-6 col-12 mb-2">
                                <label for="filter-nama" class="form-label small filter-label">Nama Produk</label>
                                <input type="text" id="filter-nama" class="form-control form-control-sm"
                                    placeholder="Filter berdasarkan nama">
                            </div>
                            <div class="col-md-3 col-sm-6 col-12 mb-2">
                                <label for="filter-packaging" class="form-label small filter-label">Packaging</label>
                                <input type="text" id="filter-packaging" class="form-control form-control-sm"
                                    placeholder="Filter berdasarkan packaging">
                            </div>
                            <div class="col-md-3 col-sm-6 col-12 mb-2">
                                <label for="filter-label" class="form-label small filter-label">Label Produk</label>
                                <select id="filter-label" class="form-select form-select-sm">
                                    <option value="">Semua Label</option>
                                    <option value="1">EXTRA SMALL PACK (15-100 GRAM)</option>
                                    <option value="2">SMALL PACK (50-250 GRAM)</option>
                                    <option value="5">TIN CANISTER SERIES</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3 col-sm-6 col-12 mb-2">
                                <label for="filter-bahan-baku" class="form-label small filter-label">Bahan Baku</label>
                                <select id="filter-bahan-baku" class="form-select form-select-sm">
                                    <option value="">Semua Bahan Baku</option>
                                    @foreach ($bahanBaku as $bahan)
                                        <option value="{{ $bahan->id }}">
                                            {{ $bahan->sku_induk }} - {{ $bahan->nama_barang }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-2 filter-controls">
                            <div class="col-md-3 col-sm-6 col-12 mb-2">
                                <label for="start-date" class="form-label small filter-label">Tanggal Mulai</label>
                                <input type="date" class="form-control form-control-sm" id="start-date"
                                    name="start_date">
                            </div>
                            <div class="col-md-3 col-sm-6 col-12 mb-2">
                                <label for="end-date" class="form-label small filter-label">Tanggal Selesai</label>
                                <input type="date" class="form-control form-control-sm" id="end-date" name="end_date">
                            </div>
                            <div class="col-md-2 col-sm-6 col-12 mb-2">
                                <button id="apply-date-filter" class="btn btn-sm btn-primary w-100">
                                    <i class="fas fa-filter"></i> Terapkan Filter
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- End Filter Section -->

                    <div class="mb-3">
                        <h6 class="text-primary">
                            <span id="table-period">Menampilkan semua data catatan produksi</span>
                        </h6>
                    </div>

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
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Catatan Produksi Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addProduksiForm">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <h6 class="mb-2"><i class="fas fa-info-circle me-2"></i>Petunjuk Pengisian</h6>
                            <ul class="mb-0 ps-3">
                                <li>Pilih produk terlebih dahulu untuk mengisi data produksi</li>
                                <li>Quantity harus berupa angka bulat (misal: 10, 25, 100)</li>
                                <li>Setiap bahan baku harus memiliki nilai gramasi (minimal 0.01)</li>
                                <li>Sistem akan otomatis menghitung total terpakai (gramasi × quantity)</li>
                            </ul>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label">Produk <span class="text-danger">*</span></label>
                                <select class="form-select product-select" name="product_id" required>
                                    <option value="">Pilih Produk</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" data-sku="{{ $product->sku }}"
                                            data-packaging="{{ $product->packaging }}">
                                            {{ $product->sku }} - {{ $product->name_product }}
                                            @if ($product->packaging)
                                                ({{ $product->packaging }})
                                            @endif
                                            - {{ $product->label_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Pilih produk yang akan dicatat produksinya (hanya menampilkan
                                    produk dengan label: EXTRA SMALL PACK dan TIN CANISTER SERIES)</small>

                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-lg-6 col-md-12 col-12 mb-3">
                                <label class="form-label">Packaging <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" readonly name="packaging" required>
                                <small class="text-muted">Kemasan produk (akan otomatis terisi sesuai produk)</small>
                            </div>
                            <div class="col-lg-6 col-md-12 col-12 mb-3">
                                <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="quantity" required min="1">
                                <small class="text-muted">Jumlah produk yang diproduksi (harus angka bulat)</small>
                            </div>
                        </div>

                        <div class="form-section">
                            <h6>Bahan Baku dan Gramasi <span class="badge bg-info">Minimal satu bahan baku</span></h6>
                            <div id="add-sku-gramasi-container">
                                <div class="array-container">
                                    <div class="row align-items-center">
                                        <div class="col-lg-5 col-md-5 col-12 mb-3">
                                            <label class="form-label">Bahan Baku <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select bahan-baku-select" name="sku_induk[]" required>
                                                <option value="">Pilih Bahan Baku</option>
                                                @foreach ($bahanBaku as $bahan)
                                                    <option value="{{ $bahan->id }}"
                                                        data-satuan="{{ $bahan->satuan }}">
                                                        {{ $bahan->sku_induk }} - {{ $bahan->nama_barang }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">Bahan baku yang digunakan dalam produksi</small>
                                        </div>
                                        <div class="col-lg-5 col-md-5 col-12 mb-3">
                                            <label class="form-label">Gramasi <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number" class="form-control gramasi-input" name="gramasi[]"
                                                    required min="0.01" step="0.01" placeholder="Gramasi">
                                                <span class="input-group-text satuan-display">Satuan</span>
                                            </div>
                                            <small class="text-muted">Jumlah bahan yang digunakan per produk</small>
                                            <!-- Hidden input for total_terpakai, automatically calculated from gramasi -->
                                            <input type="hidden" class="total-terpakai-input" name="total_terpakai[]"
                                                required>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-12 mb-3">
                                            <div class="w-100" style="padding-bottom: 1.5rem;">
                                                <button type="button" class="btn btn-danger btn-remove w-100">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="add-more-sku" class="btn btn-secondary btn-sm mt-2">
                            <i class="fas fa-plus"></i>
                            <span class="d-none d-sm-inline">Tambah Bahan Baku</span>
                            <span class="d-inline d-sm-none">Tambah</span>
                        </button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Produksi Modal -->
    <div class="modal fade" id="editProduksiModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
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
                        <div class="alert alert-info">
                            <h6 class="mb-2"><i class="fas fa-info-circle me-2"></i>Petunjuk Pengisian</h6>
                            <ul class="mb-0 ps-3">
                                <li>Pilih produk terlebih dahulu untuk mengisi data produksi</li>
                                <li>Quantity harus berupa angka bulat (misal: 10, 25, 100)</li>
                                <li>Setiap bahan baku harus memiliki nilai gramasi (minimal 0.01)</li>
                                <li>Sistem akan otomatis menghitung total terpakai (gramasi × quantity)</li>
                            </ul>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label">Produk <span class="text-danger">*</span></label>
                                <select class="form-select product-select" name="product_id" id="edit_product_id"
                                    required>
                                    <option value="">Pilih Produk</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" data-sku="{{ $product->sku }}"
                                            data-packaging="{{ $product->packaging }}">
                                            {{ $product->sku }} - {{ $product->name_product }}
                                            @if ($product->packaging)
                                                ({{ $product->packaging }})
                                            @endif
                                            - {{ $product->label_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Pilih produk yang akan dicatat produksinya (hanya menampilkan
                                    produk dengan label: EXTRA SMALL PACK dan TIN CANISTER SERIES)</small>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-lg-6 col-md-12 col-12 mb-3">
                                <label class="form-label">Packaging <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" readonly name="packaging" id="edit_packaging"
                                    required>
                                <small class="text-muted">Kemasan produk (akan otomatis terisi sesuai produk)</small>
                            </div>
                            <div class="col-lg-6 col-md-12 col-12 mb-3">
                                <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="quantity" id="edit_quantity" required
                                    min="1">
                                <small class="text-muted">Jumlah produk yang diproduksi (harus angka bulat)</small>
                            </div>
                        </div>

                        <div class="form-section">
                            <h6>Bahan Baku dan Gramasi <span class="badge bg-info">Minimal satu bahan baku</span></h6>
                            <div id="edit-sku-gramasi-container">
                                <!-- Akan diisi secara dinamis saat edit -->
                            </div>
                            <button type="button" id="edit-add-more-sku" class="btn btn-secondary btn-sm mt-2">
                                <i class="fas fa-plus"></i>
                                <span class="d-none d-sm-inline">Tambah Bahan Baku</span>
                                <span class="d-inline d-sm-none">Tambah</span>
                            </button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary btn-sm">Perbarui</button>
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
            // Debug mode untuk melacak permasalahan
            const DEBUG = true;

            function debugLog(...args) {
                if (DEBUG) {
                    console.log('[DEBUG]', new Date().toISOString(), ...args);
                }
            }

            // Function to show detailed debug alert
            function showDebugAlert(title, data) {
                if (DEBUG) {
                    console.group(`🔍 [DEBUG ALERT] ${title}`);
                    console.log('📊 Data:', data);
                    console.log('🕐 Timestamp:', new Date().toISOString());
                    console.groupEnd();

                    // Tampilkan di console table untuk data yang mudah dibaca
                    if (typeof data === 'object' && data !== null) {
                        console.table(data);
                    }
                }
            }

            // Tambahkan timestamp ke setiap request AJAX untuk menghindari caching
            $.ajaxSetup({
                cache: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            debugLog('Initializing JavaScript...');

            // Variables to hold Choices instances
            let addProductChoices = null;
            let editProductChoices = null;

            // Function to initialize product choices for add form
            function initAddProductChoices() {
                const addProductSelect = document.querySelector('#addProduksiForm .product-select');
                if (addProductSelect) {
                    // Destroy existing instance if it exists
                    if (addProductChoices) {
                        try {
                            addProductChoices.destroy();
                        } catch (e) {
                            debugLog('Error destroying add product choices:', e);
                        }
                    }

                    addProductChoices = new Choices(addProductSelect, {
                        searchEnabled: true,
                        searchPlaceholderValue: "Cari produk...",
                        itemSelectText: '',
                        placeholder: true,
                        placeholderValue: "Pilih Produk",
                        removeItemButton: true,
                        classNames: {
                            containerOuter: 'choices form-select product-select-container',
                        }
                    });
                }
            }

            // Function to initialize product choices for edit form
            function initEditProductChoices() {
                const editProductSelect = document.querySelector('#editProduksiForm .product-select');
                if (editProductSelect) {
                    // Destroy existing instance if it exists
                    if (editProductChoices) {
                        try {
                            editProductChoices.destroy();
                        } catch (e) {
                            debugLog('Error destroying edit product choices:', e);
                        }
                    }

                    editProductChoices = new Choices(editProductSelect, {
                        searchEnabled: true,
                        searchPlaceholderValue: "Cari produk...",
                        itemSelectText: '',
                        placeholder: true,
                        placeholderValue: "Pilih Produk",
                        removeItemButton: true,
                        classNames: {
                            containerOuter: 'choices form-select product-select-container',
                        }
                    });
                }
            }

            // Initialize product choices on page load
            initAddProductChoices();
            initEditProductChoices();

            // Inisialisasi Choices untuk semua dropdown bahan baku yang sudah ada
            document.querySelectorAll('.bahan-baku-select').forEach(element => {
                // Ensure element is not already initialized
                if (!element.choices) {
                    initBahanBakuChoices(element);
                }
            });

            // Fungsi untuk inisialisasi Choices pada bahan baku
            function initBahanBakuChoices(element) {
                // Destroy existing instance if it exists
                if (element.choices) {
                    try {
                        element.choices.destroy();
                        element.choices = null;
                    } catch (e) {
                        debugLog('Error destroying existing bahan baku choices:', e);
                    }
                }

                // Remove any existing choices wrapper
                const wrapper = element.parentNode.querySelector('.choices');
                if (wrapper && wrapper !== element) {
                    wrapper.remove();
                }

                // Ensure element is visible and not wrapped
                if (element.style.display === 'none') {
                    element.style.display = 'block';
                }

                const choicesInstance = new Choices(element, {
                    searchEnabled: true,
                    searchPlaceholderValue: "Cari bahan baku...",
                    itemSelectText: '',
                    placeholder: true,
                    placeholderValue: "Pilih Bahan Baku",
                    removeItemButton: true,
                    classNames: {
                        containerOuter: 'choices form-select bahan-baku-select-container',
                    }
                });

                // Store the instance on the element for later reference
                element.choices = choicesInstance;

                return choicesInstance;
            }

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

            // Feedback validasi realtime
            function showValidationFeedback(element, isValid, message) {
                const formGroup = element.closest('.mb-3');

                // Hapus feedback yang ada
                formGroup.find('.invalid-feedback, .valid-feedback').remove();
                element.removeClass('is-invalid is-valid');

                if (isValid) {
                    element.addClass('is-valid');
                    if (message) {
                        formGroup.append(`<div class="valid-feedback">${message}</div>`);
                    }
                } else {
                    element.addClass('is-invalid');
                    if (message) {
                        formGroup.append(`<div class="invalid-feedback">${message}</div>`);
                    }
                }
            }

            // Event handler untuk menangani perubahan product selection
            $(document).on('change', '.product-select', function() {
                const selectedOption = this.options[this.selectedIndex];
                let packaging = '';
                let sku = '';
                const form = $(this).closest('form');

                if (selectedOption && selectedOption.value) {
                    // Method 1: Try to get from data attributes
                    packaging = selectedOption.getAttribute('data-packaging') || '';
                    sku = selectedOption.getAttribute('data-sku') || '';

                    // Method 2: If data attributes are empty, try to parse from option text
                    if (!packaging && selectedOption.text) {
                        const optionText = selectedOption.text;
                        // Extract packaging from text like "SKU - Product Name (PACKAGING) - Label"
                        const packagingMatch = optionText.match(/\(([^)]+)\)/);
                        if (packagingMatch) {
                            packaging = packagingMatch[1];
                        }
                    }

                    // Method 3: Get from jQuery data attribute as fallback
                    if (!packaging) {
                        packaging = $(selectedOption).data('packaging') || '';
                    }
                    if (!sku) {
                        sku = $(selectedOption).data('sku') || '';
                    }
                }

                console.log('Product selection changed:', {
                    selectedIndex: this.selectedIndex,
                    value: this.value,
                    packaging: packaging,
                    sku: sku,
                    option: selectedOption,
                    optionText: selectedOption ? selectedOption.text : 'N/A',
                    dataPackaging: selectedOption ? selectedOption.getAttribute('data-packaging') :
                        'N/A',
                    dataSku: selectedOption ? selectedOption.getAttribute('data-sku') : 'N/A'
                });

                if (packaging && packaging.trim() !== '') {
                    form.find('[name="packaging"]').val(packaging.trim());
                    console.log('Packaging auto-filled:', packaging);
                    showValidationFeedback(form.find('[name="packaging"]'), true,
                        "Packaging terisi otomatis");
                } else {
                    form.find('[name="packaging"]').val('');
                    console.log('No packaging data found for selected product');
                }

                // Trigger validation feedback for product selection
                showValidationFeedback($(this), this.value !== "", "Produk dipilih dengan benar");
            });

            // Event delegation untuk semua perubahan dalam form
            $(document).on('change', function(e) {
                const target = e.target;

                // Handle product choice change for packaging auto-fill (fallback)
                if (target.classList.contains('product-select')) {
                    const selectedOption = target.options[target.selectedIndex];
                    const packaging = selectedOption ? selectedOption.dataset.packaging : '';
                    if (packaging) {
                        $(target).closest('form').find('[name="packaging"]').val(packaging);
                    }
                }

                // Handle bahan baku choice change
                if (target.classList.contains('bahan-baku-select')) {
                    const value = target.value;
                    const container = $(target).closest('.array-container');

                    // Validate
                    showValidationFeedback($(target), value !== "", "Bahan baku dipilih dengan benar");

                    // Update satuan
                    if (value) {
                        const selectedOption = target.options[target.selectedIndex];
                        const satuan = selectedOption ? selectedOption.dataset.satuan : 'Satuan';
                        container.find('.satuan-display').text(satuan || 'Satuan');

                        // Recalculate total for this row
                        calculateRowTotal(container);
                    }
                }
            });

            $(document).on('input', '[name="packaging"]', function() {
                const value = $(this).val().trim();
                showValidationFeedback($(this), value !== "", "Packaging diisi dengan benar");
            });

            $(document).on('input', '[name="quantity"]', function() {
                const value = parseInt($(this).val());
                const isValid = !isNaN(value) && value >= 1;
                showValidationFeedback($(this), isValid, isValid ? "Quantity valid" :
                    "Quantity harus angka bulat minimal 1");

                // Update total terpakai jika quantity berubah
                if (isValid) {
                    const form = $(this).closest('form');
                    form.find('.array-container').each(function() {
                        calculateRowTotal($(this));
                    });
                }
            });

            $(document).on('input', '.gramasi-input', function() {
                const value = parseFloat($(this).val());
                const isValid = !isNaN(value) && value >= 0.01;
                showValidationFeedback($(this), isValid, isValid ? "Gramasi valid" :
                    "Gramasi harus angka minimal 0.01");

                // Update total terpakai jika gramasi berubah
                if (isValid) {
                    calculateRowTotal($(this).closest('.array-container'));
                }
            });

            // Fungsi untuk generate template SKU Gramasi
            function getSkuGramasiTemplate(isEdit = false) {
                return `
                    <div class="array-container">
                        <div class="row align-items-center">
                            <div class="col-lg-5 col-md-5 col-12 mb-3">
                                <label class="form-label">Bahan Baku <span class="text-danger">*</span></label>
                                <select class="form-select bahan-baku-select" name="sku_induk[]" required>
                                    <option value="">Pilih Bahan Baku</option>
                                    @foreach ($bahanBaku as $bahan)
                                        <option value="{{ $bahan->id }}" data-satuan="{{ $bahan->satuan }}">
                                            {{ $bahan->sku_induk }} - {{ $bahan->nama_barang }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Bahan baku yang digunakan dalam produksi</small>
                            </div>
                            <div class="col-lg-5 col-md-5 col-12 mb-3">
                                <label class="form-label">Gramasi <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control gramasi-input" name="gramasi[]" required min="0.01" step="0.01" placeholder="Gramasi">
                                    <span class="input-group-text satuan-display">Satuan</span>
                                </div>
                                <small class="text-muted">Jumlah bahan yang digunakan per produk</small>
                                <input type="hidden" class="total-terpakai-input" name="total_terpakai[]" required>
                            </div>
                            <div class="col-lg-2 col-md-2 col-12 mb-3">
                                <div class="w-100" style="padding-bottom: 1.5rem;">
                                    <button type="button" class="btn btn-danger btn-remove w-100">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            // Add More SKU Button Click untuk form Add
            $('#add-more-sku').on('click', function() {
                const newContainer = $(getSkuGramasiTemplate(false));
                $('#add-sku-gramasi-container').append(newContainer);

                // Initialize Choices.js for the newly added dropdown
                const newSelect = newContainer.find('.bahan-baku-select')[0];
                if (newSelect) {
                    setTimeout(() => {
                        initBahanBakuChoices(newSelect);
                    }, 50);
                }
            });

            // Calculate total terpakai based on gramasi and quantity
            function calculateTotalTerpakai(form) {
                const quantity = parseInt(form.find('[name="quantity"]').val()) || 0;

                // For testing: just set some basic value for total_terpakai to ensure submission works
                form.find('.array-container').each(function() {
                    const gramasi = parseFloat($(this).find('.gramasi-input').val()) || 0;
                    const totalTerpakai = (gramasi * quantity).toFixed(2);
                    $(this).find('.total-terpakai-input').val(totalTerpakai ||
                        1); // Default to 1 if calculation fails

                    // Show calculation result to user
                    const satuan = $(this).find('.satuan-display').text();
                    $(this).find('.total-calculation-display').remove();
                    if (gramasi > 0 && quantity > 0) {
                        $(this).find('.input-group').after(
                            `<small class="text-info total-calculation-display">Total terpakai: ${totalTerpakai} ${satuan}</small>`
                        );
                    }
                });
            }

            // Calculate total for a specific row
            function calculateRowTotal(container) {
                const quantity = parseInt(container.closest('form').find('[name="quantity"]').val()) || 0;
                const gramasi = parseFloat(container.find('.gramasi-input').val()) || 0;

                // For testing: simplify calculation and set a minimum value of 1 to ensure it's valid
                const totalTerpakai = (gramasi * quantity).toFixed(2) || 1;
                container.find('.total-terpakai-input').val(totalTerpakai < 0.01 ? 1 : totalTerpakai);

                // Show calculation result to user
                const satuan = container.find('.satuan-display').text();
                container.find('.total-calculation-display').remove();
                if (gramasi > 0 && quantity > 0) {
                    container.find('.input-group').after(
                        `<small class="text-info total-calculation-display">Total terpakai: ${totalTerpakai} ${satuan}</small>`
                    );
                }
            }

            // Update total when quantity or gramasi changes
            $(document).on('input', '[name="quantity"]', debounce(function() {
                // Update all rows' total_terpakai when quantity changes
                const form = $(this).closest('form');
                form.find('.array-container').each(function() {
                    calculateRowTotal($(this));
                });
            }, 300));

            $(document).on('input', '.gramasi-input', debounce(function() {
                // Update just this row's total_terpakai
                calculateRowTotal($(this).closest('.array-container'));
            }, 300));

            // Add Produksi Form Submit
            $('#addProduksiForm').on('submit', function(e) {
                e.preventDefault();

                // Validasi form sebelum submit
                let isValid = true;
                const form = $(this);

                // Validasi produk
                const productSelect = form.find('[name="product_id"]');
                if (!productSelect.val()) {
                    showValidationFeedback(productSelect, false, "Silahkan pilih produk terlebih dahulu");
                    isValid = false;
                }

                // Validasi packaging
                const packagingInput = form.find('[name="packaging"]');
                if (!packagingInput.val().trim()) {
                    showValidationFeedback(packagingInput, false, "Packaging wajib diisi");
                    isValid = false;
                }

                // Validasi quantity
                const quantityInput = form.find('[name="quantity"]');
                const quantity = parseInt(quantityInput.val());
                if (isNaN(quantity) || quantity < 1) {
                    showValidationFeedback(quantityInput, false,
                        "Quantity harus berupa angka bulat minimal 1");
                    isValid = false;
                }

                // Validasi bahan baku
                let hasBahanBaku = false;
                form.find('.array-container').each(function() {
                    const bahanBakuSelect = $(this).find('.bahan-baku-select');
                    const gramasiInput = $(this).find('.gramasi-input');

                    if (bahanBakuSelect.val()) {
                        hasBahanBaku = true;

                        // Validasi gramasi
                        const gramasi = parseFloat(gramasiInput.val());
                        if (isNaN(gramasi) || gramasi < 0.01) {
                            showValidationFeedback(gramasiInput, false,
                                "Gramasi harus berupa angka minimal 0.01");
                            isValid = false;
                        }
                    } else if (gramasiInput.val().trim()) {
                        showValidationFeedback(bahanBakuSelect, false,
                            "Pilih bahan baku terlebih dahulu");
                        isValid = false;
                    }
                });

                if (!hasBahanBaku) {
                    Swal.fire({
                        title: 'Data Tidak Lengkap',
                        text: 'Minimal harus ada satu bahan baku yang dipilih',
                        icon: 'warning',
                        confirmButtonText: 'Saya Mengerti',
                        allowOutsideClick: false
                    });
                    isValid = false;
                }

                // Jika tidak valid, hentikan
                if (!isValid) {
                    Swal.fire({
                        title: 'Perhatian',
                        text: 'Mohon isi semua data yang wajib diisi dengan benar',
                        icon: 'warning',
                        confirmButtonText: 'Saya Mengerti',
                        allowOutsideClick: false
                    });
                    return;
                }

                // Debug: Log form data before submission
                debugLog('Form data before submission:', {
                    product_id: form.find('[name="product_id"]').val(),
                    packaging: form.find('[name="packaging"]').val(),
                    quantity: form.find('[name="quantity"]').val(),
                    sku_induk: form.find('[name="sku_induk[]"]').map(function() {
                        return $(this).val();
                    }).get(),
                    gramasi: form.find('[name="gramasi[]"]').map(function() {
                        return $(this).val();
                    }).get(),
                    total_terpakai: form.find('[name="total_terpakai[]"]').map(function() {
                        return $(this).val();
                    }).get()
                });

                // Fix: Ensure product_id value is properly set from Choices.js
                if (addProductChoices) {
                    const selectedProduct = addProductChoices.getValue(true);
                    if (selectedProduct) {
                        // Manually set the value to the hidden select
                        form.find('[name="product_id"]').val(selectedProduct);
                        debugLog('Product ID fixed from Choices.js:', selectedProduct);
                    }
                }

                // Fix: Ensure all bahan baku values are properly set from Choices.js
                form.find('.bahan-baku-select').each(function() {
                    if (this.choices) {
                        const selectedValue = this.choices.getValue(true);
                        if (selectedValue) {
                            $(this).val(selectedValue);
                            debugLog('Bahan baku value fixed:', selectedValue);
                        }
                    }
                });

                // Ensure total_terpakai values are calculated
                calculateTotalTerpakai($(this));

                // Final form data check after fixes
                debugLog('Final form data after fixes:', {
                    product_id: form.find('[name="product_id"]').val(),
                    packaging: form.find('[name="packaging"]').val(),
                    quantity: form.find('[name="quantity"]').val(),
                    sku_induk: form.find('[name="sku_induk[]"]').map(function() {
                        return $(this).val();
                    }).get(),
                    gramasi: form.find('[name="gramasi[]"]').map(function() {
                        return $(this).val();
                    }).get(),
                    total_terpakai: form.find('[name="total_terpakai[]"]').map(function() {
                        return $(this).val();
                    }).get()
                });

                var submitButton = form.find('button[type="submit"]');

                // Disable submit button to prevent double submission
                submitButton.prop('disabled', true);

                // Tampilkan Loading
                Swal.fire({
                    title: 'Menyimpan Data',
                    text: 'Mohon tunggu...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                var formData = new FormData(this);

                // Kirim data dengan AJAX
                $.ajax({
                    url: "{{ route('catatan-produksi.store') }}",
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        // Re-enable button setelah berhasil
                        submitButton.prop('disabled', false);

                        // Close any open Swal dialogs
                        Swal.close();

                        if (data.success) {
                            // Reset form
                            form[0].reset();

                            // Remove all containers kecuali yang pertama
                            form.find('.array-container:not(:first)').remove();

                            // Reset Choices.js selectors
                            if (addProductChoices) {
                                try {
                                    addProductChoices.setChoiceByValue('');
                                } catch (e) {
                                    debugLog(
                                        'Error resetting add product choices after submit:',
                                        e);
                                    // Reinitialize if reset fails
                                    initAddProductChoices();
                                }
                            }

                            // Reset bahan baku choices properly
                            document.querySelectorAll('#addProduksiForm .bahan-baku-select')
                                .forEach(select => {
                                    if (select.choices) {
                                        try {
                                            select.choices.destroy();
                                            select.choices = null;
                                        } catch (e) {
                                            debugLog(
                                                'Error destroying bahan baku choices after submit:',
                                                e);
                                        }
                                    }
                                });

                            // Reinitialize the first bahan baku select
                            setTimeout(() => {
                                const firstBahanBakuSelect = document.querySelector(
                                    '#addProduksiForm .bahan-baku-select');
                                if (firstBahanBakuSelect) {
                                    initBahanBakuChoices(firstBahanBakuSelect);
                                }
                            }, 50);

                            // Clear form elements
                            form.find('.gramasi-input').val('');
                            form.find('.satuan-display').text('Satuan');
                            form.find('.is-valid, .is-invalid').removeClass(
                                'is-valid is-invalid');
                            form.find(
                                '.valid-feedback, .invalid-feedback, .total-calculation-display'
                            ).remove();

                            // Properly hide modal dan clean up
                            setTimeout(function() {
                                closeModal('addProduksiModal');

                                // Explicitly ensure the backdrop is removed
                                $('body').removeClass('modal-open');
                                $('.modal-backdrop').remove();
                            }, 100);

                            // Reload table dan pastikan data ditampilkan
                            reloadTable(function() {
                                console.log(
                                    'Tabel berhasil diperbarui dengan data baru');
                            });

                            // Tampilkan pesan sukses
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

                        // Close any open Swal dialogs
                        Swal.close();

                        // Debug: Log detailed error information
                        debugLog('AJAX Error Details:', {
                            status: xhr.status,
                            statusText: xhr.statusText,
                            responseText: xhr.responseText,
                            responseJSON: xhr.responseJSON
                        });

                        // Show debug alert for immediate visibility
                        showDebugAlert('AJAX Error', {
                            status: xhr.status,
                            statusText: xhr.statusText,
                            responseJSON: xhr.responseJSON,
                            url: "{{ route('catatan-produksi.store') }}"
                        });

                        // Delay sedikit untuk memastikan loading dialog tertutup
                        setTimeout(function() {
                            if (xhr.status === 422) {
                                // Validation errors - show but don't prevent resubmission
                                const errors = xhr.responseJSON.errors;
                                const errorMessages = Object.values(errors).flat();

                                // Debug: Log validation errors
                                debugLog('Validation Errors:', errors);

                                // Highlight invalid fields
                                for (const field in errors) {
                                    if (field.includes('sku_induk.') || field.includes(
                                            'gramasi.') || field.includes(
                                            'total_terpakai.')) {
                                        const index = field.split('.')[1];
                                        const containers = form.find(
                                        '.array-container');
                                        if (containers.length > index) {
                                            const container = $(containers[index]);
                                            if (field.startsWith('sku_induk.')) {
                                                showValidationFeedback(container.find(
                                                        '.bahan-baku-select'),
                                                    false, errors[
                                                        field][0]);
                                            } else if (field.startsWith('gramasi.')) {
                                                showValidationFeedback(container.find(
                                                        '.gramasi-input'), false,
                                                    errors[field][
                                                        0
                                                    ]);
                                            }
                                        }
                                    } else if (form.find(`[name="${field}"]`).length) {
                                        showValidationFeedback(form.find(
                                                `[name="${field}"]`),
                                            false, errors[field][0]);
                                    }
                                }

                                Swal.fire({
                                    title: 'Ada kesalahan pada data',
                                    html: `<div style="text-align: left;">${errorMessages.join('<br>')}</div>`,
                                    icon: 'warning',
                                    confirmButtonText: 'Coba Lagi',
                                    confirmButtonColor: '#3085d6',
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    width: '500px'
                                });
                            } else {
                                // Other errors
                                const errorMessage = xhr.responseJSON?.message ||
                                    `Terjadi kesalahan pada server (Status: ${xhr.status})`;

                                Swal.fire({
                                    title: 'Gagal Menyimpan Data',
                                    html: `<div style="text-align: left;">
                                        <p><strong>Error:</strong> ${errorMessage}</p>
                                        <p><strong>Status:</strong> ${xhr.status} - ${xhr.statusText}</p>
                                        ${xhr.responseJSON?.error ? `<p><strong>Detail:</strong> ${xhr.responseJSON.error}</p>` : ''}
                                        <hr>
                                        <small>Silakan cek console browser untuk detail lebih lanjut (F12 → Console)</small>
                                    </div>`,
                                    icon: 'error',
                                    confirmButtonText: 'Coba Lagi',
                                    confirmButtonColor: '#3085d6',
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    width: '600px'
                                });
                            }
                        }, 300); // Delay 300ms
                    },
                    complete: function() {
                        // Always ensure the UI is restored
                        submitButton.prop('disabled', false);
                        // JANGAN panggil Swal.close() di sini karena akan menutup alert error
                    }
                });
            });

            // Modifikasi serupa untuk editProduksiForm
            $('#editProduksiForm').on('submit', function(e) {
                e.preventDefault();

                // Validasi form sebelum submit
                let isValid = true;
                const form = $(this);

                // Validasi produk
                const productSelect = form.find('[name="product_id"]');
                if (!productSelect.val()) {
                    showValidationFeedback(productSelect, false, "Silahkan pilih produk terlebih dahulu");
                    isValid = false;
                }

                // Validasi packaging
                const packagingInput = form.find('[name="packaging"]');
                if (!packagingInput.val().trim()) {
                    showValidationFeedback(packagingInput, false, "Packaging wajib diisi");
                    isValid = false;
                }

                // Validasi quantity
                const quantityInput = form.find('[name="quantity"]');
                const quantity = parseInt(quantityInput.val());
                if (isNaN(quantity) || quantity < 1) {
                    showValidationFeedback(quantityInput, false,
                        "Quantity harus berupa angka bulat minimal 1");
                    isValid = false;
                }

                // Validasi bahan baku
                let hasBahanBaku = false;
                form.find('.array-container').each(function() {
                    const bahanBakuSelect = $(this).find('.bahan-baku-select');
                    const gramasiInput = $(this).find('.gramasi-input');

                    if (bahanBakuSelect.val()) {
                        hasBahanBaku = true;

                        // Validasi gramasi
                        const gramasi = parseFloat(gramasiInput.val());
                        if (isNaN(gramasi) || gramasi < 0.01) {
                            showValidationFeedback(gramasiInput, false,
                                "Gramasi harus berupa angka minimal 0.01");
                            isValid = false;
                        }
                    } else if (gramasiInput.val().trim()) {
                        showValidationFeedback(bahanBakuSelect, false,
                            "Pilih bahan baku terlebih dahulu");
                        isValid = false;
                    }
                });

                if (!hasBahanBaku) {
                    Swal.fire({
                        title: 'Data Tidak Lengkap',
                        text: 'Minimal harus ada satu bahan baku yang dipilih',
                        icon: 'warning',
                        confirmButtonText: 'Saya Mengerti',
                        allowOutsideClick: false
                    });
                    isValid = false;
                }

                // Jika tidak valid, hentikan
                if (!isValid) {
                    Swal.fire({
                        title: 'Perhatian',
                        text: 'Mohon isi semua data yang wajib diisi dengan benar',
                        icon: 'warning',
                        confirmButtonText: 'Saya Mengerti',
                        allowOutsideClick: false
                    });
                    return;
                }

                // Debug: Log form data before submission
                debugLog('Form data before submission:', {
                    product_id: form.find('[name="product_id"]').val(),
                    packaging: form.find('[name="packaging"]').val(),
                    quantity: form.find('[name="quantity"]').val(),
                    sku_induk: form.find('[name="sku_induk[]"]').map(function() {
                        return $(this).val();
                    }).get(),
                    gramasi: form.find('[name="gramasi[]"]').map(function() {
                        return $(this).val();
                    }).get(),
                    total_terpakai: form.find('[name="total_terpakai[]"]').map(function() {
                        return $(this).val();
                    }).get()
                });

                // Fix: Ensure product_id value is properly set from Choices.js
                if (editProductChoices) {
                    const selectedProduct = editProductChoices.getValue(true);
                    if (selectedProduct) {
                        // Manually set the value to the hidden select
                        form.find('[name="product_id"]').val(selectedProduct);
                        debugLog('Product ID fixed from Choices.js:', selectedProduct);
                    }
                }

                // Fix: Ensure all bahan baku values are properly set from Choices.js
                form.find('.bahan-baku-select').each(function() {
                    if (this.choices) {
                        const selectedValue = this.choices.getValue(true);
                        if (selectedValue) {
                            $(this).val(selectedValue);
                            debugLog('Bahan baku value fixed:', selectedValue);
                        }
                    }
                });

                // Ensure total_terpakai values are calculated
                calculateTotalTerpakai($(this));

                // Final form data check after fixes
                debugLog('Final form data after fixes:', {
                    product_id: form.find('[name="product_id"]').val(),
                    packaging: form.find('[name="packaging"]').val(),
                    quantity: form.find('[name="quantity"]').val(),
                    sku_induk: form.find('[name="sku_induk[]"]').map(function() {
                        return $(this).val();
                    }).get(),
                    gramasi: form.find('[name="gramasi[]"]').map(function() {
                        return $(this).val();
                    }).get(),
                    total_terpakai: form.find('[name="total_terpakai[]"]').map(function() {
                        return $(this).val();
                    }).get()
                });

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
                        // Pastikan tombol sudah di-enable
                        submitButton.prop('disabled', false);

                        // Close any open Swal dialogs
                        Swal.close();

                        if (data.success) {
                            // Reset form and close modal
                            form[0].reset();

                            // Reset Choices.js selectors
                            if (editProductChoices) {
                                try {
                                    editProductChoices.setChoiceByValue('');
                                } catch (e) {
                                    debugLog(
                                        'Error resetting edit product choices after submit:',
                                        e);
                                    // Reinitialize if reset fails
                                    initEditProductChoices();
                                }
                            }

                            // Reset bahan baku choices properly
                            document.querySelectorAll('#editProduksiForm .bahan-baku-select')
                                .forEach(select => {
                                    if (select.choices) {
                                        try {
                                            select.choices.destroy();
                                            select.choices = null;
                                        } catch (e) {
                                            debugLog(
                                                'Error destroying bahan baku choices after submit:',
                                                e);
                                        }
                                    }
                                });

                            // Reinitialize the first bahan baku select
                            setTimeout(() => {
                                const firstBahanBakuSelect = document.querySelector(
                                    '#editProduksiForm .bahan-baku-select');
                                if (firstBahanBakuSelect) {
                                    initBahanBakuChoices(firstBahanBakuSelect);
                                }
                            }, 50);

                            form.find('.is-valid, .is-invalid').removeClass(
                                'is-valid is-invalid');
                            form.find(
                                '.valid-feedback, .invalid-feedback, .total-calculation-display'
                            ).remove();

                            // Properly hide modal
                            setTimeout(function() {
                                closeModal('editProduksiModal');

                                // Explicitly ensure the backdrop is removed
                                $('body').removeClass('modal-open');
                                $('.modal-backdrop').remove();
                            }, 100);

                            // Reload table dan pastikan data ditampilkan
                            reloadTable(function() {
                                console.log('Tabel berhasil diperbarui setelah edit');
                            });

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

                        // Close any open Swal dialogs
                        Swal.close();

                        // Debug: Log detailed error information
                        debugLog('AJAX Error Details:', {
                            status: xhr.status,
                            statusText: xhr.statusText,
                            responseText: xhr.responseText,
                            responseJSON: xhr.responseJSON
                        });

                        // Show debug alert for immediate visibility
                        showDebugAlert('AJAX Error', {
                            status: xhr.status,
                            statusText: xhr.statusText,
                            responseJSON: xhr.responseJSON,
                            url: "{{ route('catatan-produksi.store') }}"
                        });

                        // Delay sedikit untuk memastikan loading dialog tertutup
                        setTimeout(function() {
                            if (xhr.status === 422) {
                                // Validation errors - show but don't prevent resubmission
                                const errors = xhr.responseJSON.errors;
                                const errorMessages = Object.values(errors).flat();

                                // Debug: Log validation errors
                                debugLog('Validation Errors:', errors);

                                // Highlight invalid fields
                                for (const field in errors) {
                                    if (field.includes('sku_induk.') || field.includes(
                                            'gramasi.') || field.includes(
                                            'total_terpakai.')) {
                                        const index = field.split('.')[1];
                                        const containers = form.find(
                                        '.array-container');
                                        if (containers.length > index) {
                                            const container = $(containers[index]);
                                            if (field.startsWith('sku_induk.')) {
                                                showValidationFeedback(container.find(
                                                        '.bahan-baku-select'),
                                                    false, errors[
                                                        field][0]);
                                            } else if (field.startsWith('gramasi.')) {
                                                showValidationFeedback(container.find(
                                                        '.gramasi-input'), false,
                                                    errors[field][
                                                        0
                                                    ]);
                                            }
                                        }
                                    } else if (form.find(`[name="${field}"]`).length) {
                                        showValidationFeedback(form.find(
                                                `[name="${field}"]`),
                                            false, errors[field][0]);
                                    }
                                }

                                Swal.fire({
                                    title: 'Ada kesalahan pada data',
                                    html: `<div style="text-align: left;">${errorMessages.join('<br>')}</div>`,
                                    icon: 'warning',
                                    confirmButtonText: 'Coba Lagi',
                                    confirmButtonColor: '#3085d6',
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    width: '500px'
                                });
                            } else {
                                // Other errors
                                const errorMessage = xhr.responseJSON?.message ||
                                    `Terjadi kesalahan pada server (Status: ${xhr.status})`;

                                Swal.fire({
                                    title: 'Gagal Memperbarui',
                                    html: `<div style="text-align: left;">
                                        <p><strong>Error:</strong> ${errorMessage}</p>
                                        <p><strong>Status:</strong> ${xhr.status} - ${xhr.statusText}</p>
                                        ${xhr.responseJSON?.error ? `<p><strong>Detail:</strong> ${xhr.responseJSON.error}</p>` : ''}
                                        <hr>
                                        <small>Silakan cek console browser untuk detail lebih lanjut (F12 → Console)</small>
                                    </div>`,
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#3085d6',
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    width: '600px'
                                });
                            }
                        }, 300); // Delay 300ms
                    },
                    complete: function() {
                        // Always ensure the UI is restored
                        submitButton.prop('disabled', false);
                        // JANGAN panggil Swal.close() di sini karena akan menutup alert error
                    }
                });
            });

            // Remove SKU Induk field
            $(document).on('click', '.btn-remove', function() {
                if ($(this).closest('form').find('.array-container').length > 1) {
                    $(this).closest('.array-container').remove();
                } else {
                    Swal.fire({
                        title: 'Informasi',
                        text: 'Harus ada minimal satu Bahan Baku dan Gramasi',
                        icon: 'info',
                        timer: 1500,
                        showConfirmButton: false,
                    });
                }
            });

            // Edit Button Click
            $(document).on('click', '.edit-btn', function() {
                var id = $(this).data('id');

                // Show loading
                Swal.fire({
                    title: 'Memuat Data...',
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
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        Swal.close();

                        if (data.success) {
                            const catatan = data.data;

                            // Set basic data
                            $('#edit_produksi_id').val(catatan.id);
                            $('#edit_product_id').val(catatan.product_id);
                            $('#edit_packaging').val(catatan.packaging);
                            $('#edit_quantity').val(catatan.quantity);

                            // Clear existing bahan baku containers
                            $('#edit-sku-gramasi-container').empty();

                            // Populate bahan baku data
                            if (catatan.bahan_baku_details && catatan.bahan_baku_details
                                .length > 0) {
                                catatan.bahan_baku_details.forEach((bahan, index) => {
                                    const template = getSkuGramasiTemplate(true);
                                    const container = $(template);

                                    // Set values
                                    container.find('.bahan-baku-select').val(bahan.id);
                                    container.find('.gramasi-input').val(bahan.gramasi);
                                    container.find('.total-terpakai-input').val(bahan
                                        .total_terpakai);
                                    container.find('.satuan-display').text(bahan
                                        .satuan || 'Satuan');

                                    $('#edit-sku-gramasi-container').append(container);
                                });
                            }

                            // Show the modal
                            $('#editProduksiModal').modal('show');

                            // Trigger product change to auto-fill packaging if needed
                            const productSelect = document.getElementById('edit_product_id');
                            if (productSelect) {
                                const selectedOption = productSelect.options[productSelect
                                    .selectedIndex];
                                const packaging = selectedOption ? selectedOption.dataset
                                    .packaging : '';
                                if (packaging && packaging.trim() !== '') {
                                    $('#edit_packaging').val(packaging);
                                    console.log('Edit: Packaging auto-filled:', packaging);
                                }
                            }
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Gagal Memuat Data',
                            text: xhr.responseJSON?.message ||
                                'Tidak dapat memuat data catatan produksi.',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#3085d6'
                        });
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
                                    // Reload tabel dengan callback
                                    reloadTable(function() {
                                        console.log(
                                            'Tabel berhasil diperbarui setelah hapus'
                                        );
                                    });

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
                debugLog('Modal hidden - cleaning up');
                cleanupModalForm(this.id);

                // Pastikan backdrop dihapus dengan benar
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
            });

            // Initialize Choices.js when modal is shown
            $('#addProduksiModal').on('shown.bs.modal', function() {
                debugLog('Add modal shown - reinitializing Choices.js');
                // Reinitialize product choices to ensure they work properly
                setTimeout(() => {
                    initAddProductChoices();

                    // Reinitialize bahan baku choices for existing containers
                    document.querySelectorAll('#addProduksiForm .bahan-baku-select').forEach(
                        element => {
                            // Destroy existing instance first
                            if (element.choices) {
                                try {
                                    element.choices.destroy();
                                    element.choices = null;
                                } catch (e) {
                                    debugLog('Error destroying existing bahan baku choices:',
                                        e);
                                }
                            }

                            // Initialize new instance
                            initBahanBakuChoices(element);
                        });
                }, 100);
            });

            $('#editProduksiModal').on('shown.bs.modal', function() {
                debugLog('Edit modal shown - reinitializing Choices.js');
                // Reinitialize product choices to ensure they work properly
                setTimeout(() => {
                    initEditProductChoices();

                    // Reinitialize bahan baku choices for existing containers
                    document.querySelectorAll('#editProduksiForm .bahan-baku-select').forEach(
                        element => {
                            // Destroy existing instance first
                            if (element.choices) {
                                try {
                                    element.choices.destroy();
                                    element.choices = null;
                                } catch (e) {
                                    debugLog('Error destroying existing bahan baku choices:',
                                        e);
                                }
                            }

                            // Initialize new instance
                            initBahanBakuChoices(element);
                        });
                }, 100);
            });

            // Edit Button Click
            $('#edit-add-more-sku').on('click', function() {
                const newContainer = $(getSkuGramasiTemplate(true));
                $('#edit-sku-gramasi-container').append(newContainer);

                // Initialize Choices.js for the newly added dropdown
                const newSelect = newContainer.find('.bahan-baku-select')[0];
                if (newSelect) {
                    setTimeout(() => {
                        initBahanBakuChoices(newSelect);
                    }, 50);
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
                        d.sku = $('#filter-sku').val();
                        d.name_product = $('#filter-nama').val();
                        d.packaging = $('#filter-packaging').val();
                        d.label = $('#filter-label').val();
                        d.bahan_baku = $('#filter-bahan-baku').val();
                        d.start_date = $('#start-date').val();
                        d.end_date = $('#end-date').val();
                        // Tambahkan timestamp untuk menghindari cache
                        d._ts = new Date().getTime();
                        return d;
                    }
                },
                fnServerData: function(sSource, aoData, fnCallback, oSettings) {
                    debugLog('Fetching data from server with params:', aoData);

                    // Tambahkan timestamp ke aoData untuk menghindari cache
                    aoData.push({
                        "name": "_ts",
                        "value": new Date().getTime()
                    });

                    oSettings.jqXHR = $.ajax({
                        "dataType": 'json',
                        "type": oSettings.sServerMethod,
                        "url": sSource,
                        "data": aoData,
                        "success": function(data) {
                            debugLog('Server response received:', data);
                            fnCallback(data);
                        },
                        "error": function(xhr, error, thrown) {
                            debugLog('Error loading data:', error, thrown);
                            fnCallback({
                                data: []
                            });
                        }
                    });
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
                ],
                language: {
                    processing: '<div class="d-flex justify-content-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>',
                    emptyTable: 'Tidak ada data catatan produksi',
                    zeroRecords: 'Tidak ditemukan catatan produksi yang sesuai',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ catatan produksi',
                    infoEmpty: 'Menampilkan 0 sampai 0 dari 0 catatan produksi',
                    infoFiltered: '(difilter dari _MAX_ total catatan produksi)',
                    search: 'Cari:',
                    paginate: {
                        first: 'Pertama',
                        last: 'Terakhir',
                        next: 'Selanjutnya',
                        previous: 'Sebelumnya'
                    }
                },
                drawCallback: function() {
                    debugLog('DataTable selesai di-render');
                },
                initComplete: function() {
                    debugLog('DataTable selesai diinisialisasi');
                }
            });

            // Fungsi untuk reload tabel dengan anti-cache
            function reloadTable(callback) {
                debugLog('Memuat ulang tabel...');

                // Hancurkan tabel terlebih dahulu untuk memastikan data benar-benar dimuat ulang
                try {
                    table.clear().draw();
                } catch (e) {
                    debugLog('Error saat clear table:', e);
                }

                // Reload dengan parameter _ts baru untuk menghindari cache
                table.ajax.reload(function(json) {
                    debugLog('Tabel berhasil dimuat ulang dengan', json?.recordsTotal || 0, 'data');
                    if (typeof callback === 'function') {
                        callback(json);
                    }
                }, false);
            }

            // Apply filters when text inputs change with debounce (300ms delay)
            $('#filter-sku, #filter-nama, #filter-packaging, #filter-label, #filter-bahan-baku').on('keyup change',
                debounce(
                    function() {
                        table.ajax.reload();
                    }, 300));

            // Apply date filter when button is clicked
            $('#apply-date-filter').on('click', function() {
                // Update table title with date range
                updateTablePeriod();

                // Reload table
                table.ajax.reload();
            });

            // Function to update table period display
            function updateTablePeriod() {
                const startDate = $('#start-date').val();
                const endDate = $('#end-date').val();

                // Jika tidak ada filter tanggal, tampilkan semua data
                if (!startDate && !endDate) {
                    $('#table-period').text('Menampilkan semua data catatan produksi');
                    return;
                }

                // Jika ada filter tanggal
                if (startDate || endDate) {
                    const formattedStartDate = startDate ? new Date(startDate).toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'short',
                        year: 'numeric'
                    }) : 'Awal';

                    const formattedEndDate = endDate ? new Date(endDate).toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'short',
                        year: 'numeric'
                    }) : 'Sekarang';

                    // Update the table period text
                    if (startDate && endDate && startDate === endDate) {
                        $('#table-period').text(`Menampilkan data untuk tanggal: ${formattedStartDate}`);
                    } else if (startDate && endDate) {
                        $('#table-period').text(
                            `Menampilkan data untuk periode: ${formattedStartDate} - ${formattedEndDate}`);
                    } else if (startDate) {
                        $('#table-period').text(`Menampilkan data dari tanggal: ${formattedStartDate}`);
                    } else if (endDate) {
                        $('#table-period').text(`Menampilkan data sampai tanggal: ${formattedEndDate}`);
                    }
                }
            }

            // Clear filters function
            $('#clear-filters').on('click', function() {
                $('#filter-sku').val('');
                $('#filter-nama').val('');
                $('#filter-packaging').val('');
                $('#filter-label').val('').trigger('change');
                $('#filter-bahan-baku').val('').trigger('change');

                // Reset date filters to empty (show all data)
                $('#start-date').val('');
                $('#end-date').val('');

                // Update table period display
                updateTablePeriod();

                // Reload table
                table.ajax.reload();
            });

            // Initialize table period display on page load
            updateTablePeriod();

            // Function to properly clean up modal forms
            function cleanupModalForm(modalId) {
                debugLog('Cleaning up modal form:', modalId);

                const modal = document.getElementById(modalId);
                const form = modal.querySelector('form');

                // Reset form
                if (form) {
                    form.reset();
                }

                // Clean up all Choices.js instances in this modal BEFORE removing containers
                $(modal).find('.bahan-baku-select').each(function() {
                    if (this.choices) {
                        try {
                            this.choices.destroy();
                            this.choices = null;
                        } catch (e) {
                            debugLog('Error destroying bahan baku choice during cleanup:', e);
                        }
                    }

                    // Remove any orphaned choices wrapper
                    const wrapper = this.parentNode.querySelector('.choices');
                    if (wrapper && wrapper !== this) {
                        wrapper.remove();
                    }

                    // Ensure original select is visible
                    this.style.display = 'block';
                });

                // Remove extra containers
                $(modal).find('.array-container:not(:first)').remove();

                // Clean up validation states
                $(modal).find('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
                $(modal).find('.valid-feedback, .invalid-feedback, .total-calculation-display').remove();

                // Reset satuan displays
                $(modal).find('.satuan-display').text('Satuan');

                // Clean up product Choices.js instances
                if (modalId === 'addProduksiModal') {
                    if (addProductChoices) {
                        try {
                            addProductChoices.setChoiceByValue('');
                        } catch (e) {
                            debugLog('Error resetting add product choices during cleanup:', e);
                            // Reinitialize if reset fails
                            initAddProductChoices();
                        }
                    }
                } else if (modalId === 'editProduksiModal') {
                    if (editProductChoices) {
                        try {
                            editProductChoices.setChoiceByValue('');
                        } catch (e) {
                            debugLog('Error resetting edit product choices during cleanup:', e);
                            // Reinitialize if reset fails
                            initEditProductChoices();
                        }
                    }
                }

                // Reinitialize remaining bahan baku choices after cleanup
                setTimeout(() => {
                    $(modal).find('.bahan-baku-select').each(function() {
                        if (!this.choices) {
                            initBahanBakuChoices(this);
                        }
                    });
                }, 100);
            }

            // Fungsi untuk properly menutup modal
            function closeModal(modalId) {
                debugLog('Menutup modal:', modalId);

                // Clean up form first
                cleanupModalForm(modalId);

                // Tutup modal dengan Bootstrap API
                const modalElement = document.getElementById(modalId);
                const modalInstance = bootstrap.Modal.getInstance(modalElement);

                if (modalInstance) {
                    modalInstance.hide();
                } else {
                    // Fallback
                    $('#' + modalId).modal('hide');
                }

                // Pastikan backdrop dihapus
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
            }

            // Tambahkan event listener untuk tombol "Tutup" di modal
            $('.modal .btn-secondary[data-bs-dismiss="modal"]').on('click', function() {
                const modalId = $(this).closest('.modal').attr('id');
                cleanupModalForm(modalId);
            });

            // Responsive adjustments for mobile
            function adjustForMobile() {
                if (window.innerWidth < 768) {
                    // Modify table for better mobile view
                    $('.dt-responsive table').addClass('table-sm');

                    // Collapse filter controls on mobile
                    $('.filter-section .filter-controls').addClass('collapse');

                    // Add a toggle button for filters
                    if (!$('#filter-toggle-btn').length) {
                        $('.filter-section').prepend(
                            '<button id="filter-toggle-btn" class="btn btn-sm btn-outline-secondary mb-2 w-100" type="button" data-bs-toggle="collapse" data-bs-target=".filter-controls">Toggle Filters <i class="fas fa-chevron-down"></i></button>'
                        );
                    }
                } else {
                    // Remove mobile-specific modifications
                    $('.dt-responsive table').removeClass('table-sm');
                    $('.filter-section .filter-controls').removeClass('collapse show');
                    $('#filter-toggle-btn').remove();
                }
            }

            // Call adjustments on page load and resize
            adjustForMobile();
            $(window).resize(function() {
                adjustForMobile();
            });
        });
    </script>
@endsection
