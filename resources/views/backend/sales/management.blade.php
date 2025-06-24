@extends('layouts.main')

@section('title', 'Sales Management')
@section('breadcrumb-item', $item)

@section('css')
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Ensure proper mobile viewport -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <!-- [Page specific CSS] start -->
    <!-- data tables css -->
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/datatables/dataTables.bootstrap5.min.css') }}">
    <!-- [Page specific CSS] end -->
    <style>
        /* ========== VARIABEL & ROOT ========== */
        :root {
            --primary-color: #4CAF50;
            --primary-light: rgba(76, 175, 80, 0.25);
            --danger-color: #dc3545;
            --secondary-color: #6c757d;
            --light-bg: #f8f9fa;
            --border-color: #dee2e6;
            --focus-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
            --card-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
            --transition-normal: all 0.3s ease;
            --border-radius-normal: 4px;
            --border-radius-large: 8px;
            --form-element-height: 44px;
        }

        /* ========== COMMON ELEMENTS ========== */
        .card {
            border: none;
            box-shadow: var(--card-shadow);
            margin-bottom: 30px;
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid var(--border-color);
            padding: 15px;
        }

        .card-header .d-flex {
            flex-wrap: wrap;
            gap: 10px;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }

        .error-feedback {
            color: var(--danger-color);
            font-size: 0.875em;
            margin-top: 0.25rem;
            display: none;
        }

        /* ========== BUTTONS ========== */
        .btn {
            min-height: var(--form-element-height);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition-normal);
        }

        /* Button icon styling */
        .btn-icon {
            width: 36px;
            height: 36px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 3px;
            transition: all 0.2s ease;
        }

        .btn-icon i {
            font-size: 1rem;
        }

        .btn-icon:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 5px rgba(0, 0, 0, 0.2);
        }

        /* ========== TABLE STYLES ========== */
        .table-wrapper {
            position: relative;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin: 0 -15px;
            padding: 0 15px;
            width: calc(100% + 30px);
        }

        .table-wrapper::-webkit-scrollbar {
            height: 6px;
        }

        .table-wrapper::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .table-wrapper::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        #sales-management-table {
            min-width: 800px;
            width: 100%;
        }

        #sales-management-table th,
        #sales-management-table td {
            white-space: nowrap;
            vertical-align: middle;
            padding: 12px 8px;
        }

        .current-date-info {
            display: block;
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .current-date-info strong {
            color: var(--primary-color);
        }

        .table-scroll-indicator {
            display: none;
            margin-bottom: 10px;
            padding: 8px;
            background-color: #fff3cd;
            border-radius: var(--border-radius-normal);
            text-align: center;
            font-size: 0.9rem;
        }

        /* ========== RESPONSIVE STYLES ========== */
        /* Mobile styles */
        @media (max-width: 576px) {
            .card-body {
                padding: 15px;
            }

            .table-scroll-indicator {
                display: block;
                font-weight: bold;
            }

            #sales-management-table {
                font-size: 14px;
            }

            #sales-management-table td:nth-child(3),
            #sales-management-table td:nth-child(4) {
                max-width: 150px;
                white-space: normal;
                word-break: break-word;
            }

            .btn-icon {
                width: 32px;
                height: 32px;
            }

            .btn-icon i {
                font-size: 0.875rem;
            }
        }

        /* Tablet styles */
        @media (min-width: 577px) and (max-width: 991px) {

            #sales-management-table td:nth-child(3),
            #sales-management-table td:nth-child(4) {
                max-width: 200px;
                white-space: normal;
                word-break: break-word;
            }

            .table-scroll-indicator {
                display: block;
            }
        }

        /* Table loading state */
        .table-loading {
            position: relative;
        }

        .table-loading:after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.7) url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 44 44" width="44px" height="44px"><circle fill="none" stroke="%234CAF50" stroke-width="4" cx="22" cy="22" r="14"><animate attributeName="stroke-dasharray" dur="1.5s" calcMode="spline" values="0 100;100 100;0 100" keyTimes="0;0.5;1" keySplines="0.42,0,0.58,1;0.42,0,0.58,1" repeatCount="indefinite"/><animate attributeName="stroke-dashoffset" dur="1.5s" calcMode="spline" values="0;-100;-200" keyTimes="0;0.5;1" keySplines="0.42,0,0.58,1;0.42,0,0.58,1" repeatCount="indefinite"/></circle></svg>') center center no-repeat;
            background-size: 50px;
            z-index: 10;
            border-radius: var(--border-radius-large);
            animation: fadeIn 0.2s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }
    </style>
@endsection

@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">
        <!-- Sales Management Table start -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h5 class="mb-0">Sales Management</h5>
                            <span class="text-muted current-date-info">Menampilkan:
                                <strong>Semua Data Penjualan</strong></span>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-primary"
                                onclick="window.location.href='{{ route('scanner.index') }}'">
                                <i class="fas fa-plus me-1"></i>Add New Sale
                            </button>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary active" id="showActive">
                                    <i class="fas fa-list me-1 d-none d-sm-inline"></i>Data Aktif
                                </button>
                                <button type="button" class="btn btn-outline-warning" id="showArchived">
                                    <i class="fas fa-archive me-1 d-none d-sm-inline"></i>Data Terarsip
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-scroll-indicator">
                        <i class="fas fa-arrows-left-right me-1"></i> Geser kanan-kiri untuk melihat data lengkap
                    </div>
                    <div class="table-wrapper">
                        <table id="sales-management-table" class="table table-striped table-bordered">
                            <thead id="main-table-header">
                                <tr>
                                    <th style="width:.50px;">NO</th>
                                    <th style="width: 120px;">NO RESI</th>
                                    <th>NO SKU</th>
                                    <th style="width: 80px;">JUMLAH</th>
                                    <th style="width: 150px;">DIBUAT</th>
                                    <th style="width: 150px;">DIPERBARUI</th>
                                    <th style="width: 120px;">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data akan diisi oleh DataTables -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Sales Management Table end -->
    </div>
    <!-- [ Main Content ] end -->

    <!-- Edit History Sale Modal -->
    <div class="modal fade" id="editHistorySaleModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Sales Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editHistorySaleForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_history_sale_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">No Resi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="no_resi" id="edit_no_resi" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">SKU & Quantity</label>
                            <div id="edit-sku-container">
                                <!-- SKU inputs will be added here -->
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-edit-sku-btn">
                                <i class="fas fa-plus"></i> Add SKU
                            </button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
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

    <script>
        // Declare table variable globally
        var table;

        $(document).ready(function() {
            // Prevent DataTables from showing error messages in console
            $.fn.dataTable.ext.errMode = 'none';

            // Setup AJAX CSRF token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Check and remove duplicate thead elements that might already exist
            if ($('#sales-management-table thead').length > 1) {
                $('#sales-management-table thead:gt(0)').remove();
            }

            // Initialize DataTable with optimized configuration
            table = $('#sales-management-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                scrollX: true,
                dom: 'lfrtip',
                bAutoWidth: false,
                ordering: true,
                searching: true,
                stateSave: false,
                paging: true,
                fixedHeader: false,
                deferRender: true,
                pageLength: 50,
                lengthMenu: [
                    [25, 50, 100, 250, -1],
                    [25, 50, 100, 250, "All"]
                ],
                searchDelay: 500,
                ajax: {
                    url: "{{ route('history-sales.data') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}";
                        d.load_all = true; // Load all data without date filter for management
                        if ($('#showArchived').hasClass('active')) {
                            d.only_trashed = true;
                        } else if ($('#showActive').hasClass('active')) {
                            d.only_trashed = false;
                        }

                        // Add loading indicator
                        $('.table-wrapper').addClass('table-loading');
                    },
                    dataSrc: function(json) {
                        updateCurrentDateInfo();
                        // Remove loading indicator after data is loaded
                        $('.table-wrapper').removeClass('table-loading');
                        return json.data;
                    },
                    error: function(xhr, error, thrown) {
                        console.warn('AJAX error:', error);
                        // Remove loading indicator on error
                        $('.table-wrapper').removeClass('table-loading');
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to load data. Please try again.',
                            icon: 'error',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                },
                columns: [{
                        data: 'no',
                        name: 'no',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'no_resi',
                        name: 'no_resi'
                    },
                    {
                        data: 'no_sku',
                        name: 'no_sku',
                        className: 'align-top'
                    },
                    {
                        data: 'qty',
                        name: 'qty',
                        className: 'align-top',
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at',
                        visible: false,
                        searchable: false
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [4, 'desc']
                ],
                language: {
                    processing: '<i class="fas fa-spinner fa-spin fa-2x"></i><span class="ms-2">Loading data...</span>',
                    lengthMenu: "Show _MENU_ entries per page",
                    zeroRecords: "No data found",
                    info: "Showing page _PAGE_ of _PAGES_",
                    infoEmpty: "No data available",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    search: "Search:",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    },
                    emptyTable: "No data in table",
                    infoPostFix: "",
                    thousands: ".",
                    loadingRecords: "Loading...",
                    aria: {
                        sortAscending: ": activate to sort column ascending",
                        sortDescending: ": activate to sort column descending"
                    }
                },
                drawCallback: function() {
                    // Ensure no duplicate headers
                    if ($('#sales-management-table thead').length > 1) {
                        $('#sales-management-table thead:not(#main-table-header)').remove();
                    }

                    // Make sure tooltip works for action buttons
                    $('[title]').tooltip();
                },
                initComplete: function() {
                    // Update date info on first load
                    updateCurrentDateInfo();

                    // Fix header issues
                    if ($('#sales-management-table thead').length > 1) {
                        $('#sales-management-table thead:not(#main-table-header)').remove();
                    }

                    // Improve search performance by adding debounce
                    const searchInput = $('div.dataTables_filter input');
                    searchInput.unbind();
                    searchInput.bind('input', debounce(function(e) {
                        table.search(this.value).draw();
                    }, 500));
                }
            });

            // Debounce function to improve performance
            function debounce(func, wait) {
                let timeout;
                return function() {
                    const context = this,
                        args = arguments;
                    clearTimeout(timeout);
                    timeout = setTimeout(function() {
                        func.apply(context, args);
                    }, wait);
                };
            }

            // Update current date info display
            function updateCurrentDateInfo() {
                // For management page, we show all data, so update the info accordingly
                const totalRecords = table.page.info().recordsTotal;
                $('.current-date-info strong').text(`Semua Data Penjualan (${totalRecords} records)`);
            }

            // Filter buttons handler
            $('#showActive').on('click', function() {
                $(this).addClass('active').siblings().removeClass('active');
                table.ajax.reload();
            });

            $('#showArchived').on('click', function() {
                $(this).addClass('active').siblings().removeClass('active');
                table.ajax.reload();
            });

            // Add SKU button click handler in edit modal
            $('#add-edit-sku-btn').on('click', function() {
                const skuHtml = `
                    <div class="edit-sku-container d-flex mb-2">
                        <input type="text" class="form-control me-2" name="no_sku[]" placeholder="SKU">
                        <input type="number" class="form-control me-2" name="qty[]" value="1" min="1" style="width: 120px;">
                        <button type="button" class="btn btn-danger remove-edit-sku">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>`;
                $('#edit-sku-container').append(skuHtml);
            });

            // Remove SKU button click handler in edit modal
            $(document).on('click', '.remove-edit-sku', function() {
                // Don't remove the last container, just clear it
                if ($('.edit-sku-container').length > 1) {
                    $(this).closest('.edit-sku-container').remove();
                } else {
                    $(this).closest('.edit-sku-container').find('input[name="no_sku[]"]').val('');
                    $(this).closest('.edit-sku-container').find('input[name="qty[]"]').val('1');
                }
            });

            // Sales Management specific functionality
            // Edit function for sales management
            window.editHistorySale = function(id) {
                $.get(`/sales-management/${id}/edit`)
                    .done(function(response) {
                        if (response.status === 'success') {
                            const data = response.data;
                            $('#edit_history_sale_id').val(data.id);
                            $('#edit_no_resi').val(data.no_resi);

                            // Clear existing SKU containers
                            $('#edit-sku-container').empty();

                            // Add SKU data
                            if (data.no_sku && data.no_sku.length > 0) {
                                data.no_sku.forEach((sku, index) => {
                                    const qty = data.qty && data.qty[index] ? data.qty[index] : 1;
                                    const skuHtml = `
                                        <div class="edit-sku-container d-flex mb-2">
                                            <input type="text" class="form-control me-2" name="no_sku[]" value="${sku}" placeholder="SKU">
                                            <input type="number" class="form-control me-2" name="qty[]" value="${qty}" min="1" style="width: 120px;">
                                            <button type="button" class="btn btn-danger remove-edit-sku">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>`;
                                    $('#edit-sku-container').append(skuHtml);
                                });
                            } else {
                                // Add one empty container if no SKUs
                                const skuHtml = `
                                    <div class="edit-sku-container d-flex mb-2">
                                        <input type="text" class="form-control me-2" name="no_sku[]" placeholder="SKU">
                                        <input type="number" class="form-control me-2" name="qty[]" value="1" min="1" style="width: 120px;">
                                        <button type="button" class="btn btn-danger remove-edit-sku">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>`;
                                $('#edit-sku-container').append(skuHtml);
                            }

                            $('#editHistorySaleModal').modal('show');
                        }
                    })
                    .fail(function(xhr) {
                        console.error('Edit request failed:', xhr);
                        Swal.fire('Error!',
                            'Failed to load data for editing. Please check console for details.',
                            'error');
                    });
            };

            // Delete function for sales management
            window.deleteHistorySale = function(id) {
                Swal.fire({
                    title: 'Hapus Data?',
                    text: "Data akan dipindahkan ke arsip!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/sales-management/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    Swal.fire('Terhapus!', response.message, 'success');
                                    table.ajax.reload();
                                }
                            },
                            error: function(xhr) {
                                console.error('Delete request failed:', xhr);
                                Swal.fire('Error!',
                                    'Failed to delete record. Please check console for details.',
                                    'error');
                            }
                        });
                    }
                });
            };

            // Restore function for sales management
            window.restoreHistorySale = function(id) {
                Swal.fire({
                    title: 'Pulihkan Data?',
                    text: "Data akan dikembalikan ke daftar aktif.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Pulihkan!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/sales-management/${id}/restore`,
                            type: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    Swal.fire('Dipulihkan!', response.message, 'success');
                                    table.ajax.reload();
                                }
                            },
                            error: function(xhr) {
                                console.error('Restore request failed:', xhr);
                                Swal.fire('Error!',
                                    'Failed to restore record. Please check console for details.',
                                    'error');
                            }
                        });
                    }
                });
            };

            // Force delete function for sales management
            window.forceDeleteHistorySale = function(id) {
                Swal.fire({
                    title: 'Hapus Permanen?',
                    text: "Data akan dihapus permanen dan tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus Permanen!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/sales-management/${id}/force`,
                            type: 'DELETE',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    Swal.fire('Terhapus Permanen!', response.message,
                                        'success');
                                    table.ajax.reload();
                                }
                            },
                            error: function(xhr) {
                                console.error('Force delete request failed:', xhr);
                                Swal.fire('Error!',
                                    'Failed to permanently delete record. Please check console for details.',
                                    'error');
                            }
                        });
                    }
                });
            };

            // Handle edit form submission
            $('#editHistorySaleForm').on('submit', function(e) {
                e.preventDefault();
                const id = $('#edit_history_sale_id').val();
                const formData = new FormData(this);

                // Convert FormData to regular object for easier manipulation
                const data = {};
                for (let [key, value] of formData.entries()) {
                    if (data[key]) {
                        if (Array.isArray(data[key])) {
                            data[key].push(value);
                        } else {
                            data[key] = [data[key], value];
                        }
                    } else {
                        data[key] = value;
                    }
                }

                console.log('Submitting form data:', data); // Debug log

                $.ajax({
                    url: `/sales-management/${id}`,
                    type: 'PUT',
                    data: data,
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#editHistorySaleModal').modal('hide');

                            // Show success message with warning if applicable
                            if (response.warning) {
                                Swal.fire('Berhasil dengan Peringatan!',
                                    response.message + '\n\n' + response.warning,
                                    'warning');
                            } else {
                                Swal.fire('Berhasil!', response.message, 'success');
                            }

                            table.ajax.reload();
                        }
                    },
                    error: function(xhr) {
                        console.error('Update request failed:', xhr);
                        const message = xhr.responseJSON?.message || 'Failed to update record.';
                        Swal.fire('Error!', message, 'error');
                    }
                });
            });

            // Add touch scroll indicator behavior
            const tableWrapper = $('.table-wrapper');
            if (tableWrapper.length && tableWrapper[0].scrollWidth > tableWrapper[0].clientWidth) {
                $('.table-scroll-indicator').show();
            }

            // Hide scroll indicator after user has scrolled
            tableWrapper.on('scroll', function() {
                $('.table-scroll-indicator').fadeOut();
            });
        });
    </script>
@endsection
