<?php

namespace App\Services\Payment;

use App\Exceptions\ShopException;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * bKash Tokenized Checkout (v1.2.0-beta).
 * Docs: https://developer.bka.sh/docs/tokenized-checkout-overview
 */
class BkashGateway implements PaymentGateway
{
    public function initiate(Payment $payment, Order $order): string
    {
        $data = $this->call('/tokenized/checkout/create', [
            'mode' => '0011',
            'payerReference' => $order->shipping_phone,
            'callbackURL' => route('payment.bkash.callback'),
            'amount' => number_format((float) $payment->amount, 2, '.', ''),
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => $payment->transaction_id,
        ]);

        if (($data['statusCode'] ?? null) !== '0000' || empty($data['bkashURL'])) {
            Log::warning('bKash create payment failed', ['tran_id' => $payment->transaction_id, 'response' => $data]);

            throw new ShopException('Could not connect to bKash. '.($data['statusMessage'] ?? 'Please try again.'));
        }

        $payment->update(['gateway_reference' => $data['paymentID']]);

        return $data['bkashURL'];
    }

    /**
     * Finalise the payment after the customer approved it in the bKash page.
     *
     * @return array<string, mixed>
     */
    public function execute(string $paymentId): array
    {
        $data = $this->call('/tokenized/checkout/execute', ['paymentID' => $paymentId]);

        // If execute timed out on our side the payment may already be done; query it
        if (! isset($data['transactionStatus'])) {
            $data = $this->call('/tokenized/checkout/payment/status', ['paymentID' => $paymentId]);
        }

        return $data;
    }

    private function call(string $path, array $body): array
    {
        $response = Http::timeout(30)
            ->acceptJson()
            ->withHeaders([
                'Authorization' => $this->token(),
                'X-APP-Key' => config('payment.bkash.app_key'),
            ])
            ->post($this->baseUrl().$path, $body);

        return $response->json() ?? [];
    }

    /**
     * Grant token, cached for 50 minutes (bKash tokens live for 1 hour).
     */
    private function token(): string
    {
        return Cache::remember('bkash_id_token', now()->addMinutes(50), function () {
            $response = Http::timeout(30)
                ->acceptJson()
                ->withHeaders([
                    'username' => config('payment.bkash.username'),
                    'password' => config('payment.bkash.password'),
                ])
                ->post($this->baseUrl().'/tokenized/checkout/token/grant', [
                    'app_key' => config('payment.bkash.app_key'),
                    'app_secret' => config('payment.bkash.app_secret'),
                ]);

            $token = $response->json('id_token');

            if (! $token) {
                Log::warning('bKash grant token failed', ['response' => $response->json()]);

                throw new ShopException('bKash is not available right now. Please try another payment method.');
            }

            return $token;
        });
    }

    private function baseUrl(): string
    {
        $env = config('payment.bkash.sandbox') ? 'sandbox' : 'live';

        return config("payment.bkash.urls.{$env}");
    }
}
