<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@hasSection('title')@yield('title') | @endif{{ config('app.name') }}</title>
    <meta name="description" content="@yield('meta_description', config('app.name').' - Online Shop')">

    @vite(['resources/sass/frontend.scss', 'resources/js/frontend.js'])
    @stack('styles')
</head>

<body data-login-url="{{ route('customer.login') }}">
    @include('frontend.partials.header')

    <main>
        @yield('content')
    </main>

    @include('frontend.partials.footer')
    @include('frontend.partials.flash')
    @stack('scripts')
</body>

</html>
