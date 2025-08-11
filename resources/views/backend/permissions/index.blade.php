@extends('layouts.main')

@section('title', 'Permissions')
@section('breadcrumb-item', $item)

@section('breadcrumb-item-active', $itemActive)

@section('css')
    <!-- [Page specific CSS] start -->
    <!-- data tables css -->
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/dataTables.bootstrap5.min.css') }}">
    <!-- [Page specific CSS] end -->
@endsection

@section('content')
    <!-- Individual Column Searching (Text Inputs) start -->
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <small>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPermissionModal">
                        Add New Permission
                    </button>
                </small>
            </div>
            <div class="card-body">
                <div class="dt-responsive table-responsive">
                    <table id="permissions-table" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Guard Name</th>
                                <th>Created At</th>
                                <th>Updated At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Individual Column Searching (Text Inputs) end -->

    <!-- modal -->
    <!-- Add Permission Modal -->
    <div class="modal fade" id="addPermissionModal" tabindex="-1" aria-labelledby="addPermissionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPermissionModalLabel">Add New Permission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="permissionForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Permission Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="guard_name" class="form-label">Guard Name</label>
                            <input type="text" class="form-control" id="guard_name" name="guard_name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="saveClosePermissionBtn" class="btn btn-primary">Save and Close</button>
                        <button type="button" id="saveNewPermissionBtn" class="btn btn-primary">Save and Add New
                            Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- end modal -->

    <!-- Edit Permission Modal -->
    <div class="modal fade" id="editPermissionModal" tabindex="-1" aria-labelledby="editPermissionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPermissionModalLabel">Edit Permission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editPermissionForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit-permission-id" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit-name" class="form-label">Permission Name</label>
                            <input type="text" class="form-control" id="edit-name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-guard_name" class="form-label">Guard Name</label>
                            <input type="text" class="form-control" id="edit-guard_name" name="guard_name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="updatePermissionBtn" class="btn btn-primary">Update Permission</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- [Page Specific JS] start -->
    <!-- datatable Js -->
    <script src="{{ URL::asset('build/js/plugins/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/sweetalert2.all.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/dataTables.bootstrap5.min.js') }}"></script>
    
    <script>
        'use strict';
        
        /** SweetAlert2 toast helper */
        function toast(header, message, icon) {
        let swalIcon = 'info';
        if (icon === 'error') swalIcon = 'error';
        else if (icon === 'success') swalIcon = 'success';
        else if (icon === 'warning') swalIcon = 'warning';
        else if (icon === 'info') swalIcon = 'info';
        
        Swal.fire({
            title: header,
            text: message,
            icon: swalIcon,
            timer: 3000,
            timerProgressBar: true,
            toast: true,
            position: 'top-end',
            showConfirmButton: false
        });
        }
        
        $(document).ready(function () {
        const addPermissionModal = document.getElementById('addPermissionModal');
        const nameInput = document.getElementById('name');
        
        /** DataTable init */
        window.permissionsDataTable = $('#permissions-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
            url: '{{ route('permissions.data') }}',
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: function (d) {
                d.hash = btoa(JSON.stringify(d));
            },
            dataSrc: function (json) {
                return json.data;
            }
            },
            select: true,
            columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                render: function (data, type, row, meta) { return meta.row + 1; }
            },
            { data: 'name', name: 'name' },
            { data: 'guard_name', name: 'guard_name' },
            { data: 'created_at', name: 'created_at' },
            { data: 'updated_at', name: 'updated_at' },
            { data: 'actions', name: 'actions' }
            ]
        });
        
        /** Fokus input saat modal add dibuka */
        if (addPermissionModal) {
            addPermissionModal.addEventListener('shown.bs.modal', function () {
            if (nameInput) nameInput.focus();
            });
        }
        
        /** Save helper */
        function savePermission(formData, closeAfterSave) {
            $.ajax({
            url: '{{ route('permissions.store') }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function (response) {
                if (response.status === 'success') {
                window.permissionsDataTable.ajax.reload();
                if (closeAfterSave) {
                    $('#addPermissionModal').modal('hide');
                }
                $('#permissionForm')[0].reset();
                toast('Success', response.message || 'Permission added.', 'success');
                } else {
                toast('Error', response.message || 'Error adding permission', 'error');
                }
            },
            error: function () {
                toast('Error', 'An error occurred while saving the permission', 'error');
            }
            });
        }
        
        /** Edit fetcher */
        function editPermission(permissionId) {
            fetch(`{{ route('permissions.edit', ':id') }}`.replace(':id', permissionId), {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
            })
            .then((response) => {
            if (!response.ok) throw new Error('Permission not found');
            return response.json();
            })
            .then((data) => {
            const permissionData = data.data || data;
            if (permissionData && permissionData.name && permissionData.guard_name) {
                $('#editPermissionModal').modal('show');
                $('#edit-permission-id').val(permissionId);
                $('#edit-name').val(permissionData.name).attr('data-original', permissionData.name);
                $('#edit-guard_name').val(permissionData.guard_name).attr('data-original', permissionData.guard_name);
            } else {
                throw new Error('Invalid permission data received');
            }
            })
            .catch((error) => {
            toast('Error', error.message, 'error');
            });
        }
        
        /** Delete helper */
        function deletePermission(permissionId) {
            fetch(`{{ route('permissions.destroy', '') }}/${permissionId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
            })
            .then((response) => response.json())
            .then((data) => {
            if (data.status === 'success') {
                toast('Success', data.message || 'Permission deleted.', 'success');
                window.permissionsDataTable.ajax.reload();
            } else {
                toast('Error', data.message || 'Failed to delete permission', 'error');
            }
            })
            .catch(() => {
            toast('Error', 'An unexpected error occurred.', 'error');
            });
        }
        
        /** Events */
        // Save & Close
        $('#saveClosePermissionBtn').on('click', function () {
            const formData = new FormData($('#permissionForm')[0]);
            savePermission(formData, true);
        });
        
        // Save & Add New
        $('#saveNewPermissionBtn').on('click', function () {
            const formData = new FormData($('#permissionForm')[0]);
            savePermission(formData, false);
        });
        
        // Edit button (delegation)
        $(document).on('click', '.edit-permission', function () {
            const permissionId = $(this).data('id');
            if (permissionId) {
            editPermission(permissionId);
            } else {
            toast('Error', 'Unable to edit permission. No permission ID found.', 'error');
            }
        });
        
        // Update permission
        $('#updatePermissionBtn').on('click', function () {
            const permissionId = $('#edit-permission-id').val();
            const jsonData = {
            name: $('#edit-name').val(),
            guard_name: $('#edit-guard_name').val()
            };
        
            const isChanged =
            jsonData.name !== $('#edit-name').attr('data-original') ||
            jsonData.guard_name !== $('#edit-guard_name').attr('data-original');
        
            if (!isChanged) {
            $('#editPermissionModal').modal('hide');
            return;
            }
        
            $.ajax({
            url: `{{ route('permissions.update', '') }}/${permissionId}`,
            method: 'PUT',
            data: JSON.stringify({
                data: jsonData,
                hash: btoa(JSON.stringify(jsonData))
            }),
            processData: false,
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            success: function (data) {
                if (data.status === 'success') {
                window.permissionsDataTable.ajax.reload();
                $('#editPermissionModal').modal('hide');
                toast('Success', data.message || 'Permission updated.', 'success');
                } else if (data.status === 'fail' && data.message) {
                $.each(data.message, function (index, value) {
                    toast('Error', value, 'error');
                });
                } else {
                toast('Warning', data.message || 'Update warning.', 'warning');
                }
            },
            error: function () {
                toast('Error', 'An error occurred while updating the permission.', 'error');
            }
            });
        });
        
        // Delete button (delegation) + confirm dialog
        $(document).on('click', '.delete-permission', function () {
            const permissionId = $(this).data('id');
            Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
            if (result.isConfirmed) {
                deletePermission(permissionId);
            }
            });
        });
        });
    </script>
    
@endsection
