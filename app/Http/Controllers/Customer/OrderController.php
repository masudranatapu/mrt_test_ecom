<?php

namespace App\Http\Controllers\Customer;

use App\Exceptions\ShopException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CancelOrderRequest;
use App\Http\Requests\Customer\PayOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\Payment\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orders,
        private PaymentService $payments,
    ) {}

    public function index(Request $request): View
    {
        $orders = $request->user('customer')->orders()
            ->withCount('items')
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->paginate(10)
            ->withQueryString();

        return view('customer.orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order): View
    {
        abort_unless($order->customer_id === $request->user('customer')->id, 404);

        $order->load(['items.product', 'histories']);

        return view('customer.orders.show', compact('order'));
    }

    /**
     * Pay (or retry paying) an online order.
     */
    public function pay(PayOrderRequest $request, Order $order): RedirectResponse
    {
        try {
            return redirect()->away($this->payments->start($order, $request->gateway));
        } catch (ShopException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel(CancelOrderRequest $request, Order $order): RedirectResponse
    {
        try {
            $this->orders->changeStatus($order, 'cancelled', $request->reason ? "Cancelled by customer: {$request->reason}" : 'Cancelled by customer');
        } catch (ShopException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Your order has been cancelled.');
    }
}
