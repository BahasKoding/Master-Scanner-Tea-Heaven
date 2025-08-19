@extends('layouts.main')

@section('title', 'Stock Opname')
@section('breadcrumb-item', 'Stock Opname')

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
        .status-badge {
            font-size: 0.85rem;
            padding: 0.4rem 0.8rem;
        }
        
        .variance-positive {
            color: #28a745;
            font-weight: bold;
        }
        
        .variance-negative {
            color: #dc3545;
            font-weight: bold;
        }
        
        .variance-zero {
            color: #6c757d;
            font-weight: bold;
        }
        
        .form-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
@endsection

@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">
        <!-- Stock Opname Table start -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Daftar Stock Opname</h5>
                        <div>
                            <button id="clear-filters" class="btn btn-secondary">
                                <i class="fas fa-filter"></i> Hapus Filter
                            </button>
                            @can('create-stock-opname')
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addStockOpnameModal">
                                <i class="fas fa-plus"></i> Buat Stock Opname Baru
                            </button>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="mb-3 p-3 border rounded bg-light">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="filter-type" class="form-label small">Jenis Opname</label>
                                <select class="form-control form-control-sm" name="filter-type" id="filter-type">
                                    <option value="">Semua Jenis</option>
                                    <option value="bahan_baku">Bahan Baku</option>
                                    <option value="finished_goods">Finished Goods</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filter-status" class="form-label small">Status</label>
                                <select class="form-control form-control-sm" name="filter-status" id="filter-status">
                                    <option value="">Semua Status</option>
                                    <option value="draft">Draft</option>
                                    <option value="in_progress">Sedang Berlangsung</option>
                                    <option value="completed">Selesai</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filter-tanggal-dari" class="form-label small">Tanggal Dari</label>
                                <input type="date" id="filter-tanggal-dari" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="filter-tanggal-sampai" class="form-label small">Tanggal Sampai</label>
                                <input type="date" id="filter-tanggal-sampai" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                    <!-- End Filter Section -->

                    <div class="dt-responsive table-responsive">
                        <table id="stock-opname-table" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Jenis Opname</th>
                                    <th>Tanggal Opname</th>
                                    <th>Status</th>
                                    <th>Total Items</th>
                                    <th>Dibuat Oleh</th>
                                    <th>Tanggal Dibuat</th>
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
        <!-- Stock Opname Table end -->
    </div>
    <!-- [ Main Content ] end -->

    <!-- Add Stock Opname Modal -->
    <div class="modal fade" id="addStockOpnameModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Buat Stock Opname Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addStockOpnameForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Jenis Stock Opname <span class="text-danger">*</span></label>
                            <select class="form-control" name="type" id="add-type" required>
                                <option value="">Pilih Jenis Opname</option>
                                <option value="bahan_baku">Bahan Baku</option>
                                <option value="finished_goods">Finished Goods</option>
                            </select>
                            <small class="form-text text-muted">Pilih jenis inventory yang akan dilakukan stock opname</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal Opname <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal_opname" id="add-tanggal" 
                                   value="{{ date('Y-m-d') }}" required>
                            <small class="form-text text-muted">Tanggal pelaksanaan stock opname</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" name="notes" id="add-notes" rows="3" 
                                      placeholder="Catatan tambahan untuk stock opname ini..."></textarea>
                        </div>
                        
                        <!-- Info Box -->
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Informasi:</h6>
                            <ul class="mb-0 small">
                                <li>Sistem akan otomatis mengambil data stok dari sistem</li>
                                <li>Anda akan diarahkan ke halaman input stok fisik</li>
                                <li>Selisih akan dihitung otomatis: Stok Fisik - Stok Sistem</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Buat Stock Opname</button>
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

    <!-- SweetAlert2 -->
    <script src="{{ URL::asset('build/js/plugins/sweetalert2.all.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
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

            // Initialize Choices.js for add form
            var addTypeChoices;
            
            function initializeChoices() {
                if (addTypeChoices) {
                    addTypeChoices.destroy();
                }
                addTypeChoices = new Choices('#add-type', {
                    searchEnabled: true,
                    searchPlaceholderValue: "Cari jenis opname",
                    itemSelectText: '',
                    placeholder: true,
                    placeholderValue: "Pilih jenis opname",
                    allowHTML: false
                });
            }
            
            // Initialize on page load
            initializeChoices();

            // Initialize DataTable
            var table = $('#stock-opname-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('stock-opname.data') }}",
                    type: "GET",
                    data: function(d) {
                        d.type = $('#filter-type').val();
                        d.status = $('#filter-status').val();
                        d.tanggal_dari = $('#filter-tanggal-dari').val();
                        d.tanggal_sampai = $('#filter-tanggal-sampai').val();
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTable AJAX Error:', xhr.responseText);
                        alert('Error loading data: ' + xhr.status + ' - ' + xhr.statusText);
                    }
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'tanggal_opname',
                        name: 'tanggal_opname'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false
                    },
                    {
                        data: 'total_items',
                        name: 'total_items',
                        orderable: false
                    },
                    {
                        data: 'creator_name',
                        name: 'creator.name'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [[6, 'desc']], // Order by created_at instead of DT_RowIndex
                pageLength: 25,
                dom: 'Bfrtip',
                buttons: [
                    {
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
                    processing: "Memproses...",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
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
            $('#filter-type, #filter-status').on('change', function() {
                table.draw();
            });

            // Apply filters when date inputs change
            $('#filter-tanggal-dari, #filter-tanggal-sampai').on('change', function() {
                table.draw();
            });

            // Clear filters button
            $('#clear-filters').on('click', function() {
                $('#filter-type, #filter-status').val('');
                $('#filter-tanggal-dari, #filter-tanggal-sampai').val('');
                table.draw();
            });

            // Add Stock Opname Form Submit
            $('#addStockOpnameForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var submitButton = form.find('button[type="submit"]');

                // Disable submit button to prevent double submission
                submitButton.prop('disabled', true);

                var formData = new FormData(this);

                $.ajax({
                    url: "{{ route('stock-opname.store') }}",
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (data.success) {
                            // Reset form and choices
                            form[0].reset();
                            addTypeChoices.setChoiceByValue('');
                            submitButton.prop('disabled', false);

                            // Hide modal properly
                            $('#addStockOpnameModal').modal('hide');

                            // Show success message and redirect
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Stock Opname berhasil dibuat. Anda akan diarahkan ke halaman input stok fisik.',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                // Redirect to show page
                                window.location.href = data.redirect_url || "{{ route('stock-opname.index') }}";
                            });
                        }
                    },
                    error: function(xhr) {
                        // Re-enable submit button on error
                        submitButton.prop('disabled', false);

                        if (xhr.status === 422) {
                            // Validation errors
                            const errors = xhr.responseJSON.errors;
                            const errorMessages = Object.values(errors).flat();

                            Swal.fire({
                                title: 'Mohon Periksa Input Anda',
                                html: errorMessages.join('<br>'),
                                icon: 'warning',
                                confirmButtonText: 'Saya Mengerti',
                                confirmButtonColor: '#3085d6'
                            });
                        } else {
                            // Other errors
                            Swal.fire({
                                title: 'Oops...',
                                text: xhr.responseJSON?.message || 'Terjadi kesalahan pada permintaan.',
                                icon: 'error',
                                confirmButtonText: 'Coba Lagi',
                                confirmButtonColor: '#3085d6'
                            });
                        }
                    }
                });
            });

            // Clean up modal when it's hidden
            $('#addStockOpnameModal').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
                // Don't destroy choices, just reset the value
                if (addTypeChoices) {
                    addTypeChoices.setChoiceByValue('');
                }
                $(this).find('button').prop('disabled', false);
            });

            // Reinitialize choices when modal is shown
            $('#addStockOpnameModal').on('shown.bs.modal', function() {
                initializeChoices();
            });
        });
    </script>
@endsection
