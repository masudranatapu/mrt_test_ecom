@extends('admin.layouts.app')

@section('title', 'Notifications')

@section('content')
    <div class="page-header">
        <div>
            <h1>Notifications</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Notifications</li>
                </ol>
            </nav>
        </div>
        <form action="{{ route('admin.notifications.read-all') }}" method="POST">
            @csrf
            <button class="btn btn-outline-primary"><i class="bi bi-check2-all me-1"></i> Mark all read</button>
        </form>
    </div>

    <div class="card">
        <div class="list-group list-group-flush">
            @forelse ($notifications as $notification)
                <a href="{{ route('admin.notifications.read', $notification->id) }}"
                    @class(['list-group-item list-group-item-action d-flex gap-3 align-items-center py-3', 'bg-primary-subtle bg-opacity-25' => ! $notification->read_at])>
                    <span class="stat-icon bg-primary-subtle text-primary rounded-circle" style="width:40px;height:40px;display:grid;place-items:center">
                        <i class="bi {{ $notification->data['icon'] ?? 'bi-bag-check' }}"></i>
                    </span>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">{{ $notification->data['message'] ?? 'Notification' }}</div>
                        <div class="small text-muted">{{ $notification->data['total'] ?? '' }}</div>
                    </div>
                    <div class="small text-muted text-nowrap">{{ $notification->created_at->diffForHumans() }}</div>
                </a>
            @empty
                <div class="list-group-item text-center text-muted py-5">No notifications yet.</div>
            @endforelse
        </div>
        @if ($notifications->hasPages())
            <div class="card-footer bg-white">{{ $notifications->links() }}</div>
        @endif
    </div>
@endsection
