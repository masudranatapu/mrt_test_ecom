<?php

namespace App\Services\Payment;

use App\Exceptions\ShopException;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * SSLCommerz v4 hosted checkout.
 * Docs: https://developer.sslcommerz.com/doc/v4/
 */
class SslCommerzGateway implements PaymentGateway
{
    public function initiate(Payment $payment, Order $order): string
    {
        $order->loadMissing('customer', 'items');

        $response = Http::asForm()->timeout(30)->post($this->baseUrl().'/gwprocess/v4/api.php', [
            'store_id' => config('payment.sslcommerz.store_id'),
            'store_passwd' => config('payment.sslcommerz.store_password'),
            'total_amount' => number_format((float) $payment->amount, 2, '.', ''),
            'currency' => $payment->currency,
            'tran_id' => $payment->transaction_id,

            'success_url' => route('payment.sslcommerz.success'),
            'fail_url' => route('payment.sslcommerz.fail'),
            'cancel_url' => route('payment.sslcommerz.cancel'),
            'ipn_url' => route('payment.sslcommerz.ipn'),

            'cus_name' => $order->shipping_name,
            'cus_email' => $order->customer?->email ?? 'customer@example.com',
            'cus_phone' => $order->shipping_phone,
            'cus_add1' => $order->shipping_address,
            'cus_city' => $order->shipping_city,
            'cus_postcode' => $order->shipping_postal_code ?: '1000',
            'cus_country' => 'Bangladesh',

            'shipping_method' => 'NO',
            'num_of_item' => $order->items->sum('quantity'),
            'product_name' => str($order->items->pluck('product_name')->implode(', '))->limit(240)->toString(),
            'product_category' => 'General',
            'product_profile' => 'general',

            'value_a' => $order->order_number,
        ]);

        $data = $response->json() ?? [];

        if (($data['status'] ?? null) !== 'SUCCESS' || empty($data['GatewayPageURL'])) {
            Log::warning('SSLCommerz init failed', ['tran_id' => $payment->transaction_id, 'response' => $data]);

            throw new ShopException('Could not connect to SSLCommerz. '.($data['failedreason'] ?? 'Please try again.'));
        }

        $payment->update(['gateway_reference' => $data['sessionkey'] ?? null]);

        return $data['GatewayPageURL'];
    }

    /**
     * Ask SSLCommerz whether a transaction (val_id) is genuinely paid.
     * Never trust the browser POST alone.
     *
     * @return array<string, mixed>
     */
    public function validate(string $valId): array
    {
        $response = Http::timeout(30)->get($this->baseUrl().'/validator/api/validationserverAPI.php', [
            'val_id' => $valId,
            'store_id' => config('payment.sslcommerz.store_id'),
            'store_passwd' => config('payment.sslcommerz.store_password'),
            'format' => 'json',
        ]);

        return $response->json() ?? [];
    }

    private function baseUrl(): string
    {
        $env = config('payment.sslcommerz.sandbox') ? 'sandbox' : 'live';

        return config("payment.sslcommerz.urls.{$env}");
    }
}
