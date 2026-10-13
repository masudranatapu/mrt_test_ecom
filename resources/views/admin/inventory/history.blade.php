@extends('admin.layouts.app')

@section('title', 'Stock History')

@section('content')
    <div class="page-header">
        <div>
            <h1>Stock History</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.inventory.index') }}">Stock Update</a></li>
                    <li class="breadcrumb-item active">History</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white">
            <form method="GET" class="row g-2">
                <div class="col-md-4">
                    <input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Product name or SKU">
                </div>
                <div class="col-6 col-md-2">
                    <select name="type" class="form-select">
                        <option value="">In &amp; Out</option>
                        <option value="in" @selected(request('type') === 'in')>Stock In</option>
                        <option value="out" @selected(request('type') === 'out')>Stock Out</option>
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <select name="source" class="form-select">
                        <option value="">All sources</option>
                        @foreach (\App\Models\StockMovement::SOURCES as $key => $label)
                            <option value="{{ $key }}" @selected(request('source') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-primary"><i class="bi bi-funnel"></i> Filter</button>
                    @if (request()->hasAny(['search', 'type', 'source']))
                        <a href="{{ route('admin.inventory.history') }}" class="btn btn-link">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        @include('admin.inventory._movements-table', ['movements' => $movements, 'showProduct' => true])
    </div>
@endsection
