{{-- Top bar --}}
<div class="top-bar d-none d-md-block">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="d-flex gap-3">
            <span><i class="bi bi-telephone me-1"></i> {{ setting('contact_phone') }}</span>
            <span><i class="bi bi-envelope me-1"></i> {{ setting('contact_email') }}</span>
        </div>
        <div class="d-flex gap-3">
            <a href="#"><i class="bi bi-briefcase me-1"></i> Do Business With Us</a>
            <a href="{{ route('customer.orders.index') }}"><i class="bi bi-truck me-1"></i> Track Order</a>
            <a href="{{ route('faq') }}"><i class="bi bi-question-circle me-1"></i> Help</a>
        </div>
    </div>
</div>

{{-- Logo, search, actions --}}
<div class="main-header">
    <div class="container">
        <div class="row align-items-center g-3">
            <div class="col-6 col-lg-3">
                <a href="{{ route('home') }}" class="site-logo">
                    <span class="logo-icon"><i class="bi bi-bag-heart"></i></span>
                    <span>{{ config('app.name') }}</span>
                </a>
            </div>

            <div class="col-12 col-lg-6 order-3 order-lg-2">
                <form action="{{ route('shop') }}" method="GET" class="header-search" role="search">
                    <div class="input-group">
                        <select class="form-select d-none d-sm-block" name="category" aria-label="Category">
                            <option value="">All Categories</option>
                            @foreach ($headerCategories as $headerCategory)
                                <option value="{{ $headerCategory->slug }}" @selected(request('category') === $headerCategory->slug)>{{ $headerCategory->name }}</option>
                            @endforeach
                        </select>
                        <input type="search" name="q" class="form-control" placeholder="Search for products..."
                            value="{{ request('q') }}" autocomplete="off">
                        <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>
            </div>

            <div class="col-6 col-lg-3 order-2 order-lg-3">
                <div class="d-flex justify-content-end gap-4">
                    @auth('customer')
                        <div class="dropdown">
                            <a href="#" class="header-action" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-check"></i>
                                <span>{{ \Illuminate\Support\Str::limit(auth('customer')->user()->name, 10) }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                <li><a class="dropdown-item" href="{{ route('customer.dashboard') }}"><i class="bi bi-grid me-2"></i>My Account</a></li>
                                <li><a class="dropdown-item" href="{{ route('customer.orders.index') }}"><i class="bi bi-bag me-2"></i>My Orders</a></li>
                                <li><a class="dropdown-item" href="{{ route('customer.wishlist.index') }}"><i class="bi bi-heart me-2"></i>Wishlist</a></li>
                                <li><a class="dropdown-item" href="{{ route('customer.profile.edit') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('customer.logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('customer.login') }}" class="header-action">
                            <i class="bi bi-person"></i>
                            <span>Login</span>
                        </a>
                    @endauth
                    <a href="{{ route('customer.wishlist.index') }}" class="header-action">
                        <i class="bi bi-heart"></i>
                        <span>Wishlist</span>
                        <span @class(['count', 'd-none' => ! $wishlistCount]) data-wishlist-count>{{ $wishlistCount }}</span>
                    </a>
                    <a href="{{ route('cart.index') }}" class="header-action">
                        <i class="bi bi-cart3"></i>
                        <span>Cart</span>
                        <span @class(['count', 'd-none' => ! $cartCount]) data-cart-count>{{ $cartCount }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Main navigation --}}
<div class="main-nav">
    <div class="container">
        <nav class="navbar navbar-expand-lg">
            <button class="navbar-toggler my-2" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-3"></i>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a @class(['nav-link', 'active' => request()->routeIs('home')]) href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a @class(['nav-link dropdown-toggle', 'active' => request()->routeIs('shop', 'category.show', 'product.show')])
                            href="{{ route('shop') }}" role="button" data-bs-toggle="dropdown">Shop</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item fw-semibold" href="{{ route('shop') }}">All Products</a></li>
                            <li><hr class="dropdown-divider"></li>
                            @foreach ($headerCategories as $headerCategory)
                                <li>
                                    <a class="dropdown-item" href="{{ route('category.show', $headerCategory) }}">
                                        @if ($headerCategory->icon)<i class="bi {{ $headerCategory->icon }} me-2 text-primary"></i>@endif{{ $headerCategory->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('shop', ['sort' => 'latest']) }}">New Arrivals</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('shop', ['on_sale' => 1]) }}">Offers</a></li>
                    <li class="nav-item">
                        <a @class(['nav-link nav-flash', 'active' => request()->routeIs('flash-sale')]) href="{{ route('flash-sale') }}"><i class="bi bi-lightning-charge-fill"></i> Flash Sale</a>
                    </li>
                    <li class="nav-item">
                        <a @class(['nav-link', 'active' => request()->routeIs('about')]) href="{{ route('about') }}">About</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="#">Blog</a></li>
                    <li class="nav-item"><a @class(['nav-link', 'active' => request()->routeIs('faq')]) href="{{ route('faq') }}">FAQ</a></li>
                    <li class="nav-item"><a @class(['nav-link', 'active' => request()->routeIs('contact', 'complaint')]) href="{{ route('contact') }}">Contact</a></li>
                </ul>
            </div>
        </nav>
    </div>
</div>
