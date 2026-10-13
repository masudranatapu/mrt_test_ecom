@if ($product->stock_qty === 0)
    <span class="badge bg-danger-subtle text-danger">Out of stock</span>
@elseif ($product->is_low_stock)
    <span class="badge bg-warning-subtle text-warning-emphasis" title="Low stock (alert at {{ $product->low_stock_alert }})">
        {{ $product->stock_qty }} {{ $product->unit }}
    </span>
@else
    <span class="badge bg-success-subtle text-success">{{ $product->stock_qty }} {{ $product->unit }}</span>
@endif
