@extends('layouts.main')

@section('title', 'Manajemen Finished Goods')
@section('breadcrumb-item', 'Finished Goods')

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
        .is-saving { 
            opacity: .7; 
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
                            {{-- dinonaktifkan sementara --}}

                            {{-- <button id="update-all" class="btn btn-success me-2 mb-2 mb-sm-0">
                                <i class="fas fa-save"></i> Update All
                            </button>
                            <button id="sync-all" class="btn btn-primary me-2 mb-2 mb-sm-0">
                                <i class="fas fa-sync"></i> Sync All Data
                            </button> --}}

                            {{-- end dinonaktifkan sementara --}}
                            <button id="clear-filters" class="btn btn btn-secondary me-2 mb-2 mb-sm-0">
                                <i class="fas fa-filter"></i> Hapus Filter
                            </button>
                        </div>
                    </div>
                    <!-- Progress bar for sync/update operations - hidden by default -->
                    <div id="sync-progress-container" class="mt-3 d-none">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span id="sync-status">Processing...</span>
                            <span id="sync-percentage">0%</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div id="sync-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" 
                                 role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Proses chunking 50 data per batch untuk menghindari timeout pada dataset besar
                        </small>
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
                                <div class="col-lg-3 col-md-6 col-sm-12 mb-2">
                                    <label for="filter-month-year" class="form-label small filter-label">
                                        <i class="fas fa-calendar-alt"></i> Filter Bulan/Tahun
                                    </label>
                                    <input type="month" class="form-control form-control-sm" 
                                           name="filter-month-year" id="filter-month-year" 
                                           value="{{ date('Y-m') }}" 
                                           title="Filter stok berdasarkan bulan dan tahun">
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12 mb-2">
                                    <label for="simulation-date" class="form-label small filter-label">
                                        <i class="fas fa-flask text-warning"></i> Simulasi Tanggal Sistem
                                    </label>
                                    <div class="input-group">
                                        <input type="date" class="form-control form-control-sm" 
                                               name="simulation-date" id="simulation-date" 
                                               value="{{ date('Y-m-d') }}" 
                                               title="Simulasi tanggal sistem untuk testing">
                                        <button class="btn btn-outline-warning btn-sm" type="button" id="apply-simulation" title="Terapkan Simulasi">
                                            <i class="fas fa-play"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary btn-sm" type="button" id="reset-simulation" title="Reset ke Tanggal Hari Ini">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-12">
                                    <div class="alert alert-info py-2 mb-0" role="alert">
                                        <small>
                                            <i class="fas fa-info-circle"></i> 
                                            <strong>Filter Bulanan:</strong> Stok masuk/keluar akan dihitung berdasarkan transaksi pada bulan yang dipilih. 
                                            Default: bulan berjalan (<strong>{{ date('F Y') }}</strong>)
                                            <br>
                                            Filter yang sedang berjalan saat ini: <strong id="current-filter-display">{{ date('F Y') }}</strong>
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2" id="simulation-info" style="display: none;">
                                <div class="col-12">
                                    <div class="alert alert-warning py-2 mb-0" role="alert">
                                        <small>
                                            <i class="fas fa-flask"></i> 
                                            <strong>MODE SIMULASI AKTIF:</strong> Sistem menggunakan tanggal simulasi: <strong id="simulation-date-display">{{ date('d F Y') }}</strong>
                                            <br>
                                            <i class="fas fa-exclamation-triangle"></i> Data yang ditampilkan adalah simulasi berdasarkan tanggal yang dipilih, bukan data real-time.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Filter Section -->

                    <!-- Info Box -->
                    <div class="mb-3">
                        <button class="btn btn-sm btn-outline-info w-100 mb-2" 
                                type="button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#infoInputData" 
                                aria-expanded="false" 
                                aria-controls="infoInputData"
                                id="info-toggle-btn">
                            <i class="fas fa-info-circle"></i> <span id="info-toggle-text">Tampilkan Informasi Input Data</span> 
                            <i class="fas fa-chevron-down" id="info-chevron"></i>
                        </button>

                        <div class="collapse" id="infoInputData">
                            <div class="alert alert-info mb-0" role="alert">
                            <h6 class="alert-heading"><i class="fas fa-info-circle"></i> Informasi Input Data</h6>
                            <div class="row">
                                <div class="col-md-6">
                                <p class="mb-1"><strong><i class="fas fa-edit text-primary"></i> Manual Input:</strong></p>
                                <ul class="mb-2">
                                    <li><strong>Stok Awal:</strong> Input manual oleh user</li>
                                    <li><strong>Defective:</strong> Input manual oleh user</li>
                                </ul>
                                </div>
                                <div class="col-md-6">
                                <p class="mb-1"><strong><i class="fas fa-cog text-secondary"></i> Otomatis:</strong></p>
                                <ul class="mb-2">
                                    <li><strong>Stok Masuk:</strong> Dari Catatan Produksi + Purchase Finished Goods</li>
                                    <li><strong>Stok Keluar:</strong> Dari History Sales Scanner</li>
                                    <li><strong>Stok Sisa:</strong> Dari hasil Stock Opname</li>
                                    <li><strong>Live Stock:</strong> Kalkulasi otomatis</li>
                                </ul>
                                </div>
                            </div>
                            <small class="text-muted">💡 Field dengan latar abu-abu tidak dapat diedit karena nilainya dihitung otomatis dari sistem</small>
                            </div>
                        </div>
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
                                    <th>Stok Sisa</th>
                                    <th>Live Stock</th>
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
    {{-- <script src="{{ URL::asset('build/js/plugins/bootstrap.min.js') }}"></script> --}}
    <script src="{{ URL::asset('build/js/plugins/bootstrap.bundle.min.js') }}"></script>


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
        // Global simulation state
        let simulationMode = false;
        let simulationDate = null;

        // Function to update current filter display dynamically using DOM manipulation
        function updateCurrentFilterDisplay(monthYearValue) {
            try {
                if (!monthYearValue) {
                    monthYearValue = '{{ date("Y-m") }}'; // Default to current month
                }
                
                // Convert YYYY-MM format to readable month name
                const date = new Date(monthYearValue + '-01');
                const monthNames = [
                    'January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'
                ];
                
                const monthName = monthNames[date.getMonth()];
                const year = date.getFullYear();
                const displayText = `${monthName} ${year}`;
                
                // Update the DOM element with animation effect
                const displayElement = $('#current-filter-display');
                if (displayElement.length > 0) {
                    // Add fade effect for smooth transition
                    displayElement.fadeOut(200, function() {
                        $(this).text(displayText).fadeIn(200);
                    });
                    
                    // Add temporary highlight effect
                    displayElement.addClass('text-primary');
                    setTimeout(() => {
                        displayElement.removeClass('text-primary');
                    }, 1000);
                    
                    console.log('Updated filter display to:', displayText);
                } else {
                    console.warn('Current filter display element not found');
                }
            } catch (error) {
                console.error('Error updating current filter display:', error);
                // Fallback: just update with the raw value
                $('#current-filter-display').text(monthYearValue || 'Current Month');
            }
        }

        // Function to update simulation date display
        function updateSimulationDisplay(dateValue) {
            try {
                if (!dateValue) return;
                
                const date = new Date(dateValue);
                const monthNames = [
                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                ];
                
                const day = date.getDate();
                const monthName = monthNames[date.getMonth()];
                const year = date.getFullYear();
                const displayText = `${day} ${monthName} ${year}`;
                
                $('#simulation-date-display').text(displayText);
                console.log('Updated simulation display to:', displayText);
            } catch (error) {
                console.error('Error updating simulation display:', error);
                $('#simulation-date-display').text(dateValue || 'Invalid Date');
            }
        }

        // Function to toggle simulation mode
        function toggleSimulationMode(enable, dateValue = null) {
            simulationMode = enable;
            simulationDate = dateValue;
            
            const simulationInfo = $('#simulation-info');
            
            if (enable && dateValue) {
                updateSimulationDisplay(dateValue);
                simulationInfo.fadeIn(300);
                
                // Update filter to match simulation month
                const simDate = new Date(dateValue);
                const simMonthYear = simDate.getFullYear() + '-' + String(simDate.getMonth() + 1).padStart(2, '0');
                $('#filter-month-year').val(simMonthYear);
                updateCurrentFilterDisplay(simMonthYear);
                
                console.log('Simulation mode enabled for date:', dateValue);
            } else {
                simulationInfo.fadeOut(300);
                console.log('Simulation mode disabled');
            }
        }

        $(document).ready(function() {
            // Initialize the current filter display on page load
            const initialMonthValue = $('#filter-month-year').val() || '{{ date("Y-m") }}';
            updateCurrentFilterDisplay(initialMonthValue);
            
            // Handle info toggle button text and chevron rotation
            $('#infoInputData').on('show.bs.collapse', function () {
                $('#info-toggle-text').text('Sembunyikan Informasi Input Data');
                $('#info-chevron').removeClass('fa-chevron-down').addClass('fa-chevron-up');
            });
            
            $('#infoInputData').on('hide.bs.collapse', function () {
                $('#info-toggle-text').text('Tampilkan Informasi Input Data');
                $('#info-chevron').removeClass('fa-chevron-up').addClass('fa-chevron-down');
            });
            
            // Initialize choices for select inputs
            var filterProductChoices = new Choices('#filter-product', {
                searchEnabled: true,
                searchPlaceholderValue: "Cari produk",
                itemSelectText: '',
                placeholder: true,
                placeholderValue: "Semua produk"
            });

            // Initialize DataTable with optimized configuration
            var table = $('#finishedGoods-table').DataTable({
                processing: true,
                serverSide: true,
                deferRender: true, // Improve performance for large datasets
                searchDelay: 500, // Add delay to reduce server requests
                ajax: {
                    url: "{{ route('finished-goods.data') }}",
                    type: "POST",
                    data: function(d) {
                        d.product_id = $('#filter-product').val();
                        d.category_product = $('#filter-category').val();
                        d.label = $('#filter-label').val();
                        d.filter_month_year = $('#filter-month-year').val();
                        d.simulation_date = simulationMode ? simulationDate : null;
                        d._token = "{{ csrf_token() }}";
                        return d;
                    },
                    error: function(xhr, error, code) {
                        console.error('DataTable AJAX Error:', error, code);
                        showNotification('error', 'Error', 'Failed to load data. Please refresh the page.');
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
                        data: 'sku',
                        name: 'sku',
                        width: '10%'
                    },
                    {
                        data: 'name_product',
                        name: 'name_product',
                        width: '20%'
                    },
                    {
                        data: 'stok_awal_display',
                        name: 'stok_awal_display',
                        orderable: false,
                        searchable: false,
                        width: '10%',
                        // stok_awal_display
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return `<input type="number" class="form-control form-control-sm stock-input" 
                                        data-field="stok_awal" data-product-id="${row.product_id}" 
                                        value="${data}" data-prev="${data}" min="0" style="width: 80px;">`;
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
                                return `<input type="number" class="form-control form-control-sm stock-input auto-field" 
                                        data-field="stok_masuk" data-product-id="${row.product_id}" 
                                        value="${data}" min="0" style="width: 80px;" readonly disabled>
                                        <small class="text-muted d-block">Auto</small>`;
                            }
                            return data;
                        }
                    },
                    {
                        data: 'stok_keluar_display',
                        name: 'stok_keluar_display',
                        orderable: false,
                        searchable: false,
                        width: '10%',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return `<input type="number" class="form-control form-control-sm stock-input auto-field" 
                                        data-field="stok_keluar" data-product-id="${row.product_id}" 
                                        value="${data}" min="0" style="width: 80px;" readonly disabled>
                                        <small class="text-muted d-block">Auto</small>`;
                            }
                            return data;
                        }
                    },
                    {
                        data: 'defective_display',
                        name: 'defective_display',
                        orderable: false,
                        searchable: false,
                        width: '10%',
                        // defective_display
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return `<input type="number" class="form-control form-control-sm stock-input" 
                                        data-field="defective" data-product-id="${row.product_id}" 
                                        value="${data}" data-prev="${data}" min="0" style="width: 80px;">`;
                            }
                            return data;
                        }

                    },
                    {
                        data: 'stok_sisa_display',
                        name: 'stok_sisa_display',
                        orderable: false,
                        searchable: false,
                        width: '10%',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return `<input type="number" class="form-control form-control-sm stock-input auto-field" 
                                        data-field="stok_sisa" data-product-id="${row.product_id}" 
                                        value="${data || 0}" min="0" style="width: 80px;" readonly disabled>
                                        <small class="text-muted d-block">Auto</small>`;
                            }
                            return data || 0;
                        }
                    },
                    {
                        data: 'live_stock_display',
                        name: 'live_stock_display',
                        orderable: false,
                        searchable: false,
                        width: '10%',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return `<span class="badge bg-primary live-stock" data-product-id="${row.product_id}">${data}</span>
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
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
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
                responsive: true,
                // Search optimization
                search: {
                    smart: false, // Disable smart search for better performance
                    regex: false,
                    caseInsensitive: true
                }
            });

            // Apply filters when dropdown values change with debouncing
            let filterTimeout;
            $('#filter-product, #filter-category, #filter-label, #filter-month-year').on('change', function() {
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
                
                // Reset product filter
                filterProductChoices.setChoiceByValue('');

                // Reset category and label filters
                $('#filter-category').val('');
                $('#filter-label').val('');
                
                // Reset month filter to current month (or simulation month if active)
                if (simulationMode && simulationDate) {
                    const simDate = new Date(simulationDate);
                    const simMonthYear = simDate.getFullYear() + '-' + String(simDate.getMonth() + 1).padStart(2, '0');
                    $('#filter-month-year').val(simMonthYear);
                    updateCurrentFilterDisplay(simMonthYear);
                } else {
                    $('#filter-month-year').val('{{ date("Y-m") }}');
                    updateCurrentFilterDisplay('{{ date("Y-m") }}');
                }

                // Reload table with cleared filters
                table.ajax.reload(null, false);
            });

            // Simulation date controls
            $('#apply-simulation').on('click', function() {
                const selectedDate = $('#simulation-date').val();
                
                if (!selectedDate) {
                    Swal.fire({
                        title: 'Tanggal Tidak Valid',
                        text: 'Silahkan pilih tanggal untuk simulasi.',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
                
                Swal.fire({
                    title: 'Aktifkan Mode Simulasi?',
                    html: `
                        <p>Sistem akan menggunakan tanggal simulasi: <strong>${selectedDate}</strong></p>
                        <p class="text-warning"><i class="fas fa-exclamation-triangle"></i> Data yang ditampilkan akan disesuaikan dengan tanggal simulasi ini.</p>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#f39c12',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Aktifkan Simulasi!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        toggleSimulationMode(true, selectedDate);
                        
                        // Reload table with simulation date
                        table.ajax.reload(null, false);
                        
                        Swal.fire({
                            title: 'Simulasi Diaktifkan!',
                            text: `Sistem sekarang menggunakan tanggal simulasi: ${selectedDate}`,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });
            });
            
            $('#reset-simulation').on('click', function() {
                if (!simulationMode) {
                    // Just reset to today's date
                    $('#simulation-date').val('{{ date("Y-m-d") }}');
                    return;
                }
                
                Swal.fire({
                    title: 'Reset Simulasi?',
                    text: 'Sistem akan kembali menggunakan tanggal hari ini dan data real-time.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#6c757d',
                    cancelButtonColor: '#dc3545',
                    confirmButtonText: 'Ya, Reset!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Reset simulation
                        toggleSimulationMode(false);
                        
                        // Reset date input to today
                        $('#simulation-date').val('{{ date("Y-m-d") }}');
                        
                        // Reset month filter to current month
                        $('#filter-month-year').val('{{ date("Y-m") }}');
                        updateCurrentFilterDisplay('{{ date("Y-m") }}');
                        
                        // Reload table with real data
                        table.ajax.reload(null, false);
                        
                        Swal.fire({
                            title: 'Simulasi Direset!',
                            text: 'Sistem kembali menggunakan data real-time.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });
            });

            // Function to calculate live stock for a specific row
            function calculateRowLiveStock(productId) {
                // Only get values from enabled inputs (manual inputs)
                const stokAwal = $(`input[data-product-id="${productId}"][data-field="stok_awal"]`)
                    .val() || 0;
                const defective = $(`input[data-product-id="${productId}"][data-field="defective"]`)
                    .val() || 0;

                // Get auto values from disabled inputs (just for display, but actual calculation will be server-side)
                const stokMasuk = $(`input[data-product-id="${productId}"][data-field="stok_masuk"]`)
                    .val() || 0;
                const stokKeluar = $(`input[data-product-id="${productId}"][data-field="stok_keluar"]`)
                    .val() || 0;
                const stokSisa = $(`input[data-product-id="${productId}"][data-field="stok_sisa"]`)
                    .val() || 0;

                const liveStock = parseInt(stokAwal) + parseInt(stokMasuk) - parseInt(stokKeluar) - parseInt(defective) + parseInt(stokSisa);
                $(`.live-stock[data-product-id="${productId}"]`).text(liveStock);

                return liveStock;
            }

            // Live stock calculation when input changes (only for enabled inputs)
            $(document).on('input', '.stock-input:not([disabled])', function() {
                const productId = $(this).data('product-id');
                calculateRowLiveStock(productId);
            });

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1700,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });


            $(document).on('change', '.stock-input[data-field="stok_awal"], .stock-input[data-field="defective"]', function() {
                const $input = $(this);
                const productId = $input.data('product-id');
                const fieldKey  = $input.data('field'); // 'stok_awal' | 'defective'
                const fieldLabel = fieldKey === 'stok_awal' ? 'Stok Awal' : 'Defective';

                // nilai baru & lama
                const newVal = parseInt(($input.val() ?? 0), 10) || 0;
                const oldVal = parseInt(($input.data('prev') ?? 0), 10) || 0;

                // ambil info produk dari row DataTables
                const $tr = $input.closest('tr');
                const rowData = (typeof table !== 'undefined') ? table.row($tr).data() : null;
                const productName = rowData?.name_product ?? `Produk ID ${productId}`;
                const sku = rowData?.sku ? ` (${rowData.sku})` : '';

                // state UI kecil biar terasa responsif
                const setSavingUI = (saving) => {
                    if (saving) {
                    $input.prop('disabled', true).addClass('is-saving');
                    } else {
                    $input.prop('disabled', false).removeClass('is-saving');
                    }
                };

                $.ajax({
                    url: `/finished-goods/${productId}`,
                    method: 'PUT',
                    data: {
                    // kirim 2 field sesuai endpointmu
                    stok_awal: (fieldKey === 'stok_awal') ? newVal : ($(`input[data-product-id="${productId}"][data-field="stok_awal"]`).val() ?? 0),
                    defective: (fieldKey === 'defective') ? newVal : ($(`input[data-product-id="${productId}"][data-field="defective"]`).val() ?? 0),
                    // penting: ikutkan filter bulan yang sedang aktif
                    filter_month_year: $('#filter-month-year').val(),
                    _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                    setSavingUI(true);
                    },
                    success: function(response) {
                    setSavingUI(false);

                    if (response.success && response.data) {
                        // update nilai row dari server agar sinkron
                        updateRowData(productId, response.data);

                        // update baseline "prev" ke nilai baru supaya perubahan berikutnya akurat
                        $input.data('prev', newVal);

                        // tampilkan toast detail perubahan
                        const arrow = newVal > oldVal ? '⬆️' : (newVal < oldVal ? '⬇️' : '⟲');
                        Toast.fire({
                        icon: 'success',
                        title: 'Perubahan tersimpan',
                        html: `<b>${productName}</b>${sku}<br><small>${fieldLabel}: <b>${oldVal}</b> → <b>${newVal}</b> ${arrow}</small>`
                        });
                    } else {
                        // error dari server (validasi, dll.)
                        Toast.fire({
                        icon: 'error',
                        title: response.message || 'Gagal menyimpan perubahan',
                        html: `<b>${productName}</b>${sku}<br><small>${fieldLabel}: <b>${oldVal}</b> → <b>${newVal}</b></small>`
                        });

                        // rollback tampilan input ke nilai lama
                        $input.val(oldVal);
                    }
                    },
                    error: function(xhr) {
                    setSavingUI(false);

                    const msg = (xhr.responseJSON && xhr.responseJSON.message)
                        ? xhr.responseJSON.message
                        : 'Terjadi kesalahan saat update data';

                    Toast.fire({
                        icon: 'error',
                        title: msg,
                        html: `<b>${productName}</b>${sku}<br><small>${fieldLabel}: <b>${oldVal}</b> → <b>${newVal}</b></small>`
                    });

                    // rollback tampilan input ke nilai lama
                    $input.val(oldVal);
                    }
                });
            });

            // Function to update row data after successful update
            function updateRowData(productId, data) {
                // Update input values
                $(`input[data-product-id="${productId}"][data-field="stok_awal"]`).data('prev', data.stok_awal);
                $(`input[data-product-id="${productId}"][data-field="stok_masuk"]`).val(data.stok_masuk);
                $(`input[data-product-id="${productId}"][data-field="stok_keluar"]`).val(data.stok_keluar);
                $(`input[data-product-id="${productId}"][data-field="defective"]`).data('prev', data.defective);
                $(`input[data-product-id="${productId}"][data-field="stok_sisa"]`).val(data.stok_sisa || 0);
                
                // Update live stock display
                $(`.live-stock[data-product-id="${productId}"]`).text(data.live_stock);
            }

            // Fallback notification function
            function showNotification(type, title, message) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: type,
                        title: title,
                        text: message,
                        timer: type === 'success' ? 2000 : 0,
                        showConfirmButton: type !== 'success'
                    });
                } else {
                    // Fallback to browser alert if SweetAlert2 is not available
                    alert(title + ': ' + message);
                }
            }
        });
        
        // Bulk Update All functionality with progress tracking
        $('#update-all').on('click', function() {
            const updateBtn = $(this);
            const progressContainer = $('#sync-progress-container');
            const progressBar = $('#sync-progress-bar');
            const progressPercentage = $('#sync-percentage');
            const progressStatus = $('#sync-status');
            
            // Configuration
            const chunkSize = 50; // Process 50 records at a time
            
            // Show confirmation dialog
            Swal.fire({
                title: 'Update All Stock Data?',
                text: 'Ini akan mengupdate stok masuk, stok keluar, dan live stock untuk semua produk. Proses ini mungkin memakan waktu beberapa menit.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Update Semua!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    updateBtn.prop('disabled', true)
                          .html('<i class="fas fa-spinner fa-spin"></i> Updating...');
                    
                    // Show progress bar
                    progressContainer.removeClass('d-none');
                    progressBar.css('width', '0%').attr('aria-valuenow', 0);
                    progressPercentage.text('0%');
                    progressStatus.text('Starting bulk update operation...');
                    
                    // Define recursive function to process chunks
                    function processUpdateChunk(offset = 0, totalRecords = null) {
                        $.ajax({
                            url: '{{ route("finished-goods.bulk-update-all") }}',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                offset: offset,
                                chunk_size: chunkSize,
                                total_records: totalRecords,
                                filter_month_year: $('#filter-month-year').val()
                            },
                            success: function(response) {
                                if (response.success) {
                                    // Update progress
                                    const progress = response.progress;
                                    progressBar.css('width', progress + '%').attr('aria-valuenow', progress);
                                    progressPercentage.text(progress + '%');
                                    
                                    // Update status message
                                    if (!response.completed) {
                                        progressStatus.text(`Updating ${response.processed_records} of ${response.total_records} records...`);
                                        
                                        // Process next chunk
                                        processUpdateChunk(response.next_offset, response.total_records);
                                    } else {
                                        // Update completed
                                        progressStatus.text('Bulk update completed successfully!');
                                        
                                        // Show success notification
                                        Swal.fire({
                                            title: 'Update Complete!',
                                            html: `
                                                <p>Successfully updated <strong>${response.total_records}</strong> records.</p>
                                                <p>Success: <span class="text-success">${response.success_count || 0}</span> | 
                                                   Errors: <span class="text-danger">${response.error_count || 0}</span></p>
                                            `,
                                            icon: 'success',
                                            confirmButtonText: 'OK'
                                        }).then(() => {
                                            // Hide progress bar first
                                            progressContainer.addClass('d-none');
                                            progressBar.removeClass('bg-danger').addClass('progress-bar-animated');
                                            
                                            // Restore button state
                                            updateBtn.prop('disabled', false)
                                                  .html('<i class="fas fa-save"></i> Update All');
                                            
                                            // Reload table with error handling and delay
                                            setTimeout(() => {
                                                try {
                                                    // Check if table is still valid before reloading
                                                    if (table && typeof table.ajax === 'object' && typeof table.ajax.reload === 'function') {
                                                        table.ajax.reload(function(json) {
                                                            console.log('Table reloaded successfully after bulk update');
                                                            if (!json || json.error) {
                                                                console.warn('Table reload returned error or empty data');
                                                                // Try one more time with full refresh
                                                                setTimeout(() => {
                                                                    window.location.reload();
                                                                }, 1000);
                                                            }
                                                        }, false);
                                                    } else {
                                                        console.error('DataTable object is not valid, performing full page refresh');
                                                        window.location.reload();
                                                    }
                                                } catch (error) {
                                                    console.error('Table reload error:', error);
                                                    // Fallback: full page refresh if table reload fails
                                                    window.location.reload();
                                                }
                                            }, 500);
                                        });
                                        
                                        // Show 100% completion while waiting for user to click OK
                                        progressBar.css('width', '100%').attr('aria-valuenow', 100);
                                        progressPercentage.text('100%');
                                    }
                                } else {
                                    handleUpdateError(response.message || 'Bulk update failed');
                                }
                            },
                            error: function(xhr) {
                                handleUpdateError(xhr.responseJSON?.message || 'Could not update data. Please try again.');
                            }
                        });
                    }
                    
                    // Handle update errors
                    function handleUpdateError(errorMessage) {
                        // Update status
                        progressStatus.text('Bulk update failed!');
                        progressBar.removeClass('progress-bar-animated').addClass('bg-danger');
                        
                        // Show error notification
                        Swal.fire({
                            title: 'Update Failed',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            // Hide progress bar
                            progressContainer.addClass('d-none');
                            progressBar.removeClass('bg-danger').addClass('progress-bar-animated');
                            
                            // Restore button state
                            updateBtn.prop('disabled', false)
                                  .html('<i class="fas fa-save"></i> Update All');
                        });
                    }
                    
                    // Start processing first chunk
                    processUpdateChunk();
                }
            });
        });
        
        // Chunked Sync All Data functionality with progress tracking
        $('#sync-all').on('click', function() {
            const syncBtn = $(this);
            const progressContainer = $('#sync-progress-container');
            const progressBar = $('#sync-progress-bar');
            const progressPercentage = $('#sync-percentage');
            const progressStatus = $('#sync-status');
            
            // Configuration
            const chunkSize = 50; // Process 50 records at a time
            
            // Show loading state
            syncBtn.prop('disabled', true)
                  .html('<i class="fas fa-spinner fa-spin"></i> Syncing...');
            
            // Show progress bar
            progressContainer.removeClass('d-none');
            progressBar.css('width', '0%').attr('aria-valuenow', 0);
            progressPercentage.text('0%');
            progressStatus.text('Starting sync operation...');
            
            // Define recursive function to process chunks
            function processChunk(offset = 0, totalRecords = null) {
                $.ajax({
                    url: '{{ route("finished-goods.sync") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        offset: offset,
                        chunk_size: chunkSize,
                        total_records: totalRecords,
                        filter_month_year: $('#filter-month-year').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update progress
                            const progress = response.progress;
                            progressBar.css('width', progress + '%').attr('aria-valuenow', progress);
                            progressPercentage.text(progress + '%');
                            
                            // Update status message
                            if (!response.completed) {
                                progressStatus.text(`Processing ${response.processed_records} of ${response.total_records} records...`);
                                
                                // Process next chunk
                                processChunk(response.next_offset, response.total_records);
                            } else {
                                // Sync completed
                                progressStatus.text('Sync completed successfully!');
                                
                                // Show success notification
                                Swal.fire({
                                    title: 'Sync Complete!',
                                    text: `Successfully processed ${response.total_records} records.`,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    // Full page refresh after user clicks OK
                                    window.location.reload();
                                });
                                
                                // Show 100% completion while waiting for user to click OK
                                progressBar.css('width', '100%').attr('aria-valuenow', 100);
                                progressPercentage.text('100%');
                            }
                        } else {
                            handleSyncError(response.message || 'Sync failed');
                        }
                    },
                    error: function(xhr) {
                        handleSyncError(xhr.responseJSON?.message || 'Could not synchronize data. Please try again.');
                    }
                });
            }
            
            // Handle sync errors
            function handleSyncError(errorMessage) {
                // Update status
                progressStatus.text('Sync failed!');
                progressBar.removeClass('progress-bar-animated').addClass('bg-danger');
                
                // Show error notification
                Swal.fire({
                    title: 'Sync Failed',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                
                // Reset UI after user acknowledges error
                Swal.getConfirmButton().addEventListener('click', function() {
                    // Hide progress bar
                    progressContainer.addClass('d-none');
                    progressBar.removeClass('bg-danger').addClass('progress-bar-animated');
                    
                    // Restore button state
                    syncBtn.prop('disabled', false)
                          .html('<i class="fas fa-sync"></i> Sync All Data');
                });
            }
            
            // Start processing first chunk
            processChunk();
        });
        
        // Manual refresh functionality
        $(document).on('click', '.refresh-btn', function() {
            table.ajax.reload(null, false);
        });
    </script>

@endsection