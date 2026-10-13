<?php

namespace App\Http\Requests\Admin;

use App\Models\StockMovement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStockAdjustmentRequest extends FormRequest
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
        $product = $this->route('product');

        return [
            'type' => ['required', Rule::in([StockMovement::TYPE_IN, StockMovement::TYPE_OUT])],
            'quantity' => [
                'required', 'integer', 'min:1',
                // Cannot take out more than is in stock
                Rule::when($this->type === StockMovement::TYPE_OUT, ['max:'.$product->stock_qty]),
            ],
            'note' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.max' => 'Only :max units are in stock.',
        ];
    }
}
