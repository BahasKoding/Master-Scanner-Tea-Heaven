@extends('layouts.main')

@section('title', 'Users')
@section('breadcrumb-item', $item)
@section('breadcrumb-item-active', $itemActive)

@section('css')
    <style>
        /* Simple styling for the table */
        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .user-table th,
        .user-table td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        .user-table th {
            background-color: #f2f2f2;
            text-align: left;
        }

        .user-table tr:hover {
            background-color: #f5f5f5;
        }

        .badge {
            display: inline-block;
            padding: 0.25em 0.4em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
            color: #fff;
        }

        .bg-danger {
            background-color: #dc3545;
        }

        .bg-warning {
            background-color: #ffc107;
            color: #000;
        }

        .text-dark {
            color: #212529 !important;
        }

        .bg-primary {
            background-color: #007bff;
        }

        .bg-secondary {
            background-color: #6c757d;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            line-height: 1.5;
            border-radius: 0.2rem;
        }

        .form-control:disabled {
            background-color: #f8f9fa;
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
@endsection

@section('content')
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Manage Users</h5>
                    <div>
                        @can('Users Create')
                            <button type="button" class="btn btn-primary" onclick="showAddUserModal()">
                                Add New User
                            </button>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $index => $user)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @foreach ($user->roles as $role)
                                            @php
                                                $badgeClass = 'bg-primary';
                                                if ($role->name === 'Super Admin') {
                                                    $badgeClass = 'bg-danger';
                                                } elseif ($role->name === 'Admin') {
                                                    $badgeClass = 'bg-warning';
                                                }
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ $role->name }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        @php
                                            $isSuperAdmin = $user->hasRole('Super Admin');
                                            $currentUserId = Auth::id();
                                            $canEditThisUser = true;

                                            // SECURITY: Only Super Admin can edit themselves
                                            if ($isSuperAdmin && $currentUserId !== $user->id) {
                                                $canEditThisUser = false;
                                            }
                                        @endphp

                                        @can('Users Update')
                                            @if ($canEditThisUser)
                                                <button type="button" class="btn btn-sm btn-info"
                                                    onclick="editUser({{ $user->id }})">
                                                    <i class="ti ti-edit"></i> Edit
                                                </button>
                                            @else
                                                <span class="badge bg-secondary">Self-Edit Only</span>
                                            @endif
                                        @endcan

                                        <a href="{{ route('activity.user.show', $user->id) }}"
                                            class="btn btn-sm btn-secondary">
                                            <i class="ti ti-activity"></i> Activity
                                        </a>

                                        @can('Users Delete')
                                            @if (!$isSuperAdmin)
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    onclick="deleteUser({{ $user->id }})">
                                                    <i class="ti ti-trash"></i> Delete
                                                </button>
                                            @else
                                                <span class="badge bg-warning text-dark">No Delete</span>
                                            @endif
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    @can('Users Create')
        <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="userForm">
                            @csrf
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
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="saveUser()">Save User</button>
                    </div>
                </div>
            </div>
        </div>
    @endcan

    <!-- Edit User Modal -->
    @can('Users Update')
        <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editUserForm">
                            @csrf
                            @method('PUT')
                            <input type="hidden" id="edit-user-id" name="id">
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
                                <input type="password" class="form-control" id="edit-password" name="password"
                                    placeholder="Leave blank to keep current password">
                            </div>
                            <div class="mb-3">
                                <label for="edit-password-confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="edit-password-confirmation"
                                    name="password_confirmation" placeholder="Leave blank to keep current password">
                            </div>
                            <div class="mb-3">
                                <label for="edit-role" class="form-label">Role</label>
                                <select class="form-control" id="edit-role" name="role" required>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                <div id="super-admin-warning" class="alert alert-warning mt-2" style="display: none;">
                                    <small><i class="ti ti-lock"></i> Super Admin role cannot be changed for security
                                        reasons.</small>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="updateUser()">Update User</button>
                    </div>
                </div>
            </div>
        </div>
    @endcan
@endsection

@section('scripts')
    <script>
        // Function to show add user modal
        function showAddUserModal() {
            // Reset form
            document.getElementById('userForm').reset();

            // Show modal
            const modal = document.getElementById('addUserModal');
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        }

        // Function to save new user
        function saveUser() {
            const form = document.getElementById('userForm');
            const formData = new FormData(form);

            // Show loading indicator
            Swal.fire({
                title: 'Saving...',
                text: 'Creating new user',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('{{ route('users.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'User created successfully!',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Failed to create user'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error creating user:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while creating the user'
                    });
                });
        }

        // Function to show edit user modal
        function editUser(userId) {
            // Show loading indicator
            Swal.fire({
                title: 'Loading...',
                text: 'Fetching user data',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`{{ url('users') }}/${userId}/edit`, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Close loading indicator
                    Swal.close();

                    // Check if request failed due to security restrictions
                    if (data.status === 'error') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Access Denied',
                            text: data.message
                        });
                        return;
                    }

                    // Debug data in console
                    console.log('User data received:', data);

                    // Determine structure of the response
                    const userData = data.data || data;

                    console.log('Extracted user data:', userData);

                    if (userData && userData.name) {
                        // Set form values
                        document.getElementById('edit-user-id').value = userId;
                        document.getElementById('edit-name').value = userData.name;
                        document.getElementById('edit-email').value = userData.email;

                        // Clear password fields
                        document.getElementById('edit-password').value = '';
                        document.getElementById('edit-password-confirmation').value = '';

                        // Set role if available
                        if (userData.roles && userData.roles.length > 0) {
                            const userRole = userData.roles[0].name;
                            document.getElementById('edit-role').value = userRole;

                            // SECURITY: Disable role change for Super Admin
                            if (userRole === 'Super Admin') {
                                document.getElementById('edit-role').disabled = true;
                                document.getElementById('edit-role').title =
                                    'Super Admin role cannot be changed for security reasons';
                                document.getElementById('super-admin-warning').style.display = 'block';
                            } else {
                                document.getElementById('edit-role').disabled = false;
                                document.getElementById('edit-role').title = '';
                                document.getElementById('super-admin-warning').style.display = 'none';
                            }
                        }

                        // Show modal
                        const modal = document.getElementById('editUserModal');
                        const bsModal = new bootstrap.Modal(modal);
                        bsModal.show();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Invalid user data received'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching user data:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load user data'
                    });
                });
        }

        // Function to update user
        function updateUser() {
            const form = document.getElementById('editUserForm');
            const userId = document.getElementById('edit-user-id').value;
            const formData = new FormData(form);

            // Show loading indicator
            Swal.fire({
                title: 'Updating...',
                text: 'Saving user information',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`{{ url('users') }}/${userId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'User updated successfully!',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        // Handle security restrictions and other errors
                        const title = data.message && data.message.includes('security') ? 'Security Restriction' :
                            'Update Failed';
                        Swal.fire({
                            icon: 'error',
                            title: title,
                            text: data.message || 'An error occurred'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error updating user:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while updating the user'
                    });
                });
        }

        // Function to delete user
        function deleteUser(userId) {
            // Get the current user's ID from the session
            const currentUserId = {{ Auth::id() }};

            // Check if the user is trying to delete themselves
            if (userId === currentUserId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Action Not Allowed',
                    text: 'You cannot delete your own account!'
                });
                return;
            }

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
                    // Show loading indicator
                    Swal.fire({
                        title: 'Deleting...',
                        text: 'Processing your request',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch(`{{ url('users') }}/${userId}`, {
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
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'User has been deleted.',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                // Handle security restrictions and other errors
                                const title = data.message && data.message.includes('security') ?
                                    'Security Restriction' : 'Delete Failed';
                                Swal.fire({
                                    icon: 'error',
                                    title: title,
                                    text: data.message || 'Failed to delete user'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error deleting user:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while deleting the user'
                            });
                        });
                }
            });
        }
    </script>
@endsection
