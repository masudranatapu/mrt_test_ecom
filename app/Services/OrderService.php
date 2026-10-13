<?php

namespace App\Services;

use App\Events\OrderPlaced;
use App\Exceptions\ShopException;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class OrderService
{
    public function __construct(
        private CartService $cart,
        private StockService $stock,
    ) {}

    /**
     * Turn the customer's cart into an order.
     *
     * @param  array{payment_method: string, shipping_zone: string, address: array<string, string|null>, notes?: string|null}  $data
     *
     * @throws ShopException
     */
    public function place(Customer $customer, array $data): Order
    {
        $items = $this->cart->items();

        if ($items->isEmpty()) {
            throw new ShopException('Your cart is empty.');
        }

        if ($data['payment_method'] === 'credit' && ! $customer->canUseCredit()) {
            throw new ShopException('28-day credit is only available for approved customers.');
        }

        $order = DB::transaction(function () use ($customer, $data, $items) {
            // Lock the products so two customers cannot buy the last unit at once
            $products = Product::whereIn('id', $items->pluck('product_id'))->lockForUpdate()->get()->keyBy('id');

            $lines = $items->map(function ($item) use ($products) {
                $product = $products->get($item->product_id);

                if (! $product || ! $product->status) {
                    throw new ShopException('A product in your cart is no longer available. Please review your cart.');
                }

                if ($item->quantity > $product->stock_qty) {
                    throw new ShopException("Only {$product->stock_qty} {$product->unit} of {$product->name} left. Please update your cart.");
                }

                $promotion = $product->promotion();
                $price = (float) $product->final_price;

                return [
                    'product' => $product,
                    'quantity' => $item->quantity,
                    'price' => $price,
                    'line_total' => $price * $item->quantity,
                    'flash_item' => $promotion['flash_item'] ?? null,
                ];
            });

            $subtotal = $lines->sum('line_total');
            $shipping = (float) config("shop.shipping.{$data['shipping_zone']}.charge", 0);
            $address = $data['address'];

            $order = Order::create([
                'customer_id' => $customer->id,
                'status' => 'pending',
                'payment_method' => $data['payment_method'],
                'payment_status' => 'unpaid',
                'shipping_name' => $address['name'],
                'shipping_phone' => $address['phone'],
                'shipping_address' => $address['address_line'],
                'shipping_area' => $address['area'] ?? null,
                'shipping_city' => $address['city'],
                'shipping_postal_code' => $address['postal_code'] ?? null,
                'shipping_zone' => $data['shipping_zone'],
                'subtotal' => $subtotal,
                'discount' => 0,
                'shipping_charge' => $shipping,
                'total' => $subtotal + $shipping,
                'notes' => $data['notes'] ?? null,
                'due_date' => $data['payment_method'] === 'credit' ? now()->addDays(config('shop.credit_days'))->toDateString() : null,
            ]);

            $order->update(['order_number' => 'ORD-'.now()->format('ymd').'-'.str_pad($order->id, 5, '0', STR_PAD_LEFT)]);

            foreach ($lines as $line) {
                $product = $line['product'];

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'unit' => $product->unit,
                    'price' => $line['price'],
                    'quantity' => $line['quantity'],
                    'line_total' => $line['line_total'],
                ]);

                $this->stock->adjust($product, StockMovement::TYPE_OUT, $line['quantity'], 'order', "Order {$order->order_number}");
                $product->increment('sold_qty', $line['quantity']);

                // Count towards the flash sale quantity limit
                $line['flash_item']?->increment('sold', $line['quantity']);
            }

            $order->histories()->create(['status' => 'pending', 'note' => 'Order placed by customer']);

            $this->cart->clear();

            return $order;
        });

        $this->notifyAdmins($order->load('customer'));

        return $order;
    }

    /**
     * @throws ShopException
     */
    public function changeStatus(Order $order, string $status, ?string $note = null, ?int $adminId = null): Order
    {
        if ($order->isFinal()) {
            throw new ShopException("This order is already {$order->status_label} and cannot be changed.");
        }

        if ($order->status === $status) {
            throw new ShopException("The order is already {$order->status_label}.");
        }

        return DB::transaction(function () use ($order, $status, $note, $adminId) {
            $changes = ['status' => $status];

            if ($status === 'cancelled') {
                $changes['cancelled_at'] = now();
                $this->restock($order);
            }

            if ($status === 'delivered') {
                $changes['delivered_at'] = now();

                // Cash is collected on delivery
                if ($order->payment_method === 'cod' && $order->payment_status !== 'paid') {
                    $changes['payment_status'] = 'paid';
                    $changes['paid_at'] = now();
                }
            }

            $order->update($changes);
            $order->histories()->create(['status' => $status, 'note' => $note, 'user_id' => $adminId]);

            return $order;
        });
    }

    public function changePaymentStatus(Order $order, string $paymentStatus, ?int $adminId = null): Order
    {
        $order->update([
            'payment_status' => $paymentStatus,
            'paid_at' => $paymentStatus === 'paid' ? ($order->paid_at ?? now()) : null,
        ]);

        $order->histories()->create([
            'status' => $order->status,
            'note' => 'Payment marked as '.Order::PAYMENT_STATUSES[$paymentStatus]['label'],
            'user_id' => $adminId,
        ]);

        return $order;
    }

    /**
     * Put the stock of a cancelled order back and undo the sold count.
     */
    private function restock(Order $order): void
    {
        foreach ($order->items()->with('product')->get() as $item) {
            if (! $item->product) {
                continue; // product was permanently deleted
            }

            $this->stock->adjust($item->product, StockMovement::TYPE_IN, $item->quantity, 'order_cancel', "Order {$order->order_number} cancelled");
            $item->product->decrement('sold_qty', min($item->quantity, $item->product->sold_qty));
        }
    }

    /**
     * Saved notification for every active admin + a live Pusher push.
     * A Pusher outage must never break order placement, so failures are only logged.
     */
    private function notifyAdmins(Order $order): void
    {
        rescue(fn () => Notification::send(User::withPermission('orders'), new NewOrderNotification($order)));
        rescue(fn () => broadcast(new OrderPlaced($order)));
    }
}
