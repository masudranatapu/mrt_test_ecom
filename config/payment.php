<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SSLCommerz (cards, mobile banking, internet banking)
    |--------------------------------------------------------------------------
    */

    'sslcommerz' => [
        'enabled' => env('SSLCZ_ENABLED', true),
        'store_id' => env('SSLCZ_STORE_ID'),
        'store_password' => env('SSLCZ_STORE_PASSWORD'),
        'sandbox' => env('SSLCZ_SANDBOX', true),
        'urls' => [
            'sandbox' => 'https://sandbox.sslcommerz.com',
            'live' => 'https://securepay.sslcommerz.com',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | bKash Tokenized Checkout
    |--------------------------------------------------------------------------
    */

    'bkash' => [
        'enabled' => env('BKASH_ENABLED', true),
        'username' => env('BKASH_USERNAME'),
        'password' => env('BKASH_PASSWORD'),
        'app_key' => env('BKASH_APP_KEY'),
        'app_secret' => env('BKASH_APP_SECRET'),
        'sandbox' => env('BKASH_SANDBOX', true),
        'urls' => [
            'sandbox' => 'https://tokenized.sandbox.bka.sh/v1.2.0-beta',
            'live' => 'https://tokenized.pay.bka.sh/v1.2.0-beta',
        ],
    ],

];
