@extends('layouts.main')

@section('title', 'Roles')
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
                @can('Roles Create')
                    <small>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                            Add New Role
                        </button>
                    </small>
                @endcan
            </div>
            <div class="card-body">
                <div class="dt-responsive table-responsive">
                    <table id="roles-table" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Guard Name</th>
                                <th>Created At</th>
                                <th>Updated At</th>
                                @canany(['Roles Update', 'Roles Delete'])
                                    <th>Actions</th>
                                @endcanany
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Individual Column Searching (Text Inputs) end -->

    @can('Roles Create')
        <!-- modal -->
        <!-- Add Role Modal -->
        <div class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="addRoleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addRoleModalLabel">Add New Role</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="roleForm" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="name" class="form-label">Role Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="guard_name" class="form-label">Guard Name</label>
                                <input type="text" class="form-control" id="guard_name" name="guard_name" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" id="saveRoleBtn" class="btn btn-primary">Save Role</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- end modal -->
    @endcan

    @can('Roles Update')
        <!-- Edit Role Modal -->
        <div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editRoleModalLabel">Edit Role</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editRoleForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="edit-role-id" name="id">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit-name" class="form-label">Role Name</label>
                                <input type="text" class="form-control" id="edit-name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit-guard_name" class="form-label">Guard Name</label>
                                <input type="text" class="form-control" id="edit-guard_name" name="guard_name" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" id="updateRoleBtn" class="btn btn-primary">Update Role</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    <!-- Set Permissions Modal -->
    <div class="modal fade" id="setPermissionsModal" tabindex="-1" aria-labelledby="setPermissionsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Content will be dynamically populated -->
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- [Page Specific JS] start -->
    <!-- datatable Js -->
    <script src="{{ URL::asset('build/js/plugins/jquery-3.6.0.min.js') }}"></script>
    <!-- SweetAlert2 -->
    <script src="{{ URL::asset('build/js/plugins/sweetalert2.all.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/dataTables.bootstrap5.min.js') }}"></script>
    <script>
        function toast(header, message, icon) {
            // Map jQuery Toast icons to SweetAlert2 icons
            let swalIcon = icon;
            if (icon === 'error') swalIcon = 'error';
            else if (icon === 'success') swalIcon = 'success';
            else if (icon === 'warning') swalIcon = 'warning';
            else if (icon === 'info') swalIcon = 'info';
            else swalIcon = 'info';

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
        // Define editRole function in the global scope
        function editRole(roleId) {
            fetch(`{{ route('roles.edit', ':id') }}`.replace(':id', roleId), {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Role not found');
                    }
                    return response.json();
                })
                .then(data => {
                    // Check if data is directly available or nested in a data property
                    const roleData = data.data || data;
                    if (roleData && roleData.name && roleData.guard_name) {
                        $('#editRoleModal').modal('show');
                        $('#edit-role-id').val(roleId); // Use the roleId passed to the function
                        $('#edit-name').val(roleData.name);
                        $('#edit-guard_name').val(roleData.guard_name);

                        // Add data attributes to store original values
                        $('#edit-name').attr('data-original', roleData.name);
                        $('#edit-guard_name').attr('data-original', roleData.guard_name);
                    } else {
                        throw new Error('Invalid role data received');
                    }
                })
                .catch(error => {
                    toast('Error', error.message, 'error');
                });
        }

        // Move savePermissions function to global scope
        function savePermissions(roleId) {
            let selectedPermissions = Array.from(document.querySelectorAll('#setPermissionsForm input:checked')).map(el =>
                el.value);

            fetch(`{{ route('roles.givePermissionTo', ':role') }}`.replace(':role', roleId), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        permissions: selectedPermissions
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        toast('Success', 'Permissions updated successfully', 'success');
                        $('#setPermissionsModal').modal('hide');
                    } else {
                        toast('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    toast('Error', 'Failed to update permissions', 'error');
                });
        }

        $(document).ready(function() {
            var addRoleModal = document.getElementById('addRoleModal');
            var nameInput = document.getElementById('name');

            // Initialize DataTable
            window.rolesDataTable = $('#roles-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('roles.data') }}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: function(d) {
                        d.hash = btoa(JSON.stringify(d));
                    },
                    // Add this option to prevent caching
                    dataSrc: function(json) {
                        return json.data;
                    }
                },
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'guard_name',
                        name: 'guard_name'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                            <button class="btn btn-primary btn-sm edit-role" data-id="${row.id}">Edit</button>
                            <button class="btn btn-danger btn-sm delete-role" data-id="${row.id}">Delete</button>
                            <button class="btn btn-info btn-sm set-permissions" data-id="${row.id}">Set Permissions</button>
                        `;
                        }
                    }
                ]
            });

            if (addRoleModal) {
                addRoleModal.addEventListener('shown.bs.modal', function() {
                    nameInput.focus();
                });
            }

            // Add this new click event handler for the save button
            $('#saveRoleBtn').on('click', function() {
                var formData = new FormData($('#roleForm')[0]);

                $.ajax({
                    url: '{{ route('roles.store') }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            // Refresh DataTable
                            window.rolesDataTable.ajax.reload();

                            // Close modal and reset form
                            $('#addRoleModal').modal('hide');
                            $('#roleForm')[0].reset();
                        } else {
                            toast('Error', 'Error adding role'.response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        toast('Error', response.message, 'error');
                    }
                });
            });



            // Use event delegation for edit buttons
            $(document).on('click', '.edit-role', function() {
                var roleId = $(this).data('id');
                console.log('Edit button clicked, roleId:', roleId);
                if (roleId) {
                    editRole(roleId);
                } else {
                    console.error('No role ID found');
                    toast('Error', 'Unable to edit role. No role ID found.', 'error');
                }
            });

            // Update role functionality
            $('#updateRoleBtn').on('click', function() {
                var roleId = $('#edit-role-id').val();
                var formData = new FormData($('#editRoleForm')[0]);
                var jsonData = {
                    name: $('#edit-name').val(),
                    guard_name: $('#edit-guard_name').val()
                };

                // Only proceed if there are changes
                if (jsonData.name !== $('#edit-name').attr('data-original') ||
                    jsonData.guard_name !== $('#edit-guard_name').attr('data-original')) {
                    $.ajax({
                        url: `{{ route('roles.update', '') }}/${roleId}`,
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
                        success: function(data) {

                            if (data.status === 'success') {
                                window.rolesDataTable.ajax.reload();
                                $('#editRoleModal').modal('hide');
                                toast('Success', data.message, 'success');
                            } else if (data.status === 'fail') {
                                var dataEach = data.message;
                                $.each(dataEach, function(index, value) {
                                    toast('error', value, 'error');
                                });
                            } else {
                                toast('Warning', data.message, 'warning');
                            }
                        },
                        error: function(xhr, status, error) {

                        }
                    });
                } else {
                    $('#editRoleModal').modal('hide');
                }
            });

            // Add this new event listener for delete buttons
            $(document).on('click', '.delete-role', function() {
                var roleId = $(this).data('id');
                if (confirm('Are you sure you want to delete this role?')) {
                    deleteRole(roleId);
                }
            });

            // Add this new function to handle role deletion
            function deleteRole(roleId) {
                fetch(`{{ route('roles.destroy', '') }}/${roleId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status == 'success') {
                            toast('Success', data.message, 'success');
                            // Refresh DataTable without reloading the page
                            window.rolesDataTable.ajax.reload();
                        } else {
                            toast('Error', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        toast('Error', error, 'error');
                    });
            }

            // Add this new event listener for set permissions buttons
            $(document).on('click', '.set-permissions', function() {
                var roleId = $(this).data('id');
                showSetPermissionsModal(roleId);
            });

            function showSetPermissionsModal(roleId) {
                const url = '{{ route('roles.roleHasPermission', ['id' => ':id']) }}'.replace(':id', roleId);
                fetch(url, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Failed to fetch permissions');
                        }
                        return response.json();
                    })
                    .then(data => {
                        const checkboxes = data.permissions.map(permission => `
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="${permission.id}" 
                                   id="permission-${permission.id}" 
                                   ${data.rolePermissions.includes(permission.id) ? 'checked' : ''}>
                            <label class="form-check-label" for="permission-${permission.id}">
                                ${permission.name}
                            </label>
                        </div>
                    `).join('');

                        const modalContent = `
                        <div class="modal-header">
                            <h5 class="modal-title">Set Permissions for Role: ${data.roleName}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="setPermissionsForm">
                                ${checkboxes}
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" onclick="savePermissions(${roleId})">Save Permissions</button>
                        </div>
                    `;

                        $('#setPermissionsModal .modal-content').html(modalContent);
                        $('#setPermissionsModal').modal('show');
                    })
                    .catch(error => {
                        toast('Error', error.message, 'error');
                    });
            }

            // ... (rest of the code remains unchanged)
        });
    </script>
@endsection
