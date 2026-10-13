<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\CampaignController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ComplaintController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\FlashSaleController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StockController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin routes
|--------------------------------------------------------------------------
|
| Loaded with the "web" middleware, the "admin" URL prefix and the
| "admin." name prefix (see bootstrap/app.php).
|
| Each section is guarded by "admin.can:<permission>" (see User::PERMISSIONS).
| Super admins pass every check; staff only the permissions they were given.
|
*/

Route::middleware('guest:admin')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->name('login.store');

    Route::get('forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');

    Route::get('reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [ResetPasswordController::class, 'store'])->name('password.update');
});

Route::middleware('auth:admin')->group(function () {
    // Every admin / staff member
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');
    Route::post('notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');

    // Super admin only: admin / staff accounts and their permissions
    Route::middleware('admin.can:admins')->group(function () {
        Route::resource('admins', AdminController::class)->except('show');
    });

    Route::middleware('admin.can:orders')->group(function () {
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
        Route::patch('orders/{order}/payment', [OrderController::class, 'updatePayment'])->name('orders.payment');
    });

    Route::middleware('admin.can:products')->group(function () {
        Route::resource('categories', CategoryController::class)->except('show');
        Route::resource('products', ProductController::class);
        Route::delete('product-images/{image}', [ProductController::class, 'destroyImage'])->name('products.images.destroy');
    });

    Route::middleware('admin.can:inventory')->group(function () {
        Route::get('inventory', [StockController::class, 'index'])->name('inventory.index');
        Route::get('inventory/history', [StockController::class, 'history'])->name('inventory.history');
        Route::post('products/{product}/stock', [StockController::class, 'store'])->name('products.stock');
    });

    // Customers (view only + approval / status)
    Route::middleware('admin.can:customers')->group(function () {
        Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
        Route::patch('customers/{customer}/approval', [CustomerController::class, 'updateApproval'])->name('customers.approval');
        Route::patch('customers/{customer}/status', [CustomerController::class, 'updateStatus'])->name('customers.status');
    });

    Route::middleware('admin.can:marketing')->group(function () {
        Route::resource('flash-sales', FlashSaleController::class)->except('show');
        Route::resource('campaigns', CampaignController::class)->except('show');
    });

    // Support: contact messages & complaints
    Route::middleware('admin.can:support')->group(function () {
        Route::get('messages', [ContactMessageController::class, 'index'])->name('messages.index');
        Route::get('messages/{message}', [ContactMessageController::class, 'show'])->name('messages.show');
        Route::delete('messages/{message}', [ContactMessageController::class, 'destroy'])->name('messages.destroy');

        Route::get('complaints', [ComplaintController::class, 'index'])->name('complaints.index');
        Route::get('complaints/{complaint}', [ComplaintController::class, 'show'])->name('complaints.show');
        Route::patch('complaints/{complaint}', [ComplaintController::class, 'update'])->name('complaints.update');
    });

    Route::middleware('admin.can:content')->group(function () {
        Route::resource('faqs', FaqController::class)->except('show');

        Route::get('settings/contact', [SettingController::class, 'editContact'])->name('settings.contact');
        Route::put('settings/contact', [SettingController::class, 'updateContact'])->name('settings.contact.update');
    });
});
