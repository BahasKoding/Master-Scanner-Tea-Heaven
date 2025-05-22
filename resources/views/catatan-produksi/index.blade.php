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
            max-width: 95%;
        }

        @media (min-width: 992px) {
            .modal-xl {
                max-width: 90%;
            }
        }

        @media (min-width: 1200px) {
            .modal-xl {
                max-width: 1000px;
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
    </style>
@endsection

@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">
        <!-- Catatan Produksi Table start -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Daftar Catatan Produksi</h5>
                        <div>
                            <button id="clear-filters" class="btn btn btn-secondary">
                                <i class="fas fa-filter"></i> Hapus Filter
                            </button>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addProduksiModal">
                                <i class="fas fa-plus"></i> Tambah Catatan Produksi Baru
                            </button>
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
                                <input type="date" class="form-control form-control-sm" id="start-date" name="start_date"
                                    value="{{ now()->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-3 col-sm-6 col-12 mb-2">
                                <label for="end-date" class="form-label small filter-label">Tanggal Selesai</label>
                                <input type="date" class="form-control form-control-sm" id="end-date" name="end_date"
                                    value="{{ now()->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-2 col-sm-6 col-12 d-flex align-items-end mb-2">
                                <button id="apply-date-filter" class="btn btn-sm btn-primary w-100">
                                    <i class="fas fa-filter"></i> Terapkan Filter
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- End Filter Section -->

                    <div class="mb-3">
                        <h6 class="text-primary">
                            <span id="table-period">Menampilkan data untuk periode: {{ now()->format('d M Y') }}</span>
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
                                            ({{ $product->packaging }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Pilih produk yang akan dicatat produksinya</small>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                <label class="form-label">Packaging <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="packaging" required>
                                <small class="text-muted">Kemasan produk (akan otomatis terisi sesuai produk)</small>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="quantity" required min="1">
                                <small class="text-muted">Jumlah produk yang diproduksi (harus angka bulat)</small>
                            </div>
                        </div>

                        <div class="form-section">
                            <h6>Bahan Baku dan Gramasi <span class="badge bg-info">Minimal satu bahan baku</span></h6>
                            <div id="add-sku-gramasi-container">
                                <div class="array-container">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
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
                                        <div class="col-md-6 mb-3">
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
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="btn-action-group">
                                                <button type="button" class="btn btn-danger btn-remove">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="add-more-sku" class="btn btn-secondary mt-2">
                            <i class="fas fa-plus"></i> Tambah Bahan Baku
                        </button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
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
                                            ({{ $product->packaging }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Pilih produk yang akan dicatat produksinya</small>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                <label class="form-label">Packaging <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="packaging" id="edit_packaging"
                                    required>
                                <small class="text-muted">Kemasan produk (akan otomatis terisi sesuai produk)</small>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
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
                            <button type="button" id="edit-add-more-sku" class="btn btn-secondary mt-2">
                                <i class="fas fa-plus"></i> Tambah Bahan Baku
                            </button>
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
                    console.log(...args);
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

            // Inisialisasi Choices.js untuk dropdown produk dan bahan baku
            // Dropdown Produk di form tambah
            const addProductSelect = document.querySelector('#addProduksiForm .product-select');
            const addProductChoices = new Choices(addProductSelect, {
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

            // Dropdown Produk di form edit
            const editProductSelect = document.querySelector('#editProduksiForm .product-select');
            const editProductChoices = new Choices(editProductSelect, {
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

            // Fungsi untuk inisialisasi Choices pada bahan baku
            function initBahanBakuChoices(element) {
                return new Choices(element, {
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
            }

            // Inisialisasi Choices untuk semua dropdown bahan baku yang sudah ada
            document.querySelectorAll('.bahan-baku-select').forEach(element => {
                initBahanBakuChoices(element);
            });

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

            // Validasi input secara realtime dan handling Choices.js events
            $(document).on('change', '.product-select', function() {
                const value = $(this).val();
                showValidationFeedback($(this), value !== "", "Produk dipilih dengan benar");
            });

            // Listen for changes on Choices.js dropdowns
            document.addEventListener('change', function(e) {
                const target = e.target;

                // Handle product choice change for packaging auto-fill
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

            // Template function for SKU and Gramasi inputs
            function getSkuGramasiTemplate(isEdit = false) {
                const prefix = isEdit ? 'edit-' : '';
                const parentModal = isEdit ? '#editProduksiModal' : '#addProduksiModal';
                return `
                    <div class="array-container">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bahan Baku <span class="text-danger">*</span></label>
                                <select class="form-select bahan-baku-select" name="sku_induk[]" required>
                                    <option value="">Pilih Bahan Baku</option>
                                    @foreach ($bahanBaku as $bahan)
                                        <option value="{{ $bahan->id }}" data-satuan="{{ $bahan->satuan }}">
                                            {{ $bahan->sku_induk }} - {{ $bahan->nama_barang }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gramasi <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control gramasi-input" name="gramasi[]" 
                                        required min="0.01" step="0.01" placeholder="Gramasi">
                                    <span class="input-group-text satuan-display">Satuan</span>
                                </div>
                                <small class="text-muted">Jumlah bahan yang digunakan per produk</small>
                                <input type="hidden" class="total-terpakai-input" name="total_terpakai[]" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="btn-action-group">
                                    <button type="button" class="btn btn-danger btn-remove">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

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

            // Add more SKU Induk fields
            $('#add-more-sku').on('click', function() {
                const newContainer = $(getSkuGramasiTemplate());
                $('#add-sku-gramasi-container').append(newContainer);

                // Initialize Choices.js for the newly added dropdown
                const newSelect = newContainer.find('.bahan-baku-select')[0];
                if (newSelect) {
                    initBahanBakuChoices(newSelect);
                }
            });

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
                        timer: 2000,
                        showConfirmButton: false
                    });
                    isValid = false;
                }

                // Jika tidak valid, hentikan
                if (!isValid) {
                    Swal.fire({
                        title: 'Perhatian',
                        text: 'Mohon isi semua data yang wajib diisi dengan benar',
                        icon: 'warning',
                        confirmButtonText: 'Saya Mengerti'
                    });
                    return;
                }

                // For testing: ensure total_terpakai values are set
                calculateTotalTerpakai($(this));

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

                        if (data.success) {
                            // Reset form
                            form[0].reset();

                            // Remove all containers kecuali yang pertama
                            form.find('.array-container:not(:first)').remove();

                            // Reset Choices.js selectors
                            addProductChoices.setChoiceByValue('');
                            document.querySelectorAll('#addProduksiForm .bahan-baku-select')
                                .forEach(select => {
                                    const choicesInstance = select.closest('.choices') ?
                                        select.choices : new Choices(select);
                                    choicesInstance.setChoiceByValue('');
                                });

                            // Clear form elements
                            form.find('.gramasi-input').val('');
                            form.find('.satuan-display').text('Satuan');
                            form.find('.is-valid, .is-invalid').removeClass(
                                'is-valid is-invalid');
                            form.find(
                                '.valid-feedback, .invalid-feedback, .total-calculation-display'
                            ).remove();

                            // Properly hide modal dan clean up
                            closeModal('addProduksiModal');

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

                        if (xhr.status === 422) {
                            // Validation errors - show but don't prevent resubmission
                            const errors = xhr.responseJSON.errors;
                            const errorMessages = Object.values(errors).flat();

                            // Highlight invalid fields
                            for (const field in errors) {
                                if (field.includes('sku_induk.') || field.includes(
                                        'gramasi.') || field.includes('total_terpakai.')) {
                                    const index = field.split('.')[1];
                                    const containers = form.find('.array-container');
                                    if (containers.length > index) {
                                        const container = $(containers[index]);
                                        if (field.startsWith('sku_induk.')) {
                                            showValidationFeedback(container.find(
                                                '.bahan-baku-select'), false, errors[
                                                field][0]);
                                        } else if (field.startsWith('gramasi.')) {
                                            showValidationFeedback(container.find(
                                                '.gramasi-input'), false, errors[field][
                                                0
                                            ]);
                                        }
                                    }
                                } else if (form.find(`[name="${field}"]`).length) {
                                    showValidationFeedback(form.find(`[name="${field}"]`),
                                        false, errors[field][0]);
                                }
                            }

                            Swal.fire({
                                title: 'Ada kesalahan pada data',
                                html: errorMessages.join('<br>'),
                                icon: 'warning',
                                confirmButtonText: 'Coba Lagi',
                                confirmButtonColor: '#3085d6'
                            });
                        } else {
                            // Other errors
                            Swal.fire({
                                title: 'Gagal Menyimpan Data',
                                text: xhr.responseJSON?.message ||
                                    'Terjadi kesalahan pada permintaan.',
                                icon: 'error',
                                confirmButtonText: 'Coba Lagi',
                                confirmButtonColor: '#3085d6'
                            });
                        }
                    },
                    complete: function() {
                        // Always ensure the UI is restored
                        submitButton.prop('disabled', false);
                        Swal.close();
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
                        timer: 2000,
                        showConfirmButton: false
                    });
                    isValid = false;
                }

                // Jika tidak valid, hentikan
                if (!isValid) {
                    Swal.fire({
                        title: 'Perhatian',
                        text: 'Mohon isi semua data yang wajib diisi dengan benar',
                        icon: 'warning',
                        confirmButtonText: 'Saya Mengerti'
                    });
                    return;
                }

                // For testing: ensure total_terpakai values are set
                calculateTotalTerpakai($(this));

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

                        if (data.success) {
                            // Reset form and close modal
                            form[0].reset();

                            // Reset Choices.js selectors
                            editProductChoices.setChoiceByValue('');
                            document.querySelectorAll('#editProduksiForm .bahan-baku-select')
                                .forEach(select => {
                                    const choicesInstance = select.closest('.choices') ?
                                        select.choices : new Choices(select);
                                    choicesInstance.setChoiceByValue('');
                                });

                            form.find('.is-valid, .is-invalid').removeClass(
                                'is-valid is-invalid');
                            form.find(
                                '.valid-feedback, .invalid-feedback, .total-calculation-display'
                            ).remove();

                            // Properly hide modal
                            closeModal('editProduksiModal');

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
                        Swal.close();

                        if (xhr.status === 422) {
                            // Validation errors - show but don't prevent resubmission
                            const errors = xhr.responseJSON.errors;
                            const errorMessages = Object.values(errors).flat();

                            // Highlight invalid fields
                            for (const field in errors) {
                                if (field.includes('sku_induk.') || field.includes(
                                        'gramasi.') || field.includes('total_terpakai.')) {
                                    const index = field.split('.')[1];
                                    const containers = form.find('.array-container');
                                    if (containers.length > index) {
                                        const container = $(containers[index]);
                                        if (field.startsWith('sku_induk.')) {
                                            showValidationFeedback(container.find(
                                                '.bahan-baku-select'), false, errors[
                                                field][0]);
                                        } else if (field.startsWith('gramasi.')) {
                                            showValidationFeedback(container.find(
                                                '.gramasi-input'), false, errors[field][
                                                0
                                            ]);
                                        }
                                    }
                                } else if (form.find(`[name="${field}"]`).length) {
                                    showValidationFeedback(form.find(`[name="${field}"]`),
                                        false, errors[field][0]);
                                }
                            }

                            Swal.fire({
                                title: 'Ada kesalahan pada data',
                                html: errorMessages.join('<br>'),
                                icon: 'warning',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#3085d6'
                            });
                        } else {
                            // Other errors
                            Swal.fire({
                                title: 'Gagal Memperbarui',
                                text: xhr.responseJSON?.message ||
                                    'Tidak dapat memperbarui catatan produksi saat ini.',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#3085d6'
                            });
                        }
                    },
                    complete: function() {
                        // Always ensure the UI is restored
                        submitButton.prop('disabled', false);
                        Swal.close();
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
                console.log('Edit button clicked for ID:', id);

                // Show loading state
                Swal.fire({
                    title: 'Memuat...',
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
                    success: function(response) {
                        console.log('Server response:', response);
                        Swal.close();

                        if (response.success) {
                            const data = response.data;
                            console.log('Catatan produksi data:', data);

                            // Set hidden ID
                            $('#edit_produksi_id').val(data.id);

                            // Set product selection using Choices.js
                            editProductChoices.setChoiceByValue(data.product_id.toString());

                            // Set basic fields
                            $('#edit_packaging').val(data.packaging);
                            $('#edit_quantity').val(data.quantity);

                            // Clear existing SKU Induk and Gramasi fields
                            $('#edit-sku-gramasi-container').empty();

                            // Handle bahan_baku_details if available
                            if (data.bahan_baku_details && data.bahan_baku_details.length > 0) {
                                // Add bahan baku and gramasi fields from detailed data
                                data.bahan_baku_details.forEach(function(bahanDetail) {
                                    const template = getSkuGramasiTemplate(true);
                                    const $template = $(template);
                                    $('#edit-sku-gramasi-container').append($template);

                                    // Get the select element for Choices initialization
                                    const selectElement = $template.find(
                                        '.bahan-baku-select')[0];
                                    const choicesInstance = initBahanBakuChoices(
                                        selectElement);

                                    // Set values after Choices is initialized
                                    setTimeout(() => {
                                        choicesInstance.setChoiceByValue(
                                            bahanDetail.id.toString());
                                        $template.find('.gramasi-input').val(
                                            bahanDetail.gramasi);
                                        $template.find('.total-terpakai-input')
                                            .val(bahanDetail.total_terpakai);
                                        $template.find('.satuan-display').text(
                                            bahanDetail.satuan || 'Satuan');
                                    }, 50);
                                });
                            } else {
                                // Fallback to using raw arrays if details not available
                                let skuIndukArray = data.sku_induk;
                                let gramasiArray = data.gramasi;
                                let totalTerpakaiArray = data.total_terpakai;

                                if (typeof skuIndukArray === 'string') {
                                    try {
                                        skuIndukArray = JSON.parse(skuIndukArray);
                                    } catch (e) {
                                        console.error('Error parsing SKU Induk:', e);
                                        skuIndukArray = [];
                                    }
                                }

                                if (typeof gramasiArray === 'string') {
                                    try {
                                        gramasiArray = JSON.parse(gramasiArray);
                                    } catch (e) {
                                        console.error('Error parsing Gramasi:', e);
                                        gramasiArray = [];
                                    }
                                }

                                if (typeof totalTerpakaiArray === 'string') {
                                    try {
                                        totalTerpakaiArray = JSON.parse(totalTerpakaiArray);
                                    } catch (e) {
                                        console.error('Error parsing Total Terpakai:', e);
                                        totalTerpakaiArray = [];
                                    }
                                }

                                // Add SKU Induk and Gramasi fields
                                for (let i = 0; i < (skuIndukArray?.length || 0); i++) {
                                    const template = getSkuGramasiTemplate(true);
                                    const $template = $(template);
                                    $('#edit-sku-gramasi-container').append($template);

                                    // Get the select element for Choices initialization
                                    const selectElement = $template.find('.bahan-baku-select')[
                                        0];
                                    const choicesInstance = initBahanBakuChoices(selectElement);

                                    // Set values after Choices is initialized
                                    setTimeout(() => {
                                        choicesInstance.setChoiceByValue(skuIndukArray[
                                            i].toString());
                                        $template.find('.gramasi-input').val(
                                            gramasiArray[i]);
                                        $template.find('.total-terpakai-input').val(
                                            totalTerpakaiArray[i]);

                                        // Update satuan display - need to get it from the selected option
                                        const bahanBakuId = skuIndukArray[i];
                                        const bahanBakuOption = $(
                                            `#addProduksiForm .bahan-baku-select option[value="${bahanBakuId}"]`
                                        );
                                        const satuan = bahanBakuOption.data('satuan') ||
                                            'Satuan';
                                        $template.find('.satuan-display').text(satuan);
                                    }, 50);
                                }
                            }

                            // If no SKU Induk was added (empty array), add one empty input
                            if ($('#edit-sku-gramasi-container').children().length === 0) {
                                const template = getSkuGramasiTemplate(true);
                                const $template = $(template);
                                $('#edit-sku-gramasi-container').append($template);

                                // Initialize Choices.js
                                const selectElement = $template.find('.bahan-baku-select')[0];
                                initBahanBakuChoices(selectElement);
                            }

                            // Show the modal
                            var editModal = new bootstrap.Modal(document.getElementById(
                                'editProduksiModal'));
                            editModal.show();
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.message ||
                                    'Gagal mengambil data catatan produksi',
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
                            text: 'Gagal mengambil data catatan produksi. Silahkan coba lagi.',
                            icon: 'error'
                        });
                    }
                });
            });

            // Clean up modal when it's hidden
            $('.modal').on('hidden.bs.modal', function() {
                debugLog('Modal hidden - cleaning up');
                $(this).find('form')[0].reset();
                $(this).find('.array-container:not(:first)').remove();

                // Reset Choices selectors
                if (this.id === 'addProduksiModal') {
                    addProductChoices.setChoiceByValue('');
                    document.querySelectorAll('#addProduksiForm .bahan-baku-select').forEach(select => {
                        if (select.choices) {
                            select.choices.setChoiceByValue('');
                        }
                    });
                } else if (this.id === 'editProduksiModal') {
                    editProductChoices.setChoiceByValue('');
                    document.querySelectorAll('#editProduksiForm .bahan-baku-select').forEach(select => {
                        if (select.choices) {
                            select.choices.setChoiceByValue('');
                        }
                    });
                }

                // Reset validation states
                $(this).find('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
                $(this).find('.valid-feedback, .invalid-feedback, .total-calculation-display').remove();

                // Pastikan backdrop dihapus dengan benar
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
            });

            // Initialize Choices.js when modal is shown
            $('#addProduksiModal').on('shown.bs.modal', function() {
                debugLog('Add modal shown - initializing Choices.js');
                // Reinitialize if needed
                addProductChoices.setChoiceByValue('');
            });

            $('#editProduksiModal').on('shown.bs.modal', function() {
                debugLog('Edit modal shown - initializing Choices.js');
                // Reinitialize if needed
                editProductChoices.setChoiceByValue('');
            });

            // Edit Button Click
            $('#edit-add-more-sku').on('click', function() {
                const newContainer = $(getSkuGramasiTemplate(true));
                $('#edit-sku-gramasi-container').append(newContainer);

                // Initialize Choices.js for the newly added dropdown
                const newSelect = newContainer.find('.bahan-baku-select')[0];
                if (newSelect) {
                    initBahanBakuChoices(newSelect);
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
            $('#filter-sku, #filter-nama, #filter-packaging, #filter-bahan-baku').on('keyup change', debounce(
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

                const formattedStartDate = new Date(startDate).toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric'
                });

                const formattedEndDate = new Date(endDate).toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric'
                });

                // Update the table period text
                if (startDate === endDate) {
                    $('#table-period').text(`Menampilkan data untuk tanggal: ${formattedStartDate}`);
                } else {
                    $('#table-period').text(
                        `Menampilkan data untuk periode: ${formattedStartDate} - ${formattedEndDate}`);
                }
            }

            // Clear filters function
            $('#clear-filters').on('click', function() {
                $('#filter-sku').val('');
                $('#filter-nama').val('');
                $('#filter-packaging').val('');
                $('#filter-bahan-baku').val('').trigger('change');

                // Reset date filters to today
                $('#start-date').val('{{ now()->format('Y-m-d') }}');
                $('#end-date').val('{{ now()->format('Y-m-d') }}');

                // Update table period display
                updateTablePeriod();

                // Reload table
                table.ajax.reload();
            });

            // Initialize table period display on page load
            updateTablePeriod();

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

            // Fungsi untuk properly menutup modal
            function closeModal(modalId) {
                debugLog('Menutup modal:', modalId);

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
                closeModal(modalId);
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
