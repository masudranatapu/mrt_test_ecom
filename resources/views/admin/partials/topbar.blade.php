@php($admin = auth('admin')->user())

<header class="admin-topbar">
    <button type="button" class="topbar-btn d-lg-none" data-sidebar-toggle aria-label="Toggle sidebar">
        <i class="bi bi-list fs-4"></i>
    </button>

    <a href="{{ route('home') }}" target="_blank" class="btn btn-sm btn-outline-secondary d-none d-sm-inline-flex align-items-center gap-1">
        <i class="bi bi-box-arrow-up-right"></i> View Store
    </a>

    <div class="ms-auto d-flex align-items-center gap-2">
        {{-- Notifications: saved ones from the database + live ones pushed by Pusher --}}
        <div class="dropdown">
            <button class="topbar-btn" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-label="Notifications">
                <i class="bi bi-bell fs-5"></i>
                <span @class(['badge rounded-pill bg-danger badge-dot', 'd-none' => ! $unreadCount]) id="notificationCount">{{ $unreadCount }}</span>
            </button>
            <div class="dropdown-menu dropdown-menu-end shadow border-0 p-0 notification-menu">
                <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                    <span class="fw-semibold">Notifications</span>
                    @if ($unreadCount)
                        <form action="{{ route('admin.notifications.read-all') }}" method="POST">
                            @csrf
                            <button class="btn btn-link btn-sm p-0 text-decoration-none">Mark all read</button>
                        </form>
                    @endif
                </div>
                <div id="notificationList" class="notification-list">
                    @forelse ($latestNotifications as $notification)
                        <a href="{{ route('admin.notifications.read', $notification->id) }}"
                            @class(['notification-item', 'unread' => ! $notification->read_at])>
                            <span class="icon bg-primary-subtle text-primary"><i class="bi {{ $notification->data['icon'] ?? 'bi-bag-check' }}"></i></span>
                            <span class="flex-grow-1">
                                <span class="d-block small fw-semibold">{{ $notification->data['message'] ?? 'Notification' }}</span>
                                <span class="d-block small text-muted">{{ $notification->data['total'] ?? '' }} &middot; {{ $notification->created_at->diffForHumans() }}</span>
                            </span>
                        </a>
                    @empty
                        <div class="text-center text-muted small py-4" data-empty>No notifications yet</div>
                    @endforelse
                </div>
                <a href="{{ route('admin.notifications.index') }}" class="d-block text-center small py-2 border-top">View all</a>
            </div>
        </div>

        <div class="dropdown topbar-user">
            <button class="btn d-flex align-items-center gap-2 border-0" data-bs-toggle="dropdown">
                <img src="{{ $admin->avatar_url }}" {!! img_fallback(true) !!} class="rounded-circle" alt="{{ $admin->name }}">
                <span class="d-none d-md-inline fw-semibold">{{ $admin->name }}</span>
                <i class="bi bi-chevron-down small text-muted"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                <li>
                    <a class="dropdown-item" href="{{ route('admin.profile.edit') }}">
                        <i class="bi bi-person me-2"></i> My Profile
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
