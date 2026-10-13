<span class="badge bg-{{ $order->status_color }}-subtle text-{{ $order->status_color }}-emphasis">
    <i class="bi {{ \App\Models\Order::STATUSES[$order->status]['icon'] ?? 'bi-circle' }}"></i> {{ $order->status_label }}
</span>
@if ($showPayment ?? true)
    <span class="badge bg-{{ $order->payment_status_color }}-subtle text-{{ $order->payment_status_color }}-emphasis">{{ $order->payment_status_label }}</span>
    @if ($order->isOverdue())
        <span class="badge bg-danger">Overdue</span>
    @endif
@endif
