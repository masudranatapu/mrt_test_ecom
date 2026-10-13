<?php

namespace App\Services\Payment;

use App\Exceptions\ShopException;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Starts online payments and records their result on the order.
 * Every completion path is idempotent: SSLCommerz may call both the
 * success URL and the IPN for the same transaction.
 */
class PaymentService
{
    public function __construct(
        private SslCommerzGateway $sslcommerz,
        private BkashGateway $bkash,
    ) {}

    /**
     * @return string URL of the gateway's payment page
     *
     * @throws ShopException
     */
    public function start(Order $order, string $gateway): string
    {
        if ($order->payment_status === 'paid') {
            throw new ShopException('This order is already paid.');
        }

        if (! $order->canBePaidOnline()) {
            throw new ShopException('This order cannot be paid online.');
        }

        if (! config("payment.{$gateway}.enabled")) {
            throw new ShopException('This payment method is not available.');
        }

        // The customer may switch between bKash and SSLCommerz when retrying
        if ($order->payment_method !== $gateway) {
            $order->update(['payment_method' => $gateway]);
        }

        $payment = $order->payments()->create([
            'gateway' => $gateway,
            'transaction_id' => 'TXN'.$order->id.'-'.Str::upper(Str::random(10)),
            'amount' => $order->total,
            'currency' => 'BDT',
            'status' => 'initiated',
        ]);

        return $this->gateway($gateway)->initiate($payment, $order);
    }

    /**
     * SSLCommerz success URL / IPN: validate with SSLCommerz, then mark paid.
     */
    public function completeSslCommerz(array $data): ?Payment
    {
        $payment = Payment::where('transaction_id', $data['tran_id'] ?? '')->where('gateway', 'sslcommerz')->first();

        if (! $payment) {
            return null;
        }

        if ($payment->status === 'success') {
            return $payment;
        }

        if (empty($data['val_id'])) {
            $this->markFailed($payment, 'failed', $data);

            return $payment;
        }

        $validation = $this->sslcommerz->validate($data['val_id']);

        $valid = in_array($validation['status'] ?? null, ['VALID', 'VALIDATED'], true)
            && ($validation['tran_id'] ?? null) === $payment->transaction_id
            && abs((float) ($validation['amount'] ?? 0) - (float) $payment->amount) < 0.01
            && ($validation['currency_type'] ?? $validation['currency'] ?? 'BDT') === 'BDT';

        if (! $valid) {
            Log::warning('SSLCommerz validation failed', ['tran_id' => $payment->transaction_id, 'validation' => $validation]);
            $this->markFailed($payment, 'failed', $validation);

            return $payment;
        }

        $this->markPaid($payment, $validation['val_id'] ?? $data['val_id'], $validation);

        return $payment;
    }

    public function failSslCommerz(array $data, string $status): ?Payment
    {
        $payment = Payment::where('transaction_id', $data['tran_id'] ?? '')->where('gateway', 'sslcommerz')->first();

        if ($payment && $payment->status !== 'success') {
            $this->markFailed($payment, $status, $data);
        }

        return $payment;
    }

    /**
     * bKash callback: execute the payment when the customer approved it.
     */
    public function completeBkash(string $paymentId, string $status): ?Payment
    {
        $payment = Payment::where('gateway_reference', $paymentId)->where('gateway', 'bkash')->first();

        if (! $payment) {
            return null;
        }

        if ($payment->status === 'success') {
            return $payment;
        }

        if ($status !== 'success') {
            $this->markFailed($payment, $status === 'cancel' ? 'cancelled' : 'failed', ['status' => $status]);

            return $payment;
        }

        $result = $this->bkash->execute($paymentId);

        $paid = ($result['transactionStatus'] ?? null) === 'Completed'
            && abs((float) ($result['amount'] ?? 0) - (float) $payment->amount) < 0.01;

        if ($paid) {
            $this->markPaid($payment, $result['trxID'] ?? null, $result);
        } else {
            $this->markFailed($payment, 'failed', $result);
        }

        return $payment;
    }

    private function markPaid(Payment $payment, ?string $gatewayTransactionId, array $payload): void
    {
        DB::transaction(function () use ($payment, $gatewayTransactionId, $payload) {
            // Lock so a simultaneous IPN + success redirect only records the payment once
            $payment = Payment::whereKey($payment->id)->lockForUpdate()->first();
            if ($payment->status === 'success') {
                return;
            }

            $payment->update([
                'status' => 'success',
                'gateway_transaction_id' => $gatewayTransactionId,
                'payload' => $payload,
                'paid_at' => now(),
            ]);

            $order = $payment->order;
            $order->update(['payment_status' => 'paid', 'paid_at' => now()]);
            $order->histories()->create([
                'status' => $order->status,
                'note' => 'Paid '.money($payment->amount).' via '.Payment::GATEWAYS[$payment->gateway]
                    .($gatewayTransactionId ? " (Txn: {$gatewayTransactionId})" : ''),
            ]);
        });
    }

    private function markFailed(Payment $payment, string $status, array $payload): void
    {
        $payment->update(['status' => $status, 'payload' => $payload]);

        $order = $payment->order;
        if ($order->payment_status !== 'paid') {
            $order->update(['payment_status' => 'failed']);
            $order->histories()->create([
                'status' => $order->status,
                'note' => Payment::GATEWAYS[$payment->gateway].' payment '.($status === 'cancelled' ? 'cancelled by customer' : 'failed'),
            ]);
        }
    }

    private function gateway(string $name): PaymentGateway
    {
        return match ($name) {
            'sslcommerz' => $this->sslcommerz,
            'bkash' => $this->bkash,
            default => throw new ShopException('Unknown payment method.'),
        };
    }
}
