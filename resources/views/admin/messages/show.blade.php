@extends('admin.layouts.app')

@section('title', $message->subject)

@section('content')
    <div class="page-header">
        <div>
            <h1>Message</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.messages.index') }}">Messages</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: '.$message->subject) }}" class="btn btn-primary">
                <i class="bi bi-reply me-1"></i> Reply by email
            </a>
            <form action="{{ route('admin.messages.destroy', $message) }}" method="POST" data-confirm="Delete this message?">
                @csrf
                @method('DELETE')
                <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">{{ $message->subject }}</div>
                <div class="card-body" style="white-space: pre-line">{{ $message->message }}</div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">Sender</div>
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Name</span> {{ $message->name }}</li>
                    <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Email</span> <a href="mailto:{{ $message->email }}">{{ $message->email }}</a></li>
                    <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Phone</span>
                        @if ($message->phone)<a href="tel:{{ $message->phone }}">{{ $message->phone }}</a>@else — @endif
                    </li>
                    <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Customer</span>
                        @if ($message->customer)
                            <a href="{{ route('admin.customers.show', $message->customer) }}">{{ $message->customer->name }}</a>
                        @else
                            Guest
                        @endif
                    </li>
                    <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Received</span> {{ $message->created_at->format('d M Y, h:i A') }}</li>
                </ul>
            </div>
        </div>
    </div>
@endsection
