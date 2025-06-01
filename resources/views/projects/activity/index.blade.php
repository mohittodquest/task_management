@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Activity Logs</h2>
    <table class="table">
        <thead>
            <tr><th>User</th><th>Action</th><th>Time</th></tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
                <tr>
                    <td>{{ $log->user->name ?? 'Guest' }}</td>
                    <td>{{ $log->action }}</td>
                    <td>{{ $log->created_at->diffForHumans() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $logs->links() }}
</div>
@endsection
