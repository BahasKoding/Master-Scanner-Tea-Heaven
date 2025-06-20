@extends('layouts.main')

@section('title', 'Laporan Catatan Produksi')
@section('breadcrumb-item', 'Laporan Catatan Produksi')

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
            background: #6c757d;
            border-radius: 8px;
            padding: 20px;
            color: white;
            margin-bottom: 15px;
            border: 1px solid #dee2e6;
        }

        .summary-card.success {
            background: #198754;
        }

        .summary-card.warning {
            background: #fd7e14;
        }

        .summary-card.info {
            background: #0dcaf0;
            color: #212529;
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
                                <i class="fas fa-industry"></i>
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <div class="summary-value" id="total-productions">0</div>
                                <div class="summary-label">Total Produksi</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="summary-card success">
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
                    <div class="summary-card warning">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon">
                                <i class="fas fa-cubes"></i>
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <div class="summary-value" id="total-products">0</div>
                                <div class="summary-label">Unique Products</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="summary-card info">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon">
                                <i class="fas fa-layer-group"></i>
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <div class="summary-value" id="total-materials">0</div>
                                <div class="summary-label">Materials Used</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Summary Cards end -->

        <!-- Catatan Produksi Report Table start -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-6 mb-2 mb-md-0">
                            <h5 class="mb-0">Laporan Catatan Produksi</h5>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-md-end">
                                <button id="clear-filters" class="btn btn-secondary btn-sm">
                                    Clear Filters
                                </button>
                                <button id="export-excel" class="btn btn-success btn-sm">
                                    Export Excel
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
                                <label for="product-filter" class="form-label">Produk</label>
                                <select class="form-select" id="product-filter">
                                    <option value="">Semua Produk</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">
                                            {{ $product->sku }} - {{ $product->name_product }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 col-sm-6 col-12 mb-3">
                                <label for="packaging-filter" class="form-label">Packaging</label>
                                <input type="text" class="form-control" id="packaging-filter"
                                    placeholder="Filter berdasarkan packaging">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button id="apply-filters" class="btn btn-primary">
                                    Apply Filters
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- End Filter Section -->

                    <div class="mb-3">
                        <h6 class="text-primary" id="table-period">
                            Menampilkan semua data catatan produksi
                        </h6>
                    </div>

                    <div class="dt-responsive table-responsive">
                        <table id="produksi-report-table" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>SKU Produk</th>
                                    <th>Nama Produk</th>
                                    <th>Packaging</th>
                                    <th>Quantity</th>
                                    <th>Bahan Baku</th>
                                    <th>Gramasi</th>
                                    <th>Total Terpakai</th>
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
        <!-- Catatan Produksi Report Table end -->
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
            var table = $('#produksi-report-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('reports.catatan-produksi.index') }}",
                    type: "GET",
                    data: function(d) {
                        d.start_date = $('#start-date').val();
                        d.end_date = $('#end-date').val();
                        d.product_id = $('#product-filter').val();
                        d.packaging = $('#packaging-filter').val();
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
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'product_sku',
                        name: 'product_sku'
                    },
                    {
                        data: 'product_name',
                        name: 'product_name'
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
                    }
                ],
                order: [
                    [1, 'desc']
                ],
                pageLength: 25,
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'copy',
                        text: 'Copy',
                        className: 'btn btn-secondary'
                    },
                    {
                        extend: 'excel',
                        text: 'Excel',
                        className: 'btn btn-success'
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        className: 'btn btn-info'
                    }
                ],
                language: {
                    processing: '<div class="d-flex justify-content-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>',
                    emptyTable: 'Tidak ada data catatan produksi',
                    zeroRecords: 'Tidak ditemukan data catatan produksi yang sesuai',
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
                $('#product-filter').val('');
                $('#packaging-filter').val('');
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

                let periodText = 'Menampilkan semua data catatan produksi';

                if (startDate || endDate) {
                    const formattedStartDate = startDate ? new Date(startDate).toLocaleDateString('id-ID') : 'Awal';
                    const formattedEndDate = endDate ? new Date(endDate).toLocaleDateString('id-ID') : 'Sekarang';

                    if (startDate && endDate && startDate === endDate) {
                        periodText = `Data catatan produksi untuk tanggal: ${formattedStartDate}`;
                    } else if (startDate && endDate) {
                        periodText = `Data catatan produksi periode: ${formattedStartDate} - ${formattedEndDate}`;
                    } else if (startDate) {
                        periodText = `Data catatan produksi dari tanggal: ${formattedStartDate}`;
                    } else if (endDate) {
                        periodText = `Data catatan produksi sampai tanggal: ${formattedEndDate}`;
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

                let totalProductions = table.page.info().recordsTotal;
                let totalQuantity = 0;
                let uniqueProducts = new Set();
                let totalMaterials = 0;

                // Calculate from current visible data
                tableData.each(function(row) {
                    totalQuantity += parseInt(row.quantity) || 0;
                    if (row.product_sku) {
                        uniqueProducts.add(row.product_sku);
                    }
                    // Count materials (assuming sku_induk contains comma-separated materials)
                    if (row.sku_induk) {
                        const materials = row.sku_induk.split(',').length;
                        totalMaterials += materials;
                    }
                });

                // Update summary cards
                $('#total-productions').text(totalProductions.toLocaleString('id-ID'));
                $('#total-quantity').text(totalQuantity.toLocaleString('id-ID'));
                $('#total-products').text(uniqueProducts.size);
                $('#total-materials').text(totalMaterials.toLocaleString('id-ID'));
            }

            // Initialize table period display
            updateTablePeriod();
        });
    </script>
@endsection
