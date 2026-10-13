@extends('admin.layouts.app')

@section('title', 'Categories')

@section('content')
    <div class="page-header">
        <div>
            <h1>Categories</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Categories</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add Category
        </a>
    </div>

    <div class="card">
        <div class="card-header bg-white">
            <form method="GET" class="row g-2">
                <div class="col-md-5">
                    <input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search categories">
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-primary"><i class="bi bi-search"></i> Search</button>
                    @if (request('search'))
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-link">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Parent</th>
                        <th class="text-center">Products</th>
                        <th class="text-center">Order</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2 @if ($category->parent_id) ps-4 @endif">
                                    @if ($category->parent_id)
                                        <i class="bi bi-arrow-return-right text-muted"></i>
                                    @endif
                                    <img src="{{ $category->image_url }}" {!! img_fallback() !!} class="rounded avatar-sm" alt="">
                                    <div>
                                        <div class="fw-semibold">
                                            @if ($category->icon)<i class="bi {{ $category->icon }} me-1"></i>@endif
                                            {{ $category->name }}
                                            @if ($category->is_featured)
                                                <span class="badge bg-info-subtle text-info ms-1">Featured</span>
                                            @endif
                                        </div>
                                        <div class="small text-muted">/{{ $category->slug }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $category->parent?->name ?? '—' }}</td>
                            <td class="text-center">{{ $category->products_count }}</td>
                            <td class="text-center">{{ $category->sort_order }}</td>
                            <td>
                                @if ($category->status)
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline"
                                    data-confirm="Delete this category?">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">No categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($categories->hasPages())
            <div class="card-footer bg-white">{{ $categories->links() }}</div>
        @endif
    </div>
@endsection
