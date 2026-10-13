<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'orders_today' => Order::whereDate('created_at', today())->count(),
            'sales_month' => (float) Order::where('status', '!=', 'cancelled')
                ->whereBetween('created_at', [now()->startOfMonth(), now()])
                ->sum('total'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'pending_customers' => Customer::where('is_approved', false)->count(),
            'products' => Product::count(),
            'low_stock' => Product::whereColumn('stock_qty', '<=', 'low_stock_alert')->count(),
            'unpaid_credit' => (float) Order::where('payment_method', 'credit')
                ->where('payment_status', '!=', 'paid')
                ->where('status', '!=', 'cancelled')
                ->sum('total'),
        ];

        $recentOrders = Order::with('customer')->latest()->limit(8)->get();

        $lowStockProducts = Product::whereColumn('stock_qty', '<=', 'low_stock_alert')
            ->orderBy('stock_qty')
            ->limit(6)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'lowStockProducts'));
    }
}
