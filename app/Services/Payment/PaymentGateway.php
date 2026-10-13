<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\Payment;

interface PaymentGateway
{
    /**
     * Create the payment at the gateway and return the URL the customer
     * must be redirected to. Implementations may store a gateway reference
     * on the payment.
     *
     * @throws \App\Exceptions\ShopException
     */
    public function initiate(Payment $payment, Order $order): string;
}
