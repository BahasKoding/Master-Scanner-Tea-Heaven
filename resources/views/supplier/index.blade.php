@extends('layouts.main')

@section('title', 'Supplier Management')
@section('breadcrumb-item', 'Supplier')
@section('breadcrumb-item-active', 'List')

@section('css')
    <!-- data tables css -->
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/dataTables.bootstrap5.min.css') }}">
    <style>
        .form-select {
            width: 100%;
            padding: 0.375rem 0.75rem;
        }
    </style>
@endsection

@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">
        <!-- DOM/Jquery table start -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Supplier Management</h5>
                    <div class="float-end">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#addSupplierModal">
                            Add New Supplier
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="dt-responsive table-responsive">
                        <table id="supplier-table" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Category</th>
                                    <th>Code</th>
                                    <th>Product Name</th>
                                    <th>Unit</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->

    <!-- Add Supplier Modal -->
    <div class="modal fade" id="addSupplierModal" tabindex="-1" aria-labelledby="addSupplierModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSupplierModalLabel">Add New Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addSupplierForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category" required>
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
                            <label for="code" class="form-label">Code</label>
                            <input type="text" class="form-control" id="code" name="code" required>
                        </div>
                        <div class="mb-3">
                            <label for="product_name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="product_name" name="product_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="unit" class="form-label">Unit</label>
                            <select class="form-select" id="unit" name="unit" required>
                                <option value="">Select Unit</option>
                                <option value="pcs">pcs</option>
                                <option value="kg">kg</option>
                                <option value="gram">gram</option>
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
    <div class="modal fade" id="editSupplierModal" tabindex="-1" aria-labelledby="editSupplierModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSupplierModalLabel">Edit Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editSupplierForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_id" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_category" class="form-label">Category</label>
                            <select class="form-select" id="edit_category" name="category" required>
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
                            <label for="edit_code" class="form-label">Code</label>
                            <input type="text" class="form-control" id="edit_code" name="code" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_product_name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="edit_product_name" name="product_name"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_unit" class="form-label">Unit</label>
                            <select class="form-select" id="edit_unit" name="unit" required>
                                <option value="">Select Unit</option>
                                <option value="pcs">pcs</option>
                                <option value="kg">kg</option>
                                <option value="gram">gram</option>
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
    <!-- datatable Js -->
    <script src="{{ URL::asset('build/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/dataTables.bootstrap5.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#supplier-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('suppliers.index') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
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
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-warning edit-btn" data-id="${row.id}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="${row.id}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ]
            });

            // Add Supplier Form Submit
            $('#addSupplierForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('suppliers.store') }}",
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#addSupplierModal').modal('hide');
                        $('#addSupplierForm')[0].reset();
                        table.ajax.reload();
                        alert('Supplier added successfully!');
                    },
                    error: function(error) {
                        alert('Error adding supplier!');
                        console.log(error);
                    }
                });
            });

            // Edit Supplier Button Click
            $('#supplier-table').on('click', '.edit-btn', function() {
                var id = $(this).data('id');
                $.get("{{ route('suppliers.index') }}/" + id + "/edit", function(data) {
                    $('#edit_id').val(data.id);
                    $('#edit_category').val(data.category);
                    $('#edit_code').val(data.code);
                    $('#edit_product_name').val(data.product_name);
                    $('#edit_unit').val(data.unit);
                    $('#editSupplierModal').modal('show');
                });
            });

            // Update Supplier Form Submit
            $('#editSupplierForm').on('submit', function(e) {
                e.preventDefault();
                var id = $('#edit_id').val();
                $.ajax({
                    url: "{{ route('suppliers.index') }}/" + id,
                    method: 'PUT',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#editSupplierModal').modal('hide');
                        table.ajax.reload();
                        alert('Supplier updated successfully!');
                    },
                    error: function(error) {
                        alert('Error updating supplier!');
                        console.log(error);
                    }
                });
            });

            // Delete Supplier Button Click
            $('#supplier-table').on('click', '.delete-btn', function() {
                if (confirm('Are you sure you want to delete this supplier?')) {
                    var id = $(this).data('id');
                    $.ajax({
                        url: "{{ route('suppliers.index') }}/" + id,
                        method: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            table.ajax.reload();
                            alert('Supplier deleted successfully!');
                        },
                        error: function(error) {
                            alert('Error deleting supplier!');
                            console.log(error);
                        }
                    });
                }
            });
        });
    </script>
@endsection
