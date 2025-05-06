@extends('layouts.main')

@section('title', 'User Activities')
@section('breadcrumb-item', 'Users')
@section('breadcrumb-item-active', 'User Activities')

@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5>Activities for {{ $user->name }}</h5>
                    <a href="{{ route('users.index') }}" class="btn btn-sm btn-primary">Back to Users</a>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs mb-3" id="activityTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="all-tab" data-bs-toggle="tab" href="#all" role="tab"
                                aria-controls="all" aria-selected="true">All Activities</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="login-tab" data-bs-toggle="tab" href="#auth" role="tab"
                                aria-controls="auth" aria-selected="false">Login/Logout</a>
                        </li>
                    </ul>

                    <div class="tab-content" id="activityTabsContent">
                        <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Timestamp</th>
                                            <th>Category</th>
                                            <th>Action</th>
                                            <th>Details</th>
                                        </tr>
                                    </thead>
                                    <tbody id="all-activities">
                                        @foreach ($activities as $activity)
                                            <tr>
                                                <td>{{ $activity->created_at }}</td>
                                                <td>{{ ucfirst($activity->category) }}</td>
                                                <td>
                                                    @if ($activity->action == 'login')
                                                        <span class="badge bg-success">Login</span>
                                                    @elseif($activity->action == 'logout')
                                                        <span class="badge bg-danger">Logout</span>
                                                    @elseif($activity->action == 'failed_login')
                                                        <span class="badge bg-warning">Failed Login</span>
                                                    @else
                                                        <span class="badge bg-info">{{ ucfirst($activity->action) }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $activity->note }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                <button id="load-more-all" class="btn btn-primary" data-skip="10"
                                    data-user="{{ $user->id }}">Load More</button>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="auth" role="tabpanel" aria-labelledby="login-tab">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Timestamp</th>
                                            <th>Action</th>
                                            <th>Details</th>
                                        </tr>
                                    </thead>
                                    <tbody id="auth-activities">
                                        @foreach ($authActivities as $activity)
                                            <tr>
                                                <td>{{ $activity->created_at }}</td>
                                                <td>
                                                    @if ($activity->action == 'login')
                                                        <span class="badge bg-success">Login</span>
                                                    @elseif($activity->action == 'logout')
                                                        <span class="badge bg-danger">Logout</span>
                                                    @elseif($activity->action == 'failed_login')
                                                        <span class="badge bg-warning">Failed Login</span>
                                                    @endif
                                                </td>
                                                <td>{{ $activity->note }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                <button id="load-more-auth" class="btn btn-primary" data-skip="10"
                                    data-user="{{ $user->id }}">Load More</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            // Load more all activities
            $('#load-more-all').on('click', function() {
                let skip = $(this).data('skip');
                let userId = $(this).data('user');

                $.ajax({
                    url: "{{ route('user.activities', '') }}/" + userId,
                    type: 'GET',
                    data: {
                        skip: skip,
                        limit: 10
                    },
                    success: function(response) {
                        if (response.length > 0) {
                            $.each(response, function(index, activity) {
                                let actionBadge = '';

                                if (activity.action === 'login') {
                                    actionBadge =
                                        '<span class="badge bg-success">Login</span>';
                                } else if (activity.action === 'logout') {
                                    actionBadge =
                                        '<span class="badge bg-danger">Logout</span>';
                                } else if (activity.action === 'failed_login') {
                                    actionBadge =
                                        '<span class="badge bg-warning">Failed Login</span>';
                                } else {
                                    actionBadge = '<span class="badge bg-info">' +
                                        activity.action.charAt(0).toUpperCase() +
                                        activity.action.slice(1) + '</span>';
                                }

                                let row = `<tr>
                                <td>${activity.created_at}</td>
                                <td>${activity.category.charAt(0).toUpperCase() + activity.category.slice(1)}</td>
                                <td>${actionBadge}</td>
                                <td>${activity.note}</td>
                            </tr>`;

                                $('#all-activities').append(row);
                            });

                            $('#load-more-all').data('skip', parseInt(skip) + 10);
                        } else {
                            $('#load-more-all').text('No More Activities').prop('disabled',
                                true);
                        }
                    }
                });
            });

            // Load more auth activities
            $('#load-more-auth').on('click', function() {
                let skip = $(this).data('skip');
                let userId = $(this).data('user');

                $.ajax({
                    url: "{{ route('user.activities', '') }}/" + userId,
                    type: 'GET',
                    data: {
                        skip: skip,
                        limit: 10,
                        category: 'auth'
                    },
                    success: function(response) {
                        if (response.length > 0) {
                            $.each(response, function(index, activity) {
                                let actionBadge = '';

                                if (activity.action === 'login') {
                                    actionBadge =
                                        '<span class="badge bg-success">Login</span>';
                                } else if (activity.action === 'logout') {
                                    actionBadge =
                                        '<span class="badge bg-danger">Logout</span>';
                                } else if (activity.action === 'failed_login') {
                                    actionBadge =
                                        '<span class="badge bg-warning">Failed Login</span>';
                                }

                                let row = `<tr>
                                <td>${activity.created_at}</td>
                                <td>${actionBadge}</td>
                                <td>${activity.note}</td>
                            </tr>`;

                                $('#auth-activities').append(row);
                            });

                            $('#load-more-auth').data('skip', parseInt(skip) + 10);
                        } else {
                            $('#load-more-auth').text('No More Activities').prop('disabled',
                                true);
                        }
                    }
                });
            });
        });
    </script>
@endsection
