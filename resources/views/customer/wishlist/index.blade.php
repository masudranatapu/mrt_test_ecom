@extends('customer.layouts.account')

@section('title', 'My Wishlist')

@section('account')
    <h1 class="h4 fw-bold mb-4">My Wishlist</h1>

    <div class="row g-3">
        @forelse ($wishlists as $wishlist)
            <div class="col-6 col-md-4">
                @include('frontend.products._card', ['product' => $wishlist->product])
            </div>
        @empty
            <div class="col-12">
                <div class="text-center text-muted py-5 bg-white rounded-3">
                    <i class="bi bi-heart fs-1 d-block mb-2"></i>
                    Your wishlist is empty. Tap the heart on any product to save it here.
                    <div class="mt-3"><a href="{{ route('shop') }}" class="btn btn-primary btn-sm">Browse Products</a></div>
                </div>
            </div>
        @endforelse
    </div>

    @if ($wishlists->hasPages())
        <div class="mt-3">{{ $wishlists->links() }}</div>
    @endif
@endsection
