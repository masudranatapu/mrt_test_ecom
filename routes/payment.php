<?php

use App\Http\Controllers\PaymentCallbackController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Payment gateway callbacks
|--------------------------------------------------------------------------
|
| Loaded WITHOUT the "web" middleware group (no session, no cookies, no CSRF):
| the gateways call these URLs directly. See PaymentCallbackController.
|
*/

Route::post('sslcommerz/success', [PaymentCallbackController::class, 'sslcommerzSuccess'])->name('sslcommerz.success');
Route::post('sslcommerz/fail', [PaymentCallbackController::class, 'sslcommerzFail'])->name('sslcommerz.fail');
Route::post('sslcommerz/cancel', [PaymentCallbackController::class, 'sslcommerzCancel'])->name('sslcommerz.cancel');
Route::post('sslcommerz/ipn', [PaymentCallbackController::class, 'sslcommerzIpn'])->name('sslcommerz.ipn');

Route::get('bkash/callback', [PaymentCallbackController::class, 'bkashCallback'])->name('bkash.callback');
