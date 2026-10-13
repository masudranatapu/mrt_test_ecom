@extends('admin.layouts.app')

@section('title', 'Flash Sales')

@section('content')
    <div class="page-header">
        <div>
            <h1>Flash Sales</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Flash Sales</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.flash-sales.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> New Flash Sale</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Starts</th>
                        <th>Ends</th>
                        <th class="text-center">Products</th>
                        <th class="text-center">Sold</th>
                        <th>State</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($flashSales as $sale)
                        @php($state = $sale->state())
                        <tr>
                            <td class="fw-semibold">{{ $sale->title }}</td>
                            <td class="small">{{ $sale->starts_at->format('d M Y, h:i A') }}</td>
                            <td class="small">{{ $sale->ends_at->format('d M Y, h:i A') }}</td>
                            <td class="text-center">{{ $sale->items_count }}</td>
                            <td class="text-center">{{ (int) $sale->items_sum_sold }}</td>
                            <td>
                                <span @class([
                                    'badge',
                                    'bg-success' => $state === 'running',
                                    'bg-info' => $state === 'upcoming',
                                    'bg-secondary' => in_array($state, ['ended', 'disabled']),
                                ])>{{ ucfirst($state) }}</span>
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('admin.flash-sales.edit', $sale) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.flash-sales.destroy', $sale) }}" method="POST" class="d-inline" data-confirm="Delete this flash sale?">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-5">No flash sales yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($flashSales->hasPages())
            <div class="card-footer bg-white">{{ $flashSales->links() }}</div>
        @endif
    </div>
@endsection
