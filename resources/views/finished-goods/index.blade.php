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
                            <button id="update-all" class="btn btn-success me-2 mb-2 mb-sm-0">
                                <i class="fas fa-save"></i> Update All
                            </button>
                            <button id="sync-all" class="btn btn-primary me-2 mb-2 mb-sm-0">
                                <i class="fas fa-sync"></i> Sync All Data
                            </button>
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
                                    <li><strong>Stok Masuk:</strong> Dari Catatan Produksi + Purchase Finished Goods</li>
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

        $(document).ready(function() {
            // Initialize the current filter display on page load
            const initialMonthValue = $('#filter-month-year').val() || '{{ date("Y-m") }}';
            updateCurrentFilterDisplay(initialMonthValue);
            
            // Initialize choices for select inputs
            var filterProductChoices = new Choices('#filter-product', {
                searchEnabled: true,
                searchPlaceholderValue: "Cari produk",
                itemSelectText: '',
                placeholder: true,
                placeholderValue: "Semua produk"
            });

            // Initialize DataTable
            var table = $('#finishedGoods-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('finished-goods.data') }}",
                    type: "POST",
                    data: function(d) {
                        d.product_id = $('#filter-product').val();
                        d.category_product = $('#filter-category').val();
                        d.label = $('#filter-label').val();
                        d.filter_month_year = $('#filter-month-year').val();
                        d._token = "{{ csrf_token() }}";
                        
                        // // Debug: Log filter values being sent
                        // console.log('DataTables AJAX Data:', {
                        //     product_id: d.product_id,
                        //     category_product: d.category_product,
                        //     label: d.label,
                        //     filter_month_year: d.filter_month_year
                        // });
                        
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
                        data: 'sku',
                        name: 'sku'
                    },
                    {
                        data: 'name_product',
                        name: 'name_product'
                    },
                    {
                        data: 'stok_awal_display',
                        name: 'stok_awal_display',
                        render: function(data, type, row) {
                            return `<input type="number" class="form-control form-control-sm stock-input" 
                                    data-field="stok_awal" data-product-id="${row.product_id}" 
                                    value="${data}" min="0" style="width: 80px;">`;
                        }
                    },
                    {
                        data: 'stok_masuk_display',
                        name: 'stok_masuk_display',
                        render: function(data, type, row) {
                            return `<input type="number" class="form-control form-control-sm stock-input auto-field" 
                                    data-field="stok_masuk" data-product-id="${row.product_id}" 
                                    value="${data}" min="0" style="width: 80px;" readonly disabled>
                                    <small class="text-muted d-block">Auto</small>`;
                        }
                    },
                    {
                        data: 'stok_keluar_display',
                        name: 'stok_keluar_display',
                        render: function(data, type, row) {
                            return `<input type="number" class="form-control form-control-sm stock-input auto-field" 
                                    data-field="stok_keluar" data-product-id="${row.product_id}" 
                                    value="${data}" min="0" style="width: 80px;" readonly disabled>
                                    <small class="text-muted d-block">Auto</small>`;
                        }
                    },
                    {
                        data: 'defective_display',
                        name: 'defective_display',
                        render: function(data, type, row) {
                            return `<input type="number" class="form-control form-control-sm stock-input" 
                                    data-field="defective" data-product-id="${row.product_id}" 
                                    value="${data}" min="0" style="width: 80px;">`;
                        }
                    },
                    {
                        data: 'live_stock_display',
                        name: 'live_stock_display',
                        render: function(data, type, row) {
                            return `<span class="badge bg-primary live-stock" data-product-id="${row.product_id}">${data}</span>
                                    <small class="text-muted d-block">Auto</small>`;
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <button type="button" class="btn btn-sm btn-success update-btn" data-id="${row.product_id}">
                                    <i class="fas fa-save"></i> Update
                                </button>
                                <button type="button" class="btn btn-sm btn-secondary reset-btn" data-id="${row.product_id}">
                                    <i class="fas fa-undo"></i> Reset
                                </button>
                            `;
                        }
                    }
                ],
                order: [
                    [2, 'asc']
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
                ]
            });

            // Apply filters when dropdown values change
            $('#filter-product, #filter-category, #filter-label, #filter-month-year').on('change', function() {
                const filterId = $(this).attr('id');
                const filterValue = $(this).val();
                
                // Special handling for month filter - update display dynamically
                if (filterId === 'filter-month-year') {
                    // Update the current filter display dynamically using DOM manipulation
                    updateCurrentFilterDisplay(filterValue);
                }
                
                table.ajax.reload();
            });

            // Clear filters function
            $('#clear-filters').on('click', function() {
                // Reset product filter
                filterProductChoices.setChoiceByValue('');

                // Reset category and label filters
                $('#filter-category').val('').trigger('change');
                $('#filter-label').val('').trigger('change');
                
                // Reset month filter to current month
                $('#filter-month-year').val('{{ date("Y-m") }}').trigger('change');
                
                // Update display to current month when clearing filters
                updateCurrentFilterDisplay('{{ date("Y-m") }}');

                // Reload table
                table.ajax.reload();
            });

            // Function to calculate live stock for a specific row
            function calculateRowLiveStock(productId) {
                // Only get values from enabled inputs (manual inputs)
                const stokAwal = parseInt($(`input[data-product-id="${productId}"][data-field="stok_awal"]`)
                    .val()) || 0;
                const defective = parseInt($(`input[data-product-id="${productId}"][data-field="defective"]`)
                    .val()) || 0;

                // Get auto values from disabled inputs (just for display, but actual calculation will be server-side)
                const stokMasuk = parseInt($(`input[data-product-id="${productId}"][data-field="stok_masuk"]`)
                    .val()) || 0;
                const stokKeluar = parseInt($(`input[data-product-id="${productId}"][data-field="stok_keluar"]`)
                    .val()) || 0;

                const liveStock = stokAwal + stokMasuk - stokKeluar - defective;
                $(`.live-stock[data-product-id="${productId}"]`).text(liveStock);

                return liveStock;
            }

            // Live stock calculation when input changes (only for enabled inputs)
            $(document).on('input', '.stock-input:not([disabled])', function() {
                const productId = $(this).data('product-id');
                calculateRowLiveStock(productId);
            });

            // Function to update row data after successful update
            function updateRowData(productId, data) {
                // Update input values
                $(`input[data-product-id="${productId}"][data-field="stok_awal"]`).val(data.stok_awal);
                $(`input[data-product-id="${productId}"][data-field="stok_masuk"]`).val(data.stok_masuk);
                $(`input[data-product-id="${productId}"][data-field="stok_keluar"]`).val(data.stok_keluar);
                $(`input[data-product-id="${productId}"][data-field="defective"]`).val(data.defective);
                
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

            // Update button click - with better error handling
            $(document).on('click', '.update-btn', function(e) {
                e.preventDefault();
                
                const btn = $(this);
                const productId = btn.data('id');

                // Validate productId
                if (!productId) {
                    showNotification('error', 'Error', 'Product ID tidak ditemukan');
                    return;
                }

                // Get values from manual input fields
                const stokAwalInput = $(`input[data-product-id="${productId}"][data-field="stok_awal"]`);
                const defectiveInput = $(`input[data-product-id="${productId}"][data-field="defective"]`);
                
                if (stokAwalInput.length === 0 || defectiveInput.length === 0) {
                    showNotification('error', 'Error', 'Input field tidak ditemukan');
                    return;
                }

                const stokAwal = parseInt(stokAwalInput.val()) || 0;
                const defective = parseInt(defectiveInput.val()) || 0;

                // Disable button and show loading state
                btn.prop('disabled', true);
                const originalText = btn.html();
                btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');

                // Send update request
                $.ajax({
                    url: `/finished-goods/${productId}`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'PUT',
                        stok_awal: stokAwal,
                        defective: defective
                    },
                    timeout: 30000, // 30 second timeout
                    success: function(response) {
                        if (response && response.success) {
                            showNotification('success', 'Berhasil!', response.message || 'Data berhasil diperbarui');

                            // Update the row with new data
                            if (response.data) {
                                updateRowData(productId, response.data);
                            }
                        } else {
                            showNotification('error', 'Gagal!', response.message || 'Terjadi kesalahan');
                        }
                    },
                    error: function(xhr, status, error) {
                        let errorMessage = 'Terjadi kesalahan saat menyimpan data';
                        
                        if (xhr.status === 422) {
                            // Validation errors
                            try {
                                const errorResponse = JSON.parse(xhr.responseText);
                                if (errorResponse.errors) {
                                    const errorList = Object.values(errorResponse.errors).flat();
                                    errorMessage = errorList.join(', ');
                                } else if (errorResponse.message) {
                                    errorMessage = errorResponse.message;
                                }
                            } catch (parseError) {
                                errorMessage = 'Data tidak valid. Periksa input Anda.';
                            }
                        } else if (xhr.status === 500) {
                            // Server error
                            try {
                                const errorResponse = JSON.parse(xhr.responseText);
                                errorMessage = errorResponse.message || 'Terjadi kesalahan server';
                            } catch (parseError) {
                                errorMessage = 'Terjadi kesalahan server. Silahkan coba lagi.';
                            }
                        } else if (xhr.status === 404) {
                            errorMessage = 'Produk tidak ditemukan';
                        } else if (xhr.status === 0) {
                            errorMessage = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
                        } else if (status === 'timeout') {
                            errorMessage = 'Request timeout. Silahkan coba lagi.';
                        }

                        showNotification('error', 'Error!', errorMessage);
                    },
                    complete: function() {
                        // Re-enable button and restore original text
                        btn.prop('disabled', false);
                        btn.html(originalText);
                    }
                });
            });

            // Reset button click - only reset manual fields
            $(document).on('click', '.reset-btn', function() {
                const productId = $(this).data('id');

                Swal.fire({
                    title: 'Reset Data Manual?',
                    text: "Apakah Anda yakin ingin mereset data stok manual (Stok Awal & Defective) ke 0?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Reset!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Reset only manual input fields to 0
                        $(`input[data-product-id="${productId}"][data-field="stok_awal"]`).val(0);
                        $(`input[data-product-id="${productId}"][data-field="defective"]`).val(0);
                        calculateRowLiveStock(productId);

                        Swal.fire({
                            title: 'Direset!',
                            text: 'Data stok manual telah direset ke 0.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    }
                });
            });
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