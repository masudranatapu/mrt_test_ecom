@extends('customer.layouts.account')

@section('title', 'My Account')

@section('account')
    <h1 class="h4 fw-bold mb-1">Hello, {{ $customer->name }}!</h1>
    <p class="text-muted">Manage your orders, addresses and account details from here.</p>

    @unless ($customer->is_approved)
        <div class="alert alert-warning d-flex gap-2">
            <i class="bi bi-hourglass-split fs-5"></i>
            <div>
                <strong>Your account is waiting for approval.</strong>
                Once our team approves it, you will be able to see product prices and use the 28-day credit payment option.
            </div>
        </div>
    @endunless

    <div class="row g-3 mb-4">
        @foreach ([
            ['label' => 'Orders', 'value' => $orderCount, 'icon' => 'bi-bag-check', 'url' => route('customer.orders.index')],
            ['label' => 'Wishlist', 'value' => $wishlistCount, 'icon' => 'bi-heart', 'url' => route('customer.wishlist.index')],
            ['label' => 'Addresses', 'value' => $addressCount, 'icon' => 'bi-geo-alt', 'url' => route('customer.addresses.index')],
        ] as $card)
            <div class="col-md-4">
                <a href="{{ $card['url'] }}" class="account-stat">
                    <i class="bi {{ $card['icon'] }}"></i>
                    <div>
                        <div class="fs-4 fw-bold">{{ $card['value'] }}</div>
                        <div class="text-muted small">{{ $card['label'] }}</div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    @if ($recentOrders->isNotEmpty())
        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between">
                <span class="fw-semibold">Recent Orders</span>
                <a href="{{ route('customer.orders.index') }}" class="small">View all</a>
            </div>
            <ul class="list-group list-group-flush">
                @foreach ($recentOrders as $order)
                    <li class="list-group-item d-flex flex-wrap align-items-center gap-2">
                        <div class="flex-grow-1">
                            <a href="{{ route('customer.orders.show', $order) }}" class="fw-semibold">{{ $order->order_number }}</a>
                            <div class="small text-muted">{{ $order->created_at->format('d M Y') }} &middot; {{ $order->items_count }} items</div>
                        </div>
                        <span class="fw-semibold">{{ money($order->total) }}</span>
                        @include('admin.orders._badges', ['showPayment' => false])
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between">
                    <span class="fw-semibold">Account Details</span>
                    <a href="{{ route('customer.profile.edit') }}" class="small">Edit</a>
                </div>
                <div class="card-body small">
                    <div class="mb-1"><i class="bi bi-person me-2 text-muted"></i>{{ $customer->name }}</div>
                    <div class="mb-1"><i class="bi bi-envelope me-2 text-muted"></i>{{ $customer->email }}</div>
                    <div class="mb-1"><i class="bi bi-telephone me-2 text-muted"></i>{{ $customer->phone ?? '—' }}</div>
                    @if ($customer->business_name)
                        <div><i class="bi bi-building me-2 text-muted"></i>{{ $customer->business_name }}</div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between">
                    <span class="fw-semibold">Default Address</span>
                    <a href="{{ route('customer.addresses.index') }}" class="small">Manage</a>
                </div>
                <div class="card-body small">
                    @if ($address = $customer->defaultAddress)
                        <div class="fw-semibold">{{ $address->name }} &middot; {{ $address->phone }}</div>
                        <div class="text-muted">{{ $address->full_address }}</div>
                    @else
                        <p class="text-muted mb-2">No address added yet.</p>
                        <a href="{{ route('customer.addresses.create') }}" class="btn btn-sm btn-outline-primary">Add Address</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
