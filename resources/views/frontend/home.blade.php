@extends('frontend.layouts.app')

@section('content')
    {{-- Hero --}}
    <section class="hero">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="hero-badge"><i class="bi bi-stars me-1"></i> New arrivals every week</span>
                    <h1 class="mb-3">Groceries &amp; daily essentials, <span class="text-primary">delivered</span> to your door</h1>
                    <p class="lead text-muted mb-4">
                        Quality products at wholesale-friendly prices. Pay with bKash, card or cash on delivery.
                    </p>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <a href="{{ route('shop') }}" class="btn btn-primary btn-lg px-4">Shop Now <i class="bi bi-arrow-right ms-1"></i></a>
                        <a href="{{ route('shop', ['on_sale' => 1]) }}" class="btn btn-outline-dark btn-lg px-4"><i class="bi bi-tags me-1"></i> Today's Deals</a>
                    </div>
                    <div class="hero-stats">
                        <div><strong>{{ \App\Models\Product::active()->count() }}+</strong><span>Products</span></div>
                        <div><strong>{{ \App\Models\Category::active()->count() }}</strong><span>Categories</span></div>
                        <div><strong>COD</strong><span>Available</span></div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-products">
                        @foreach ($heroProducts as $product)
                            <a href="{{ route('product.show', $product->slug) }}" class="hero-product">
                                <img src="{{ $product->thumbnail_url }}" {!! img_fallback() !!} alt="{{ $product->name }}">
                                <div class="info">
                                    <div class="name">{{ $product->name }}</div>
                                    @if (can_see_price())
                                        <div class="price">{{ money($product->final_price, 0) }}</div>
                                    @else
                                        <div class="price-hidden"><i class="bi bi-lock"></i> Login for price</div>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section class="feature-strip">
        <div class="container">
            <div class="row g-3">
                @foreach ([
                    ['icon' => 'bi-truck', 'title' => 'Fast Delivery', 'text' => 'All over Bangladesh', 'color' => 'primary'],
                    ['icon' => 'bi-shield-check', 'title' => 'Secure Payment', 'text' => 'bKash & SSLCommerz', 'color' => 'success'],
                    ['icon' => 'bi-cash-coin', 'title' => 'Cash on Delivery', 'text' => 'Pay when you receive', 'color' => 'warning'],
                    ['icon' => 'bi-arrow-repeat', 'title' => 'Easy Returns', 'text' => 'Hassle-free policy', 'color' => 'danger'],
                ] as $feature)
                    <div class="col-6 col-lg-3">
                        <div class="feature">
                            <span class="feature-icon bg-{{ $feature['color'] }}-subtle text-{{ $feature['color'] }}"><i class="bi {{ $feature['icon'] }}"></i></span>
                            <div>
                                <h6>{{ $feature['title'] }}</h6>
                                <small>{{ $feature['text'] }}</small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Categories --}}
    @if ($featuredCategories->isNotEmpty())
        <section class="section pb-0">
            <div class="container">
                <div class="section-head">
                    <div>
                        <span class="section-kicker">Browse</span>
                        <h2 class="section-title">Shop by Category</h2>
                    </div>
                    <a href="{{ route('shop') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">View all <i class="bi bi-arrow-right"></i></a>
                </div>
                <div class="row g-3">
                    @foreach ($featuredCategories as $category)
                        <div class="col-6 col-md-4 col-lg-2">
                            <a href="{{ route('category.show', $category) }}" class="category-tile tone-{{ $loop->index % 6 }}">
                                <span class="icon"><i class="bi {{ $category->icon ?? 'bi-grid' }}"></i></span>
                                <span class="name">{{ $category->name }}</span>
                                <span class="count">{{ $category->children_count }} sub-categories</span>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Campaigns (right after categories) --}}
    @if ($campaigns->isNotEmpty())
        <section class="section pb-0">
            <div class="container">
                <div class="row g-3">
                    @foreach ($campaigns as $campaign)
                        @php($upcoming = $campaign->starts_at->isFuture())
                        <div @class(['col-12', 'col-lg-6' => $campaigns->count() > 1])>
                            <div class="campaign-banner tone-{{ $loop->index % 3 }}"
                                @if ($campaign->banner_url) style="background-image: linear-gradient(90deg, rgba(15,23,42,.85) 0%, rgba(15,23,42,.35) 100%), url('{{ $campaign->banner_url }}')" @endif>
                                <div class="campaign-content">
                                    <span class="campaign-tag">
                                        <i class="bi bi-megaphone-fill me-1"></i> {{ $upcoming ? 'Coming soon' : 'Campaign is live' }}
                                    </span>
                                    <h3>{{ $campaign->title }}</h3>
                                    @if ($campaign->subtitle)
                                        <p class="mb-2">{{ $campaign->subtitle }}</p>
                                    @endif
                                    <div class="campaign-off">Up to <strong>{{ $campaign->discount_percent }}% OFF</strong> on {{ $campaign->products_count }} products</div>

                                    <div class="small text-uppercase fw-semibold opacity-75 mt-3 mb-1">{{ $upcoming ? 'Starts in' : 'Ends in' }}</div>
                                    @include('frontend.partials.countdown', ['target' => $campaign->countdownTarget(), 'class' => 'countdown-light'])

                                    <a href="{{ route('campaign.show', $campaign->slug) }}" class="btn btn-light fw-semibold mt-3">
                                        {{ $upcoming ? 'Preview products' : 'Shop the campaign' }} <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                                <span class="campaign-percent">{{ $campaign->discount_percent }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Flash sale --}}
    @if ($flashSale && $flashSale->items->isNotEmpty())
        <section class="section pb-0">
            <div class="container">
                <div class="flash-box">
                    <div class="flash-head">
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <h2 class="section-title text-white mb-0"><i class="bi bi-lightning-charge-fill text-warning"></i> {{ $flashSale->title }}</h2>
                            <div class="d-flex align-items-center gap-2">
                                <span class="small text-white-50 text-uppercase fw-semibold">Ends in</span>
                                @include('frontend.partials.countdown', ['target' => $flashSale->ends_at, 'class' => 'countdown-sm'])
                            </div>
                        </div>
                        <a href="{{ route('flash-sale') }}" class="btn btn-warning btn-sm rounded-pill px-3 fw-semibold">See all <i class="bi bi-arrow-right"></i></a>
                    </div>
                    <div class="row g-3">
                        @foreach ($flashSale->items->filter(fn ($i) => $i->product?->status)->take(4) as $item)
                            <div class="col-6 col-lg-3">
                                @include('frontend.flash-sale._item', ['item' => $item])
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- New arrivals --}}
    <section class="section">
        <div class="container">
            <div class="section-head">
                <div>
                    <span class="section-kicker">Just in</span>
                    <h2 class="section-title">New Arrivals</h2>
                </div>
                <a href="{{ route('shop', ['sort' => 'latest']) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">View all <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="row g-3 g-lg-4">
                @forelse ($newArrivals as $product)
                    <div class="col-6 col-md-4 col-lg-3">
                        @include('frontend.products._card')
                    </div>
                @empty
                    <div class="col-12">
                        <div class="empty-state">
                            <img src="{{ asset('default/no-data.png') }}" alt="">
                            <p>No products yet.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Promo banners --}}
    <section class="pb-2">
        <div class="container">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="promo-banner promo-blue">
                        <div>
                            <span class="promo-kicker">For businesses</span>
                            <h3>Get 28-day credit on your orders</h3>
                            <p>Register your business and get approved to unlock credit terms and wholesale prices.</p>
                            @guest('customer')
                                <a href="{{ route('customer.register') }}" class="btn btn-light btn-sm fw-semibold">Create Account</a>
                            @else
                                <a href="{{ route('customer.dashboard') }}" class="btn btn-light btn-sm fw-semibold">My Account</a>
                            @endguest
                        </div>
                        <i class="bi bi-briefcase promo-icon"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="promo-banner promo-orange">
                        <div>
                            <span class="promo-kicker">Pay your way</span>
                            <h3>Cash on delivery &amp; bKash</h3>
                            <p>Pay when your order arrives, or pay instantly with bKash, cards and mobile banking.</p>
                            <a href="{{ route('shop') }}" class="btn btn-light btn-sm fw-semibold">Start Shopping</a>
                        </div>
                        <i class="bi bi-wallet2 promo-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Deals --}}
    @if ($onSaleProducts->isNotEmpty())
        <section class="section">
            <div class="container">
                <div class="deals-box">
                    <div class="section-head">
                        <div>
                            <span class="section-kicker text-danger"><i class="bi bi-lightning-charge-fill"></i> Hot deals</span>
                            <h2 class="section-title">On Sale Now</h2>
                        </div>
                        <a href="{{ route('shop', ['on_sale' => 1]) }}" class="btn btn-danger btn-sm rounded-pill px-3">All deals <i class="bi bi-arrow-right"></i></a>
                    </div>
                    <div class="row g-3 g-lg-4">
                        @foreach ($onSaleProducts as $product)
                            <div class="col-6 col-lg-3">
                                @include('frontend.products._card')
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- Featured --}}
    @if ($featuredProducts->isNotEmpty())
        <section class="section bg-soft">
            <div class="container">
                <div class="section-head">
                    <div>
                        <span class="section-kicker">Hand-picked</span>
                        <h2 class="section-title">Featured Products</h2>
                    </div>
                </div>
                <div class="row g-3 g-lg-4">
                    @foreach ($featuredProducts as $product)
                        <div class="col-6 col-md-4 col-lg-3">
                            @include('frontend.products._card')
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Best sellers --}}
    @if ($bestSellers->isNotEmpty())
        <section class="section">
            <div class="container">
                <div class="section-head">
                    <div>
                        <span class="section-kicker">Customer favourites</span>
                        <h2 class="section-title">Best Sellers</h2>
                    </div>
                    <a href="{{ route('shop', ['sort' => 'popular']) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">View all <i class="bi bi-arrow-right"></i></a>
                </div>
                <div class="row g-3 g-lg-4">
                    @foreach ($bestSellers as $product)
                        <div class="col-6 col-md-4 col-lg-3">
                            @include('frontend.products._card')
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
