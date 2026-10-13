<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Live new-order notifications (Pusher), only for staff who handle orders --}}
    <meta name="pusher-key" content="{{ auth('admin')->user()->hasPermission('orders') ? config('broadcasting.connections.pusher.key') : '' }}">
    <meta name="pusher-cluster" content="{{ config('broadcasting.connections.pusher.options.cluster') }}">
    <meta name="broadcast-auth" content="{{ url('broadcasting/auth') }}">

    <title>@yield('title', 'Dashboard') | {{ config('app.name') }} Admin</title>

    @vite(['resources/sass/admin.scss', 'resources/js/admin.js'])
    @stack('styles')
</head>

<body>
    <div class="admin-wrapper">
        @include('admin.partials.sidebar')
        <div class="sidebar-backdrop"></div>

        <div class="admin-main">
            @include('admin.partials.topbar')

            <main class="admin-content">
                @yield('content')
            </main>

            <footer class="admin-footer d-flex justify-content-between">
                <span>&copy; {{ date('Y') }} {{ config('app.name') }}</span>
                <span>Admin Panel</span>
            </footer>
        </div>
    </div>

    @include('admin.partials.flash')
    @stack('scripts')
</body>

</html>
