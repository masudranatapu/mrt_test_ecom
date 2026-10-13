@extends('admin.layouts.app')

@section('title', 'Orders')

@section('content')
    <div class="page-header">
        <div>
            <h1>Orders</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Orders</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        {{-- Status tabs --}}
        <ul class="nav status-tabs px-3 pt-2 border-bottom flex-nowrap overflow-auto">
            <li class="nav-item">
                <a href="{{ route('admin.orders.index', request()->except(['status', 'page'])) }}" @class(['nav-link text-nowrap', 'active' => ! request('status')])>
                    All <span class="badge bg-light text-dark">{{ $statusCounts->sum() }}</span>
                </a>
            </li>
            @foreach (\App\Models\Order::STATUSES as $key => $status)
                <li class="nav-item">
                    <a href="{{ route('admin.orders.index', ['status' => $key] + request()->except(['status', 'page'])) }}"
                        @class(['nav-link text-nowrap', 'active' => request('status') === $key])>
                        {{ $status['label'] }} <span class="badge bg-{{ $status['color'] }}-subtle text-{{ $status['color'] }}-emphasis">{{ $statusCounts[$key] ?? 0 }}</span>
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="card-header bg-white">
            <form method="GET" class="row g-2">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <div class="col-md-3">
                    <input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Order #, customer, phone">
                </div>
                <div class="col-6 col-md-2">
                    <select name="payment_status" class="form-select">
                        <option value="">Any payment</option>
                        @foreach (\App\Models\Order::PAYMENT_STATUSES as $key => $status)
                            <option value="{{ $key }}" @selected(request('payment_status') === $key)>{{ $status['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="payment_method" class="form-select">
                        <option value="">Any method</option>
                        @foreach (\App\Models\Order::PAYMENT_METHODS as $key => $label)
                            <option value="{{ $key }}" @selected(request('payment_method') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control" title="From date">
                </div>
                <div class="col-6 col-md-2">
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control" title="To date">
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-primary"><i class="bi bi-funnel"></i></button>
                    @if (request()->hasAny(['search', 'payment_status', 'payment_method', 'from', 'to']))
                        <a href="{{ route('admin.orders.index', request()->only('status')) }}" class="btn btn-link">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th class="text-center">Items</th>
                        <th class="text-end">Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td>
                                <a href="{{ route('admin.orders.show', $order) }}" class="fw-semibold">{{ $order->order_number }}</a>
                                <div class="small text-muted">{{ $order->created_at->format('d M Y, h:i A') }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $order->customer?->name }}</div>
                                <div class="small text-muted">{{ $order->shipping_phone }}</div>
                            </td>
                            <td class="text-center">{{ $order->items_count }}</td>
                            <td class="text-end fw-semibold text-nowrap">{{ money($order->total) }}</td>
                            <td>
                                <div class="small">{{ $order->payment_method_label }}</div>
                                <span class="badge bg-{{ $order->payment_status_color }}-subtle text-{{ $order->payment_status_color }}-emphasis">{{ $order->payment_status_label }}</span>
                                @if ($order->isOverdue())
                                    <span class="badge bg-danger">Overdue</span>
                                @endif
                            </td>
                            <td>@include('admin.orders._badges', ['showPayment' => false])</td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary" title="View"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Invoice"><i class="bi bi-printer"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">No orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($orders->hasPages())
            <div class="card-footer bg-white">{{ $orders->links() }}</div>
        @endif
    </div>
@endsection
