@extends('admin.layouts.app')

@section('title', 'Complaints')

@section('content')
    <div class="page-header">
        <div>
            <h1>Complaints</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Complaints</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <ul class="nav status-tabs px-3 pt-2 border-bottom flex-nowrap overflow-auto">
            <li class="nav-item">
                <a href="{{ route('admin.complaints.index', request()->except(['status', 'page'])) }}" @class(['nav-link text-nowrap', 'active' => ! request('status')])>
                    All <span class="badge bg-light text-dark">{{ $statusCounts->sum() }}</span>
                </a>
            </li>
            @foreach (\App\Models\Complaint::STATUSES as $key => $status)
                <li class="nav-item">
                    <a href="{{ route('admin.complaints.index', ['status' => $key] + request()->except(['status', 'page'])) }}"
                        @class(['nav-link text-nowrap', 'active' => request('status') === $key])>
                        {{ $status['label'] }} <span class="badge bg-{{ $status['color'] }}-subtle text-{{ $status['color'] }}-emphasis">{{ $statusCounts[$key] ?? 0 }}</span>
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="card-header bg-white">
            <form method="GET" class="row g-2">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <div class="col-md-5">
                    <input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Reference, name, phone or subject">
                </div>
                <div class="col-md-3">
                    <select name="category" class="form-select">
                        <option value="">All types</option>
                        @foreach (\App\Models\Complaint::CATEGORIES as $key => $label)
                            <option value="{{ $key }}" @selected(request('category') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-primary"><i class="bi bi-funnel"></i> Filter</button>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>From</th>
                        <th>Subject</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($complaints as $complaint)
                        <tr>
                            <td>
                                <a href="{{ route('admin.complaints.show', $complaint) }}" class="fw-semibold">{{ $complaint->complaint_number }}</a>
                                <div class="small text-muted">{{ $complaint->created_at->format('d M Y') }}</div>
                            </td>
                            <td>
                                <div>{{ $complaint->name }}</div>
                                <div class="small text-muted">{{ $complaint->phone }}</div>
                            </td>
                            <td>
                                <div>{{ \Illuminate\Support\Str::limit($complaint->subject, 50) }}</div>
                                <div class="small text-muted">{{ $complaint->category_label }}</div>
                            </td>
                            <td>
                                @if ($complaint->order)
                                    <a href="{{ route('admin.orders.show', $complaint->order) }}">{{ $complaint->order->order_number }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td><span class="badge bg-{{ $complaint->status_color }}-subtle text-{{ $complaint->status_color }}-emphasis">{{ $complaint->status_label }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('admin.complaints.show', $complaint) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">No complaints found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($complaints->hasPages())
            <div class="card-footer bg-white">{{ $complaints->links() }}</div>
        @endif
    </div>
@endsection
