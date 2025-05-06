@extends('layouts.main')

@section('title', 'Home')
@section('breadcrumb-item', 'Dashboard')

@section('breadcrumb-item-active', 'Home')

@section('css')
    <!-- map-vector css -->
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/jsvectormap.min.css') }}">
@endsection

@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">

        <div class="col-md-12 col-xl-8">
            <div class="card table-card">
                <div class="card-header d-flex align-items-center justify-content-between py-3">
                    <h5>Recent Users</h5>
                    <div class="dropdown">
                        <a class="avtar avtar-xs btn-link-secondary dropdown-toggle arrow-none" href="#"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                class="material-icons-two-tone f-18">more_vert</i></a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="#">View</a>
                            <a class="dropdown-item" href="#">Edit</a>
                        </div>
                    </div>
                </div>
                <div class="card-body py-2 px-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless table-sm mb-0">
                            <tbody id="activity-list">
                                @php
                                    $activities = App\Models\Backend\Activity::orderBy('created_at', 'desc')
                                        ->take(5)
                                        ->get();
                                @endphp
                                @foreach ($activities as $activity)
                                    <?php
                                    $user = App\Models\User::where('id', $activity->user_id)->first();
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="d-inline-block align-middle">
                                                <img src="{{ URL::asset('build/images/user/avatar-4.jpg') }}"
                                                    alt="user image" class="img-radius align-top m-r-15"
                                                    style="width:40px;">
                                                <div class="d-inline-block">
                                                    <h6 class="m-b-0">
                                                        @if ($user && !empty($user->name))
                                                            {{ $user->name }}
                                                        @else
                                                            <span class="text-danger">User deleted</span>
                                                        @endif
                                                    </h6>
                                                    <p class="m-b-0">{{ $activity->note }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="mb-0"><i class="ph-duotone ph-circle text-danger f-12"></i>
                                                {{ $activity->created_at }}</p>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>

                            <tfoot>
                                <tr>
                                    <!-- Load More Button -->
                                    <td colspan="3" style="text-align: right;">
                                        <button id="load-more" class="btn btn-primary" data-skip="5">Load More</button>
                                    </td>
                                </tr>

                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 col-xl-4">
            <div class="card table-card">
                <div class="card-header d-flex align-items-center justify-content-between py-3">
                    <h5>Login/Logout Activities</h5>
                    <div class="dropdown">
                        <a class="avtar avtar-xs btn-link-secondary dropdown-toggle arrow-none" href="#"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                class="material-icons-two-tone f-18">more_vert</i></a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="{{ route('activity') }}">View All</a>
                        </div>
                    </div>
                </div>
                <div class="card-body py-2 px-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless table-sm mb-0">
                            <tbody id="auth-activity-list">
                                @php
                                    $authActivities = App\Models\Backend\Activity::where('category', 'auth')
                                        ->orderBy('created_at', 'desc')
                                        ->take(5)
                                        ->get();
                                @endphp
                                @foreach ($authActivities as $activity)
                                    <?php
                                    $user = App\Models\User::where('id', $activity->user_id)->first();
                                    $iconClass = $activity->action == 'login' ? 'text-success' : 'text-danger';
                                    $icon = $activity->action == 'login' ? 'login' : 'logout';
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="d-inline-block align-middle">
                                                <i
                                                    class="material-icons-two-tone {{ $iconClass }} f-24 align-top m-r-15">{{ $icon }}</i>
                                                <div class="d-inline-block">
                                                    <h6 class="m-b-0">
                                                        @if ($user && !empty($user->name))
                                                            {{ $user->name }}
                                                        @else
                                                            <span class="text-danger">User deleted</span>
                                                        @endif
                                                    </h6>
                                                    <p class="m-b-0">{{ $activity->note }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="mb-0"><i class="ph-duotone ph-clock text-info f-12"></i>
                                                {{ $activity->created_at->diffForHumans() }}</p>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <!-- Load More Button -->
                                    <td colspan="2" style="text-align: right;">
                                        <button id="load-more-auth" class="btn btn-primary btn-sm" data-skip="5">Load
                                            More</button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="world-map-markers" class="set-map d-none" style="height:365px;"></div>

    <!-- [ Main Content ] end -->
@endsection

@section('scripts')
    <!-- [Page Specific JS] start -->
    <script src="{{ URL::asset('build/js/plugins/apexcharts.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/jsvectormap.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/world.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/world-merc.js') }}"></script>
    <!-- [Page Specific JS] end -->

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi jsvectormap
            if (document.getElementById('world-map-markers')) {
                var worldMap = new jsVectorMap({
                    map: 'world_merc',
                    selector: '#world-map-markers',
                    zoomOnScroll: false,
                    zoomButtons: false,
                    selectedMarkers: [1, 10],
                    markersSelectable: true,
                    markers: [{
                            name: "Indonesia",
                            coords: [-6.2088, 106.8456]
                        },
                        // Tambahkan marker lain sesuai kebutuhan
                    ],
                    markerStyle: {
                        initial: {
                            fill: '#4680ff'
                        },
                        selected: {
                            fill: '#ff5252'
                        }
                    },
                    labels: {
                        markers: {
                            render: function(marker) {
                                return marker.name
                            }
                        }
                    }
                });
            }

            // Inisialisasi chart dan fungsi lainnya dari dashboard-default.js
            if (typeof initDashboardDefault === 'function') {
                initDashboardDefault();
            }

            // Kode untuk load more activities
            const loadMoreButton = document.getElementById('load-more');
            if (loadMoreButton) {
                loadMoreButton.addEventListener('click', function() {
                    let skip = this.dataset.skip;

                    $.ajax({
                        url: "{{ route('load.more.activities') }}",
                        type: 'GET',
                        data: {
                            skip: skip
                        },
                        success: function(response) {
                            if (response.length > 0) {
                                $.each(response, function(index, activity) {
                                    let user = activity.user;
                                    let row = `<tr>
                                    <td>
                                        <div class="d-inline-block align-middle">
                                            <img src="{{ URL::asset('build/images/user/avatar-4.jpg') }}" alt="user image"
                                                class="img-radius align-top m-r-15" style="width:40px;">
                                            <div class="d-inline-block">
                                                <h6 class="m-b-0">${user ? user.name : 'Deleted User'}</h6>
                                                <p class="m-b-0">${activity.note}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="mb-0"><i class="ph-duotone ph-circle text-danger f-12"></i> ${activity.created_at}</p>
                                    </td>
                                </tr>`;

                                    $('#activity-list').append(row);
                                });

                                loadMoreButton.dataset.skip = parseInt(skip) + 5;
                            } else {
                                loadMoreButton.style.display = 'none';
                            }
                        }
                    });
                });
            }

            // Load more auth activities
            const loadMoreAuthButton = document.getElementById('load-more-auth');
            if (loadMoreAuthButton) {
                loadMoreAuthButton.addEventListener('click', function() {
                    let skip = this.dataset.skip;

                    $.ajax({
                        url: "{{ route('auth.activities') }}",
                        type: 'GET',
                        data: {
                            skip: skip
                        },
                        success: function(response) {
                            if (response.length > 0) {
                                $.each(response, function(index, activity) {
                                    let user = activity.user;
                                    let iconClass = activity.action === 'login' ?
                                        'text-success' : 'text-danger';
                                    let icon = activity.action === 'login' ? 'login' :
                                        'logout';

                                    let row = `<tr>
                                    <td>
                                        <div class="d-inline-block align-middle">
                                            <i class="material-icons-two-tone ${iconClass} f-24 align-top m-r-15">${icon}</i>
                                            <div class="d-inline-block">
                                                <h6 class="m-b-0">${user ? user.name : 'Deleted User'}</h6>
                                                <p class="m-b-0">${activity.note}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="mb-0"><i class="ph-duotone ph-clock text-info f-12"></i> ${activity.created_at}</p>
                                    </td>
                                </tr>`;

                                    $('#auth-activity-list').append(row);
                                });

                                loadMoreAuthButton.dataset.skip = parseInt(skip) + 5;
                            } else {
                                loadMoreAuthButton.style.display = 'none';
                            }
                        }
                    });
                });
            }
        });

        $(document).ready(function() {
            $('#load-more').on('click', function() {
                let skip = $(this).data('skip');

                $.ajax({
                    url: "{{ route('load.more.activities') }}",
                    type: 'GET',
                    data: {
                        skip: skip
                    },
                    success: function(response) {
                        if (response.length > 0) {
                            $.each(response, function(index, activity) {
                                let user = activity
                                    .user; // Assuming you return the user as well
                                let row = `<tr>
                            <td>
                                <div class="d-inline-block align-middle">
                                    <img src="{{ URL::asset('build/images/user/avatar-4.jpg') }}" alt="user image"
                                        class="img-radius align-top m-r-15" style="width:40px;">
                                    <div class="d-inline-block">
                                        <h6 class="m-b-0">${user.name}</h6>
                                        <p class="m-b-0">${activity.note}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p class="mb-0"><i class="ph-duotone ph-circle text-danger f-12"></i> ${activity.created_at}</p>
                            </td>
                        </tr>`;

                                $('#activity-list').append(row);
                            });

                            // Increase skip count for the next batch of data
                            $('#load-more').data('skip', skip + 5);
                        } else {
                            // Hide the Load More button if no more data
                            $('#load-more').hide();
                        }
                    }
                });
            });
        });
    </script>

    <script src="{{ URL::asset('build/js/pages/dashboard-default.js') }}"></script>

@endsection
