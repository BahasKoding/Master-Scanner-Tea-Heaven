@extends('layouts.main')

@section('title', 'Purchase Items')
@section('breadcrumb-item', 'Purchase Items')

@section('css')
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- [Page specific CSS] start -->
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
                                    <th>Retur ke Supplier</th>
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
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">Detail Penerimaan Barang</h6>
                            <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="collapse" data-bs-target="#calculationGuide" aria-expanded="false">
                                <i class="fas fa-question-circle"></i> Panduan Kalkulasi
                            </button>
                        </div>
                        
                        <!-- Calculation Guide Collapsible -->
                        <div class="collapse" id="calculationGuide">
                            <div class="card card-body bg-light mb-3">
                                <h6 class="text-primary"><i class="fas fa-info-circle"></i> Cara Menghitung Stok Masuk</h6>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-success">📋 Rumus Dasar:</h6>
                                        <div class="bg-white p-3 rounded border">
                                            <strong class="text-primary">Total Stok Masuk = Qty Masuk − Defect − Retur ke Supplier</strong>
                                        </div>
                                        
                                        <h6 class="text-info mt-3">📝 Penjelasan:</h6>
                                        <ul class="list-unstyled">
                                            <li><strong>Qty Masuk:</strong> Jumlah barang yang benar-benar diterima dari supplier</li>
                                            <li><strong>Defect:</strong> Barang rusak/cacat yang <u>tetap di kita</u> dan mengurangi stok</li>
                                            <li><strong>Retur ke Supplier:</strong> Barang yang <u>dikirim balik</u> ke supplier, sehingga <u>stok kita berkurang</u></li>
                                        </ul>
                                        
                                        <div class="alert alert-warning mt-2">
                                            <small><strong>⚠️ Penting:</strong> Jika supplier mengirim <em>replacement</em> di kemudian hari, <u>catat sebagai purchase baru</u> (bukan ditambah balik ke transaksi lama).</small>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <h6 class="text-warning">💡 Contoh Kasus:</h6>
                                        
                                        <div class="bg-white p-3 rounded border mb-2">
                                            <strong>Kasus Normal:</strong><br>
                                            • Beli: 1000 pcs<br>
                                            • Masuk: 1000 pcs<br>
                                            • Defect: 20 pcs<br>
                                            • Retur ke Supplier: 5 pcs<br>
                                            <span class="text-success"><strong>= 1000 − 20 − 5 = 975 pcs</strong></span>
                                        </div>
                                        
                                        <div class="bg-white p-3 rounded border mb-2">
                                            <strong>Kasus Pengiriman Kurang:</strong><br>
                                            • Beli: 1000 pcs<br>
                                            • Masuk: 950 pcs<br>
                                            • Defect: 5 pcs<br>
                                            • Retur ke Supplier: 0 pcs<br>
                                            <span class="text-success"><strong>= 950 − 5 − 0 = 945 pcs</strong></span>
                                        </div>

                                        <div class="bg-white p-3 rounded border">
                                            <strong>Kasus Ada Retur Balik ke Supplier:</strong><br>
                                            • Beli: 500 pcs<br>
                                            • Masuk: 500 pcs<br>
                                            • Defect: 10 pcs<br>
                                            • Retur ke Supplier: 30 pcs<br>
                                            <span class="text-success"><strong>= 500 − 10 − 30 = 460 pcs</strong></span><br>
                                            <small class="text-info">Replacement (jika ada) dicatat di purchase baru.</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h6 class="text-danger">⚠️ Hal yang Perlu Diperhatikan:</h6>
                                        <div class="bg-white p-3 rounded border">
                                            <ul class="mb-0">
                                                <li><strong>Defect/Retur tidak boleh melebihi Qty Masuk</strong></li>
                                                <li><strong>Total Stok Masuk tidak boleh negatif</strong> (hasil minimal 0)</li>
                                                <li><strong>Rate Defect/Retur &gt; 50% akan diberi peringatan</strong></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

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
                                    <label for="barang_diretur_ke_supplier" class="form-label">Retur ke Supplier</label>
                                    <input type="number" class="form-control" id="barang_diretur_ke_supplier"
                                        name="barang_diretur_ke_supplier" min="0" value="0">
                                    <div class="invalid-feedback" id="error-barang_diretur_ke_supplier"></div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info" id="calculation_info">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <strong>Total Stok Masuk:</strong> <span id="total_stok_display">0</span> <span id="satuan_display"></span>
                                    <br><small>Formula: <span id="calculation_formula">Qty Masuk − Defect − Retur ke Supplier</span></small>
                                    <div id="calculation_rates" class="mt-2" style="display: none;">
                                        <small>
                                            <span class="badge bg-secondary me-2">Defect Rate: <span id="defect_rate">0%</span></span>
                                            <span class="badge bg-secondary">Return Rate: <span id="return_rate">0%</span></span>
                                        </small>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="showQuickGuide()" title="Lihat Panduan Cepat">
                                    <i class="fas fa-lightbulb"></i>
                                </button>
                            </div>
                            
                            <!-- Quick Tip Display -->
                            <div id="quick_tip" class="mt-2" style="display: none;">
                                <div class="bg-white p-2 rounded border">
                                    <small class="text-muted">
                                        <strong>💡 Tips:</strong> <span id="tip_content">Masukkan data secara berurutan untuk hasil yang akurat</span>
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Calculation Validation Alerts -->
                        <div id="calculation_alerts"></div>
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

            function initItemChoices() {
                const itemSelect = document.getElementById('bahan_baku_id');
                if (itemSelect && !itemSelect.disabled) {
                    if (window.itemChoices) {
                        try { window.itemChoices.destroy(); window.itemChoices = null; } catch (e) {}
                    }
                    window.itemChoices = new Choices(itemSelect, {
                        searchEnabled: true,
                        searchPlaceholderValue: "Cari item berdasarkan SKU atau nama...",
                        itemSelectText: '',
                        placeholder: true,
                        placeholderValue: "-- Pilih Item --",
                        removeItemButton: false,
                        allowHTML: false,
                        classNames: { containerOuter: 'choices' }
                    });
                }
            }

            // Flash messages
            @if (session('success'))
                Swal.fire({ title: 'Berhasil!', text: '{{ session('success') }}', icon: 'success', timer: 2000, showConfirmButton: false, toast: true, position: 'top-end' });
            @endif
            @if (session('error'))
                Swal.fire({ title: 'Error!', text: '{{ session('error') }}', icon: 'error', confirmButtonText: 'OK' });
            @endif

            function debounce(func, wait) {
                let timeout;
                return function() { const context = this, args = arguments; clearTimeout(timeout); timeout = setTimeout(() => func.apply(context, args), wait); };
            }

            function adjustForMobile() {
                if (window.innerWidth < 768) {
                    $('.dt-responsive table').addClass('table-sm');
                    if (!$('#filterControls').hasClass('show')) { $('#filterControls').collapse('hide'); }
                } else {
                    $('.dt-responsive table').removeClass('table-sm');
                    $('#filterControls').addClass('show');
                }
            }
            adjustForMobile(); $(window).resize(function(){ adjustForMobile(); });

            // DataTable
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
                            console.error('DataTables Ajax Error:', xhr.responseText, error, code, xhr.status);
                            Swal.fire({ title: 'Error', text: 'Gagal memuat data. Silakan refresh halaman.', icon: 'error', confirmButtonText: 'OK' });
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'kategori_display', name: 'kategori_display', orderable: false, searchable: false },
                        { data: 'item_name', name: 'item_name' },
                        { data: 'item_sku', name: 'item_sku' },
                        { data: 'satuan', name: 'satuan', orderable: false, searchable: false },
                        { data: 'qty_pembelian', name: 'qty_pembelian' },
                        { data: 'tanggal_kedatangan_barang', name: 'tanggal_kedatangan_barang' },
                        { data: 'qty_barang_masuk', name: 'qty_barang_masuk' },
                        { data: 'barang_defect_tanpa_retur', name: 'barang_defect_tanpa_retur' },
                        { data: 'barang_diretur_ke_supplier', name: 'barang_diretur_ke_supplier' },
                        { data: 'total_stok_masuk', name: 'total_stok_masuk' },
                        { data: 'checker_penerima_barang', name: 'checker_penerima_barang' },
                        {
                            data: 'action', name: 'action', orderable: false, searchable: false,
                            render: function(data) {
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
                                    </div>`;
                            }
                        }
                    ],
                    order: [[6, 'desc']],
                    pageLength: 25,
                    responsive: true,
                    dom: 'Bfrtip',
                    buttons: [
                        { extend: 'copy',  text: '<i class="fas fa-copy"></i> Salin', className: 'btn btn-secondary' },
                        { extend: 'excel', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn btn-success' },
                        { extend: 'print', text: '<i class="fas fa-print"></i> Cetak', className: 'btn btn-info' }
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
                        paginate: { first: "Pertama", previous: "Sebelumnya", next: "Selanjutnya", last: "Terakhir" }
                    }
                });
                window.purchaseTable = table;

                $('#filter_kategori').on('change', function(){ table.draw(); });
                $('#filter_item').on('change', function(){ table.draw(); });
                $('#filter_start_date, #filter_end_date').on('change', function(){ table.draw(); });

                // realtime calc
                $('#qty_pembelian, #qty_barang_masuk, #barang_defect_tanpa_retur, #barang_diretur_ke_supplier').on('input',
                    debounce(function() {
                        calculateTotalStock();
                        validateCalculationsRealTime();
                        showContextualTips();
                    }, 500)
                );

                $(document).on('change', '#bahan_baku_id', function() {
                    var selectedOption = $(this).find('option:selected');
                    if (selectedOption.val()) { updateSatuanDisplay(selectedOption[0]); } else { $('#satuan_display').text(''); }
                });

                $(document).on('choice', '#bahan_baku_id', function() {
                    const selectedOption = $(this).find('option:selected')[0];
                    if (selectedOption) { updateSatuanDisplay(selectedOption); } else { $('#satuan_display').text(''); }
                });

                $('#purchaseForm').on('submit', function(e) {
                    e.preventDefault();
                    var formData = new FormData(this);
                    var purchaseId = $('#purchase_id').val();
                    var url = purchaseId ? `/purchase/${purchaseId}` : "{{ route('purchase.store') }}";
                    if (purchaseId) { formData.append('_method', 'PUT'); }

                    Swal.fire({ title: purchaseId ? 'Memperbarui...' : 'Menyimpan...', text: 'Sedang memproses data purchase', allowOutsideClick: false, showConfirmButton: false, willOpen: () => { Swal.showLoading(); } });

                    $.ajax({
                        url: url, method: 'POST', data: formData, processData: false, contentType: false,
                        beforeSend: function() { $('#saveBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...'); clearValidationErrors(); },
                        success: function(response) {
                            if (response.success) {
                                $('#purchaseModal').modal('hide');
                                if (window.purchaseTable) { window.purchaseTable.ajax.reload(null, false); }
                                Swal.fire({ title: 'Berhasil!', text: response.message, icon: 'success', timer: 1500, showConfirmButton: false, toast: true, position: 'top-end' });
                                resetForm();
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                const errors = xhr.responseJSON.errors; showValidationErrors(errors);
                                Swal.fire({ title: 'Mohon Periksa Input Anda', text: 'Ada beberapa kesalahan pada form yang Anda isi.', icon: 'warning', confirmButtonText: 'Saya Mengerti', confirmButtonColor: '#3085d6' });
                            } else {
                                Swal.fire({ title: 'Oops...', text: xhr.responseJSON?.message || 'Terjadi kesalahan pada permintaan.', icon: 'error', confirmButtonText: 'Coba Lagi', confirmButtonColor: '#3085d6' });
                            }
                        },
                        complete: function() { $('#saveBtn').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Simpan'); }
                    });
                });

            } catch (error) {
                console.error('Error initializing DataTable:', error);
                Swal.fire({ title: 'Error', text: 'Gagal menginisialisasi tabel data.', icon: 'error' });
            }

            $('#purchaseModal, #detailModal').on('hidden.bs.modal', function() {
                $(this).find('form')[0]?.reset();
                $(this).find('.is-invalid').removeClass('is-invalid');
                resetKategoriAndItem();
                $('.modal-backdrop').remove(); $('body').removeClass('modal-open'); $('body').css('padding-right', '');
            });

            $(document).on('change', '#kategori', function() { updateItemOptions(); });
        });

        // ===== Global functions

        window.showCreateModal = function() {
            try {
                if (!window.itemChoices) { window.itemChoices = null; }
                $('#purchaseModalLabel').text('Tambah Purchase Item');
                $('#purchaseModal').modal('show');
                setTimeout(() => { resetForm(); }, 100);
            } catch (e) {
                console.error('Error opening create modal:', e);
                Swal.fire({ title: 'Error', text: 'Gagal membuka form tambah purchase', icon: 'error' });
            }
        };

        window.resetKategoriAndItem = function(showNotification = false) {
            $('#kategori').val('');
            if (window.itemChoices) { try { window.itemChoices.destroy(); window.itemChoices = null; } catch (e) {} }
            const selectElement = document.getElementById('bahan_baku_id');
            if (selectElement) {
                selectElement.innerHTML = '<option value="">-- Pilih Kategori Terlebih Dahulu --</option>';
                selectElement.disabled = true; selectElement.value = '';
            }
            $('#satuan_display').text('');
            if (showNotification) {
                Swal.fire({ title: 'Reset Berhasil', text: 'Kategori dan item telah direset', icon: 'info', timer: 1500, showConfirmButton: false, toast: true, position: 'top-end' });
            }
        };

        window.updateItemFilter = function() {
            var selectedKategori = $('#filter_kategori').val();
            var itemSelect = $('#filter_item');
            itemSelect.find('option').show();
            if (selectedKategori) {
                itemSelect.find('option[data-kategori]').each(function() {
                    if ($(this).data('kategori') !== selectedKategori) { $(this).hide(); }
                });
            }
            if (itemSelect.find('option:selected').is(':hidden')) { itemSelect.val(''); }
        };

        window.updateItemOptions = function() {
            var selectedKategori = $('#kategori').val();
            const selectElement = document.getElementById('bahan_baku_id');

            if (window.itemChoices) { try { window.itemChoices.destroy(); window.itemChoices = null; } catch (e) {} }
            selectElement.innerHTML = '';

            if (!selectedKategori) {
                selectElement.innerHTML = '<option value="">-- Pilih Kategori Terlebih Dahulu --</option>';
                selectElement.disabled = true; hideItemDetail?.(); return;
            }

            selectElement.disabled = false;
            selectElement.innerHTML = '<option value="">-- Pilih Item --</option>';

            @foreach ($bahanBakus as $bahanBaku)
                @php $bkOpt = "[{$bahanBaku->sku_induk}] {$bahanBaku->nama_barang}"; @endphp
            @endforeach
            @foreach ($products as $product)
                @php $pdOpt = "[{$product->sku}] {$product->name_product}"; @endphp
            @endforeach

            if (selectedKategori === 'bahan_baku') {
                @foreach ($bahanBakus as $bahanBaku)
                    selectElement.innerHTML += `<option value="{{ $bahanBaku->id }}" 
                        data-satuan="{{ $bahanBaku->satuan }}" data-kategori="bahan_baku" 
                        data-sku="{{ $bahanBaku->sku_induk }}" data-name="{{ $bahanBaku->nama_barang }}">
                        [{{ $bahanBaku->sku_induk }}] {{ $bahanBaku->nama_barang }}
                    </option>`;
                @endforeach
            } else if (selectedKategori === 'finished_goods') {
                @foreach ($products as $product)
                    selectElement.innerHTML += `<option value="{{ $product->id }}" 
                        data-satuan="{{ $product->satuan ?? 'pcs' }}" data-kategori="finished_goods" 
                        data-sku="{{ $product->sku }}" data-name="{{ $product->name_product }}">
                        [{{ $product->sku }}] {{ $product->name_product }}
                    </option>`;
                @endforeach
            }

            if (!selectElement.disabled) {
                window.itemChoices = new Choices(selectElement, {
                    searchEnabled: true,
                    searchPlaceholderValue: "Cari item berdasarkan SKU atau nama...",
                    itemSelectText: '',
                    placeholder: true,
                    placeholderValue: "-- Pilih Item --",
                    removeItemButton: false,
                    allowHTML: false,
                    classNames: { containerOuter: 'choices' }
                });
            }
            $('#satuan_display').text('');
        };

        function updateSatuanDisplay(option) {
            if (option && option.dataset && option.dataset.satuan) { $('#satuan_display').text(option.dataset.satuan); }
            else { $('#satuan_display').text(''); }
        }

        window.editPurchase = function(id) {
            Swal.fire({ title: 'Memuat...', text: 'Mengambil data purchase', allowOutsideClick: false, showConfirmButton: false, willOpen: () => { Swal.showLoading(); } });
            $.get(`/purchase/${id}/edit`, function(response) {
                Swal.close();
                if (response.success) {
                    var data = response.data;
                    $('#purchase_id').val(data.id);
                    $('#kategori').val(data.kategori);
                    updateItemOptions();
                    setTimeout(() => {
                        if (window.itemChoices) { try { window.itemChoices.setChoiceByValue(data.bahan_baku_id.toString()); } catch (e) { $('#bahan_baku_id').val(data.bahan_baku_id); } }
                        else { $('#bahan_baku_id').val(data.bahan_baku_id); }
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
                    Swal.fire({ title: 'Error', text: response.message || 'Gagal mengambil data purchase', icon: 'error' });
                }
            }).fail(function(xhr) {
                Swal.fire({ title: 'Error', text: xhr.responseJSON?.message || 'Gagal memuat data purchase', icon: 'error' });
            });
        };

        window.deletePurchase = function(id) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: "Apakah Anda yakin ingin menghapus purchase ini? Tindakan ini tidak dapat dibatalkan.",
                icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Menghapus...', text: 'Sedang menghapus data purchase', allowOutsideClick: false, showConfirmButton: false, willOpen: () => { Swal.showLoading(); } });
                    $.ajax({
                        url: `/purchase/${id}`, method: 'DELETE',
                        data: { _token: $('meta[name="csrf-token"]').attr('content') },
                        success: function(response) {
                            if (response.success) {
                                if (window.purchaseTable) { window.purchaseTable.ajax.reload(null, false); }
                                Swal.fire({ title: 'Terhapus!', text: response.message, icon: 'success', timer: 1500, showConfirmButton: false, toast: true, position: 'top-end' });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({ title: 'Gagal Menghapus', text: xhr.responseJSON?.message || 'Tidak dapat menghapus purchase saat ini.', icon: 'error', confirmButtonText: 'OK', confirmButtonColor: '#3085d6' });
                        }
                    });
                }
            });
        };

        window.showDetail = function(id) {
            Swal.fire({ title: 'Memuat...', text: 'Mengambil detail purchase', allowOutsideClick: false, showConfirmButton: false, willOpen: () => { Swal.showLoading(); } });
            $.get(`/purchase/${id}`, function(response) {
                Swal.close(); $('#detailContent').html(response); $('#detailModal').modal('show');
            }).fail(function() { Swal.fire({ title: 'Error', text: 'Gagal memuat detail purchase', icon: 'error' }); });
        };

        window.applyFilters = function() { if (window.purchaseTable) { window.purchaseTable.ajax.reload(); } };
        window.clearFilters = function() {
            $('#filter_kategori').val(''); $('#filter_item').val(''); $('#filter_start_date').val(''); $('#filter_end_date').val('');
            updateItemFilter(); if (window.purchaseTable) { window.purchaseTable.ajax.reload(); }
        };

        function calculateTotalStock() {
            var masuk = parseInt($('#qty_barang_masuk').val()) || 0;
            var defect = parseInt($('#barang_defect_tanpa_retur').val()) || 0;
            var retur = parseInt($('#barang_diretur_ke_supplier').val()) || 0;
            var total = masuk - defect - retur;

            if (total < 0) total = 0;

            $('#total_stok_display').text(total);
            $('#calculation_formula').text(masuk + ' − ' + defect + ' − ' + retur + ' = ' + total);

            if (masuk > 0) {
                var defectRate = ((defect / masuk) * 100).toFixed(1);
                var returnRate = ((retur / masuk) * 100).toFixed(1);
                
                $('#defect_rate').text(defectRate + '%');
                $('#return_rate').text(returnRate + '%');
                $('#calculation_rates').show();

                var defectBadge = $('#calculation_rates .badge:first');
                var returnBadge = $('#calculation_rates .badge:last');

                defectBadge.removeClass('bg-success bg-warning bg-danger').addClass('bg-secondary');
                returnBadge.removeClass('bg-success bg-warning bg-danger').addClass('bg-secondary');

                if (defectRate <= 5) { defectBadge.removeClass('bg-secondary').addClass('bg-success'); }
                else if (defectRate <= 15) { defectBadge.removeClass('bg-secondary').addClass('bg-warning'); }
                else { defectBadge.removeClass('bg-secondary').addClass('bg-danger'); }

                if (returnRate <= 5) { returnBadge.removeClass('bg-secondary').addClass('bg-success'); }
                else if (returnRate <= 15) { returnBadge.removeClass('bg-secondary').addClass('bg-warning'); }
                else { returnBadge.removeClass('bg-secondary').addClass('bg-danger'); }
            } else {
                $('#calculation_rates').hide();
            }
        }
        
        function validateCalculationsRealTime() {
            var qtyPembelian = parseInt($('#qty_pembelian').val()) || 0;
            var qtyMasuk = parseInt($('#qty_barang_masuk').val()) || 0;
            var defect = parseInt($('#barang_defect_tanpa_retur').val()) || 0;
            var retur = parseInt($('#barang_diretur_ke_supplier').val()) || 0;

            // NOTE: server-side endpoint masih pakai validasi lama di proyek kamu.
            // Setelah kamu update controller ke rumus baru, ini akan match 100%.
            if (qtyPembelian === 0 && qtyMasuk === 0 && defect === 0 && retur === 0) {
                clearCalculationAlerts(); return;
            }
            
            $.ajax({
                url: "{{ route('purchase.validate-calculations') }}",
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    qty_pembelian: qtyPembelian,
                    qty_barang_masuk: qtyMasuk,
                    barang_defect_tanpa_retur: defect,
                    barang_diretur_ke_supplier: retur
                },
                success: function(response) {
                    if (response.success) { showCalculationSuccess({ calculation_formula: $('#calculation_formula').text() }); }
                },
                error: function(xhr) {
                    if (xhr.status === 422) { showCalculationErrors(xhr.responseJSON.errors); }
                }
            });
        }
        
        function showCalculationSuccess(data) {
            clearCalculationAlerts();
            var alertHtml = `
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Kalkulasi Valid!</strong> 
                    <br><small>Formula: ${data.calculation_formula}</small>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>`;
            $('#calculation_alerts').html(alertHtml);
            setTimeout(function(){ $('#calculation_alerts .alert-success').fadeOut(); }, 3000);
        }
        
        function showCalculationErrors(errors) {
            clearCalculationAlerts();
            var alertsHtml = '';
            $.each(errors, function(field, messages) {
                $.each(messages, function(_, message) {
                    var alertType = message.toLowerCase().includes('tingkat') || message.toLowerCase().includes('rate') ? 'warning' : 'danger';
                    var icon = alertType === 'warning' ? 'fas fa-exclamation-circle' : 'fas fa-exclamation-triangle';
                    alertsHtml += `
                        <div class="alert alert-${alertType} alert-dismissible fade show" role="alert">
                            <i class="${icon} me-2"></i>
                            <strong>Kesalahan Kalkulasi:</strong> ${message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>`;
                });
            });
            $('#calculation_alerts').html(alertsHtml);
        }
        function clearCalculationAlerts() { $('#calculation_alerts').empty(); }
        
        function showQuickGuide() {
            var qtyPembelian = parseInt($('#qty_pembelian').val()) || 0;
            var qtyMasuk = parseInt($('#qty_barang_masuk').val()) || 0;
            var defect = parseInt($('#barang_defect_tanpa_retur').val()) || 0;
            var retur = parseInt($('#barang_diretur_ke_supplier').val()) || 0;
            
            var tipContent = '';
            var tipClass = 'text-info';
            
            if (qtyPembelian === 0) {
                tipContent = 'Mulai dengan mengisi Quantity Pembelian terlebih dahulu';
            } else if (qtyMasuk === 0) {
                tipContent = 'Isi Qty Barang Masuk (yang benar-benar diterima dari supplier)';
            } else if (defect > 0 && retur > 0) {
                tipContent = 'Ada defect & retur. Ingat: total stok masuk = masuk − defect − retur.';
                tipClass = 'text-warning';
            } else if (defect > 0) {
                tipContent = 'Barang defect mengurangi stok (tetap di kita, tapi tidak bisa dipakai/jual).';
                tipClass = 'text-warning';
            } else if (retur > 0) {
                tipContent = 'Retur ke supplier mengurangi stok (barang dikirim balik). Replacement dicatat sebagai purchase baru.';
                tipClass = 'text-info';
            } else if (qtyMasuk > 0) {
                tipContent = 'Jika tidak ada defect/retur, total stok masuk = qty masuk.';
                tipClass = 'text-success';
            } else {
                tipContent = 'Masukkan data berurutan: Qty Pembelian → Qty Masuk → Defect → Retur ke Supplier';
            }
            
            $('#tip_content').text(tipContent).removeClass('text-info text-warning text-success text-danger').addClass(tipClass);
            if ($('#quick_tip').is(':visible')) { $('#quick_tip').slideUp(); } else { $('#quick_tip').slideDown(); }
        }
        
        function showContextualTips() {
            var qtyMasuk = parseInt($('#qty_barang_masuk').val()) || 0;
            var defect = parseInt($('#barang_defect_tanpa_retur').val()) || 0;
            var retur = parseInt($('#barang_diretur_ke_supplier').val()) || 0;
            if (qtyMasuk > 0 && (defect > qtyMasuk * 0.3 || retur > qtyMasuk * 0.3)) {
                if (!$('#quick_tip').is(':visible')) { showQuickGuide(); }
            }
        }

        function resetForm() {
            $('#purchaseForm')[0].reset();
            $('#purchase_id').val('');
            $('#total_stok_display').text('0');
            $('#satuan_display').text('');
            $('#calculation_formula').text('Qty Masuk − Defect − Retur ke Supplier');
            $('#calculation_rates').hide();
            clearValidationErrors();
            clearCalculationAlerts();

            $('#kategori').val('');
            if (window.itemChoices) { try { window.itemChoices.destroy(); window.itemChoices = null; } catch (e) {} }
            const selectElement = document.getElementById('bahan_baku_id');
            if (selectElement) {
                selectElement.innerHTML = '<option value="">-- Pilih Kategori Terlebih Dahulu --</option>';
                selectElement.disabled = true; selectElement.value = '';
            }
            $('#satuan_display').text('');
        }

        function showValidationErrors(errors) {
            $.each(errors, function(field, messages) {
                const input = $(`[name="${field}"]`);
                input.addClass('is-invalid');
                $(`#error-${field}`).text(messages[0]);
            });
        }
        function clearValidationErrors() { $('.is-invalid').removeClass('is-invalid'); $('.invalid-feedback').text(''); }
    </script>
@endsection
