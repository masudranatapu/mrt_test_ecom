@extends('admin.layouts.app')

@section('title', $complaint->complaint_number)

@section('content')
    <div class="page-header">
        <div>
            <h1>Complaint {{ $complaint->complaint_number }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.complaints.index') }}">Complaints</a></li>
                    <li class="breadcrumb-item active">{{ $complaint->complaint_number }}</li>
                </ol>
            </nav>
        </div>
        <span class="badge fs-6 bg-{{ $complaint->status_color }}-subtle text-{{ $complaint->status_color }}-emphasis">{{ $complaint->status_label }}</span>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between">
                    <span>{{ $complaint->subject }}</span>
                    <span class="badge bg-light text-dark border fw-normal">{{ $complaint->category_label }}</span>
                </div>
                <div class="card-body" style="white-space: pre-line">{{ $complaint->message }}</div>
                <div class="card-footer bg-white small text-muted">Filed {{ $complaint->created_at->format('d M Y, h:i A') }}</div>
            </div>

            <div class="card">
                <div class="card-header">Update</div>
                <div class="card-body">
                    <form action="{{ route('admin.complaints.update', $complaint) }}" method="POST" novalidate>
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label class="form-label" for="status">Status</label>
                            <select id="status" name="status" class="form-select">
                                @foreach (\App\Models\Complaint::STATUSES as $key => $status)
                                    <option value="{{ $key }}" @selected(old('status', $complaint->status) === $key)>{{ $status['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="admin_note">Internal note</label>
                            <textarea id="admin_note" name="admin_note" rows="4" class="form-control @error('admin_note') is-invalid @enderror"
                                placeholder="What was done to resolve it (only admins see this)">{{ old('admin_note', $complaint->admin_note) }}</textarea>
                            @error('admin_note')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button class="btn btn-primary"><i class="bi bi-check2 me-1"></i> Save</button>
                        @if ($complaint->resolved_at)
                            <span class="small text-muted ms-2">Resolved {{ $complaint->resolved_at->format('d M Y, h:i A') }}</span>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">Customer</div>
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Name</span> {{ $complaint->name }}</li>
                    <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Phone</span> <a href="tel:{{ $complaint->phone }}">{{ $complaint->phone }}</a></li>
                    <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Email</span> <a href="mailto:{{ $complaint->email }}">{{ $complaint->email }}</a></li>
                    <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Account</span>
                        @if ($complaint->customer)
                            <a href="{{ route('admin.customers.show', $complaint->customer) }}">{{ $complaint->customer->name }}</a>
                        @else
                            Guest
                        @endif
                    </li>
                </ul>
            </div>

            @if ($complaint->order)
                <div class="card">
                    <div class="card-header">Related Order</div>
                    <div class="card-body small">
                        <a href="{{ route('admin.orders.show', $complaint->order) }}" class="fw-semibold">{{ $complaint->order->order_number }}</a>
                        <div class="text-muted">{{ $complaint->order->created_at->format('d M Y') }} &middot; {{ money($complaint->order->total) }}</div>
                        <div class="mt-1">@include('admin.orders._badges', ['order' => $complaint->order])</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
