@extends('admin.layouts.app')

@section('title', 'Products')

@section('content')
    <div class="page-header">
        <div>
            <h1>Products</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Products</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add Product
        </a>
    </div>

    <div class="card">
        <div class="card-header bg-white">
            <form method="GET" class="row g-2">
                <div class="col-md-3">
                    <input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Name or SKU">
                </div>
                <div class="col-md-3">
                    <select name="category" class="form-select">
                        <option value="">All categories</option>
                        @foreach ($categories as $id => $name)
                            <option value="{{ $id }}" @selected(request('category') == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="stock" class="form-select">
                        <option value="">All stock</option>
                        <option value="low" @selected(request('stock') === 'low')>Low stock</option>
                        <option value="out" @selected(request('stock') === 'out')>Out of stock</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All status</option>
                        <option value="1" @selected(request('status') === '1')>Active</option>
                        <option value="0" @selected(request('status') === '0')>Inactive</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-primary"><i class="bi bi-funnel"></i> Filter</button>
                    @if (request()->hasAny(['search', 'category', 'stock', 'status']))
                        <a href="{{ route('admin.products.index') }}" class="btn btn-link">Reset</a>
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
                        <th class="text-end">Price</th>
                        <th class="text-center">Stock</th>
                        <th class="text-center">Sold</th>
                        <th>Status</th>
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
                                        @if ($product->is_featured)
                                            <i class="bi bi-star-fill text-warning small" title="Featured"></i>
                                        @endif
                                        <div class="small text-muted">SKU: {{ $product->sku }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $product->category?->name }}</td>
                            <td class="text-end text-nowrap">
                                @if ($product->hasSalePrice())
                                    <div class="fw-semibold">{{ money($product->sale_price) }}</div>
                                    <del class="small text-muted">{{ money($product->price) }}</del>
                                @else
                                    <span class="fw-semibold">{{ money($product->price) }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @include('admin.products._stock-badge')
                            </td>
                            <td class="text-center">{{ number_format($product->sold_qty) }}</td>
                            <td>
                                @if ($product->status)
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                @if (auth('admin')->user()->hasPermission('inventory'))
                                    <button type="button" class="btn btn-sm btn-outline-success" title="Adjust stock"
                                        data-stock-adjust data-url="{{ route('admin.products.stock', $product) }}"
                                        data-name="{{ $product->name }}" data-stock="{{ $product->stock_qty }}" data-unit="{{ $product->unit }}">
                                        <i class="bi bi-boxes"></i>
                                    </button>
                                @endif
                                <a href="{{ route('admin.products.show', $product) }}" class="btn btn-sm btn-outline-secondary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline"
                                    data-confirm="Delete this product?">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                </form>
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
