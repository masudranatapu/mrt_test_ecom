<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Price visibility
    |--------------------------------------------------------------------------
    |
    | Default: everyone (including guests) sees prices and can buy.
    | Set SHOP_PRICE_FOR_APPROVED_ONLY=true to hide prices from guests and
    | not-yet-approved customers (B2B mode).
    |
    */

    'price_for_approved_only' => env('SHOP_PRICE_FOR_APPROVED_ONLY', false),

    'currency_symbol' => '৳',

    'products_per_page' => 12,

    /*
    |--------------------------------------------------------------------------
    | Delivery charges (৳) per zone
    |--------------------------------------------------------------------------
    */

    'shipping' => [
        'inside_dhaka' => ['label' => 'Inside Dhaka', 'charge' => 60],
        'outside_dhaka' => ['label' => 'Outside Dhaka', 'charge' => 120],
    ],

    /*
    |--------------------------------------------------------------------------
    | Credit terms for approved customers
    |--------------------------------------------------------------------------
    */

    'credit_days' => 28,

    /** Maximum quantity of one product per order line. */
    'max_qty_per_item' => 500,

];
