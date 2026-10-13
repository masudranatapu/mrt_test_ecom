@extends('frontend.layouts.app')

@section('content')
    @php($customer = auth('customer')->user())

    <section class="account-section">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3">
                    <div class="account-sidebar">
                        <div class="account-user">
                            <img src="{{ $customer->avatar_url }}" {!! img_fallback(true) !!} class="rounded-circle" alt="">
                            <div>
                                <div class="fw-bold">{{ $customer->name }}</div>
                                @if ($customer->is_approved)
                                    <span class="badge bg-success-subtle text-success"><i class="bi bi-patch-check"></i> Approved</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis">Pending approval</span>
                                @endif
                            </div>
                        </div>

                        <nav class="nav flex-column">
                            <a href="{{ route('customer.dashboard') }}" @class(['nav-link', 'active' => request()->routeIs('customer.dashboard')])>
                                <i class="bi bi-grid"></i> Dashboard
                            </a>
                            <a href="{{ route('customer.orders.index') }}" @class(['nav-link', 'active' => request()->routeIs('customer.orders.*')])>
                                <i class="bi bi-bag"></i> My Orders
                            </a>
                            <a href="{{ route('customer.wishlist.index') }}" @class(['nav-link', 'active' => request()->routeIs('customer.wishlist.*')])>
                                <i class="bi bi-heart"></i> Wishlist
                            </a>
                            <a href="{{ route('customer.addresses.index') }}" @class(['nav-link', 'active' => request()->routeIs('customer.addresses.*')])>
                                <i class="bi bi-geo-alt"></i> Addresses
                            </a>
                            <a href="{{ route('customer.profile.edit') }}" @class(['nav-link', 'active' => request()->routeIs('customer.profile.*')])>
                                <i class="bi bi-person"></i> Profile
                            </a>
                            <form action="{{ route('customer.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="nav-link text-danger w-100 text-start border-0 bg-transparent">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </button>
                            </form>
                        </nav>
                    </div>
                </div>

                <div class="col-lg-9">
                    @yield('account')
                </div>
            </div>
        </div>
    </section>
@endsection
