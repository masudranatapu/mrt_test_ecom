@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="page-header">
        <div>
            <h1>Dashboard</h1>
            <p class="text-muted mb-0">Welcome back, {{ auth('admin')->user()->name }}!</p>
        </div>
    </div>

    <div class="row g-3">
        @foreach ([
            ['label' => "Today's Orders", 'value' => number_format($stats['orders_today']), 'icon' => 'bi-bag-check', 'color' => 'primary', 'url' => route('admin.orders.index', ['from' => today()->toDateString()])],
            ['label' => 'Sales This Month', 'value' => money($stats['sales_month'], 0), 'icon' => 'bi-graph-up-arrow', 'color' => 'success', 'url' => route('admin.orders.index')],
            ['label' => 'Pending Orders', 'value' => number_format($stats['pending_orders']), 'icon' => 'bi-hourglass-split', 'color' => 'warning', 'url' => route('admin.orders.index', ['status' => 'pending'])],
            ['label' => 'Awaiting Approval', 'value' => number_format($stats['pending_customers']), 'icon' => 'bi-person-check', 'color' => 'info', 'url' => route('admin.customers.index', ['approval' => 'pending'])],
        ] as $card)
            <div class="col-sm-6 col-xl-3">
                <a href="{{ $card['url'] }}" class="text-decoration-none text-body">
                    <div class="card stat-card h-100">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="stat-icon bg-{{ $card['color'] }}-subtle text-{{ $card['color'] }}">
                                <i class="bi {{ $card['icon'] }}"></i>
                            </div>
                            <div>
                                <div class="stat-value">{{ $card['value'] }}</div>
                                <div class="stat-label">{{ $card['label'] }}</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="row g-4 mt-1">
        <div class="col-xl-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Recent Orders</span>
                    <a href="{{ route('admin.orders.index') }}" class="small fw-normal">View all</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Customer</th>
                                <th class="text-end">Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentOrders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order) }}" class="fw-semibold">{{ $order->order_number }}</a>
                                        <div class="small text-muted">{{ $order->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td>{{ $order->customer?->name }}</td>
                                    <td class="text-end fw-semibold text-nowrap">{{ money($order->total) }}</td>
                                    <td>@include('admin.orders._badges')</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-5">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i> No orders yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Unpaid credit (28-day)</span>
                        <i class="bi bi-credit-card text-warning"></i>
                    </div>
                    <div class="fs-4 fw-bold">{{ money($stats['unpaid_credit'], 0) }}</div>
                    <a href="{{ route('admin.orders.index', ['payment_method' => 'credit', 'payment_status' => 'unpaid']) }}" class="small">View credit orders</a>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Low Stock <span class="badge bg-danger-subtle text-danger">{{ $stats['low_stock'] }}</span></span>
                    <a href="{{ route('admin.inventory.index', ['stock' => 'low']) }}" class="small fw-normal">View all</a>
                </div>
                <ul class="list-group list-group-flush">
                    @forelse ($lowStockProducts as $product)
                        <li class="list-group-item d-flex align-items-center gap-2">
                            <img src="{{ $product->thumbnail_url }}" {!! img_fallback() !!} class="rounded avatar-sm" alt="">
                            <a href="{{ route('admin.products.show', $product) }}" class="text-body flex-grow-1 small">{{ $product->name }}</a>
                            @include('admin.products._stock-badge')
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted py-4">All products are well stocked.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection
