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

        .bg-primary {
            background-color: #007bff;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            line-height: 1.5;
            border-radius: 0.2rem;
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
                                        @can('Users Update')
                                            <button type="button" class="btn btn-sm btn-info"
                                                onclick="editUser({{ $user->id }})">
                                                <i class="ti ti-edit"></i> Edit
                                            </button>
                                        @endcan

                                        <a href="{{ route('activity.user.show', $user->id) }}"
                                            class="btn btn-sm btn-secondary">
                                            <i class="ti ti-activity"></i> Activity
                                        </a>

                                        @can('Users Delete')
                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="deleteUser({{ $user->id }})">
                                                <i class="ti ti-trash"></i> Delete
                                            </button>
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
                        alert('User created successfully!');
                        window.location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error: ' + error);
                });
        }

        // Function to show edit user modal
        function editUser(userId) {
            fetch(`{{ route('users.edit', '') }}/${userId}`, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    const userData = data.data || data;

                    if (userData) {
                        // Set form values
                        document.getElementById('edit-user-id').value = userId;
                        document.getElementById('edit-name').value = userData.name;
                        document.getElementById('edit-email').value = userData.email;

                        // Clear password fields
                        document.getElementById('edit-password').value = '';
                        document.getElementById('edit-password-confirmation').value = '';

                        // Set role if available
                        if (userData.roles && userData.roles.length > 0) {
                            document.getElementById('edit-role').value = userData.roles[0].name;
                        }

                        // Show modal
                        const modal = document.getElementById('editUserModal');
                        const bsModal = new bootstrap.Modal(modal);
                        bsModal.show();
                    } else {
                        alert('Error: Invalid user data received');
                    }
                })
                .catch(error => {
                    alert('Error: ' + error);
                });
        }

        // Function to update user
        function updateUser() {
            const form = document.getElementById('editUserForm');
            const userId = document.getElementById('edit-user-id').value;
            const formData = new FormData(form);

            fetch(`{{ route('users.update', '') }}/${userId}`, {
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
                        alert('User updated successfully!');
                        window.location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error: ' + error);
                });
        }

        // Function to delete user
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
                            alert('User deleted successfully!');
                            window.location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        alert('Error: An error occurred while deleting the user');
                    });
            }
        }
    </script>
@endsection
