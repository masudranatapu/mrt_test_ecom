<?php

if (! function_exists('money')) {
    /**
     * Format an amount in Bangladeshi Taka, e.g. ৳1,250.00
     */
    function money(float|int|string|null $amount, int $decimals = 2): string
    {
        return config('shop.currency_symbol', '৳').number_format((float) $amount, $decimals);
    }
}

if (! function_exists('system_key_valid')) {
    /**
     * Whether the given key unlocks the /system maintenance page.
     */
    function system_key_valid(mixed $key): bool
    {
        $secret = (string) config('system.key');

        return strlen($secret) >= 16 && is_string($key) && hash_equals($secret, $key);
    }
}

if (! function_exists('setting')) {
    /**
     * Site setting from the admin "Contact & Store Info" page.
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return \App\Models\Setting::get($key, $default);
    }
}

if (! function_exists('storage_url')) {
    /**
     * Public URL of a file on the "public" disk, built from the current
     * request URL (so it works under WAMP sub-folders too, not only APP_URL).
     */
    function storage_url(string $path): string
    {
        return asset('storage/'.ltrim($path, '/'));
    }
}

if (! function_exists('default_image')) {
    /**
     * Local placeholder used when a product / category has no image.
     */
    function default_image(): string
    {
        return asset('default/demo_image.jpg');
    }
}

if (! function_exists('default_avatar')) {
    function default_avatar(): string
    {
        return asset('default/demo_user.png');
    }
}

if (! function_exists('img_fallback')) {
    /**
     * onerror attribute that swaps a broken image for the local placeholder.
     * Usage: <img src="..." {!! img_fallback() !!}>
     */
    function img_fallback(bool $avatar = false): string
    {
        $src = $avatar ? default_avatar() : default_image();

        return 'onerror="this.onerror=null;this.src=\''.e($src).'\'"';
    }
}

if (! function_exists('wishlist_ids')) {
    /**
     * Product ids in the logged-in customer's wishlist (one query per request).
     *
     * @return array<int, int>
     */
    function wishlist_ids(): array
    {
        return once(fn () => auth('customer')->user()?->wishlists()->pluck('product_id')->all() ?? []);
    }
}

if (! function_exists('online_payment_methods')) {
    /**
     * Enabled online gateways, e.g. ['sslcommerz', 'bkash'].
     *
     * @return array<int, string>
     */
    function online_payment_methods(): array
    {
        return array_values(array_filter(['bkash', 'sslcommerz'], fn ($gateway) => config("payment.{$gateway}.enabled")));
    }
}

if (! function_exists('can_see_price')) {
    /**
     * Whether the current visitor is allowed to see product prices.
     */
    function can_see_price(): bool
    {
        if (! config('shop.price_for_approved_only')) {
            return true;
        }

        return (bool) auth('customer')->user()?->is_approved;
    }
}
