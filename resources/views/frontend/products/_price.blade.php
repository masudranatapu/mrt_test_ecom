{{-- Price block that respects the "price only for approved customers" rule --}}
@if (can_see_price())
    <div class="product-price {{ $size ?? '' }}">
        <span class="current">{{ money($product->final_price) }}</span>
        @if ($product->hasDiscount())
            <del class="old">{{ money($product->price) }}</del>
        @endif
        <span class="per-unit">/ {{ $product->unit }}</span>
    </div>
@elseif (auth('customer')->check())
    <div class="price-locked"><i class="bi bi-hourglass-split"></i> Price visible after approval</div>
@else
    <a href="{{ route('customer.login') }}" class="price-locked"><i class="bi bi-lock"></i> Login to see price</a>
@endif
