@extends('layouts.main')

@section('title', 'Users')
@section('breadcrumb-item', $item)
@section('breadcrumb-item-active', $itemActive)

@section('css')
    <!-- [Page specific CSS] start -->
    <!-- data tables css -->
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/jquery.toast.min.css') }}">
    <!-- [Page specific CSS] end -->
@endsection

@section('content')
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <small>
                    @if (auth()->user()->getRoleNames()->contains('Super Admin') || auth()->user()->getRoleNames()->contains('Admin'))
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

    @if (auth()->user()->getRoleNames()->contains('Super Admin') || auth()->user()->getRoleNames()->contains('Admin'))
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
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" required>
                                </div>
                                <div class="mb-3">
                                    <label for="role" class="form-label">Role</label>
                                    <select class="form-control" id="role" name="role" required>
                                        @foreach ($roles as $role)
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
    @if (auth()->user()->getRoleNames()->contains('Super Admin') || auth()->user()->getRoleNames()->contains('Admin'))
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
                                    <input type="password" class="form-control" id="edit-password-confirmation"
                                        name="password_confirmation">
                                </div>
                                <div class="mb-3">
                                    <label for="edit-role" class="form-label">Role</label>
                                    <select class="form-control" id="edit-role" name="role" required>
                                        @foreach ($roles as $role)
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
    <script src="{{ URL::asset('build/js/plugins/jquery.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/jquery.toast.min.js') }}"></script>
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

        // Function to view user activities
        function viewUserActivities(userId) {
            window.location.href = `{{ route('user.activities.show', ':id') }}`.replace(':id', userId);
        }

        // Function to delete a user
        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user?')) {
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
                        if (data.status === 'success') {
                            toast('Success', data.message, 'success');
                            window.usersDataTable.ajax.reload();
                        } else {
                            toast('Error', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        toast('Error', 'An error occurred while deleting the user', 'error');
                    });
            }
        }

        $(document).ready(function() {
            var addUserModal = document.getElementById('addUserModal');
            var nameInput = document.getElementById('name');

            // Initialize DataTable
            window.usersDataTable = $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('users.data') }}',
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
                        data: 'roles',
                        name: 'roles',
                        render: function(data, type, row) {
                            // Check if roles data exists as a string
                            if (typeof row.role === 'string' && row.role !== '') {
                                // Convert comma-separated string to array
                                const roles = row.role.split(', ');
                                let roleHtml = '';

                                roles.forEach(function(role) {
                                    let badgeClass = 'bg-primary';
                                    if (role === 'Super Admin') {
                                        badgeClass = 'bg-danger';
                                    } else if (role === 'Admin') {
                                        badgeClass = 'bg-warning';
                                    }
                                    roleHtml +=
                                        `<span class="badge ${badgeClass} me-1">${role}</span>`;
                                });

                                return roleHtml;
                            }

                            // If roles data exists as an array
                            if (Array.isArray(data) && data.length > 0) {
                                let roleHtml = '';

                                data.forEach(function(role) {
                                    let badgeClass = 'bg-primary';
                                    const roleName = typeof role === 'object' ? role.name :
                                        role;

                                    if (roleName === 'Super Admin') {
                                        badgeClass = 'bg-danger';
                                    } else if (roleName === 'Admin') {
                                        badgeClass = 'bg-warning';
                                    }

                                    roleHtml +=
                                        `<span class="badge ${badgeClass} me-1">${roleName}</span>`;
                                });

                                return roleHtml;
                            }

                            return '<span class="badge bg-secondary">No Role</span>';
                        }
                    },
                    @canany(['Users Update', 'Users Delete'])
                        {
                            data: null,
                            name: 'actions',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                let buttons = '';
                                @can('Users Update')
                                    buttons += `<button type="button" class="btn btn-sm btn-info me-1" onclick="editUser(${row.id})">
                                      <i class="ti ti-edit"></i>
                                    </button>`;
                                @endcan

                                // Add view activities button
                                buttons += `<button type="button" class="btn btn-sm btn-primary me-1" onclick="viewUserActivities(${row.id})">
                                      <i class="ti ti-activity"></i>
                                    </button>`;

                                @can('Users Delete')
                                    buttons += `<button type="button" class="btn btn-sm btn-danger" onclick="deleteUser(${row.id})">
                                      <i class="ti ti-trash"></i>
                                    </button>`;
                                @endcan

                                return buttons;
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
                    url: '{{ route('users.store') }}',
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
                            toast('Error', 'An error occurred while updating the user',
                                'error');
                        }
                    });
                } else {
                    $('#editUserModal').modal('hide');
                }
            });
        });
    </script>
@endsection
