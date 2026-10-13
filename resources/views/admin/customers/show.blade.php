@extends('admin.layouts.app')

@section('title', $customer->name)

@section('content')
    <div class="page-header">
        <div>
            <h1>Customer Profile</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">Customers</a></li>
                    <li class="breadcrumb-item active">{{ $customer->name }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <img src="{{ $customer->avatar_url }}" {!! img_fallback(true) !!} class="rounded-circle avatar-lg mb-3" alt="">
                    <h5 class="mb-0">{{ $customer->name }}</h5>
                    @if ($customer->business_name)
                        <div class="text-muted">{{ $customer->business_name }}</div>
                    @endif
                    <div class="mt-2">
                        @if ($customer->is_approved)
                            <span class="badge bg-success-subtle text-success"><i class="bi bi-patch-check"></i> Approved</span>
                        @else
                            <span class="badge bg-warning-subtle text-warning-emphasis"><i class="bi bi-hourglass-split"></i> Pending approval</span>
                        @endif
                        @if ($customer->status)
                            <span class="badge bg-success-subtle text-success">Active</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                        @endif
                    </div>
                </div>
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Email</span> {{ $customer->email }}</li>
                    <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Phone</span> {{ $customer->phone ?? '—' }}</li>
                    <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Joined</span> {{ $customer->created_at->format('d M Y, h:i A') }}</li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Approved at</span> {{ $customer->approved_at?->format('d M Y, h:i A') ?? '—' }}
                    </li>
                </ul>
            </div>

            <div class="card">
                <div class="card-header">Account Control</div>
                <div class="card-body">
                    <p class="small text-muted mb-2">
                        Approved customers can see prices and use the 28-day credit payment option.
                    </p>
                    <form action="{{ route('admin.customers.approval', $customer) }}" method="POST" class="mb-3">
                        @csrf
                        @method('PATCH')
                        @if ($customer->is_approved)
                            <input type="hidden" name="is_approved" value="0">
                            <button class="btn btn-outline-warning w-100"><i class="bi bi-x-circle me-1"></i> Remove Approval</button>
                        @else
                            <input type="hidden" name="is_approved" value="1">
                            <button class="btn btn-success w-100"><i class="bi bi-check-circle me-1"></i> Approve Customer</button>
                        @endif
                    </form>

                    <p class="small text-muted mb-2">Inactive customers cannot log in.</p>
                    <form action="{{ route('admin.customers.status', $customer) }}" method="POST"
                        @if ($customer->status) data-confirm="Deactivate this customer? They will not be able to log in." @endif>
                        @csrf
                        @method('PATCH')
                        @if ($customer->status)
                            <input type="hidden" name="status" value="0">
                            <button class="btn btn-outline-danger w-100"><i class="bi bi-slash-circle me-1"></i> Deactivate Account</button>
                        @else
                            <input type="hidden" name="status" value="1">
                            <button class="btn btn-outline-success w-100"><i class="bi bi-check2-circle me-1"></i> Activate Account</button>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">Addresses</div>
                <div class="card-body">
                    <div class="row g-3">
                        @forelse ($customer->addresses as $address)
                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="fw-semibold">{{ $address->label }}</span>
                                        @if ($address->is_default)
                                            <span class="badge bg-primary-subtle text-primary">Default</span>
                                        @endif
                                    </div>
                                    <div>{{ $address->name }} &middot; {{ $address->phone }}</div>
                                    <div class="text-muted small">{{ $address->full_address }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-muted">No address added yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Order History</span>
                    <span class="small fw-normal text-muted">
                        {{ $orderStats['count'] }} orders &middot; {{ money($orderStats['spent']) }} spent
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th class="text-center">Items</th>
                                <th class="text-end">Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order) }}" class="fw-semibold">{{ $order->order_number }}</a>
                                        <div class="small text-muted">{{ $order->created_at->format('d M Y') }} &middot; {{ $order->payment_method_label }}</div>
                                    </td>
                                    <td class="text-center">{{ $order->items_count }}</td>
                                    <td class="text-end text-nowrap">{{ money($order->total) }}</td>
                                    <td>@include('admin.orders._badges')</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-5">
                                        <i class="bi bi-bag fs-1 d-block mb-2"></i> No orders yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($orderStats['count'] > $orders->count())
                    <div class="card-footer bg-white text-center">
                        <a href="{{ route('admin.orders.index', ['search' => $customer->email]) }}" class="small">View all orders</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
