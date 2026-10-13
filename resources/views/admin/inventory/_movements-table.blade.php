@php($showProduct ??= true)

<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead>
            <tr>
                <th>Date</th>
                @if ($showProduct)
                    <th>Product</th>
                @endif
                <th>Type</th>
                <th class="text-center">Qty</th>
                <th class="text-center">Before → After</th>
                <th>Source / Note</th>
                <th>By</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($movements as $movement)
                <tr>
                    <td class="text-nowrap small">{{ $movement->created_at->format('d M Y') }}<br><span class="text-muted">{{ $movement->created_at->format('h:i A') }}</span></td>
                    @if ($showProduct)
                        <td>
                            @if ($movement->product && ! $movement->product->trashed())
                                <a href="{{ route('admin.products.show', $movement->product) }}" class="fw-semibold text-body">{{ $movement->product->name }}</a>
                            @else
                                <span class="fw-semibold">{{ $movement->product?->name ?? 'Deleted product' }}</span>
                            @endif
                            <div class="small text-muted">{{ $movement->product?->sku }}</div>
                        </td>
                    @endif
                    <td>
                        @if ($movement->type === 'in')
                            <span class="badge bg-success-subtle text-success"><i class="bi bi-arrow-down-circle"></i> In</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger"><i class="bi bi-arrow-up-circle"></i> Out</span>
                        @endif
                    </td>
                    <td class="text-center fw-semibold {{ $movement->type === 'in' ? 'text-success' : 'text-danger' }}">
                        {{ $movement->type === 'in' ? '+' : '−' }}{{ $movement->quantity }}
                    </td>
                    <td class="text-center text-nowrap">{{ $movement->before_qty }} → <strong>{{ $movement->after_qty }}</strong></td>
                    <td>
                        <div class="small fw-semibold">{{ $movement->sourceLabel() }}</div>
                        @if ($movement->note)
                            <div class="small text-muted">{{ $movement->note }}</div>
                        @endif
                    </td>
                    <td class="small">{{ $movement->admin?->name ?? 'System' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $showProduct ? 7 : 6 }}" class="text-center text-muted py-4">No stock changes yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($movements->hasPages())
    <div class="card-footer bg-white">{{ $movements->links() }}</div>
@endif
