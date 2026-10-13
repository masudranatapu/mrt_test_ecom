@extends('admin.layouts.app')

@section('title', $product->name)

@section('content')
    <div class="page-header">
        <div>
            <h1>{{ $product->name }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
                    <li class="breadcrumb-item active">Details</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            @if (auth('admin')->user()->hasPermission('inventory'))
            <button type="button" class="btn btn-success"
                data-stock-adjust data-url="{{ route('admin.products.stock', $product) }}"
                data-name="{{ $product->name }}" data-stock="{{ $product->stock_qty }}" data-unit="{{ $product->unit }}">
                <i class="bi bi-boxes me-1"></i> Adjust Stock
            </button>
            @endif
            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i> Edit</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body">
                    <img src="{{ $product->thumbnail_url }}" {!! img_fallback() !!} class="img-fluid rounded mb-3 w-100" alt="">
                    @if ($product->images->isNotEmpty())
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($product->images as $image)
                                <img src="{{ $image->url }}" {!! img_fallback() !!} class="rounded border" style="width: 64px; height: 64px; object-fit: cover" alt="">
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="row g-3 mb-4">
                @foreach ([
                    ['label' => 'In Stock', 'value' => $product->stock_qty.' '.$product->unit, 'icon' => 'bi-boxes', 'color' => $product->stock_qty === 0 ? 'danger' : ($product->is_low_stock ? 'warning' : 'success')],
                    ['label' => 'Sold', 'value' => number_format($product->sold_qty), 'icon' => 'bi-graph-up-arrow', 'color' => 'primary'],
                    ['label' => 'Price', 'value' => money($product->final_price), 'icon' => 'bi-tag', 'color' => 'info'],
                ] as $card)
                    <div class="col-md-4">
                        <div class="card stat-card h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="stat-icon bg-{{ $card['color'] }}-subtle text-{{ $card['color'] }}"><i class="bi {{ $card['icon'] }}"></i></div>
                                <div>
                                    <div class="stat-value fs-5">{{ $card['value'] }}</div>
                                    <div class="stat-label">{{ $card['label'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card mb-4">
                <div class="card-header">Details</div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted fw-normal">SKU</dt>
                        <dd class="col-sm-8">{{ $product->sku }}</dd>
                        <dt class="col-sm-4 text-muted fw-normal">Category</dt>
                        <dd class="col-sm-8">{{ $product->category?->name }}</dd>
                        <dt class="col-sm-4 text-muted fw-normal">Regular / Sale Price</dt>
                        <dd class="col-sm-8">
                            {{ money($product->price) }}
                            @if ($product->hasSalePrice())
                                / <strong class="text-success">{{ money($product->sale_price) }}</strong>
                                <span class="badge bg-danger-subtle text-danger">-{{ (int) round(100 - $product->sale_price / $product->price * 100) }}%</span>
                            @endif
                        </dd>
                        <dt class="col-sm-4 text-muted fw-normal">Low Stock Alert</dt>
                        <dd class="col-sm-8">{{ $product->low_stock_alert }} {{ $product->unit }}</dd>
                        <dt class="col-sm-4 text-muted fw-normal">Best Before</dt>
                        <dd class="col-sm-8">
                            @if ($product->best_before_date)
                                {{ $product->best_before_date->format('d M Y') }}
                                @if ($product->best_before_date->isPast())
                                    <span class="badge bg-danger">Expired</span>
                                @elseif ($product->best_before_date->lte(now()->addDays(30)))
                                    <span class="badge bg-warning text-dark">Expires soon</span>
                                @endif
                            @else
                                —
                            @endif
                        </dd>
                        <dt class="col-sm-4 text-muted fw-normal">Status</dt>
                        <dd class="col-sm-8">
                            {!! $product->status ? '<span class="badge bg-success-subtle text-success">Active</span>' : '<span class="badge bg-secondary-subtle text-secondary">Inactive</span>' !!}
                            @if ($product->is_featured)
                                <span class="badge bg-warning-subtle text-warning-emphasis"><i class="bi bi-star-fill"></i> Featured</span>
                            @endif
                        </dd>
                        @if ($product->short_description)
                            <dt class="col-sm-4 text-muted fw-normal">Short Description</dt>
                            <dd class="col-sm-8">{{ $product->short_description }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Stock History</span>
                    <a href="{{ route('admin.inventory.history', ['search' => $product->sku]) }}" class="small fw-normal">View all</a>
                </div>
                @include('admin.inventory._movements-table', ['movements' => $movements, 'showProduct' => false])
            </div>
        </div>
    </div>

    @include('admin.inventory._adjust-modal')
@endsection
