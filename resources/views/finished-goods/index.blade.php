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

        .update-btn {
            margin-right: 5px;
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
                            <button id="sync-all" class="btn btn-primary me-2 mb-2 mb-sm-0">
                                <i class="fas fa-sync"></i> Sync All Data
                            </button>
                            <button id="clear-filters" class="btn btn btn-secondary me-2 mb-2 mb-sm-0">
                                <i class="fas fa-filter"></i> Hapus Filter
                            </button>
                        </div>
                    </div>
                    <!-- Progress bar for sync operation - hidden by default -->
                    <div id="sync-progress-container" class="mt-3 d-none">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span id="sync-status">Processing...</span>
                            <span id="sync-percentage">0%</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div id="sync-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" 
                                 role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
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
                                <div class="col-lg-4 col-md-6 col-sm-12 mb-2">
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
                                <div class="col-lg-4 col-md-6 col-sm-12 mb-2">
                                    <label for="filter-category" class="form-label small filter-label">Kategori</label>
                                    <select class="form-control form-control-sm" name="filter-category"
                                        id="filter-category">
                                        <option value="">Semua Kategori</option>
                                        @foreach ($categories as $key => $category)
                                            <option value="{{ $key }}">{{ $category }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-12 mb-2">
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
                            </div>
                        </div>
                    </div>
                    <!-- End Filter Section -->

                    <!-- Info Box -->
                    <div class="alert alert-info" role="alert">
                        <h6 class="alert-heading"><i class="fas fa-info-circle"></i> Informasi Input Data</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong><i class="fas fa-edit text-primary"></i> Manual Input:</strong>
                                </p>
                                <ul class="mb-2">
                                    <li><strong>Stok Awal:</strong> Input manual oleh user</li>
                                    <li><strong>Defective:</strong> Input manual oleh user</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong><i class="fas fa-cog text-secondary"></i> Otomatis:</strong></p>
                                <ul class="mb-2">
                                    <li><strong>Stok Masuk:</strong> Dari Catatan Produksi</li>
                                    <li><strong>Stok Keluar:</strong> Dari History Sales Scanner</li>
                                    <li><strong>Live Stock:</strong> Kalkulasi otomatis</li>
                                </ul>
                            </div>
                        </div>
                        <small class="text-muted">💡 Field dengan latar abu-abu tidak dapat diedit karena nilainya dihitung
                            otomatis dari sistem</small>
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
                                    <th>Live Stock</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="products-body">
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                         <div class="pagination-container">
                            <nav>
                                <ul class="pagination justify-content-center">
                                    <!-- Pagination akan diisi via AJAX -->
                                </ul>
                            </nav>
                        </div>
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
            let currentPage = 1;
            const perPage = 25;
            
            // Load initial data
            loadProducts();
            
            // Filter change handler with debounce
            let filterTimeout;
            $('#filter-product, #filter-category, #filter-label').on('change', function() {
                clearTimeout(filterTimeout);
                filterTimeout = setTimeout(() => {
                    currentPage = 1;
                    loadProducts();
                }, 300);
            });
            
            // Function to load products
            function loadProducts() {
                $('#loading-spinner').show();
                
                $.ajax({
                    url: '/products',
                    type: 'GET',
                    data: {
                        page: currentPage,
                        per_page: perPage,
                        product_id: $('#filter-product').val(),
                        category_product: $('#filter-category').val(),
                        label: $('#filter-label').val()
                    },
                    success: function(response) {
                        renderProducts(response.data);
                        renderPagination(response);
                        $('#loading-spinner').hide();
                    },
                    error: function() {
                        $('#loading-spinner').hide();
                        alert('Gagal memuat data');
                    }
                });
            }
            
            // Render products to table
            function renderProducts(products) {
                const $tbody = $('#products-body');
                $tbody.empty();
                
                if (products.length === 0) {
                    $tbody.append('<tr><td colspan="9" class="text-center">Tidak ada data</td></tr>');
                    return;
                }
                
                products.forEach((product, index) => {
                    const rowNum = (currentPage - 1) * perPage + index + 1;
                    
                    // Format angka untuk tampilan
                    const formatNumber = (num) => {
                        return num !== null && num !== undefined ? num : 0;
                    };
                    
                    const stokAwal = formatNumber(product.stok_awal);
                    const stokMasuk = formatNumber(product.stok_masuk);
                    const stokKeluar = formatNumber(product.stok_keluar);
                    const defective = formatNumber(product.defective);
                    const liveStock = formatNumber(product.live_stock);
                    
                    const row = `
                        <tr data-id="${product.id}">
                            <td>${rowNum}</td>
                            <td>${product.sku}</td>
                            <td>${product.name_product}</td>
                            <td>
                                <input type="number" class="form-control form-control-sm stock-input" 
                                    data-field="stok_awal" value="${stokAwal}" min="0">
                            </td>
                            <td>
                                <input type="number" class="form-control form-control-sm auto-field" 
                                    value="${stokMasuk}" readonly>
                                <small class="text-muted">Auto</small>
                            </td>
                            <td>
                                <input type="number" class="form-control form-control-sm auto-field" 
                                    value="${stokKeluar}" readonly>
                                <small class="text-muted">Auto</small>
                            </td>
                            <td>
                                <input type="number" class="form-control form-control-sm stock-input" 
                                    data-field="defective" value="${defective}" min="0">
                            </td>
                            <td>
                                <span class="badge bg-primary live-stock">${liveStock}</span>
                                <small class="text-muted">Auto</small>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-success update-btn">Update</button>
                                <button class="btn btn-sm btn-secondary reset-btn">Reset</button>
                            </td>
                        </tr>
                    `;
                    $tbody.append(row);
                });
            }
            // Render pagination
            function renderProducts(products) {
                const $tbody = $('#products-body');
                $tbody.empty();
                
                if (products.length === 0) {
                    $tbody.append('<tr><td colspan="9" class="text-center">Tidak ada data</td></tr>');
                    return;
                }
                
                products.forEach((product, index) => {
                    const rowNum = (currentPage - 1) * perPage + index + 1;
                    
                    // Pastikan semua nilai stok memiliki default 0
                    const stokAwal = product.stok_awal ?? 0;
                    const stokMasuk = product.stok_masuk ?? 0;
                    const stokKeluar = product.stok_keluar ?? 0;
                    const defective = product.defective ?? 0;
                    const liveStock = product.live_stock ?? 0;
                    
                    const row = `
                        <tr data-id="${product.id}">
                            <td>${rowNum}</td>
                            <td>${product.sku}</td>
                            <td>${product.name_product}</td>
                            <td>
                                <input type="number" class="form-control form-control-sm stock-input" 
                                    data-field="stok_awal" value="${stokAwal}" min="0">
                            </td>
                            <td>
                                <input type="number" class="form-control form-control-sm auto-field" 
                                    value="${stokMasuk}" readonly>
                                <small class="text-muted">Auto</small>
                            </td>
                            <td>
                                <input type="number" class="form-control form-control-sm auto-field" 
                                    value="${stokKeluar}" readonly>
                                <small class="text-muted">Auto</small>
                            </td>
                            <td>
                                <input type="number" class="form-control form-control-sm stock-input" 
                                    data-field="defective" value="${defective}" min="0">
                            </td>
                            <td>
                                <span class="badge bg-primary live-stock">${liveStock}</span>
                                <small class="text-muted">Auto</small>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-success update-btn">Update</button>
                                <button class="btn btn-sm btn-secondary reset-btn">Reset</button>
                            </td>
                        </tr>
                    `;
                    $tbody.append(row);
                });
            }

            // Pagination click handler
            $(document).on('click', '.page-link', function(e) {
                e.preventDefault();
                currentPage = parseInt($(this).data('page'));
                loadProducts();
            });
            
            // Live stock calculation
            $(document).on('input', '.stock-input', function() {
                const $row = $(this).closest('tr');
                const stokAwal = parseInt($row.find('[data-field="stok_awal"]').val()) || 0;
                const stokMasuk = parseInt($row.find('[data-field="stok_masuk"]').val()) || 0;
                const stokKeluar = parseInt($row.find('[data-field="stok_keluar"]').val()) || 0;
                const defective = parseInt($row.find('[data-field="defective"]').val()) || 0;
                
                const liveStock = stokAwal + stokMasuk - stokKeluar - defective;
                $row.find('.live-stock').text(liveStock);
            });
            
            // Update button handler
           $(document).on('click', '.update-btn', function() {
                const $row = $(this).closest('tr');
                const productId = $row.data('id');
                
                // Ambil nilai dengan default 0 jika null/undefined
                const stokAwal = parseInt($row.find('[data-field="stok_awal"]').val()) || 0;
                const defective = parseInt($row.find('[data-field="defective"]').val()) || 0;
                
                // Tampilkan loading
                $(this).html('<i class="fas fa-spinner fa-spin"></i> Updating...').prop('disabled', true);
                
                $.ajax({
                    url: `/finished-goods/${productId}/update`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        stok_awal: stokAwal,
                        defective: defective
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update tampilan dengan data terbaru dari server
                            $row.find('[data-field="stok_awal"]').val(response.data.stok_awal);
                            $row.find('[data-field="defective"]').val(response.data.defective);
                            $row.find('.live-stock').text(response.data.live_stock);
                            
                            Swal.fire('Success', response.message, 'success');
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Failed to update data', 'error');
                    },
                    complete: function() {
                        $('.update-btn').html('Update').prop('disabled', false);
                    }
                });
            });
            
            // Reset button handler
            $(document).on('click', '.reset-btn', function() {
                const $row = $(this).closest('tr');
                $row.find('[data-field="stok_awal"]').val(0);
                $row.find('[data-field="defective"]').val(0);
                $row.find('.live-stock').text(0);
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
                        total_records: totalRecords
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
