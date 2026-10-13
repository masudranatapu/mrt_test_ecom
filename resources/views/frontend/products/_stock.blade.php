{{-- Stock label: "50+" above the display limit, exact number below it --}}
@if ($product->stock_qty === 0)
    <span class="stock-label out"><i class="bi bi-x-circle"></i> Out of stock</span>
@elseif ($product->is_low_stock)
    <span class="stock-label low"><i class="bi bi-exclamation-circle"></i> Only {{ $product->stock_label }} left</span>
@else
    <span class="stock-label in"><i class="bi bi-check-circle"></i> In stock: {{ $product->stock_label }}</span>
@endif
