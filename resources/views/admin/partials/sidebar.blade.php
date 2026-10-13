@php($me = auth('admin')->user())

<aside class="admin-sidebar">
    <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
        <span class="brand-icon"><i class="bi bi-shop"></i></span>
        <span>{{ config('app.name') }}</span>
    </a>

    <nav class="sidebar-nav">
        <a href="{{ route('admin.dashboard') }}" @class(['nav-link', 'active' => request()->routeIs('admin.dashboard')])>
            <i class="bi bi-grid-1x2"></i> Dashboard
        </a>

        @if ($me->hasPermission('orders'))
            <div class="nav-heading">Sales</div>
            <a href="{{ route('admin.orders.index') }}" @class(['nav-link', 'active' => request()->routeIs('admin.orders.*')])>
                <i class="bi bi-receipt"></i> Orders
                <span @class(['badge rounded-pill bg-danger ms-auto', 'd-none' => ! $pendingOrders]) data-pending-orders>{{ $pendingOrders }}</span>
            </a>
        @endif

        @if ($me->hasPermission('products'))
            <div class="nav-heading">Catalog</div>
            <a href="{{ route('admin.categories.index') }}" @class(['nav-link', 'active' => request()->routeIs('admin.categories.*')])>
                <i class="bi bi-diagram-3"></i> Categories
            </a>
            <a href="{{ route('admin.products.index') }}" @class(['nav-link', 'active' => request()->routeIs('admin.products.*')])>
                <i class="bi bi-box-seam"></i> Products
            </a>
        @endif

        @if ($me->hasPermission('inventory'))
            <div class="nav-heading">Inventory</div>
            <a href="{{ route('admin.inventory.index') }}" @class(['nav-link', 'active' => request()->routeIs('admin.inventory.index')])>
                <i class="bi bi-boxes"></i> Stock Update
            </a>
            <a href="{{ route('admin.inventory.history') }}" @class(['nav-link', 'active' => request()->routeIs('admin.inventory.history')])>
                <i class="bi bi-clock-history"></i> Stock History
            </a>
        @endif

        @if ($me->hasPermission('customers'))
            <div class="nav-heading">Customers</div>
            <a href="{{ route('admin.customers.index') }}" @class(['nav-link', 'active' => request()->routeIs('admin.customers.*')])>
                <i class="bi bi-people"></i> Customers
                @if ($pendingCustomers)
                    <span class="badge rounded-pill bg-warning text-dark ms-auto">{{ $pendingCustomers }}</span>
                @endif
            </a>
        @endif

        @if ($me->hasPermission('marketing'))
            <div class="nav-heading">Marketing</div>
            <a href="{{ route('admin.flash-sales.index') }}" @class(['nav-link', 'active' => request()->routeIs('admin.flash-sales.*')])>
                <i class="bi bi-lightning-charge"></i> Flash Sales
            </a>
            <a href="{{ route('admin.campaigns.index') }}" @class(['nav-link', 'active' => request()->routeIs('admin.campaigns.*')])>
                <i class="bi bi-megaphone"></i> Campaigns
            </a>
        @endif

        @if ($me->hasPermission('support'))
            <div class="nav-heading">Support</div>
            <a href="{{ route('admin.messages.index') }}" @class(['nav-link', 'active' => request()->routeIs('admin.messages.*')])>
                <i class="bi bi-envelope"></i> Messages
                @if ($unreadMessages)
                    <span class="badge rounded-pill bg-primary ms-auto">{{ $unreadMessages }}</span>
                @endif
            </a>
            <a href="{{ route('admin.complaints.index') }}" @class(['nav-link', 'active' => request()->routeIs('admin.complaints.*')])>
                <i class="bi bi-exclamation-octagon"></i> Complaints
                @if ($openComplaints)
                    <span class="badge rounded-pill bg-danger ms-auto">{{ $openComplaints }}</span>
                @endif
            </a>
        @endif

        @if ($me->hasPermission('content'))
            <div class="nav-heading">Content</div>
            <a href="{{ route('admin.faqs.index') }}" @class(['nav-link', 'active' => request()->routeIs('admin.faqs.*')])>
                <i class="bi bi-patch-question"></i> FAQs
            </a>
            <a href="{{ route('admin.settings.contact') }}" @class(['nav-link', 'active' => request()->routeIs('admin.settings.*')])>
                <i class="bi bi-shop-window"></i> Contact &amp; Store Info
            </a>
        @endif

        <div class="nav-heading">Account</div>
        @if ($me->is_super_admin)
            <a href="{{ route('admin.admins.index') }}" @class(['nav-link', 'active' => request()->routeIs('admin.admins.*')])>
                <i class="bi bi-shield-lock"></i> Admins &amp; Staff
            </a>
        @endif
        <a href="{{ route('admin.profile.edit') }}" @class(['nav-link', 'active' => request()->routeIs('admin.profile.*')])>
            <i class="bi bi-person-circle"></i> My Profile
        </a>
    </nav>

    <div class="sidebar-footer">
        <span class="text-white">{{ $me->name }}</span> &middot; {{ $me->roleLabel() }}
    </div>
</aside>
