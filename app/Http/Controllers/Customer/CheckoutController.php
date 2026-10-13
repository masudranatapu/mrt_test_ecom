<?php

namespace App\Http\Controllers\Customer;

use App\Exceptions\ShopException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\PlaceOrderRequest;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\Payment\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cart,
        private OrderService $orders,
        private PaymentService $payments,
    ) {}

    public function create(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->guard()) {
            return $redirect;
        }

        $customer = $request->user('customer');
        $items = $this->cart->items();
        $zone = old('shipping_zone', 'inside_dhaka');

        return view('customer.checkout.create', [
            'customer' => $customer,
            'items' => $items,
            'addresses' => $customer->addresses()->get(),
            'zones' => config('shop.shipping'),
            'selectedZone' => $zone,
            'totals' => $this->cart->totals($items, $zone),
        ]);
    }

    public function store(PlaceOrderRequest $request): RedirectResponse
    {
        if ($redirect = $this->guard()) {
            return $redirect;
        }

        $customer = $request->user('customer');

        try {
            $order = $this->orders->place($customer, [
                'payment_method' => $request->payment_method,
                'shipping_zone' => $request->shipping_zone,
                'address' => $request->shippingAddress(),
                'notes' => $request->notes,
            ]);
        } catch (ShopException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        // Optionally keep the new address for next time
        if (! $request->filled('address_id') && $request->boolean('save_address')) {
            $customer->addresses()->create($request->shippingAddress() + [
                'label' => 'Home',
                'is_default' => ! $customer->addresses()->exists(),
            ]);
        }

        // Online payment: send the customer to bKash / SSLCommerz right away
        if ($order->isOnlinePayment()) {
            try {
                return redirect()->away($this->payments->start($order, $order->payment_method));
            } catch (ShopException $e) {
                return redirect()->route('customer.orders.show', $order)
                    ->with('warning', "Your order {$order->order_number} is saved, but the payment could not start: {$e->getMessage()} You can try again below.");
            }
        }

        return redirect()->route('customer.orders.show', $order)
            ->with('success', "Thank you! Your order {$order->order_number} has been placed.");
    }

    /**
     * Checkout needs a non-empty cart and permission to see prices.
     */
    private function guard(): ?RedirectResponse
    {
        if (! can_see_price()) {
            return redirect()->route('customer.dashboard')->with('warning', 'Your account must be approved before you can place orders.');
        }

        if ($this->cart->count() === 0) {
            return redirect()->route('cart.index')->with('warning', 'Your cart is empty.');
        }

        return null;
    }
}
