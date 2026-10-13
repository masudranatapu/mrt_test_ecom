@extends('customer.layouts.account')

@section('title', 'My Addresses')

@section('account')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 fw-bold mb-0">My Addresses</h1>
        <a href="{{ route('customer.addresses.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Add Address</a>
    </div>

    <div class="row g-3">
        @forelse ($addresses as $address)
            <div class="col-md-6">
                <div @class(['card h-100', 'border-primary' => $address->is_default])>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="badge bg-light text-dark border"><i class="bi bi-geo-alt"></i> {{ $address->label }}</span>
                            @if ($address->is_default)
                                <span class="badge bg-primary">Default</span>
                            @endif
                        </div>
                        <div class="fw-semibold">{{ $address->name }}</div>
                        <div class="small">{{ $address->phone }}</div>
                        <div class="small text-muted">{{ $address->full_address }}</div>
                    </div>
                    <div class="card-footer bg-white d-flex gap-2">
                        <a href="{{ route('customer.addresses.edit', $address) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i> Edit</a>
                        <form action="{{ route('customer.addresses.destroy', $address) }}" method="POST" data-confirm="Delete this address?">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center text-muted py-5 border rounded-3 bg-light">
                    <i class="bi bi-geo-alt fs-1 d-block mb-2"></i>
                    You have not added any address yet.
                </div>
            </div>
        @endforelse
    </div>
@endsection
