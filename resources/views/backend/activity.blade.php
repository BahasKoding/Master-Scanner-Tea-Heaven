@extends('layouts.main')

@section('title', 'Activity Log')
@section('breadcrumb-item', 'Activity')
@section('breadcrumb-item-active', 'Log')

@section('css')
    <style>
        .activity-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .activity-table th,
        .activity-table td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        .activity-table th {
            background-color: #f2f2f2;
            text-align: left;
        }

        .activity-table tr:hover {
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

        .bg-info {
            background-color: #17a2b8;
        }

        .bg-warning {
            background-color: #ffc107;
            color: #000;
        }

        .bg-success {
            background-color: #28a745;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Activity Log</h5>
                    <a href="{{ route('users.index') }}" class="btn btn-primary">Back to Users</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="activity-table">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Category</th>
                                <th>Action</th>
                                <th>Details</th>
                                <th>User</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($activities as $activity)
                                <tr>
                                    <td>{{ $activity->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td>
                                        @php
                                            $badgeClass = 'bg-info';
                                            if ($activity->category === 'auth') {
                                                $badgeClass = 'bg-warning';
                                            } elseif ($activity->category === 'user') {
                                                $badgeClass = 'bg-success';
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $activity->category }}</span>
                                    </td>
                                    <td>{{ $activity->action }}</td>
                                    <td>{{ $activity->note }}</td>
                                    <td>{{ $activity->user ? $activity->user->name : 'System' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- [Page Specific JS] end -->
@endsection
