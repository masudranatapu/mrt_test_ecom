@php($isNew = $product->created_at?->gt(now()->subDays(14)))
@php($promo = $product->promotion())

<div class="product-card">
    <div class="product-thumb">
        <a href="{{ route('product.show', $product->slug) }}">
            <img src="{{ $product->thumbnail_url }}" {!! img_fallback() !!} alt="{{ $product->name }}" loading="lazy">
        </a>

        <div class="thumb-badges">
            @if ($product->stock_qty === 0)
                <span class="badge-tag dark">Sold out</span>
            @elseif ($product->hasDiscount() && can_see_price())
                <span class="badge-tag danger">-{{ $product->discount_percent }}%</span>
            @endif
            @if ($promo && can_see_price())
                <span class="badge-tag {{ $promo['type'] === 'flash' ? 'flash' : 'campaign' }}">
                    <i class="bi {{ $promo['type'] === 'flash' ? 'bi-lightning-charge-fill' : 'bi-megaphone-fill' }}"></i>
                    {{ $promo['type'] === 'flash' ? 'Flash' : 'Campaign' }}
                </span>
            @endif
            @if ($isNew)
                <span class="badge-tag success">New</span>
            @endif
        </div>

        <div class="thumb-actions">
            @php($inWishlist = in_array($product->id, wishlist_ids(), true))
            <form action="{{ route('customer.wishlist.store') }}" method="POST" data-ajax-wishlist>
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <button type="submit" @class(['action-btn', 'active' => $inWishlist]) title="Wishlist">
                    <i @class(['bi', 'bi-heart-fill' => $inWishlist, 'bi-heart' => ! $inWishlist])></i>
                </button>
            </form>
            <a href="{{ route('product.show', $product->slug) }}" class="action-btn" title="View details"><i class="bi bi-eye"></i></a>
        </div>
    </div>

    <div class="product-body">
        <div class="product-meta-line">
            <span>{{ $product->category?->name }}</span>
            <span class="unit">{{ $product->unit }}</span>
        </div>

        <h3 class="product-name">
            <a href="{{ route('product.show', $product->slug) }}" title="{{ $product->name }}">{{ $product->name }}</a>
        </h3>

        <div class="product-stats">
            @include('frontend.products._stock', ['product' => $product])
            <span class="sold-count"><i class="bi bi-fire"></i> {{ number_format($product->sold_qty) }} sold</span>
        </div>
    </div>

    <div class="product-footer">
        @if (can_see_price())
            <div class="product-price">
                <span class="current">{{ money($product->final_price, 0) }}</span>
                @if ($product->hasDiscount())
                    <del class="old">{{ money($product->price, 0) }}</del>
                @endif
            </div>
            <form action="{{ route('cart.store') }}" method="POST" data-ajax-cart>
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="cart-btn border-0" title="Add to cart" @disabled($product->stock_qty === 0)>
                    <i class="bi bi-bag-plus"></i>
                </button>
            </form>
        @else
            @include('frontend.products._price', ['product' => $product])
        @endif
    </div>
</div>
