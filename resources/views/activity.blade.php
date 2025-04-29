@extends('layouts.main')

@section('title', 'Activity Log')
@section('breadcrumb-item', 'Activity')
@section('breadcrumb-item-active', 'Log')

@section('css')
<!-- [Page specific CSS] start -->
<link rel="stylesheet" href="{{ URL::asset('build/css/plugins/dataTables.bootstrap5.min.css') }}">
<!-- [Page specific CSS] end -->
@endsection

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Activity Log</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Action</th>
                            <th>Note</th>
                            <th>Activity ID</th>
                            <th>User</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($activities as $activity)
                        <?php
                        $user = App\Models\User::where('id', $activity->user_id)->first();
                        ?>
                        <tr>
                            <td>{{ $activity->category }}</td>
                            <td>{{ $activity->action }}</td>
                            <td>{{ $activity->note }}</td>
                            <td>{{ $activity->id }}</td>
                            <td>
                                {{ $user ? $user->name : 'no name' }}
                            </td>
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