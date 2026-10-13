<?php

namespace App\Http\Requests\Frontend;

use App\Models\Complaint;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreComplaintRequest extends FormRequest
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

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^(\+?88)?01[3-9]\d{8}$/'],
            'category' => ['required', Rule::in(array_keys(Complaint::CATEGORIES))],
            // Logged-in customers can link one of their own orders
            'order_id' => [
                'nullable',
                Rule::prohibitedIf(! $customer),
                Rule::exists('orders', 'id')->where('customer_id', $customer?->id),
            ],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:20', 'max:3000'],
            'website' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Please enter a valid Bangladeshi mobile number (e.g. 01712345678).',
            'order_id.exists' => 'Please choose one of your own orders.',
        ];
    }
}
