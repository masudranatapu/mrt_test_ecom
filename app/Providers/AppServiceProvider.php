<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\ContactMessage;
use App\Models\Customer;
use App\Models\Order;
use App\Services\CartService;
use App\Services\PromotionService;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // One cart instance per request (keeps a freshly created guest token)
        $this->app->scoped(CartService::class);

        // Running flash sales / campaigns are loaded once per request
        $this->app->scoped(PromotionService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Paginator::useBootstrapFive();

        // Category menu, search dropdown and cart / wishlist counters in the storefront header
        View::composer('frontend.partials.header', function ($view) {
            $customer = auth('customer')->user();

            $view->with([
                'headerCategories' => Category::active()->root()
                    ->with(['children' => fn ($q) => $q->active()])
                    ->orderBy('sort_order')
                    ->get(),
                'cartCount' => app(CartService::class)->count(),
                'wishlistCount' => $customer ? $customer->wishlists()->count() : 0,
            ]);
        });

        // Notification bell in the admin top bar
        View::composer('admin.partials.topbar', function ($view) {
            $admin = auth('admin')->user();

            $view->with([
                'unreadCount' => $admin->unreadNotifications()->count(),
                'latestNotifications' => $admin->notifications()->limit(8)->get(),
            ]);
        });

        View::composer('admin.partials.sidebar', function ($view) {
            $view->with([
                'pendingCustomers' => Customer::where('is_approved', false)->count(),
                'pendingOrders' => Order::where('status', 'pending')->count(),
                'unreadMessages' => ContactMessage::unread()->count(),
                'openComplaints' => Complaint::where('status', 'open')->count(),
            ]);
        });

        // A guest's cart follows them into their account
        Event::listen(Login::class, function (Login $event) {
            if ($event->guard === 'customer') {
                app(CartService::class)->mergeGuestCart($event->user);
            }
        });

        // Admins and customers reset their password on different pages
        ResetPassword::createUrlUsing(function ($notifiable, string $token) {
            $route = $notifiable instanceof Customer ? 'customer.password.reset' : 'admin.password.reset';

            return route($route, ['token' => $token, 'email' => $notifiable->getEmailForPasswordReset()]);
        });
    }
}
