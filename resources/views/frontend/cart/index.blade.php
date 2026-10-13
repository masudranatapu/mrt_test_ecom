@extends('frontend.layouts.app')

@section('title', 'Shopping Cart')

@section('content')
    <div class="page-banner">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Cart</li>
                </ol>
            </nav>
            <h1>Shopping Cart</h1>
        </div>
    </div>

    <section class="section pt-4">
        <div class="container">
            @if ($items->isEmpty())
                <div class="empty-state bg-white">
                    <i class="bi bi-cart-x display-4 d-block mb-2"></i>
                    <h5 class="fw-bold text-dark">Your cart is empty</h5>
                    <p>Looks like you have not added anything yet.</p>
                    <a href="{{ route('shop') }}" class="btn btn-primary">Start Shopping</a>
                </div>
            @else
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="cart-box">
                            @foreach ($items as $item)
                                @php($product = $item->product)
                                <div class="cart-row">
                                    <a href="{{ route('product.show', $product->slug) }}" class="cart-thumb">
                                        <img src="{{ $product->thumbnail_url }}" {!! img_fallback() !!} alt="{{ $product->name }}">
                                    </a>
                                    <div class="flex-grow-1">
                                        <a href="{{ route('product.show', $product->slug) }}" class="fw-semibold text-body">{{ $product->name }}</a>
                                        <div class="small text-muted">{{ $product->category?->name }} &middot; {{ money($product->final_price) }} / {{ $product->unit }}</div>
                                        @if ($item->quantity > $product->stock_qty)
                                            <div class="small text-danger"><i class="bi bi-exclamation-triangle"></i> Only {{ $product->stock_qty }} left — please reduce quantity.</div>
                                        @else
                                            @include('frontend.products._stock')
                                        @endif
                                    </div>
                                    <form action="{{ route('cart.update', $item) }}" method="POST" class="cart-qty">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ max(1, $product->stock_qty) }}"
                                            class="form-control form-control-sm text-center" data-auto-submit aria-label="Quantity">
                                    </form>
                                    <div class="cart-line-total">{{ money($item->line_total) }}</div>
                                    <form action="{{ route('cart.destroy', $item) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-link text-danger" title="Remove"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                        <a href="{{ route('shop') }}" class="btn btn-link px-0 mt-3"><i class="bi bi-arrow-left"></i> Continue shopping</a>
                    </div>

                    <div class="col-lg-4">
                        <div class="summary-box">
                            <h5 class="fw-bold mb-3">Order Summary</h5>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal ({{ $items->sum('quantity') }} items)</span>
                                <span class="fw-semibold">{{ money($totals['subtotal']) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3 small text-muted">
                                <span>Delivery</span>
                                <span>Calculated at checkout</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-4">
                                <span class="fw-bold">Total</span>
                                <span class="fw-bold fs-5 text-primary">{{ money($totals['subtotal']) }}</span>
                            </div>
                            <a href="{{ route('customer.checkout') }}" class="btn btn-primary btn-lg w-100">
                                Proceed to Checkout <i class="bi bi-arrow-right"></i>
                            </a>
                            @guest('customer')
                                <p class="small text-muted text-center mt-2 mb-0">You will be asked to log in first.</p>
                            @endguest
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
