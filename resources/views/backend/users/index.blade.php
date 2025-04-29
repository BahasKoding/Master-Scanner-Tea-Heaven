@extends('layouts.main')

@section('title', 'Users')
@section('breadcrumb-item', $item)
@section('breadcrumb-item-active', $itemActive)

@section('css')
<!-- [Page specific CSS] start -->
<!-- data tables css -->
<link rel="stylesheet" href="{{ URL::asset('build/css/plugins/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css">
<!-- [Page specific CSS] end -->
@endsection

@section('content')
<div class="col-sm-12">
    <div class="card">
        <div class="card-header">
            <small>
                @if(auth()->user()->getRoleNames()->contains('Super Admin') || auth()->user()->getRoleNames()->contains('Admin'))
                @can('Users Create')
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    Add New User
                </button>
                @endcan
                @endif
            </small>
        </div>
        <div class="card-body">
            <div class="dt-responsive table-responsive">
                <table id="users-table" class="table table-striped table-bordered nowrap">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            @canany(['Users Update', 'Users Delete'])
                            <th>Actions</th>
                            @endcanany
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->getRoleNames()->contains('Super Admin') || auth()->user()->getRoleNames()->contains('Admin'))
@can('Users Create')
<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="userForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-control" id="role" name="role" required>
                            @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="saveUserBtn" class="btn btn-primary">Save User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@endif

<!-- Edit User Modal -->
@if(auth()->user()->getRoleNames()->contains('Super Admin') || auth()->user()->getRoleNames()->contains('Admin'))
@can('Users Update')
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit-user-id" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit-name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="edit-name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit-email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="edit-password" name="password">
                    </div>
                    <div class="mb-3">
                        <label for="edit-password-confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="edit-password-confirmation" name="password_confirmation">
                    </div>
                    <div class="mb-3">
                        <label for="edit-role" class="form-label">Role</label>
                        <select class="form-control" id="edit-role" name="role" required>
                            @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="updateUserBtn" class="btn btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@endif
@endsection

@section('scripts')
<!-- [Page Specific JS] start -->
<!-- datatable Js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>
<script src="{{ URL::asset('build/js/plugins/dataTables.min.js') }}"></script>
<script src="{{ URL::asset('build/js/plugins/dataTables.bootstrap5.min.js') }}"></script>
<script>
    function toast(header, message, icon) {
        if ($.toast) {
            $.toast({
                heading: header,
                text: message,
                showHideTransition: 'fade',
                hideAfter: 5000, // Fade in for 5 seconds
                icon: icon
            });
        } else {
            console.error('$.toast is not available. Falling back to alert.');
            alert(header + ': ' + message);
        }
    }
    // Define editUser function in the global scope
    function editUser(userId) {
        fetch(`{{ route('users.edit', ':id') }}`.replace(':id', userId), {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('User not found');
                }
                return response.json();
            })
            .then(data => {
                // Check if data is directly available or nested in a data property
                const userData = data.data || data;

                if (userData && userData.name && userData.email) {
                    $('#editUserModal').modal('show');
                    $('#edit-user-id').val(userId); // Use the userId passed to the function
                    $('#edit-name').val(userData.name);
                    $('#edit-email').val(userData.email);
                    if (userData.roles && userData.roles.length > 0) {
                        $('#edit-role').val(userData.roles[0].name);
                    } else {
                        $('#edit-role').val(null);
                    }

                    // Add data attributes to store original values
                    $('#edit-name').attr('data-original', userData.name);
                    $('#edit-email').attr('data-original', userData.email);
                } else {
                    throw new Error('Invalid user data received');
                }
            })
            .catch(error => {
                toast('Error', error.message, 'error');
            });
    }

    $(document).ready(function() {
        var addUserModal = document.getElementById('addUserModal');
        var nameInput = document.getElementById('name');

        // Initialize DataTable
        window.usersDataTable = $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("users.data") }}',
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
                    name: 'no',
                    searchable: false,
                    orderable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'role',
                    name: 'role'
                },
                @canany(['Users Update', 'Users Delete']) {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        let actions = '';
                        @can('Users Update')
                        actions += `<button class="btn btn-primary btn-sm edit-user" data-id="${row.id}">Edit</button> &nbsp;`;
                        @endcan
                        @can('Users Delete')
                        actions += `<button class="btn btn-danger btn-sm delete-user" data-id="${row.id}">Delete</button>`;
                        @endcan
                        return actions;
                    }
                }
                @endcanany
            ]
        });

        if (addUserModal) {
            addUserModal.addEventListener('shown.bs.modal', function() {
                nameInput.focus();
            });
        }

        // Add this new click event handler for the save button
        $('#saveUserBtn').on('click', function() {
            var formData = new FormData($('#userForm')[0]);

            $.ajax({
                url: '{{ route("users.store") }}',
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
                        window.usersDataTable.ajax.reload();

                        // Close modal and reset form
                        $('#addUserModal').modal('hide');
                        $('#userForm')[0].reset();
                        toast('Success', 'User added successfully', 'success');
                    } else {
                        if (typeof response.message === 'object') {
                            $.each(response.message, function(key, value) {
                                toast('Error', value[0], 'error');
                            });
                        } else {
                            toast('Error', response.message, 'error');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    var errors = xhr.responseJSON.errors;
                    if (errors) {
                        $.each(errors, function(key, value) {
                            toast('Error', value[0], 'error');
                        });
                    } else {
                        toast('Error', 'An error occurred while adding the user', 'error');
                    }
                }
            });
        });



        // Use event delegation for edit buttons
        $(document).on('click', '.edit-user', function() {
            var userId = $(this).data('id');
            if (userId) {
                editUser(userId);
            } else {
                console.error('No user ID found');
                toast('Error', 'Unable to edit user. No user ID found.', 'error');
            }
        });

        // Update user functionality
        $('#updateUserBtn').on('click', function() {
            var userId = $('#edit-user-id').val();
            var formData = new FormData($('#editUserForm')[0]);
            var jsonData = {
                name: $('#edit-name').val(),
                email: $('#edit-email').val(),
                role: $('#edit-role').val()
            };

            // Only proceed if there are changes
            if (jsonData.name !== $('#edit-name').attr('data-original') ||
                jsonData.email !== $('#edit-email').attr('data-original') ||
                jsonData.role !== $('#edit-role').attr('data-original')) {
                $.ajax({
                    url: `{{ route('users.update', '') }}/${userId}`,
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
                            window.usersDataTable.ajax.reload();
                            $('#editUserModal').modal('hide');
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
                $('#editUserModal').modal('hide');
            }
        });

        // Add this new event listener for delete buttons
        $(document).on('click', '.delete-user', function() {
            var userId = $(this).data('id');
            if (confirm('Are you sure you want to delete this user?')) {
                deleteUser(userId);
            }
        });

        // Add this new function to handle user deletion
        function deleteUser(userId) {
            fetch(`{{ route('users.destroy', '') }}/${userId}`, {
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
                        window.usersDataTable.ajax.reload();
                    } else {
                        toast('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    toast('Error', error, 'error');
                });
        }
    });
</script>
@endsection