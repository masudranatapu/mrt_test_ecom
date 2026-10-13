@extends('frontend.layouts.app')

@section('title', $product->name)
@section('meta_description', $product->short_description)

@section('content')
    <div class="page-banner py-3">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('shop') }}">Shop</a></li>
                    @if ($product->category?->parent)
                        <li class="breadcrumb-item"><a href="{{ route('category.show', $product->category->parent) }}">{{ $product->category->parent->name }}</a></li>
                    @endif
                    @if ($product->category)
                        <li class="breadcrumb-item"><a href="{{ route('category.show', $product->category) }}">{{ $product->category->name }}</a></li>
                    @endif
                    <li class="breadcrumb-item active">{{ $product->name }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="section pt-4">
        <div class="container">
            <div class="row g-5">
                {{-- Gallery --}}
                <div class="col-lg-6">
                    <div class="product-gallery">
                        <div class="main-image">
                            <img src="{{ $product->thumbnail_url }}" {!! img_fallback() !!} id="mainProductImage" alt="{{ $product->name }}">
                            @if ($product->hasDiscount() && can_see_price())
                                <span class="badge-discount">-{{ $product->discount_percent }}%</span>
                            @endif
                        </div>
                        @if ($product->images->isNotEmpty())
                            <div class="thumbs">
                                @foreach (collect([$product->thumbnail_url])->merge($product->images->pluck('url')) as $url)
                                    <img src="{{ $url }}" {!! img_fallback() !!} alt="" onclick="document.getElementById('mainProductImage').src = this.src">
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Info --}}
                <div class="col-lg-6">
                    <div class="product-category mb-1">
                        <a href="{{ route('category.show', $product->category) }}">{{ $product->category?->name }}</a>
                    </div>
                    <h1 class="product-title">{{ $product->name }}</h1>

                    <div class="d-flex flex-wrap align-items-center gap-3 mb-3 small">
                        @include('frontend.products._stock')
                        <span class="text-muted"><i class="bi bi-bag-check"></i> {{ number_format($product->sold_qty) }} sold</span>
                        <span class="text-muted">SKU: {{ $product->sku }}</span>
                    </div>

                    <div class="mb-3">
                        @include('frontend.products._price', ['size' => 'lg'])
                    </div>

                    @if (($promo = $product->promotion()) && can_see_price())
                        <div class="promo-strip {{ $promo['type'] }}">
                            <div>
                                <i class="bi {{ $promo['type'] === 'flash' ? 'bi-lightning-charge-fill' : 'bi-megaphone-fill' }} me-1"></i>
                                <strong>{{ $promo['label'] }}</strong> price — ends in
                            </div>
                            @include('frontend.partials.countdown', ['target' => $promo['ends_at'], 'class' => 'countdown-sm'])
                        </div>
                    @endif

                    @if ($product->short_description)
                        <p class="text-muted">{{ $product->short_description }}</p>
                    @endif

                    <ul class="product-meta list-unstyled">
                        <li><span>Unit</span> {{ $product->unit }}</li>
                        @if ($product->best_before_date)
                            <li><span>Best Before</span> {{ $product->best_before_date->format('d M Y') }}</li>
                        @endif
                        <li><span>Availability</span> {{ $product->stock_qty > 0 ? 'In stock' : 'Out of stock' }}</li>
                    </ul>

                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <form action="{{ route('cart.store') }}" method="POST" class="d-flex gap-2 flex-grow-1" data-ajax-cart>
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <div class="input-group qty-input">
                                <button class="btn btn-outline-secondary" type="button" onclick="this.nextElementSibling.stepDown()">−</button>
                                <input type="number" name="quantity" class="form-control text-center" value="1" min="1" max="{{ max(1, $product->stock_qty) }}">
                                <button class="btn btn-outline-secondary" type="button" onclick="this.previousElementSibling.stepUp()">+</button>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg flex-grow-1" @disabled($product->stock_qty === 0)>
                                <i class="bi bi-cart-plus me-1"></i> {{ $product->stock_qty === 0 ? 'Out of Stock' : 'Add to Cart' }}
                            </button>
                        </form>
                        @php($inWishlist = in_array($product->id, wishlist_ids(), true))
                        <form action="{{ route('customer.wishlist.store') }}" method="POST" data-ajax-wishlist>
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button type="submit" @class(['btn btn-outline-danger btn-lg', 'active' => $inWishlist]) title="Wishlist">
                                <i @class(['bi', 'bi-heart-fill' => $inWishlist, 'bi-heart' => ! $inWishlist])></i>
                            </button>
                        </form>
                    </div>
                    @unless (can_see_price())
                        <div class="small text-muted mt-2">
                            @auth('customer')
                                <i class="bi bi-info-circle"></i> You can order once your account is approved.
                            @else
                                <i class="bi bi-info-circle"></i> <a href="{{ route('customer.login') }}">Log in</a> to see the price and order.
                            @endauth
                        </div>
                    @endunless

                    <div class="product-assurance mt-4">
                        <div><i class="bi bi-truck"></i> Delivery all over Bangladesh</div>
                        <div><i class="bi bi-cash-coin"></i> Cash on delivery available</div>
                        <div><i class="bi bi-arrow-repeat"></i> Easy return &amp; refund</div>
                    </div>
                </div>
            </div>

            {{-- Description --}}
            <div class="card mt-5 border-0 shadow-sm">
                <div class="card-header bg-white fw-bold">Product Description</div>
                <div class="card-body">
                    {!! nl2br(e($product->description ?: 'No description available.')) !!}
                </div>
            </div>

            {{-- Related --}}
            @if ($related->isNotEmpty())
                <div class="mt-5">
                    <h2 class="section-title mb-4">Related Products</h2>
                    <div class="row g-3">
                        @foreach ($related as $relatedProduct)
                            <div class="col-6 col-md-3">
                                @include('frontend.products._card', ['product' => $relatedProduct])
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
