<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlaceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $customer = $this->user('customer');

        // Credit is only offered to approved customers
        $methods = array_merge(['cod'], online_payment_methods(), $customer->canUseCredit() ? ['credit'] : []);

        return [
            'address_id' => [
                'nullable',
                Rule::exists('customer_addresses', 'id')->where('customer_id', $customer->id),
            ],
            'name' => ['required_without:address_id', 'nullable', 'string', 'max:255'],
            'phone' => ['required_without:address_id', 'nullable', 'string', 'regex:/^(\+?88)?01[3-9]\d{8}$/'],
            'address_line' => ['required_without:address_id', 'nullable', 'string', 'max:255'],
            'area' => ['nullable', 'string', 'max:255'],
            'city' => ['required_without:address_id', 'nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'save_address' => ['nullable', 'boolean'],

            'shipping_zone' => ['required', Rule::in(array_keys(config('shop.shipping')))],
            'payment_method' => ['required', Rule::in($methods)],
            'notes' => ['nullable', 'string', 'max:1000'],
            'terms' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Please enter a valid Bangladeshi mobile number (e.g. 01712345678).',
            'payment_method.in' => 'Please choose an available payment method.',
            'terms.accepted' => 'You must accept the terms and conditions to place the order.',
            '*.required_without' => 'The :attribute field is required for a new address.',
        ];
    }

    public function attributes(): array
    {
        return [
            'address_line' => 'address',
            'shipping_zone' => 'delivery area',
        ];
    }

    /**
     * The shipping address: a saved address or the one typed in the form.
     *
     * @return array<string, string|null>
     */
    public function shippingAddress(): array
    {
        if ($this->filled('address_id')) {
            $address = $this->user('customer')->addresses()->findOrFail($this->address_id);

            return $address->only(['name', 'phone', 'address_line', 'area', 'city', 'postal_code']);
        }

        return $this->only(['name', 'phone', 'address_line', 'area', 'city', 'postal_code']);
    }
}
