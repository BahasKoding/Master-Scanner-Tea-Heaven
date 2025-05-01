@extends('layouts.main')

@section('title', 'Supplier Management')
@section('breadcrumb-item', 'Supplier')

@section('css')
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- [Page specific CSS] start -->
    <!-- data tables css -->
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/datatables/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/datatables/buttons.bootstrap5.min.css') }}">
    <!-- [Page specific CSS] end -->
    <style>
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
        <!-- Supplier Table start -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Supplier List</h5>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#addSupplierModal">
                            <i class="fas fa-plus"></i> Add New Supplier
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="dt-responsive table-responsive">
                        <table id="supplier-table" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Category</th>
                                    <th>Code</th>
                                    <th>Product Name</th>
                                    <th>Unit</th>
                                    <th>Actions</th>
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
        <!-- Supplier Table end -->
    </div>
    <!-- [ Main Content ] end -->

    <!-- Add Supplier Modal -->
    <div class="modal fade" id="addSupplierModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addSupplierForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" name="category" required>
                                <option value="">Select Category</option>
                                <option value="CRAFTED TEAS">CRAFTED TEAS</option>
                                <option value="LOOSE LEAF TEA">LOOSE LEAF TEA</option>
                                <option value="PURE TISANE">PURE TISANE</option>
                                <option value="DRIED FRUIT & SPICES">DRIED FRUIT & SPICES</option>
                                <option value="PURE POWDER">PURE POWDER</option>
                                <option value="SWEET POWDER">SWEET POWDER</option>
                                <option value="LATTE POWDER">LATTE POWDER</option>
                                <option value="JAPANESE TEA BAGS">JAPANESE TEA BAGS</option>
                                <option value="TEAWARE">TEAWARE</option>
                                <option value="ESSENCE">ESSENCE</option>
                                <option value="PACKAGING- TEA HEAVEN POUCH">PACKAGING- TEA HEAVEN POUCH</option>
                                <option value="PACKAGING- FOIL FLAT BOTTOM">PACKAGING- FOIL FLAT BOTTOM</option>
                                <option value="PACKAGING- FOIL GUSSET / SACHET">PACKAGING- FOIL GUSSET / SACHET</option>
                                <option value="PACKAGING- TRANSMETZ ZIPPER">PACKAGING- TRANSMETZ ZIPPER</option>
                                <option value="PACKAGING- VACCUM">PACKAGING- VACCUM</option>
                                <option value="PACKAGING- TIN CANISTER">PACKAGING- TIN CANISTER</option>
                                <option value="BOX">BOX</option>
                                <option value="PRINTING & LABELLING">PRINTING & LABELLING</option>
                                <option value="OUTER PACKAGING">OUTER PACKAGING</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="code" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="product_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Unit <span class="text-danger">*</span></label>
                            <select class="form-select" name="unit" required>
                                <option value="">Select Unit</option>
                                <option value="PCS">PCS</option>
                                <option value="GRAM">GRAM</option>
                                <option value="KG">KG</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Supplier Modal -->
    <div class="modal fade" id="editSupplierModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editSupplierForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_supplier_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" name="category" required>
                                <option value="">Select Category</option>
                                <option value="CRAFTED TEAS">CRAFTED TEAS</option>
                                <option value="LOOSE LEAF TEA">LOOSE LEAF TEA</option>
                                <option value="PURE TISANE">PURE TISANE</option>
                                <option value="DRIED FRUIT & SPICES">DRIED FRUIT & SPICES</option>
                                <option value="PURE POWDER">PURE POWDER</option>
                                <option value="SWEET POWDER">SWEET POWDER</option>
                                <option value="LATTE POWDER">LATTE POWDER</option>
                                <option value="JAPANESE TEA BAGS">JAPANESE TEA BAGS</option>
                                <option value="TEAWARE">TEAWARE</option>
                                <option value="ESSENCE">ESSENCE</option>
                                <option value="PACKAGING- TEA HEAVEN POUCH">PACKAGING- TEA HEAVEN POUCH</option>
                                <option value="PACKAGING- FOIL FLAT BOTTOM">PACKAGING- FOIL FLAT BOTTOM</option>
                                <option value="PACKAGING- FOIL GUSSET / SACHET">PACKAGING- FOIL GUSSET / SACHET</option>
                                <option value="PACKAGING- TRANSMETZ ZIPPER">PACKAGING- TRANSMETZ ZIPPER</option>
                                <option value="PACKAGING- VACCUM">PACKAGING- VACCUM</option>
                                <option value="PACKAGING- TIN CANISTER">PACKAGING- TIN CANISTER</option>
                                <option value="BOX">BOX</option>
                                <option value="PRINTING & LABELLING">PRINTING & LABELLING</option>
                                <option value="OUTER PACKAGING">OUTER PACKAGING</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="code" id="edit_code" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="product_name" id="edit_product_name"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Unit <span class="text-danger">*</span></label>
                            <select class="form-select" name="unit" required>
                                <option value="">Select Unit</option>
                                <option value="PCS">PCS</option>
                                <option value="GRAM">GRAM</option>
                                <option value="KG">KG</option>
                            </select>
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
            var table = $('#supplier-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('suppliers.index') }}",
                    type: "GET"
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'category',
                        name: 'category'
                    },
                    {
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'product_name',
                        name: 'product_name'
                    },
                    {
                        data: 'unit',
                        name: 'unit'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <button type="button" class="btn btn-sm btn-warning edit-btn" data-id="${row.id}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="${row.id}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            `;
                        }
                    }
                ],
                order: [
                    [5, 'desc']
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
                ]
            });

            // Add Supplier Form Submit
            $('#addSupplierForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var submitButton = form.find('button[type="submit"]');

                // Disable submit button to prevent double submission
                submitButton.prop('disabled', true);

                var formData = new FormData(this);

                $.ajax({
                    url: "{{ route('suppliers.store') }}",
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (data.success) {
                            // Reset form first
                            form[0].reset();
                            submitButton.prop('disabled', false);

                            // Properly hide modal and remove backdrop
                            $('#addSupplierModal').modal('hide');
                            $('body').removeClass('modal-open');
                            $('.modal-backdrop').remove();

                            // Reload table
                            table.ajax.reload(null, false);

                            // Show success message
                            Swal.fire({
                                title: 'Success!',
                                text: data.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
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
                                title: 'Please Check Your Input',
                                html: errorMessages.join('<br>'),
                                icon: 'warning',
                                confirmButtonText: 'I\'ll Fix It',
                                confirmButtonColor: '#3085d6'
                            });
                        } else {
                            // Other errors
                            Swal.fire({
                                title: 'Oops...',
                                text: xhr.responseJSON?.message ||
                                    'Something went wrong with the request.',
                                icon: 'error',
                                confirmButtonText: 'Try Again',
                                confirmButtonColor: '#3085d6'
                            });
                        }
                    },
                    complete: function() {
                        // Always ensure the UI is restored
                        submitButton.prop('disabled', false);
                        $('body').removeClass('modal-open');
                        $('.modal-backdrop').remove();
                    }
                });
            });

            // Edit Button Click
            $(document).on('click', '.edit-btn', function() {
                var id = $(this).data('id');
                console.log('Edit button clicked for ID:', id);

                // Show loading state
                Swal.fire({
                    title: 'Loading...',
                    text: 'Fetching supplier data',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: "{{ url('suppliers') }}/" + id + "/edit",
                    method: 'GET',
                    success: function(response) {
                        console.log('Server response:', response);
                        Swal.close();

                        if (response.success) {
                            const data = response.data;
                            console.log('Supplier data:', data);

                            // Set hidden ID
                            $('#edit_supplier_id').val(data.id);

                            // Set category
                            $('#editSupplierModal select[name="category"]').val(data.category);

                            // Set code
                            $('#edit_code').val(data.code);

                            // Set product name
                            $('#edit_product_name').val(data.product_name);

                            // Set unit
                            $('#editSupplierModal select[name="unit"]').val(data.unit);

                            // Log the current values
                            console.log('Form values after setting:', {
                                id: $('#edit_supplier_id').val(),
                                category: $(
                                        '#editSupplierModal select[name="category"]')
                                    .val(),
                                code: $('#edit_code').val(),
                                product_name: $('#edit_product_name').val(),
                                unit: $('#editSupplierModal select[name="unit"]').val()
                            });

                            // Show the modal
                            var editModal = new bootstrap.Modal(document.getElementById(
                                'editSupplierModal'));
                            editModal.show();

                            // Double check values after modal is shown
                            setTimeout(() => {
                                console.log('Values after modal shown:', {
                                    id: $('#edit_supplier_id').val(),
                                    category: $(
                                        '#editSupplierModal select[name="category"]'
                                    ).val(),
                                    code: $('#edit_code').val(),
                                    product_name: $('#edit_product_name').val(),
                                    unit: $(
                                            '#editSupplierModal select[name="unit"]'
                                            )
                                        .val()
                                });
                            }, 500);
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.message ||
                                    'Failed to fetch supplier data',
                                icon: 'error'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', {
                            xhr: xhr,
                            status: status,
                            error: error
                        });
                        Swal.fire({
                            title: 'Error',
                            text: 'Failed to fetch supplier data. Please try again.',
                            icon: 'error'
                        });
                    }
                });
            });

            // Edit Supplier Form Submit
            $('#editSupplierForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var submitButton = form.find('button[type="submit"]');
                var id = $('#edit_supplier_id').val();

                // Disable submit button to prevent double submission
                submitButton.prop('disabled', true);

                // Show loading state
                Swal.fire({
                    title: 'Updating...',
                    text: 'Saving supplier data',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                var formData = new FormData(this);
                formData.append('_method', 'PUT');

                $.ajax({
                    url: `/suppliers/${id}`,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        if (data.success) {
                            // Reset form and close modal
                            form[0].reset();
                            $('#editSupplierModal').modal('hide');

                            // Reload table
                            table.ajax.reload(null, false);

                            // Show success message
                            Swal.fire({
                                title: 'Success!',
                                text: data.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });
                        }
                    },
                    error: function(xhr) {
                        submitButton.prop('disabled', false);

                        if (xhr.status === 422) {
                            // Validation errors
                            const errors = xhr.responseJSON.errors;
                            const errorMessages = Object.values(errors).flat();

                            Swal.fire({
                                title: 'Please Check Your Input',
                                html: errorMessages.join('<br>'),
                                icon: 'warning',
                                confirmButtonText: 'I\'ll Fix It',
                                confirmButtonColor: '#3085d6'
                            });
                        } else {
                            // Other errors
                            Swal.fire({
                                title: 'Update Failed',
                                text: xhr.responseJSON?.message ||
                                    'Could not update the supplier at this time.',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#3085d6'
                            });
                        }
                    },
                    complete: function() {
                        submitButton.prop('disabled', false);
                    }
                });
            });

            // Delete Button Click
            $(document).on('click', '.delete-btn', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Delete Confirmation',
                    text: "Are you sure you want to remove this supplier? This action cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, keep it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/suppliers/${id}`,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            success: function(data) {
                                if (data.success) {
                                    table.ajax.reload();
                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: data.message,
                                        icon: 'success',
                                        timer: 1500,
                                        showConfirmButton: false,
                                        toast: true,
                                        position: 'top-end'
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: 'Delete Failed',
                                    text: xhr.responseJSON?.message ||
                                        'Could not delete the supplier at this time.',
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#3085d6'
                                });
                            }
                        });
                    }
                });
            });

            // Clean up modal when it's hidden
            $('#editSupplierModal').on('hidden.bs.modal', function() {
                console.log('Modal hidden - cleaning up');
                $(this).find('form')[0].reset();
            });

            // Prevent form reset when modal is shown
            $('#editSupplierModal').on('show.bs.modal', function() {
                console.log('Modal showing - preventing auto reset');
                return true;
            });
        });
    </script>
@endsection
