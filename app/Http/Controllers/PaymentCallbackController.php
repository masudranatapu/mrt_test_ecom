<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\Payment\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Return URLs called by the payment gateways.
 *
 * These routes run WITHOUT the session middleware (see routes/payment.php):
 * SSLCommerz returns with a cross-site POST, so the browser does not send the
 * session cookie — starting a new session here would log the customer out.
 * The result is passed to the order page with ?payment=... instead of a flash message.
 */
class PaymentCallbackController extends Controller
{
    public function __construct(private PaymentService $payments) {}

    public function sslcommerzSuccess(Request $request): RedirectResponse
    {
        return $this->backToOrder($this->payments->completeSslCommerz($request->all()));
    }

    public function sslcommerzFail(Request $request): RedirectResponse
    {
        return $this->backToOrder($this->payments->failSslCommerz($request->all(), 'failed'));
    }

    public function sslcommerzCancel(Request $request): RedirectResponse
    {
        return $this->backToOrder($this->payments->failSslCommerz($request->all(), 'cancelled'));
    }

    /**
     * Server-to-server notification from SSLCommerz (works even if the
     * customer closes the browser before returning).
     */
    public function sslcommerzIpn(Request $request): Response
    {
        $this->payments->completeSslCommerz($request->all());

        return response('OK');
    }

    public function bkashCallback(Request $request): RedirectResponse
    {
        $payment = $this->payments->completeBkash(
            (string) $request->query('paymentID'),
            (string) $request->query('status'),
        );

        return $this->backToOrder($payment);
    }

    private function backToOrder(?Payment $payment): RedirectResponse
    {
        if (! $payment) {
            return redirect()->route('home');
        }

        $result = match ($payment->status) {
            'success' => 'success',
            'cancelled' => 'cancelled',
            default => 'failed',
        };

        return redirect()->to(route('customer.orders.show', $payment->order_id).'?payment='.$result);
    }
}
