@extends('frontend.layouts.app')

@section('title', $category?->name ?? (request('q') ? 'Search: '.request('q') : 'Shop'))

@section('content')
    <div class="page-banner">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('shop') }}">Shop</a></li>
                    @if ($category?->parent)
                        <li class="breadcrumb-item"><a href="{{ route('category.show', $category->parent) }}">{{ $category->parent->name }}</a></li>
                    @endif
                    @if ($category)
                        <li class="breadcrumb-item active">{{ $category->name }}</li>
                    @endif
                </ol>
            </nav>
            <h1>
                @if (request('q'))
                    Results for "{{ request('q') }}"
                @else
                    {{ $category?->name ?? 'All Products' }}
                @endif
            </h1>
        </div>
    </div>

    <section class="section pt-4">
        <div class="container">
            <div class="row g-4">
                {{-- Sidebar --}}
                <aside class="col-lg-3">
                    <div class="shop-sidebar">
                        <h6 class="sidebar-title">Categories</h6>
                        <ul class="list-unstyled category-list">
                            <li>
                                <a href="{{ route('shop', request()->only('q')) }}" @class(['active' => ! $category])>All Products</a>
                            </li>
                            @foreach ($sidebarCategories as $parent)
                                <li>
                                    <a href="{{ route('category.show', $parent) }}" @class(['active' => $category?->id === $parent->id])>
                                        @if ($parent->icon)<i class="bi {{ $parent->icon }} me-1"></i>@endif {{ $parent->name }}
                                    </a>
                                    @if ($parent->children->isNotEmpty() && ($category?->id === $parent->id || $category?->parent_id === $parent->id))
                                        <ul class="list-unstyled ms-3">
                                            @foreach ($parent->children as $child)
                                                <li>
                                                    <a href="{{ route('category.show', $child) }}" @class(['active' => $category?->id === $child->id])>{{ $child->name }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>

                        <h6 class="sidebar-title mt-4">Filter</h6>
                        <form method="GET">
                            @foreach (request()->only(['q', 'sort', 'category']) as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="in_stock" value="1" id="inStock"
                                    @checked(request()->boolean('in_stock')) onchange="this.form.submit()">
                                <label class="form-check-label" for="inStock">In stock only</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="on_sale" value="1" id="onSale"
                                    @checked(request()->boolean('on_sale')) onchange="this.form.submit()">
                                <label class="form-check-label" for="onSale">On sale</label>
                            </div>
                        </form>
                    </div>
                </aside>

                {{-- Products --}}
                <div class="col-lg-9">
                    <div class="shop-toolbar">
                        <span class="text-muted small">
                            Showing {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products
                        </span>
                        <form method="GET" class="d-flex align-items-center gap-2">
                            @foreach (request()->except(['sort', 'page']) as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <label for="sort" class="small text-muted text-nowrap">Sort by</label>
                            <select name="sort" id="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                                @foreach (['latest' => 'Newest', 'popular' => 'Best selling', 'price_asc' => 'Price: Low to High', 'price_desc' => 'Price: High to Low', 'name' => 'Name A–Z'] as $value => $label)
                                    <option value="{{ $value }}" @selected(request('sort', 'latest') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                    <div class="row g-3">
                        @forelse ($products as $product)
                            <div class="col-6 col-md-4">
                                @include('frontend.products._card')
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center text-muted py-5 border rounded-3 bg-light">
                                    <i class="bi bi-search fs-1 d-block mb-2"></i>
                                    No products found.
                                </div>
                            </div>
                        @endforelse
                    </div>

                    @if ($products->hasPages())
                        <div class="mt-4 d-flex justify-content-center">{{ $products->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
