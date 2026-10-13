<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') | {{ config('app.name') }} Admin</title>

    @vite(['resources/sass/admin.scss', 'resources/js/admin.js'])
</head>

<body>
    <div class="auth-page">
        <div class="card auth-card">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="auth-logo"><i class="bi bi-shop"></i></div>
                    <h1 class="h4 fw-bold mb-1">@yield('heading')</h1>
                    <p class="text-muted small mb-0">@yield('subheading')</p>
                </div>

                @if (session('success'))
                    <div class="alert alert-success small">{{ session('success') }}</div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>
</body>

</html>
