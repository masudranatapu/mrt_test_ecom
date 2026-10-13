@extends('customer.layouts.account')

@section('title', 'Order '.$order->order_number)

@section('account')
    @php($steps = ['pending', 'confirmed', 'processing', 'shipped', 'delivered'])
    @php($currentStep = array_search($order->status, $steps, true))

    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
        <div>
            <a href="{{ route('customer.orders.index') }}" class="small"><i class="bi bi-arrow-left"></i> My orders</a>
            <h1 class="h4 fw-bold mb-0 mt-1">Order {{ $order->order_number }}</h1>
            <div class="small text-muted">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</div>
        </div>
        <div>@include('admin.orders._badges')</div>
    </div>

    {{-- Result of an online payment (set by the gateway return URL) --}}
    @switch(request('payment'))
        @case('success')
            <div class="alert alert-success d-flex gap-2"><i class="bi bi-check-circle-fill fs-5"></i>
                <div><strong>Payment successful!</strong> We have received {{ money($order->total) }}. Your order is being processed.</div>
            </div>
            @break
        @case('cancelled')
            <div class="alert alert-warning d-flex gap-2"><i class="bi bi-exclamation-circle-fill fs-5"></i>
                <div><strong>Payment cancelled.</strong> Your order is saved — you can pay again below.</div>
            </div>
            @break
        @case('failed')
            <div class="alert alert-danger d-flex gap-2"><i class="bi bi-x-circle-fill fs-5"></i>
                <div><strong>Payment failed.</strong> No money was taken. Please try again or choose another method.</div>
            </div>
            @break
    @endswitch

    @if ($order->canBePaidOnline())
        <div class="card border-primary mb-4">
            <div class="card-body d-flex flex-wrap align-items-center gap-3">
                <div class="flex-grow-1">
                    <div class="fw-bold">Payment pending: {{ money($order->total) }}</div>
                    <div class="small text-muted">Complete the payment to confirm your order.</div>
                </div>
                @foreach (online_payment_methods() as $gateway)
                    <form action="{{ route('customer.orders.pay', $order) }}" method="POST">
                        @csrf
                        <input type="hidden" name="gateway" value="{{ $gateway }}">
                        @if ($gateway === 'bkash')
                            <button class="btn btn-bkash"><i class="bi bi-phone me-1"></i> Pay with bKash</button>
                        @else
                            <button class="btn btn-primary"><i class="bi bi-credit-card me-1"></i> Card / Mobile Banking</button>
                        @endif
                    </form>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Progress --}}
    <div class="card mb-4">
        <div class="card-body">
            @if ($order->status === 'cancelled')
                <div class="alert alert-danger mb-0">
                    <i class="bi bi-x-circle me-1"></i> This order was cancelled on {{ $order->cancelled_at?->format('d M Y, h:i A') }}.
                </div>
            @else
                <div class="order-progress">
                    @foreach ($steps as $i => $step)
                        <div @class(['step', 'done' => $currentStep !== false && $i <= $currentStep])>
                            <span class="circle"><i class="bi {{ \App\Models\Order::STATUSES[$step]['icon'] }}"></i></span>
                            <span class="label">{{ \App\Models\Order::STATUSES[$step]['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white fw-semibold">Items</div>
                <ul class="list-group list-group-flush">
                    @foreach ($order->items as $item)
                        <li class="list-group-item d-flex align-items-center gap-3">
                            <img src="{{ $item->product?->thumbnail_url ?? default_image() }}" {!! img_fallback() !!} class="rounded" style="width:56px;height:56px;object-fit:cover" alt="">
                            <div class="flex-grow-1">
                                @if ($item->product && $item->product->status && ! $item->product->trashed())
                                    <a href="{{ route('product.show', $item->product->slug) }}" class="fw-semibold text-body">{{ $item->product_name }}</a>
                                @else
                                    <span class="fw-semibold">{{ $item->product_name }}</span>
                                @endif
                                <div class="small text-muted">{{ $item->quantity }} {{ $item->unit }} × {{ money($item->price) }}</div>
                            </div>
                            <div class="fw-semibold text-nowrap">{{ money($item->line_total) }}</div>
                        </li>
                    @endforeach
                </ul>
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between small"><span class="text-muted">Subtotal</span><span>{{ money($order->subtotal) }}</span></div>
                    <div class="d-flex justify-content-between small"><span class="text-muted">Delivery</span><span>{{ money($order->shipping_charge) }}</span></div>
                    <div class="d-flex justify-content-between fw-bold mt-1"><span>Total</span><span class="text-primary">{{ money($order->total) }}</span></div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header bg-white fw-semibold">Delivery Address</div>
                <div class="card-body small">
                    <div class="fw-semibold">{{ $order->shipping_name }}</div>
                    <div>{{ $order->shipping_phone }}</div>
                    <div class="text-muted">{{ $order->shipping_full_address }}</div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header bg-white fw-semibold">Payment</div>
                <div class="card-body small">
                    <div>{{ $order->payment_method_label }}</div>
                    <div>Status: <strong>{{ $order->payment_status_label }}</strong></div>
                    @if ($order->due_date)
                        <div @class(['text-danger fw-semibold' => $order->isOverdue()])>Due by {{ $order->due_date->format('d M Y') }}</div>
                    @endif
                </div>
            </div>

            <a href="{{ route('complaint', ['order' => $order->id]) }}" class="btn btn-light border w-100 mb-2">
                <i class="bi bi-exclamation-octagon me-1"></i> Report a problem
            </a>

            @if ($order->canBeCancelledByCustomer())
                <form action="{{ route('customer.orders.cancel', $order) }}" method="POST" data-confirm="Cancel this order?">
                    @csrf
                    @method('PATCH')
                    <input type="text" name="reason" class="form-control form-control-sm mb-2" maxlength="255" placeholder="Reason (optional)">
                    <button class="btn btn-outline-danger w-100"><i class="bi bi-x-circle me-1"></i> Cancel Order</button>
                </form>
            @endif
        </div>
    </div>
@endsection
