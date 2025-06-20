@extends('layouts.main')

@section('title', 'Activity Log')
@section('breadcrumb-item', 'Activity')
@section('breadcrumb-item-active', 'Log')

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

        .help-card {
            background: #e3f2fd;
            border: 1px solid #90caf9;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .help-item {
            margin-bottom: 8px;
        }

        .category-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Help Manual Card -->
        <div class="help-card">
            <h6 class="mb-3">Panduan Penggunaan</h6>
            <div class="help-item">Gunakan filter untuk mencari aktivitas berdasarkan kategori, user, atau tanggal</div>
            <div class="help-item">Data ditampilkan dari yang terbaru hingga yang terlama</div>
            <div class="help-item">Klik tombol Refresh untuk memperbarui data</div>
        </div>

        <!-- Main Content Card -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <h5 class="mb-2 mb-sm-0">Riwayat Aktivitas Sistem</h5>
                    <div class="d-flex flex-wrap">
                        <button id="clear-filters" class="btn btn btn-secondary me-2 mb-2 mb-sm-0">
                            Hapus Filter
                        </button>
                        <button class="btn btn-outline-primary btn-sm mb-2 mb-sm-0" id="refresh-data">
                            Refresh
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="mb-3 filter-section p-3 border rounded bg-light">
                    <button id="filter-toggle-btn" class="btn btn-sm btn-outline-secondary mb-2 w-100 d-md-none"
                        type="button" data-bs-toggle="collapse" data-bs-target="#filterControls">
                        Toggle Filters
                    </button>
                    <div id="filterControls" class="collapse show">
                        <div class="row">
                            <div class="col-md-3 col-sm-12 mb-2">
                                <label for="category-filter" class="form-label filter-label">Filter Kategori</label>
                                <select id="category-filter" class="form-select form-select-sm">
                                    <option value="">Semua Kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category }}">
                                            @switch($category)
                                                @case('auth')
                                                    Login/Logout
                                                @break

                                                @case('user')
                                                    User Management
                                                @break

                                                @case('product')
                                                    Product Management
                                                @break

                                                @case('sale')
                                                    Sales/Transaction
                                                @break

                                                @case('system')
                                                    System Activity
                                                @break

                                                @default
                                                    {{ ucfirst($category) }}
                                            @endswitch
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 col-sm-12 mb-2">
                                <label for="user-filter" class="form-label filter-label">Filter User</label>
                                <select id="user-filter" class="form-select form-select-sm">
                                    <option value="">Semua User</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 col-sm-12 mb-2">
                                <label for="date-filter" class="form-label filter-label">Filter Tanggal</label>
                                <input type="date" class="form-control form-control-sm" id="date-filter">
                            </div>
                            <div class="col-md-2 col-sm-12 mb-2">
                                <label class="form-label filter-label">&nbsp;</label>
                                <button class="btn btn-primary btn-sm w-100 d-block" id="apply-filters">
                                    Cari
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Simple DataTable -->
                <div class="table-responsive">
                    <table id="activities-table" class="table table-striped table-hover" style="width:100%">
                        <thead class="table-dark">
                            <tr>
                                <th>Tanggal & Waktu</th>
                                <th>Kategori</th>
                                <th>Aktivitas</th>
                                <th>Keterangan</th>
                                <th>User</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <!-- Legend -->
                <div class="mt-3">
                    <small class="text-muted">
                        <strong>Keterangan:</strong>
                        <span class="badge bg-warning text-dark ms-2">Auth</span>
                        <span class="badge bg-success ms-1">User</span>
                        <span class="badge bg-info ms-1">Product</span>
                        <span class="badge bg-primary ms-1">Sale</span>
                        <span class="badge bg-secondary ms-1">System</span>
                    </small>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
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

    <!-- SweetAlert2 -->
    <script src="{{ URL::asset('build/js/plugins/sweetalert2.all.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            let table = $('#activities-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('activity') }}',
                    data: function(d) {
                        d.category = $('#category-filter').val();
                        d.user_id = $('#user-filter').val();
                        d.date = $('#date-filter').val();
                    }
                },
                columns: [{
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'category',
                        name: 'category',
                        orderable: false
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },
                    {
                        data: 'note',
                        name: 'note',
                        orderable: false
                    },
                    {
                        data: 'user',
                        name: 'user.name'
                    }
                ],
                order: [
                    [0, 'desc']
                ],
                pageLength: 25,
                responsive: true,
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'copy',
                        text: 'Salin',
                        className: 'btn btn-secondary'
                    },
                    {
                        extend: 'excel',
                        text: 'Excel',
                        className: 'btn btn-success'
                    },
                    {
                        extend: 'print',
                        text: 'Cetak',
                        className: 'btn btn-info'
                    }
                ],
                language: {
                    processing: 'Memuat data...',
                    emptyTable: 'Belum ada aktivitas yang tercatat',
                    zeroRecords: 'Tidak ada aktivitas yang sesuai dengan filter',
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ aktivitas",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 aktivitas",
                    infoFiltered: "(disaring dari _MAX_ total aktivitas)",
                    lengthMenu: "Tampilkan _MENU_ aktivitas per halaman",
                    search: "Cari:",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                }
            });

            // Apply filters when dropdown values change
            $('#category-filter, #user-filter, #date-filter').on('change', function() {
                table.ajax.reload();
            });

            // Clear filters function
            $('#clear-filters').on('click', function() {
                $('#category-filter').val('');
                $('#user-filter').val('');
                $('#date-filter').val('');
                table.search('').columns().search('').draw();
            });

            // Filter handlers
            $('#apply-filters').click(function() {
                table.ajax.reload();
            });

            $('#refresh-data').click(function() {
                table.ajax.reload();
            });
        });
    </script>
@endsection
