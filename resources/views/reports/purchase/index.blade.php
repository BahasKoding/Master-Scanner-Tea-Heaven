@extends('layouts.main')

@section('title', 'Laporan Purchase')
@section('breadcrumb-item', 'Laporan Purchase')

@section('css')
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- [Page specific CSS] start -->
    <!-- data tables css -->
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/datatables/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/datatables/buttons.bootstrap5.min.css') }}">
    <!-- [Page specific CSS] end -->
    <style>
        .filter-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .summary-cards {
            margin-bottom: 20px;
        }

        .summary-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            padding: 20px;
            color: white;
            margin-bottom: 15px;
        }

        .summary-card.success {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .summary-card.warning {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }

        .summary-card .summary-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }

        .summary-card .summary-value {
            font-size: 1.8rem;
            font-weight: bold;
            margin: 10px 0 5px 0;
        }

        .summary-card .summary-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        @media (max-width: 768px) {
            .filter-section {
                padding: 15px 10px;
            }

            .summary-card {
                text-align: center;
                padding: 15px;
            }

            .summary-card .summary-value {
                font-size: 1.4rem;
            }
        }
    </style>
@endsection

@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">
        <!-- Summary Cards start -->
        <div class="col-12 summary-cards">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <div class="summary-value" id="total-purchases">0</div>
                                <div class="summary-label">Total Purchases</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="summary-card success">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <div class="summary-value" id="total-amount">Rp 0</div>
                                <div class="summary-label">Total Amount</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="summary-card warning">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon">
                                <i class="fas fa-boxes"></i>
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <div class="summary-value" id="total-quantity">0</div>
                                <div class="summary-label">Total Quantity</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon">
                                <i class="fas fa-truck"></i>
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <div class="summary-value" id="total-suppliers">0</div>
                                <div class="summary-label">Total Suppliers</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Summary Cards end -->

        <!-- Purchase Report Table start -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-6 mb-2 mb-md-0">
                            <h5 class="mb-0">Laporan Purchase</h5>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-md-end">
                                <button id="clear-filters" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-filter"></i>
                                    <span class="d-none d-sm-inline">Clear Filters</span>
                                </button>
                                <button id="export-excel" class="btn btn-success btn-sm">
                                    <i class="fas fa-file-excel"></i>
                                    <span class="d-none d-sm-inline">Export Excel</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="filter-section">
                        <div class="row">
                            <div class="col-md-3 col-sm-6 col-12 mb-3">
                                <label for="start-date" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="start-date" name="start_date">
                            </div>
                            <div class="col-md-3 col-sm-6 col-12 mb-3">
                                <label for="end-date" class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control" id="end-date" name="end_date">
                            </div>
                            <div class="col-md-3 col-sm-6 col-12 mb-3">
                                <label for="kategori-filter" class="form-label">Kategori</label>
                                <select class="form-select" id="kategori-filter">
                                    <option value="">Semua Kategori</option>
                                    <option value="bahan_baku">Bahan Baku</option>
                                    <option value="finished_goods">Finished Goods</option>
                                </select>
                            </div>
                            <div class="col-md-3 col-sm-6 col-12 mb-3">
                                <label for="item-filter" class="form-label">Item</label>
                                <select class="form-select" id="item-filter">
                                    <option value="">Semua Item</option>
                                    <optgroup label="Bahan Baku">
                                        @foreach ($bahanBaku as $bahan)
                                            <option value="{{ $bahan->id }}" data-kategori="bahan_baku">
                                                {{ $bahan->sku_induk }} - {{ $bahan->nama_barang }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Finished Goods">
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" data-kategori="finished_goods">
                                                {{ $product->sku }} - {{ $product->name_product }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button id="apply-filters" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Apply Filters
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- End Filter Section -->

                    <div class="mb-3">
                        <h6 class="text-primary" id="table-period">
                            Menampilkan semua data purchase
                        </h6>
                    </div>

                    <div class="dt-responsive table-responsive">
                        <table id="purchase-report-table" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Kedatangan</th>
                                    <th>Kategori</th>
                                    <th>SKU Item</th>
                                    <th>Nama Item</th>
                                    <th>Qty Pembelian</th>
                                    <th>Qty Masuk</th>
                                    <th>Defect</th>
                                    <th>Total Masuk</th>
                                    <th>Penerima</th>
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
        <!-- Purchase Report Table end -->
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

    <script type="text/javascript">
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#purchase-report-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('reports.purchase.index') }}",
                    type: "GET",
                    data: function(d) {
                        d.start_date = $('#start-date').val();
                        d.end_date = $('#end-date').val();
                        d.kategori = $('#kategori-filter').val();
                        d.bahan_baku_id = $('#item-filter').val();
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
                        data: 'tanggal_kedatangan_barang',
                        name: 'tanggal_kedatangan_barang'
                    },
                    {
                        data: 'kategori',
                        name: 'kategori'
                    },
                    {
                        data: 'item_sku',
                        name: 'item_sku'
                    },
                    {
                        data: 'item_name',
                        name: 'item_name'
                    },
                    {
                        data: 'qty_pembelian',
                        name: 'qty_pembelian'
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
                        data: 'total_stok_masuk',
                        name: 'total_stok_masuk'
                    },
                    {
                        data: 'checker_penerima_barang',
                        name: 'checker_penerima_barang'
                    }
                ],
                order: [
                    [1, 'desc']
                ],
                pageLength: 25,
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'copy',
                        text: '<i class="fas fa-copy"></i> Copy',
                        className: 'btn btn-secondary'
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-success'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Print',
                        className: 'btn btn-info'
                    }
                ],
                language: {
                    processing: '<div class="d-flex justify-content-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>',
                    emptyTable: 'Tidak ada data purchase',
                    zeroRecords: 'Tidak ditemukan data purchase yang sesuai',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                    infoEmpty: 'Menampilkan 0 sampai 0 dari 0 data',
                    infoFiltered: '(difilter dari _MAX_ total data)',
                    search: 'Cari:',
                    paginate: {
                        first: 'Pertama',
                        last: 'Terakhir',
                        next: 'Selanjutnya',
                        previous: 'Sebelumnya'
                    }
                },
                drawCallback: function(settings) {
                    // Update summary cards after table is drawn
                    updateSummaryCards();
                }
            });

            // Apply filters
            $('#apply-filters').on('click', function() {
                updateTablePeriod();
                table.ajax.reload();
            });

            // Clear filters
            $('#clear-filters').on('click', function() {
                $('#start-date').val('');
                $('#end-date').val('');
                $('#kategori-filter').val('');
                $('#item-filter').val('');
                updateTablePeriod();
                table.ajax.reload();
            });

            // Export Excel
            $('#export-excel').on('click', function() {
                // Implementation for custom export if needed
                table.button('.buttons-excel').trigger();
            });

            // Function to update table period display
            function updateTablePeriod() {
                const startDate = $('#start-date').val();
                const endDate = $('#end-date').val();

                let periodText = 'Menampilkan semua data purchase';

                if (startDate || endDate) {
                    const formattedStartDate = startDate ? new Date(startDate).toLocaleDateString('id-ID') : 'Awal';
                    const formattedEndDate = endDate ? new Date(endDate).toLocaleDateString('id-ID') : 'Sekarang';

                    if (startDate && endDate && startDate === endDate) {
                        periodText = `Data purchase untuk tanggal: ${formattedStartDate}`;
                    } else if (startDate && endDate) {
                        periodText = `Data purchase periode: ${formattedStartDate} - ${formattedEndDate}`;
                    } else if (startDate) {
                        periodText = `Data purchase dari tanggal: ${formattedStartDate}`;
                    } else if (endDate) {
                        periodText = `Data purchase sampai tanggal: ${formattedEndDate}`;
                    }
                }

                $('#table-period').text(periodText);
            }

            // Function to update summary cards
            function updateSummaryCards() {
                // Get current table data
                const tableData = table.rows({
                    page: 'current'
                }).data();

                let totalPurchases = table.page.info().recordsTotal;
                let totalAmount = 0;
                let totalQuantity = 0;
                let suppliers = new Set();

                // If we have filtered data, calculate from all filtered data
                tableData.each(function(row) {
                    // Parse quantities (remove formatting)
                    const qtyPembelian = parseInt(row.qty_pembelian.replace(/[^0-9]/g, '')) || 0;
                    const totalMasuk = parseInt(row.total_stok_masuk.replace(/[^0-9]/g, '')) || 0;
                    totalQuantity += qtyPembelian;
                    totalAmount += totalMasuk; // Using total_stok_masuk as amount
                    if (row.checker_penerima_barang) {
                        suppliers.add(row.checker_penerima_barang);
                    }
                });

                // Update summary cards
                $('#total-purchases').text(totalPurchases.toLocaleString('id-ID'));
                $('#total-amount').text('Rp ' + totalAmount.toLocaleString('id-ID'));
                $('#total-quantity').text(totalQuantity.toLocaleString('id-ID'));
                $('#total-suppliers').text(suppliers.size);
            }

            // Initialize table period display
            updateTablePeriod();
        });
    </script>
@endsection
