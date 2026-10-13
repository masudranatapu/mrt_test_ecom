<?php

use App\Http\Controllers\Frontend\CampaignController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\FlashSaleController;
use App\Http\Controllers\Frontend\ComplaintController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\FaqController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\ShopController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Frontend (public storefront) routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('shop', [ShopController::class, 'index'])->name('shop');
Route::get('category/{category:slug}', [ShopController::class, 'category'])->name('category.show');
Route::get('product/{product:slug}', [ProductController::class, 'show'])->name('product.show');

Route::get('flash-sale', [FlashSaleController::class, 'index'])->name('flash-sale');
Route::get('campaign/{campaign:slug}', [CampaignController::class, 'show'])->name('campaign.show');
Route::view('about', 'frontend.about')->name('about');

Route::get('faq', [FaqController::class, 'index'])->name('faq');

Route::get('contact', [ContactController::class, 'create'])->name('contact');
Route::post('contact', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');

Route::get('complaint', [ComplaintController::class, 'create'])->name('complaint');
Route::post('complaint', [ComplaintController::class, 'store'])->middleware('throttle:5,1')->name('complaint.store');

// Cart works for guests too (cookie) and is merged into the customer cart on login
Route::get('cart', [CartController::class, 'index'])->name('cart.index');
Route::post('cart', [CartController::class, 'store'])->name('cart.store');
Route::patch('cart/{item}', [CartController::class, 'update'])->name('cart.update');
Route::delete('cart/{item}', [CartController::class, 'destroy'])->name('cart.destroy');
