@extends('layouts.main')

@section('title', 'Manajemen Catatan Produksi')
@section('breadcrumb-item', 'Catatan Produksi')

@section('css')
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- [Page specific CSS] start -->
    <!-- data tables css -->
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/buttons.bootstrap5.min.css') }}">
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

        /* Gramasi form enhancements */
        .gramasi-helper {
            margin-top: 0.25rem;
        }

        .convert-btn {
            border-left: none !important;
            border-top-left-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
            min-width: 45px !important;
        }

        .convert-btn:hover {
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
            color: white !important;
        }

        .satuan-display {
            font-weight: 500;
            border-right: none !important;
        }

        .gramasi-input:focus+.satuan-display {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .total-calculation-display {
            display: block;
            margin-top: 0.5rem;
            padding: 0.375rem 0.75rem;
            background-color: #d1e7dd;
            border: 1px solid #badbcc;
            border-radius: 0.375rem;
            color: #0f5132;
        }

        /* Icon styling for better UX */
        .conversion-text {
            font-weight: 500;
        }

        .gramasi-helper .text-info {
            background-color: #cff4fc;
            border: 1px solid #b6effb;
            border-radius: 0.25rem;
            padding: 0.25rem 0.5rem;
            display: inline-block;
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
                                <li>Gunakan tombol <i class="fas fa-exchange-alt text-primary"></i> untuk konversi satuan
                                    (kg ↔ gram)</li>
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
                                                    required min="0.01" step="0.01" placeholder="0.00">
                                                <div class="input-group-text satuan-display bg-light"
                                                    style="min-width: 60px;">
                                                    <span class="satuan-text">gr</span>
                                                </div>
                                                <button class="btn btn-outline-secondary btn-sm convert-btn"
                                                    type="button" title="Konversi Satuan (kg ↔ gram)">
                                                    <i class="fas fa-exchange-alt"></i>
                                                </button>
                                            </div>
                                            <div class="gramasi-helper">
                                                <small class="text-info">
                                                    <i class="fas fa-info-circle"></i>
                                                    <span class="conversion-text">Contoh: 500 gram = 0.5 kg, 1200 gram =
                                                        1.2 kg</span>
                                                </small>
                                            </div>
                                            <small class="text-muted">Masukkan jumlah bahan yang digunakan per
                                                produk</small>
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
                                <li>Gunakan tombol <i class="fas fa-exchange-alt text-primary"></i> untuk konversi satuan
                                    (kg ↔ gram)</li>
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
            // Debug mode disabled for production
            const DEBUG = false; // CHANGED FOR PRODUCTION

            function debugLog(...args) {
                if (DEBUG) {
                    // Debug logging disabled
                }
            }

            // Function to show detailed debug alert
            function showDebugAlert(title, data) {
                if (DEBUG) {
                    // Debug alerts disabled
                }
            }

            // Setup AJAX untuk menghindari caching dan force fresh data
            $.ajaxSetup({
                cache: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Cache-Control': 'no-cache, no-store, must-revalidate',
                    'Pragma': 'no-cache',
                    'Expires': '0'
                },
                beforeSend: function(xhr, settings) {
                    // Add timestamp to all AJAX requests to prevent caching
                    if (settings.url.indexOf('?') === -1) {
                        settings.url += '?_t=' + Date.now();
                    } else {
                        settings.url += '&_t=' + Date.now();
                    }
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
                        allowHTML: false,
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
                        allowHTML: false,
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
                    allowHTML: false,
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

            // Update satuan display with user-friendly formatting
            function updateSatuanDisplay(container, satuan) {
                const satuanDisplay = container.find('.satuan-text');
                const convertBtn = container.find('.convert-btn');
                const helperDiv = container.find('.gramasi-helper');
                const conversionText = container.find('.conversion-text');

                if (!satuan || satuan === 'Satuan') {
                    satuanDisplay.text('Satuan');
                    convertBtn.hide();
                    helperDiv.hide();
                    return;
                }

                // Format satuan display
                const formattedSatuan = formatSatuanDisplay(satuan);
                satuanDisplay.text(formattedSatuan);

                // Show conversion helper for weight units
                const isWeight = isWeightUnit(satuan);
                if (isWeight) {
                    convertBtn.show();
                    helperDiv.show();
                    updateConversionHelper(container, satuan);
                } else {
                    convertBtn.hide();
                    helperDiv.hide();
                }
            }

            // Format satuan display for better UX
            function formatSatuanDisplay(satuan) {
                const satuanMap = {
                    'gram': 'gr',
                    'g': 'gr',
                    'kg': 'kg',
                    'kilogram': 'kg',
                    'liter': 'L',
                    'l': 'L',
                    'ml': 'mL',
                    'milliliter': 'mL',
                    'pcs': 'pcs',
                    'pieces': 'pcs',
                    'unit': 'unit'
                };

                const lowerSatuan = satuan.toLowerCase();
                return satuanMap[lowerSatuan] || satuan;
            }

            // Check if satuan is a weight unit
            function isWeightUnit(satuan) {
                const weightUnits = ['gram', 'g', 'kg', 'kilogram', 'gr'];
                const isWeight = weightUnits.includes(satuan.toLowerCase());

                return isWeight;
            }

            // Update conversion helper text
            function updateConversionHelper(container, satuan) {
                const conversionText = container.find('.conversion-text');
                const gramasiInput = container.find('.gramasi-input');
                const currentValue = parseFloat(gramasiInput.val()) || 0;

                // Update placeholder based on satuan
                if (satuan.toLowerCase() === 'kg' || satuan.toLowerCase() === 'kilogram') {
                    gramasiInput.attr('placeholder', 'Contoh: 0.5 (setengah kg)');
                } else if (satuan.toLowerCase() === 'gram' || satuan.toLowerCase() === 'g') {
                    gramasiInput.attr('placeholder', 'Contoh: 500 (gram)');
                } else {
                    gramasiInput.attr('placeholder', '0.00');
                }

                if (currentValue === 0) {
                    if (satuan.toLowerCase() === 'kg' || satuan.toLowerCase() === 'kilogram') {
                        conversionText.text('Contoh: 0.5 kg = 500 gram, 1.2 kg = 1200 gram');
                    } else {
                        conversionText.text('Contoh: 500 gram = 0.5 kg, 1200 gram = 1.2 kg');
                    }
                } else {
                    // Show actual conversion for current value
                    if (satuan.toLowerCase() === 'kg' || satuan.toLowerCase() === 'kilogram') {
                        const inGrams = currentValue * 1000;
                        conversionText.text(`${currentValue} kg = ${inGrams} gram`);
                    } else {
                        const inKg = (currentValue / 1000).toFixed(3);
                        conversionText.text(`${currentValue} gram = ${inKg} kg`);
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

                if (packaging && packaging.trim() !== '') {
                    form.find('[name="packaging"]').val(packaging.trim());
                    showValidationFeedback(form.find('[name="packaging"]'), true,
                        "Packaging terisi otomatis");
                } else {
                    form.find('[name="packaging"]').val('');
                }

                // Trigger validation feedback for product selection
                showValidationFeedback($(this), this.value !== "", "Produk dipilih dengan benar");
            });

            // Event delegation untuk bahan baku selection change - improved
            $(document).on('change', '.bahan-baku-select', function() {
                const value = $(this).val();
                const container = $(this).closest('.array-container');

                // Validate
                showValidationFeedback($(this), value !== "", "Bahan baku dipilih dengan benar");

                // Update satuan
                if (value) {
                    const selectedOption = this.options[this.selectedIndex];
                    const satuan = selectedOption ? selectedOption.dataset.satuan : null;

                    if (satuan) {
                        updateSatuanDisplay(container, satuan);
                    } else {
                        updateSatuanDisplay(container, 'gram'); // Default to gram if no satuan
                    }

                    // Recalculate total for this row
                    calculateRowTotal(container);
                } else {
                    // Reset satuan if no bahan baku selected
                    updateSatuanDisplay(container, 'gram'); // Default to gram instead of 'Satuan'
                }
            });

            // Event delegation untuk product change
            $(document).on('change', '.product-select', function() {
                const selectedOption = this.options[this.selectedIndex];
                const packaging = selectedOption ? selectedOption.dataset.packaging : '';
                if (packaging) {
                    $(this).closest('form').find('[name="packaging"]').val(packaging);
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
                const container = $(this).closest('.array-container');

                showValidationFeedback($(this), isValid, isValid ? "Gramasi valid" :
                    "Gramasi harus angka minimal 0.01");

                // Update conversion helper when value changes
                const satuanText = container.find('.satuan-text').text();
                if (isWeightUnit(satuanText)) {
                    updateConversionHelper(container, satuanText);
                }

                // Update total terpakai jika gramasi berubah
                if (isValid) {
                    calculateRowTotal(container);
                }
            });

            // Handle conversion button click
            $(document).on('click', '.convert-btn', function() {
                const container = $(this).closest('.array-container');
                const gramasiInput = container.find('.gramasi-input');
                const satuanText = container.find('.satuan-text').text();
                const currentValue = parseFloat(gramasiInput.val()) || 0;

                if (currentValue === 0) {
                    Swal.fire({
                        title: 'Masukkan Nilai',
                        text: 'Silakan masukkan nilai gramasi terlebih dahulu untuk dikonversi',
                        icon: 'info',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                let convertedValue, newUnit, conversionText;

                if (satuanText.toLowerCase() === 'kg') {
                    // Convert kg to gram
                    convertedValue = currentValue * 1000;
                    newUnit = 'gr';
                    conversionText = `${currentValue} kg = ${convertedValue} gram`;
                } else {
                    // Convert gram to kg
                    convertedValue = (currentValue / 1000).toFixed(3);
                    newUnit = 'kg';
                    conversionText = `${currentValue} gram = ${convertedValue} kg`;
                }

                Swal.fire({
                    title: 'Konversi Satuan',
                    html: `
                        <div class="text-center">
                            <p class="mb-3">${conversionText}</p>
                            <p class="text-muted">Apakah Anda ingin menggunakan nilai yang dikonversi?</p>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: `Ya, gunakan ${convertedValue} ${newUnit}`,
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        gramasiInput.val(convertedValue);
                        container.find('.satuan-text').text(newUnit);

                        // Update helper and recalculate
                        updateConversionHelper(container, newUnit);
                        calculateRowTotal(container);

                        // Show validation feedback
                        showValidationFeedback(gramasiInput, true, "Nilai berhasil dikonversi");
                    }
                });
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
                                    <input type="number" class="form-control gramasi-input" name="gramasi[]" required min="0.01" step="0.01" placeholder="0.00">
                                    <div class="input-group-text satuan-display bg-light" style="min-width: 60px;">
                                        <span class="satuan-text">gr</span>
                                    </div>
                                    <button class="btn btn-outline-secondary btn-sm convert-btn" type="button" 
                                        title="Konversi Satuan (kg ↔ gram)">
                                        <i class="fas fa-exchange-alt"></i>
                                    </button>
                                </div>
                                <div class="gramasi-helper">
                                    <small class="text-info">
                                        <i class="fas fa-info-circle"></i>
                                        <span class="conversion-text">Contoh: 500 gram = 0.5 kg, 1200 gram = 1.2 kg</span>
                                    </small>
                                </div>
                                <small class="text-muted">Masukkan jumlah bahan yang digunakan per produk</small>
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

            // Show convert button and helper for all containers on modal shown
            $('#addProduksiModal, #editProduksiModal').on('shown.bs.modal', function() {
                setTimeout(() => {
                    // Show convert button and helper for all containers
                    $(this).find('.convert-btn').show();
                    $(this).find('.gramasi-helper').show();
                    $(this).find('.conversion-text').text(
                        'Icon konversi tersedia untuk satuan berat (kg/gram)');
                }, 200);
            });

            // Add More SKU Button Click untuk form Add
            $('#add-more-sku').on('click', function() {
                const newContainer = $(getSkuGramasiTemplate(false));
                $('#add-sku-gramasi-container').append(newContainer);

                // Initialize Choices.js for the newly added dropdown
                const newSelect = newContainer.find('.bahan-baku-select')[0];
                if (newSelect) {
                    setTimeout(() => {
                        initBahanBakuChoices(newSelect);

                        // Ensure convert button and helper are visible for new containers
                        updateSatuanDisplay(newContainer, 'gram');

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
                    const satuan = $(this).find('.satuan-text').text();
                    $(this).find('.total-calculation-display').remove();
                    if (gramasi > 0 && quantity > 0) {
                        $(this).find('.gramasi-helper').after(
                            `<small class="text-success total-calculation-display mt-1">
                                <i class="fas fa-calculator me-1"></i>
                                Total terpakai: <strong>${totalTerpakai} ${satuan}</strong>
                            </small>`
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
                const satuan = container.find('.satuan-text').text();
                container.find('.total-calculation-display').remove();
                if (gramasi > 0 && quantity > 0) {
                    container.find('.gramasi-helper').after(
                        `<small class="text-success total-calculation-display mt-1">
                            <i class="fas fa-calculator me-1"></i>
                            Total terpakai: <strong>${totalTerpakai} ${satuan}</strong>
                        </small>`
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
                            // Reset form first
                            form[0].reset();

                            // Remove all containers kecuali yang pertama
                            form.find('.array-container:not(:first)').remove();

                            // Get modal instance and hide it properly
                            const modalElement = document.getElementById('addProduksiModal');
                            const modalInstance = bootstrap.Modal.getInstance(modalElement) ||
                                new bootstrap.Modal(modalElement);
                            modalInstance.hide();

                            // Clean up modal backdrop immediately
                            setTimeout(function() {
                                $('.modal-backdrop').remove();
                                $('body').removeClass('modal-open').css('padding-right',
                                    '');
                            }, 100);

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

                            // Reload table with error handling and reset to first page to show new data
                            try {
                                table.ajax.reload(function() {
                                    // After reload, go to first page to see the newest data
                                    table.page('first').draw('page');

                                    // Scroll to top of table to make sure user sees the data
                                    setTimeout(function() {
                                        $('html, body').animate({
                                            scrollTop: $(
                                                    '#produksi-table')
                                                .offset().top - 100
                                        }, 500);
                                    }, 200);
                                }, true); // true = reset paging to first page
                            } catch (error) {
                                // Fallback: reload the page if table reload fails
                                setTimeout(function() {
                                    window.location.reload();
                                }, 1000);
                            }

                            // Show success message
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                timer: 2000,
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
                            $('#editProduksiModal').modal('hide');

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

                            // Reload table and scroll to show updated data
                            table.ajax.reload(function() {
                                // Scroll to table to make sure user sees the updated data
                                setTimeout(function() {
                                    $('html, body').animate({
                                        scrollTop: $('#produksi-table')
                                            .offset().top - 100
                                    }, 500);
                                }, 200);
                            }, false);

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

            // Complete rewrite of edit button click handler with proper product value setting
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

                            // Reset form first
                            $('#editProduksiForm')[0].reset();
                            $('#edit-sku-gramasi-container').empty();

                            // Set basic data
                            $('#edit_produksi_id').val(catatan.id);
                            $('#edit_packaging').val(catatan.packaging);
                            $('#edit_quantity').val(catatan.quantity);

                            // Store product_id for setting after modal is shown
                            $('#editProduksiModal').data('product-id', catatan.product_id);
                            debugLog('Stored product_id for edit modal:', catatan.product_id);

                            // Populate bahan baku data first
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

                                    // Update satuan display
                                    updateSatuanDisplay(container, bahan.satuan ||
                                        'gram');

                                    $('#edit-sku-gramasi-container').append(container);
                                });
                            }

                            // Show the modal - product will be set after modal is shown
                            $('#editProduksiModal').modal('show');
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
                                    // Reload table and scroll to show remaining data
                                    table.ajax.reload(function() {
                                        // Scroll to table to make sure user sees the remaining data
                                        setTimeout(function() {
                                            $('html, body').animate({
                                                scrollTop: $(
                                                        '#produksi-table'
                                                    )
                                                    .offset()
                                                    .top - 100
                                            }, 500);
                                        }, 200);
                                    });

                                    // Show success message
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

            // Clean up modal when it's hidden (same as product)
            $('#editProduksiModal, #addProduksiModal').on('hidden.bs.modal', function() {
                debugLog('Modal hidden - cleaning up');

                // Reset form and clear validation states
                $(this).find('form')[0].reset();
                $(this).find('.is-invalid').removeClass('is-invalid');
                $(this).find('.invalid-feedback').text('');

                // Re-enable all buttons
                $(this).find('button').prop('disabled', false);

                // Clean up any lingering modal artifacts
                setTimeout(function() {
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open').css('padding-right', '');

                    // Force remove any stuck modal classes
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                }, 100);

                // Also call our cleanup function
                cleanupModalForm(this.id);
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

            $('#editProduksiModal').on('show.bs.modal', function() {
                debugLog('Edit modal showing - preparing Choices.js');
                // Initialize product choices BEFORE modal is shown
                setTimeout(() => {
                    initEditProductChoices();
                }, 50);
            });

            $('#editProduksiModal').on('shown.bs.modal', function() {
                debugLog('Edit modal shown - setting product and finalizing Choices.js');

                const modal = $(this);
                const productId = modal.data('product-id');

                // Set product value first
                if (productId) {
                    // Set HTML select value directly
                    $('#edit_product_id').val(productId);

                    // Update Choices.js if initialized
                    if (editProductChoices) {
                        try {
                            editProductChoices.setChoiceByValue(productId.toString());
                            debugLog('Product choice set successfully:', productId);
                        } catch (e) {
                            debugLog('Failed to set product choice, trying alternative method:', e);
                            // Destroy and recreate if setting fails
                            try {
                                editProductChoices.destroy();
                            } catch (destroyError) {
                                debugLog('Error destroying product choices:', destroyError);
                            }
                            initEditProductChoices();
                            setTimeout(() => {
                                if (editProductChoices) {
                                    editProductChoices.setChoiceByValue(productId.toString());
                                }
                            }, 200);
                        }
                    }

                    // Trigger change event for packaging auto-fill
                    setTimeout(() => {
                        $('#edit_product_id').trigger('change');
                    }, 300);
                }

                // Initialize bahan baku choices for existing containers
                setTimeout(() => {
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
                }, 400);
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

                        // Ensure convert button and helper are visible for new containers
                        updateSatuanDisplay(newContainer, 'gram');

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

                        return d;
                    },
                    error: function(xhr, error, thrown) {
                        debugLog('Error loading data:', error, thrown, xhr.responseText);
                    }
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
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        visible: false, // Hidden column for sorting
                        searchable: false
                    }
                ],
                order: [
                    [9, 'desc'] // Order by created_at (newest first) so new data appears at top
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
                drawCallback: function(settings) {
                    const api = this.api();
                    const data = api.rows().data();
                    debugLog('DataTable selesai di-render dengan', data.length, 'baris data');
                },
                initComplete: function() {
                    debugLog('DataTable selesai diinisialisasi');
                },
                preDrawCallback: function() {
                    debugLog('DataTable mulai menggambar ulang...');
                }
            });

            // Fungsi untuk reload tabel - Simplified
            function reloadTable(callback) {
                debugLog('Memuat ulang tabel...');

                table.ajax.reload(function(json) {
                    debugLog('Tabel berhasil dimuat ulang dengan', json?.recordsTotal || 0, 'data');

                    if (typeof callback === 'function') {
                        callback(json);
                    }
                }, false);
            }

            // Fungsi untuk force reload tabel yang sangat agresif sebagai backup
            function emergencyReloadTable(callback) {
                debugLog('Emergency reload tabel...');

                // Destroy and recreate the entire DataTable if needed
                try {
                    // Clear all data first
                    table.clear().draw();

                    // Force ajax reload with aggressive cache busting
                    const ajaxUrl = table.ajax.url();
                    const cacheBuster = '?_emergency=' + Date.now() + '&_force=true';

                    table.ajax.url(ajaxUrl + cacheBuster).load(function(json) {
                        debugLog('Emergency reload selesai dengan', json?.recordsTotal || 0, 'data');

                        // Restore original URL
                        table.ajax.url(ajaxUrl);

                        if (typeof callback === 'function') {
                            callback(json);
                        }
                    });
                } catch (error) {
                    debugLog('Emergency reload error:', error);
                    // Fallback to page reload if all else fails
                    if (callback) callback();
                }
            }

            // Fungsi untuk force reload tabel dengan cache busting dan loading indicator
            function forceReloadTable(callback) {
                debugLog('Force reload tabel...');

                // Show loading indicator
                showTableLoading();

                // Add cache busting parameter to force fresh data
                const originalAjaxData = table.settings()[0].ajax.data;

                // Temporarily modify ajax data to include cache buster
                table.settings()[0].ajax.data = function(d) {
                    // Call original data function
                    const result = originalAjaxData.call(this, d);

                    // Add cache buster to force fresh request
                    result._t = Date.now();
                    result._refresh = 'force';

                    return result;
                };

                // Use requestAnimationFrame for smooth DOM operations
                requestAnimationFrame(function() {
                    // Force reload with cache busting
                    table.ajax.reload(function(json) {
                        debugLog('Force reload selesai dengan', json?.recordsTotal || 0, 'data');

                        // Hide loading indicator
                        hideTableLoading();

                        // Show refresh success notification
                        showRefreshNotification(json?.recordsTotal || 0);

                        // Restore original ajax data function
                        table.settings()[0].ajax.data = originalAjaxData;

                        if (typeof callback === 'function') {
                            callback(json);
                        }
                    }, true); // true = reset paging to first page
                });
            }

            // Show loading indicator on table
            function showTableLoading() {
                $('#produksi-table_processing').show();
                $('.dataTables_empty').hide();
            }

            // Hide loading indicator
            function hideTableLoading() {
                $('#produksi-table_processing').hide();
            }

            // Show refresh success notification
            function showRefreshNotification(recordCount) {
                // Create or update notification badge
                let badge = $('#table-refresh-badge');
                if (badge.length === 0) {
                    badge = $(
                        '<div id="table-refresh-badge" class="alert alert-success alert-dismissible fade show position-fixed" style="top: 100px; right: 20px; z-index: 9999; min-width: 300px;"></div>'
                    );
                    $('body').append(badge);
                }

                badge.html(`
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle me-2 text-success"></i>
                        <span><strong>Data Diperbarui!</strong> Menampilkan ${recordCount} catatan produksi</span>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                    </div>
                `).removeClass('fade').addClass('show');

                // Auto hide after 3 seconds
                setTimeout(function() {
                    badge.fadeOut(500, function() {
                        badge.remove();
                    });
                }, 3000);
            }

            // Apply filters when text inputs change with debounce (300ms delay)
            $('#filter-sku, #filter-nama, #filter-packaging, #filter-label, #filter-bahan-baku').on('keyup change',
                debounce(
                    function() {
                        forceReloadTable();
                    }, 300));

            // Apply date filter when button is clicked
            $('#apply-date-filter').on('click', function() {
                // Update table title with date range
                updateTablePeriod();

                // Force reload table
                forceReloadTable();
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

                // Force reload table
                forceReloadTable();
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
                $(modal).find('.satuan-text').text('Satuan');
                $(modal).find('.convert-btn').hide();
                $(modal).find('.gramasi-helper').hide();

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

            // Fungsi untuk properly menutup modal dengan focus management
            function closeModal(modalId) {
                debugLog('Menutup modal:', modalId);

                const modalElement = document.getElementById(modalId);

                // Clear focus from modal elements before closing
                if (modalElement) {
                    const focusedElement = modalElement.querySelector(':focus');
                    if (focusedElement) {
                        focusedElement.blur();
                    }
                }

                // Clean up form first
                cleanupModalForm(modalId);

                // Use requestAnimationFrame for smooth modal closing
                requestAnimationFrame(function() {
                    // Tutup modal dengan Bootstrap API
                    const modalInstance = bootstrap.Modal.getInstance(modalElement);

                    if (modalInstance) {
                        modalInstance.hide();
                    } else {
                        // Fallback
                        $('#' + modalId).modal('hide');
                    }

                    // Delay backdrop cleanup to avoid reflow
                    setTimeout(function() {
                        $('body').removeClass('modal-open');
                        $('.modal-backdrop').remove();

                        // Return focus to body to prevent accessibility issues
                        document.body.focus();
                    }, 150);
                });
            }

            // Tambahkan event listener untuk tombol "Tutup" di modal
            $('.modal .btn-secondary[data-bs-dismiss="modal"]').on('click', function() {
                const modalId = $(this).closest('.modal').attr('id');
                cleanupModalForm(modalId);
            });

            // Additional cleanup on page unload (same as product)
            $(window).on('beforeunload', function() {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css('padding-right', '');
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
