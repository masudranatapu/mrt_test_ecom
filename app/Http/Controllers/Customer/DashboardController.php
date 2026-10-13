<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $customer = $request->user('customer')->load('defaultAddress');

        return view('customer.dashboard', [
            'customer' => $customer,
            'orderCount' => $customer->orders()->count(),
            'wishlistCount' => $customer->wishlists()->count(),
            'addressCount' => $customer->addresses()->count(),
            'recentOrders' => $customer->orders()->withCount('items')->limit(5)->get(),
        ]);
    }
}
