@extends('admin.layouts.app')

@section('title', $order->order_number)

@section('content')
    <div class="page-header">
        <div>
            <h1>Order {{ $order->order_number }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
                    <li class="breadcrumb-item active">{{ $order->order_number }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-2">
            @include('admin.orders._badges')
            <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank" class="btn btn-outline-secondary"><i class="bi bi-printer me-1"></i> Invoice</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            {{-- Items --}}
            <div class="card mb-4">
                <div class="card-header">Items</div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-end">Price</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ $item->product?->thumbnail_url ?? default_image() }}" {!! img_fallback() !!} class="rounded avatar-sm" alt="">
                                            <div>
                                                @if ($item->product && ! $item->product->trashed())
                                                    <a href="{{ route('admin.products.show', $item->product) }}" class="fw-semibold text-body">{{ $item->product_name }}</a>
                                                @else
                                                    <span class="fw-semibold">{{ $item->product_name }}</span>
                                                @endif
                                                <div class="small text-muted">SKU: {{ $item->sku }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end text-nowrap">{{ money($item->price) }}</td>
                                    <td class="text-center">{{ $item->quantity }} {{ $item->unit }}</td>
                                    <td class="text-end fw-semibold text-nowrap">{{ money($item->line_total) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="text-end">Subtotal</td>
                                <td class="text-end">{{ money($order->subtotal) }}</td>
                            </tr>
                            @if ((float) $order->discount > 0)
                                <tr>
                                    <td colspan="3" class="text-end">Discount</td>
                                    <td class="text-end text-danger">−{{ money($order->discount) }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td colspan="3" class="text-end">Delivery ({{ config("shop.shipping.{$order->shipping_zone}.label", $order->shipping_zone) }})</td>
                                <td class="text-end">{{ money($order->shipping_charge) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Total</td>
                                <td class="text-end fw-bold fs-5">{{ money($order->total) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Timeline --}}
            <div class="card">
                <div class="card-header">Order History</div>
                <div class="card-body">
                    <ul class="order-timeline">
                        @foreach ($order->histories as $history)
                            @php($meta = \App\Models\Order::STATUSES[$history->status] ?? ['label' => ucfirst($history->status), 'color' => 'secondary', 'icon' => 'bi-circle'])
                            <li>
                                <span class="dot bg-{{ $meta['color'] }}"><i class="bi {{ $meta['icon'] }}"></i></span>
                                <div class="fw-semibold">{{ $meta['label'] }}</div>
                                @if ($history->note)
                                    <div class="small">{{ $history->note }}</div>
                                @endif
                                <div class="small text-muted">
                                    {{ $history->created_at->format('d M Y, h:i A') }} &middot; {{ $history->admin?->name ?? 'Customer / System' }}
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Status --}}
            <div class="card mb-4">
                <div class="card-header">Update Status</div>
                <div class="card-body">
                    @if ($order->isFinal())
                        <p class="text-muted small mb-0">This order is <strong>{{ $order->status_label }}</strong> and can no longer be changed.</p>
                    @else
                        <form action="{{ route('admin.orders.status', $order) }}" method="POST" novalidate>
                            @csrf
                            @method('PATCH')
                            <select name="status" class="form-select mb-2">
                                @foreach (\App\Models\Order::STATUSES as $key => $status)
                                    <option value="{{ $key }}" @selected($order->status === $key)>{{ $status['label'] }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="note" class="form-control mb-2" maxlength="255" placeholder="Note (optional)">
                            <button class="btn btn-primary w-100"><i class="bi bi-check2 me-1"></i> Update Status</button>
                            <div class="form-text">Cancelling puts the items back in stock.</div>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Payment --}}
            <div class="card mb-4">
                <div class="card-header">Payment</div>
                <div class="card-body">
                    <dl class="row small mb-3">
                        <dt class="col-5 text-muted fw-normal">Method</dt>
                        <dd class="col-7">{{ $order->payment_method_label }}</dd>
                        <dt class="col-5 text-muted fw-normal">Status</dt>
                        <dd class="col-7"><span class="badge bg-{{ $order->payment_status_color }}-subtle text-{{ $order->payment_status_color }}-emphasis">{{ $order->payment_status_label }}</span></dd>
                        @if ($order->due_date)
                            <dt class="col-5 text-muted fw-normal">Due date</dt>
                            <dd class="col-7 {{ $order->isOverdue() ? 'text-danger fw-semibold' : '' }}">{{ $order->due_date->format('d M Y') }}</dd>
                        @endif
                        @if ($order->paid_at)
                            <dt class="col-5 text-muted fw-normal">Paid at</dt>
                            <dd class="col-7">{{ $order->paid_at->format('d M Y, h:i A') }}</dd>
                        @endif
                    </dl>
                    @if ($order->payments->isNotEmpty())
                        <div class="small fw-semibold mb-1">Online payment attempts</div>
                        <ul class="list-unstyled small mb-3">
                            @foreach ($order->payments as $payment)
                                <li class="border rounded p-2 mb-1">
                                    <div class="d-flex justify-content-between">
                                        <span>{{ \App\Models\Payment::GATEWAYS[$payment->gateway] ?? $payment->gateway }} &middot; {{ money($payment->amount) }}</span>
                                        <span @class([
                                            'badge',
                                            'bg-success' => $payment->status === 'success',
                                            'bg-danger' => $payment->status === 'failed',
                                            'bg-secondary' => in_array($payment->status, ['cancelled', 'initiated']),
                                        ])>{{ ucfirst($payment->status) }}</span>
                                    </div>
                                    <div class="text-muted">{{ $payment->transaction_id }}</div>
                                    @if ($payment->gateway_transaction_id)
                                        <div class="text-muted">Gateway Txn: {{ $payment->gateway_transaction_id }}</div>
                                    @endif
                                    <div class="text-muted">{{ $payment->created_at->format('d M Y, h:i A') }}</div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    <form action="{{ route('admin.orders.payment', $order) }}" method="POST" class="d-flex gap-2">
                        @csrf
                        @method('PATCH')
                        <select name="payment_status" class="form-select form-select-sm">
                            @foreach (\App\Models\Order::PAYMENT_STATUSES as $key => $status)
                                <option value="{{ $key }}" @selected($order->payment_status === $key)>{{ $status['label'] }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-sm btn-outline-primary text-nowrap">Save</button>
                    </form>
                </div>
            </div>

            {{-- Customer & shipping --}}
            <div class="card">
                <div class="card-header">Customer &amp; Delivery</div>
                <div class="card-body small">
                    @if ($order->customer)
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <img src="{{ $order->customer->avatar_url }}" {!! img_fallback(true) !!} class="rounded-circle avatar-sm" alt="">
                            <div>
                                <a href="{{ route('admin.customers.show', $order->customer) }}" class="fw-semibold">{{ $order->customer->name }}</a>
                                <div class="text-muted">{{ $order->customer->email }}</div>
                            </div>
                        </div>
                    @endif
                    <div class="fw-semibold">{{ $order->shipping_name }}</div>
                    <div><i class="bi bi-telephone me-1 text-muted"></i> {{ $order->shipping_phone }}</div>
                    <div><i class="bi bi-geo-alt me-1 text-muted"></i> {{ $order->shipping_full_address }}</div>
                    <div class="mt-1"><span class="badge bg-light text-dark border">{{ config("shop.shipping.{$order->shipping_zone}.label", $order->shipping_zone) }}</span></div>
                    @if ($order->notes)
                        <div class="alert alert-light border small mt-3 mb-0"><strong>Customer note:</strong> {{ $order->notes }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
