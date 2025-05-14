@extends('layouts.master')

@section('title', 'Daftar Produk')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendors/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/datatables/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/datatables/responsive.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
@endsection

@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    @include('components.breadcrumb', ['items' => $items])
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h2 class="mb-0">Daftar Produk</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Data Produk</h5>
                        @can('Product Create')
                            <button type="button" class="btn btn-primary" id="btn-tambah">
                                <i class="ph-duotone ph-plus me-1"></i> Tambah Produk
                            </button>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 mb-2">
                            <select id="filter-category" class="select2 form-control">
                                <option value="">Semua Kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="datatable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No</th>
                                    <th>SKU</th>
                                    <th>Nama Produk</th>
                                    <th>Pack</th>
                                    <th>Kategori</th>
                                    <th>Dibuat</th>
                                    <th style="width: 10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah/Edit -->
    <div class="modal fade" id="modalProduk" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Tambah Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formProduk">
                    <div class="modal-body">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="POST">
                        <input type="hidden" name="id" id="id">

                        <div class="mb-3">
                            <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select class="form-control" id="category_id" name="category_id" required>
                                <option value="">Pilih Kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="error-category_id"></div>
                        </div>

                        <div class="mb-3">
                            <label for="sku" class="form-label">SKU <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="sku" name="sku"
                                placeholder="Masukkan SKU produk" required>
                            <div class="invalid-feedback" id="error-sku"></div>
                        </div>

                        <div class="mb-3">
                            <label for="product_name" class="form-label">Nama Produk <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="product_name" name="product_name"
                                placeholder="Masukkan nama produk" required>
                            <div class="invalid-feedback" id="error-product_name"></div>
                        </div>

                        <div class="mb-3">
                            <label for="pack" class="form-label">Pack <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="pack" name="pack"
                                placeholder="Masukkan pack produk" required>
                            <div class="invalid-feedback" id="error-pack"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btn-submit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendors/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/datatables/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/datatables/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/select2/select2.full.min.js') }}"></script>
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                width: '100%'
            });

            // Initialize DataTable
            let table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ordering: true,
                ajax: {
                    url: "{{ route('products.index') }}",
                    data: function(d) {
                        d.category_id = $('#filter-category').val();
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
                        data: 'product_name',
                        name: 'product_name'
                    },
                    {
                        data: 'pack',
                        name: 'pack'
                    },
                    {
                        data: 'category',
                        name: 'category'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data) {
                            return moment(data).format('DD/MM/YYYY HH:mm:ss');
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let buttons = '';

                            @can('Product Update')
                                buttons +=
                                    `<button class="btn btn-sm btn-info btn-edit me-1" data-id="${row.id}" title="Edit"><i class="ph-duotone ph-pencil"></i></button>`;
                            @endcan

                            @can('Product Delete')
                                buttons +=
                                    `<button class="btn btn-sm btn-danger btn-delete" data-id="${row.id}" title="Hapus"><i class="ph-duotone ph-trash"></i></button>`;
                            @endcan

                            return buttons || 'Tidak ada aksi';
                        }
                    }
                ],
                order: [
                    [5, 'desc']
                ]
            });

            // Filter by category
            $('#filter-category').change(function() {
                table.ajax.reload();
            });

            // Clear form when modal is closed
            $('#modalProduk').on('hidden.bs.modal', function() {
                $('#formProduk').trigger('reset');
                $('#formProduk').find('.is-invalid').removeClass('is-invalid');
                $('input[name="_method"]').val('POST');
                $('#modalLabel').text('Tambah Produk');
            });

            // Show modal for adding new product
            $('#btn-tambah').click(function() {
                $('#modalProduk').modal('show');
            });

            // Show modal for editing product
            $(document).on('click', '.btn-edit', function() {
                let id = $(this).data('id');

                $.ajax({
                    url: "{{ url('products') }}/" + id + "/edit",
                    type: "GET",
                    dataType: "JSON",
                    success: function(response) {
                        $('#id').val(response.data.id);
                        $('#category_id').val(response.data.category_id).trigger('change');
                        $('#sku').val(response.data.sku);
                        $('#product_name').val(response.data.product_name);
                        $('#pack').val(response.data.pack);

                        $('#modalLabel').text('Edit Produk');
                        $('input[name="_method"]').val('PUT');
                        $('#modalProduk').modal('show');
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: xhr.responseJSON.message ||
                                'Terjadi kesalahan. Silahkan coba lagi.'
                        });
                    }
                });
            });

            // Submit form
            $('#formProduk').submit(function(e) {
                e.preventDefault();

                // Clear any previous errors
                $('#formProduk').find('.is-invalid').removeClass('is-invalid');

                let id = $('#id').val();
                let formData = $(this).serialize();
                let method = $('input[name="_method"]').val();
                let url = method === 'POST' ? "{{ route('products.store') }}" : "{{ url('products') }}/" +
                    id;

                $.ajax({
                    url: url,
                    type: "POST",
                    data: formData,
                    dataType: "JSON",
                    success: function(response) {
                        $('#modalProduk').modal('hide');
                        table.ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key).addClass('is-invalid');
                                $('#error-' + key).text(value[0]);
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: xhr.responseJSON.message ||
                                    'Terjadi kesalahan. Silahkan coba lagi.'
                            });
                        }
                    }
                });
            });

            // Delete product
            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Produk akan dihapus dari sistem!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('products') }}/" + id,
                            type: "DELETE",
                            data: {
                                "_token": "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                table.ajax.reload();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: xhr.responseJSON.message ||
                                        'Terjadi kesalahan. Silahkan coba lagi.'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
