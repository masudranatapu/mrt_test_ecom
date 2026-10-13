{{-- Product card + "sold / limit" progress for a flash sale item --}}
<div class="flash-item">
    @include('frontend.products._card', ['product' => $item->product])
    @if ($item->quantity_limit)
        <div class="flash-progress">
            <div class="d-flex justify-content-between small mb-1">
                <span>{{ $item->isSoldOut() ? 'Flash stock sold out' : 'Sold '.$item->sold.' / '.$item->quantity_limit }}</span>
                <span class="fw-semibold">{{ $item->soldPercent() }}%</span>
            </div>
            <div class="progress" style="height: 6px">
                <div class="progress-bar bg-danger" style="width: {{ $item->soldPercent() }}%"></div>
            </div>
        </div>
    @endif
</div>
