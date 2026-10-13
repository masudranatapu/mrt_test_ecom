@extends('frontend.layouts.app')

@section('title', 'Vision & Mission')

@section('content')
    <div class="page-banner text-center">
        <div class="container">
            <span class="section-kicker">About {{ setting('store_name', config('app.name')) }}</span>
            <h1>Our Vision &amp; Mission</h1>
            <p class="text-muted mb-0 mx-auto" style="max-width: 640px">
                We bring quality groceries and daily essentials to homes and businesses across Bangladesh —
                at fair prices, delivered on time.
            </p>
        </div>
    </div>

    <section class="section">
        <div class="container">
            <div class="row g-4 align-items-stretch">
                <div class="col-md-6">
                    <div class="vm-card vm-vision h-100">
                        <span class="vm-icon"><i class="bi bi-eye"></i></span>
                        <h2 class="h4 fw-bold">Our Vision</h2>
                        <p>
                            To become Bangladesh's most trusted online source for groceries and daily essentials, where every
                            family and every business can get genuine products at a fair price with just a few clicks.
                        </p>
                        <p class="mb-0">
                            We imagine a future where shopping for the things you need every day is simple, transparent and
                            available to everyone — in the city and beyond.
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="vm-card vm-mission h-100">
                        <span class="vm-icon"><i class="bi bi-bullseye"></i></span>
                        <h2 class="h4 fw-bold">Our Mission</h2>
                        <ul class="vm-list">
                            <li><i class="bi bi-check2-circle"></i> Source only genuine products directly from trusted suppliers.</li>
                            <li><i class="bi bi-check2-circle"></i> Keep prices fair and clearly shown — no hidden charges.</li>
                            <li><i class="bi bi-check2-circle"></i> Deliver on time, every time, with careful packing.</li>
                            <li><i class="bi bi-check2-circle"></i> Support local shops and businesses with flexible credit terms.</li>
                            <li><i class="bi bi-check2-circle"></i> Listen to our customers and solve every problem quickly.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-4 text-center">
                @foreach ([
                    ['icon' => 'bi-shield-check', 'title' => 'Genuine Products', 'text' => 'Sourced from authorised distributors'],
                    ['icon' => 'bi-tags', 'title' => 'Fair Prices', 'text' => 'Transparent pricing, no surprises'],
                    ['icon' => 'bi-truck', 'title' => 'Reliable Delivery', 'text' => 'Inside and outside Dhaka'],
                    ['icon' => 'bi-headset', 'title' => 'Real Support', 'text' => 'A team that answers and helps'],
                ] as $value)
                    <div class="col-6 col-lg-3">
                        <div class="contact-info-card flex-column text-center">
                            <span class="icon"><i class="bi {{ $value['icon'] }}"></i></span>
                            <div class="fw-bold">{{ $value['title'] }}</div>
                            <div class="small text-muted">{{ $value['text'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="help-cta mt-5">
                <div>
                    <h5 class="fw-bold mb-1">Shop with us today</h5>
                    <p class="mb-0 text-muted">Everyday essentials, delivered to your door.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('shop') }}" class="btn btn-primary">Start Shopping</a>
                    <a href="{{ route('contact') }}" class="btn btn-outline-primary">Contact Us</a>
                </div>
            </div>
        </div>
    </section>
@endsection
