<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class CancelOrderRequest extends FormRequest
{
    /**
     * A customer may cancel only their own order, and only while it is pending.
     */
    public function authorize(): bool
    {
        $order = $this->route('order');

        return $order->customer_id === $this->user('customer')->id && $order->canBeCancelledByCustomer();
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'reason' => ['nullable', 'string', 'max:255'],
        ];
    }
}
