@extends('customer.layouts.account')

@section('title', 'My Orders')

@section('account')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <h1 class="h4 fw-bold mb-0">My Orders</h1>
        <form method="GET">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All orders</option>
                @foreach (\App\Models\Order::STATUSES as $key => $status)
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $status['label'] }}</option>
                @endforeach
            </select>
        </form>
    </div>

    @forelse ($orders as $order)
        <div class="card mb-3">
            <div class="card-body d-flex flex-wrap align-items-center gap-3">
                <div class="flex-grow-1">
                    <a href="{{ route('customer.orders.show', $order) }}" class="fw-bold">{{ $order->order_number }}</a>
                    <div class="small text-muted">
                        {{ $order->created_at->format('d M Y, h:i A') }} &middot; {{ $order->items_count }} items &middot; {{ $order->payment_method_label }}
                    </div>
                </div>
                <div class="text-end">
                    <div class="fw-bold">{{ money($order->total) }}</div>
                    @include('admin.orders._badges')
                </div>
                <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">Details</a>
            </div>
        </div>
    @empty
        <div class="text-center text-muted py-5 bg-white rounded-3">
            <i class="bi bi-bag fs-1 d-block mb-2"></i>
            You have not placed any orders yet.
            <div class="mt-3"><a href="{{ route('shop') }}" class="btn btn-primary btn-sm">Start Shopping</a></div>
        </div>
    @endforelse

    @if ($orders->hasPages())
        <div class="mt-3">{{ $orders->links() }}</div>
    @endif
@endsection
