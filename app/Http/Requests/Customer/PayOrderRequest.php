<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PayOrderRequest extends FormRequest
{
    /**
     * Customers may only pay their own unpaid online orders.
     */
    public function authorize(): bool
    {
        $order = $this->route('order');

        return $order->customer_id === $this->user('customer')->id && $order->canBePaidOnline();
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'gateway' => ['required', Rule::in(online_payment_methods())],
        ];
    }
}
