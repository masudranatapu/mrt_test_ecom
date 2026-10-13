@extends('admin.layouts.app')

@section('title', 'Messages')

@section('content')
    <div class="page-header">
        <div>
            <h1>Contact Messages</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Messages</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white">
            <form method="GET" class="row g-2">
                <div class="col-md-5">
                    <input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Name, email or subject">
                </div>
                <div class="col-md-3">
                    <select name="filter" class="form-select">
                        <option value="">All messages</option>
                        <option value="unread" @selected(request('filter') === 'unread')>Unread only</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-primary"><i class="bi bi-funnel"></i> Filter</button>
                    @if (request()->hasAny(['search', 'filter']))
                        <a href="{{ route('admin.messages.index') }}" class="btn btn-link">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="list-group list-group-flush">
            @forelse ($messages as $message)
                <a href="{{ route('admin.messages.show', $message) }}"
                    @class(['list-group-item list-group-item-action py-3', 'fw-semibold bg-primary-subtle bg-opacity-25' => ! $message->read_at])>
                    <div class="d-flex justify-content-between gap-2">
                        <span>
                            @unless ($message->read_at)<span class="badge bg-primary me-1">New</span>@endunless
                            {{ $message->name }} <span class="text-muted fw-normal small">&lt;{{ $message->email }}&gt;</span>
                        </span>
                        <span class="small text-muted text-nowrap">{{ $message->created_at->diffForHumans() }}</span>
                    </div>
                    <div>{{ $message->subject }}</div>
                    <div class="small text-muted fw-normal">{{ \Illuminate\Support\Str::limit($message->message, 120) }}</div>
                </a>
            @empty
                <div class="list-group-item text-center text-muted py-5">No messages yet.</div>
            @endforelse
        </div>

        @if ($messages->hasPages())
            <div class="card-footer bg-white">{{ $messages->links() }}</div>
        @endif
    </div>
@endsection
