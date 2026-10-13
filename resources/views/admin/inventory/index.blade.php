@extends('admin.layouts.app')

@section('title', 'Stock Update')

@section('content')
    <div class="page-header">
        <div>
            <h1>Stock Update</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Stock Update</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.inventory.history') }}" class="btn btn-outline-secondary">
            <i class="bi bi-clock-history me-1"></i> Stock History
        </a>
    </div>

    <div class="row g-3 mb-4">
        @foreach ([
            ['label' => 'Total Products', 'value' => $summary['total'], 'icon' => 'bi-box-seam', 'color' => 'primary', 'filter' => null],
            ['label' => 'Units in Stock', 'value' => $summary['units'], 'icon' => 'bi-boxes', 'color' => 'success', 'filter' => null],
            ['label' => 'Low Stock', 'value' => $summary['low'], 'icon' => 'bi-exclamation-triangle', 'color' => 'warning', 'filter' => 'low'],
            ['label' => 'Out of Stock', 'value' => $summary['out'], 'icon' => 'bi-x-octagon', 'color' => 'danger', 'filter' => 'out'],
        ] as $card)
            <div class="col-sm-6 col-xl-3">
                <a href="{{ $card['filter'] ? route('admin.inventory.index', ['stock' => $card['filter']]) : route('admin.inventory.index') }}" class="text-decoration-none text-body">
                    <div class="card stat-card h-100">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="stat-icon bg-{{ $card['color'] }}-subtle text-{{ $card['color'] }}"><i class="bi {{ $card['icon'] }}"></i></div>
                            <div>
                                <div class="stat-value">{{ number_format($card['value']) }}</div>
                                <div class="stat-label">{{ $card['label'] }}</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-header bg-white">
            <form method="GET" class="row g-2">
                <div class="col-md-4">
                    <input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Name or SKU">
                </div>
                <div class="col-md-3">
                    <select name="stock" class="form-select">
                        <option value="">All products</option>
                        <option value="low" @selected(request('stock') === 'low')>Low stock</option>
                        <option value="out" @selected(request('stock') === 'out')>Out of stock</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-primary"><i class="bi bi-funnel"></i> Filter</button>
                    @if (request()->hasAny(['search', 'stock']))
                        <a href="{{ route('admin.inventory.index') }}" class="btn btn-link">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th class="text-center">Stock</th>
                        <th class="text-center">Alert At</th>
                        <th class="text-center">Sold</th>
                        <th>Best Before</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $product->thumbnail_url }}" {!! img_fallback() !!} class="rounded avatar-sm" alt="">
                                    <div>
                                        <a href="{{ route('admin.products.show', $product) }}" class="fw-semibold text-body">{{ $product->name }}</a>
                                        <div class="small text-muted">SKU: {{ $product->sku }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $product->category?->name }}</td>
                            <td class="text-center">@include('admin.products._stock-badge')</td>
                            <td class="text-center">{{ $product->low_stock_alert }}</td>
                            <td class="text-center">{{ number_format($product->sold_qty) }}</td>
                            <td class="small">
                                @if ($product->best_before_date)
                                    <span @class(['text-danger fw-semibold' => $product->best_before_date->isPast()])>
                                        {{ $product->best_before_date->format('d M Y') }}
                                    </span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-success"
                                    data-stock-adjust data-url="{{ route('admin.products.stock', $product) }}"
                                    data-name="{{ $product->name }}" data-stock="{{ $product->stock_qty }}" data-unit="{{ $product->unit }}">
                                    <i class="bi bi-plus-slash-minus me-1"></i> Adjust
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">No products found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($products->hasPages())
            <div class="card-footer bg-white">{{ $products->links() }}</div>
        @endif
    </div>

    @include('admin.inventory._adjust-modal')
@endsection
