<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));

            Route::middleware('web')
                ->name('customer.')
                ->group(base_path('routes/customer.php'));

            // Gateway callbacks: no session / CSRF on purpose
            Route::middleware(SubstituteBindings::class)
                ->prefix('payment')
                ->name('payment.')
                ->group(base_path('routes/payment.php'));
        },
    )
    ->withBroadcasting(
        __DIR__.'/../routes/channels.php',
        ['middleware' => ['web']],
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin.can' => \App\Http\Middleware\EnsureAdminHasPermission::class,
        ]);

        $isAdminArea = fn (Request $request) => $request->is('admin', 'admin/*');

        // Where a guest is sent when hitting a protected page
        $middleware->redirectGuestsTo(function (Request $request) use ($isAdminArea) {
            if ($isAdminArea($request)) {
                return route('admin.login');
            }

            return Route::has('customer.login') ? route('customer.login') : route('home');
        });

        // Where a logged-in user is sent when hitting a guest-only page
        $middleware->redirectUsersTo(function (Request $request) use ($isAdminArea) {
            if ($isAdminArea($request)) {
                return route('admin.dashboard');
            }

            return Route::has('customer.dashboard') ? route('customer.dashboard') : route('home');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
