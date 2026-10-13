@extends('admin.layouts.app')

@section('title', 'FAQs')

@section('content')
    <div class="page-header">
        <div>
            <h1>FAQs</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">FAQs</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('faq') }}" target="_blank" class="btn btn-outline-secondary"><i class="bi bi-box-arrow-up-right me-1"></i> View page</a>
            <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Add FAQ</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white">
            <form method="GET" class="row g-2">
                <div class="col-md-5">
                    <input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search questions">
                </div>
                <div class="col-md-3">
                    <select name="category" class="form-select">
                        <option value="">All topics</option>
                        @foreach (\App\Models\Faq::CATEGORIES as $key => $category)
                            <option value="{{ $key }}" @selected(request('category') === $key)>{{ $category['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-primary"><i class="bi bi-funnel"></i> Filter</button>
                    @if (request()->hasAny(['search', 'category']))
                        <a href="{{ route('admin.faqs.index') }}" class="btn btn-link">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Question</th>
                        <th>Topic</th>
                        <th class="text-center">Order</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($faqs as $faq)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $faq->question }}</div>
                                <div class="small text-muted">{{ \Illuminate\Support\Str::limit($faq->answer, 110) }}</div>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $faq->categoryLabel() }}</span></td>
                            <td class="text-center">{{ $faq->sort_order }}</td>
                            <td>
                                @if ($faq->status)
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">Hidden</span>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('admin.faqs.edit', $faq) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.faqs.destroy', $faq) }}" method="POST" class="d-inline" data-confirm="Delete this FAQ?">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">No FAQs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($faqs->hasPages())
            <div class="card-footer bg-white">{{ $faqs->links() }}</div>
        @endif
    </div>
@endsection
