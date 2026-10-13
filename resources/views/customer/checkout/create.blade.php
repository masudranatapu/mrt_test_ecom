@extends('frontend.layouts.app')

@section('title', 'Checkout')

@section('content')
    <div class="page-banner">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('cart.index') }}">Cart</a></li>
                    <li class="breadcrumb-item active">Checkout</li>
                </ol>
            </nav>
            <h1>Checkout</h1>
        </div>
    </div>

    <section class="section pt-4">
        <div class="container">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Please check the form:</strong>
                    <ul class="mb-0 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('customer.checkout.store') }}" novalidate>
                @csrf
                <div class="row g-4">
                    <div class="col-lg-7">
                        {{-- Address --}}
                        <div class="checkout-step">
                            <h5 class="step-title"><span>1</span> Delivery Address</h5>

                            @php($selectedAddress = old('address_id', $addresses->firstWhere('is_default', true)?->id ?? ''))
                            @foreach ($addresses as $address)
                                <label class="option-card">
                                    <input type="radio" name="address_id" value="{{ $address->id }}" @checked((string) $selectedAddress === (string) $address->id)>
                                    <div>
                                        <div class="fw-semibold">{{ $address->label }} @if ($address->is_default)<span class="badge bg-primary-subtle text-primary">Default</span>@endif</div>
                                        <div class="small">{{ $address->name }} &middot; {{ $address->phone }}</div>
                                        <div class="small text-muted">{{ $address->full_address }}</div>
                                    </div>
                                </label>
                            @endforeach

                            <label class="option-card">
                                <input type="radio" name="address_id" value="" @checked((string) $selectedAddress === '')>
                                <div class="fw-semibold"><i class="bi bi-plus-circle me-1"></i> Use a new address</div>
                            </label>

                            <div id="newAddressFields" class="mt-3">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required" for="name">Receiver name</label>
                                        <input type="text" id="name" name="name" value="{{ old('name', $customer->name) }}" class="form-control @error('name') is-invalid @enderror">
                                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required" for="phone">Phone</label>
                                        <input type="tel" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}" class="form-control @error('phone') is-invalid @enderror">
                                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label required" for="address_line">Address</label>
                                    <input type="text" id="address_line" name="address_line" value="{{ old('address_line') }}" placeholder="House, road, block"
                                        class="form-control @error('address_line') is-invalid @enderror">
                                    @error('address_line')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="area">Area / Thana</label>
                                        <input type="text" id="area" name="area" value="{{ old('area') }}" class="form-control">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label required" for="city">City / District</label>
                                        <input type="text" id="city" name="city" value="{{ old('city') }}" class="form-control @error('city') is-invalid @enderror">
                                        @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="postal_code">Postal code</label>
                                        <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" class="form-control">
                                    </div>
                                </div>
                                <input type="hidden" name="save_address" value="0">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="save_address" value="1" id="saveAddress" @checked(old('save_address', true))>
                                    <label class="form-check-label" for="saveAddress">Save this address for next time</label>
                                </div>
                            </div>
                        </div>

                        {{-- Delivery zone --}}
                        <div class="checkout-step">
                            <h5 class="step-title"><span>2</span> Delivery Area</h5>
                            <div class="row g-2">
                                @foreach ($zones as $key => $zone)
                                    <div class="col-md-6">
                                        <label class="option-card h-100">
                                            <input type="radio" name="shipping_zone" value="{{ $key }}" data-charge="{{ $zone['charge'] }}" @checked($selectedZone === $key)>
                                            <div class="d-flex justify-content-between w-100">
                                                <span class="fw-semibold">{{ $zone['label'] }}</span>
                                                <span class="text-primary fw-semibold">{{ money($zone['charge'], 0) }}</span>
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Payment --}}
                        <div class="checkout-step">
                            <h5 class="step-title"><span>3</span> Payment Method</h5>
                            <label class="option-card">
                                <input type="radio" name="payment_method" value="cod" @checked(old('payment_method', 'cod') === 'cod')>
                                <div>
                                    <div class="fw-semibold"><i class="bi bi-cash-coin me-1 text-success"></i> Cash on Delivery</div>
                                    <div class="small text-muted">Pay in cash when your order arrives.</div>
                                </div>
                            </label>
                            @if ($customer->canUseCredit())
                                <label class="option-card">
                                    <input type="radio" name="payment_method" value="credit" @checked(old('payment_method') === 'credit')>
                                    <div>
                                        <div class="fw-semibold"><i class="bi bi-calendar-check me-1 text-primary"></i> {{ config('shop.credit_days') }}-Day Credit</div>
                                        <div class="small text-muted">Pay within {{ config('shop.credit_days') }} days — due {{ now()->addDays(config('shop.credit_days'))->format('d M Y') }}.</div>
                                    </div>
                                </label>
                            @endif
                            @if (in_array('bkash', online_payment_methods(), true))
                                <label class="option-card">
                                    <input type="radio" name="payment_method" value="bkash" @checked(old('payment_method') === 'bkash')>
                                    <div>
                                        <div class="fw-semibold"><span class="pay-logo bkash">bKash</span> Pay with bKash</div>
                                        <div class="small text-muted">You will be taken to bKash to pay securely with your bKash account.</div>
                                    </div>
                                </label>
                            @endif
                            @if (in_array('sslcommerz', online_payment_methods(), true))
                                <label class="option-card">
                                    <input type="radio" name="payment_method" value="sslcommerz" @checked(old('payment_method') === 'sslcommerz')>
                                    <div>
                                        <div class="fw-semibold"><i class="bi bi-credit-card me-1 text-primary"></i> Card / Mobile Banking / Net Banking</div>
                                        <div class="small text-muted">Visa, Mastercard, Amex, Nagad, Rocket, bank accounts — secured by SSLCommerz.</div>
                                    </div>
                                </label>
                            @endif
                        </div>

                        <div class="checkout-step">
                            <label class="form-label" for="notes">Order note <span class="text-muted small">(optional)</span></label>
                            <textarea id="notes" name="notes" rows="2" maxlength="1000" class="form-control" placeholder="Any instruction for delivery">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    {{-- Summary --}}
                    <div class="col-lg-5">
                        <div class="summary-box" id="orderSummary" data-subtotal="{{ $totals['subtotal'] }}">
                            <h5 class="fw-bold mb-3">Your Order</h5>
                            <div class="summary-items">
                                @foreach ($items as $item)
                                    <div class="d-flex gap-2 mb-3">
                                        <img src="{{ $item->product->thumbnail_url }}" {!! img_fallback() !!} class="rounded" style="width:52px;height:52px;object-fit:cover" alt="">
                                        <div class="flex-grow-1 small">
                                            <div class="fw-semibold">{{ $item->product->name }}</div>
                                            <div class="text-muted">{{ $item->quantity }} × {{ money($item->product->final_price) }}</div>
                                        </div>
                                        <div class="small fw-semibold text-nowrap">{{ money($item->line_total) }}</div>
                                    </div>
                                @endforeach
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal</span>
                                <span>{{ money($totals['subtotal']) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Delivery</span>
                                <span data-shipping>{{ money($totals['shipping']) }}</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-4">
                                <span class="fw-bold">Total</span>
                                <span class="fw-bold fs-4 text-primary" data-total>{{ money($totals['total']) }}</span>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" name="terms" value="1" id="terms" @checked(old('terms'))>
                                <label class="form-check-label small" for="terms">
                                    I have read and agree to the <a href="#">Terms &amp; Conditions</a> and <a href="#">Return Policy</a>.
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100"><i class="bi bi-lock me-1"></i> Place Order</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
