<?php

namespace App\Services;

use App\Exceptions\ShopException;
use App\Models\CartItem;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Logged-in customers keep their cart in the database by customer_id.
 * Guests are identified by a "cart_token" cookie; the guest cart is merged
 * into the customer cart when they log in.
 */
class CartService
{
    public const COOKIE = 'cart_token';

    private ?string $token = null;

    /**
     * Cart items with their products. Items whose product was removed or
     * deactivated are dropped automatically.
     *
     * @return Collection<int, CartItem>
     */
    public function items(): Collection
    {
        if (! $this->hasCart()) {
            return collect();
        }

        $items = $this->query()->with('product.category')->oldest('id')->get();

        $invalid = $items->filter(fn (CartItem $item) => ! $item->product || ! $item->product->status);
        if ($invalid->isNotEmpty()) {
            CartItem::whereKey($invalid->modelKeys())->delete();
        }

        return $items->diff($invalid)->values();
    }

    public function count(): int
    {
        return $this->hasCart() ? (int) $this->query()->sum('quantity') : 0;
    }

    public function add(Product $product, int $quantity): CartItem
    {
        $this->ensurePurchasable($product);

        $item = $this->query()->firstOrNew(['product_id' => $product->id], $this->owner());
        $newQty = ($item->exists ? $item->quantity : 0) + $quantity;

        $this->ensureAvailable($product, $newQty);

        $item->quantity = $newQty;
        $item->save();

        return $item;
    }

    public function update(CartItem $item, int $quantity): CartItem
    {
        $this->ensureAvailable($item->product, $quantity);

        $item->update(['quantity' => $quantity]);

        return $item;
    }

    public function remove(CartItem $item): void
    {
        $item->delete();
    }

    public function clear(): void
    {
        if ($this->hasCart()) {
            $this->query()->delete();
        }
    }

    public function owns(CartItem $item): bool
    {
        $customer = auth('customer')->user();

        return $customer
            ? $item->customer_id === $customer->id
            : $item->cart_token !== null && $item->cart_token === $this->existingToken();
    }

    /**
     * @return array{subtotal: float, shipping: float, total: float}
     */
    public function totals(Collection $items, ?string $zone = null): array
    {
        $subtotal = (float) $items->sum(fn (CartItem $item) => $item->line_total);
        $shipping = $zone && $items->isNotEmpty() ? (float) config("shop.shipping.{$zone}.charge", 0) : 0.0;

        return [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $subtotal + $shipping,
        ];
    }

    /**
     * Move the guest cart (cookie) into the customer's cart after login.
     */
    public function mergeGuestCart(Customer $customer): void
    {
        $token = $this->existingToken();
        if (! $token) {
            return;
        }

        DB::transaction(function () use ($customer, $token) {
            $guestItems = CartItem::with('product')->where('cart_token', $token)->get();

            foreach ($guestItems as $guestItem) {
                $item = CartItem::firstOrNew(['customer_id' => $customer->id, 'product_id' => $guestItem->product_id]);
                $stock = $guestItem->product?->stock_qty ?? 0;
                $item->quantity = min(($item->exists ? $item->quantity : 0) + $guestItem->quantity, max($stock, 1));
                $item->save();
            }

            CartItem::where('cart_token', $token)->delete();
        });

        Cookie::queue(Cookie::forget(self::COOKIE));
    }

    public function ensurePurchasable(Product $product): void
    {
        if (! $product->status || $product->trashed()) {
            throw new ShopException('This product is not available.');
        }

        if ($product->stock_qty < 1) {
            throw new ShopException("{$product->name} is out of stock.");
        }
    }

    public function ensureAvailable(Product $product, int $quantity): void
    {
        if ($quantity > config('shop.max_qty_per_item')) {
            throw new ShopException('You can order at most '.config('shop.max_qty_per_item')." {$product->unit} of one product.");
        }

        if ($quantity > $product->stock_qty) {
            throw new ShopException("Only {$product->stock_qty} {$product->unit} of {$product->name} left in stock.");
        }
    }

    private function query(): Builder
    {
        $customer = auth('customer')->user();

        return $customer
            ? CartItem::where('customer_id', $customer->id)
            : CartItem::where('cart_token', $this->token());
    }

    private function owner(): array
    {
        $customer = auth('customer')->user();

        return $customer ? ['customer_id' => $customer->id] : ['cart_token' => $this->token()];
    }

    private function hasCart(): bool
    {
        return auth('customer')->check() || $this->existingToken() !== null;
    }

    private function existingToken(): ?string
    {
        $token = $this->token ?? request()->cookie(self::COOKIE);

        return is_string($token) && Str::isUuid($token) ? $token : null;
    }

    /**
     * The guest cart token, created (and queued as a cookie) on first use.
     */
    private function token(): string
    {
        if ($this->token = $this->existingToken()) {
            return $this->token;
        }

        $this->token = (string) Str::uuid();
        Cookie::queue(self::COOKIE, $this->token, 60 * 24 * 30);

        return $this->token;
    }
}
