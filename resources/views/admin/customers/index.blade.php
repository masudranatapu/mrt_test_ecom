@extends('admin.layouts.app')

@section('title', 'Customers')

@section('content')
    <div class="page-header">
        <div>
            <h1>Customers</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Customers</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white">
            <form method="GET" class="row g-2">
                <div class="col-md-4">
                    <input type="search" name="search" value="{{ request('search') }}" class="form-control"
                        placeholder="Name, email, phone or business">
                </div>
                <div class="col-6 col-md-2">
                    <select name="approval" class="form-select">
                        <option value="">All approval</option>
                        <option value="approved" @selected(request('approval') === 'approved')>Approved</option>
                        <option value="pending" @selected(request('approval') === 'pending')>Pending</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All status</option>
                        <option value="1" @selected(request('status') === '1')>Active</option>
                        <option value="0" @selected(request('status') === '0')>Inactive</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-primary"><i class="bi bi-funnel"></i> Filter</button>
                    @if (request()->hasAny(['search', 'approval', 'status']))
                        <a href="{{ route('admin.customers.index') }}" class="btn btn-link">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Business</th>
                        <th>Approval</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $customer->avatar_url }}" {!! img_fallback(true) !!} class="rounded-circle avatar-sm" alt="">
                                    <div>
                                        <div class="fw-semibold">{{ $customer->name }}</div>
                                        <div class="small text-muted">{{ $customer->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $customer->phone ?? '—' }}</td>
                            <td>{{ $customer->business_name ?? '—' }}</td>
                            <td>
                                @if ($customer->is_approved)
                                    <span class="badge bg-success-subtle text-success"><i class="bi bi-patch-check"></i> Approved</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis"><i class="bi bi-hourglass-split"></i> Pending</span>
                                @endif
                            </td>
                            <td>
                                @if ($customer->status)
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $customer->created_at->format('d M Y') }}</td>
                            <td class="text-end text-nowrap">
                                @unless ($customer->is_approved)
                                    <form action="{{ route('admin.customers.approval', $customer) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="is_approved" value="1">
                                        <button class="btn btn-sm btn-success" title="Approve"><i class="bi bi-check-lg"></i> Approve</button>
                                    </form>
                                @endunless
                                <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-outline-primary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">No customers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($customers->hasPages())
            <div class="card-footer bg-white">{{ $customers->links() }}</div>
        @endif
    </div>
@endsection
