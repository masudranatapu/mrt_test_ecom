<?php

namespace App\Http\Requests\Admin;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreFlashSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Ignore empty product rows from the form
        $this->merge([
            'items' => collect($this->input('items', []))->filter(fn ($row) => ! empty($row['product_id']))->values()->all(),
        ]);
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'status' => ['required', 'boolean'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'distinct', 'exists:products,id'],
            'items.*.price' => ['required', 'numeric', 'min:1'],
            'items.*.quantity_limit' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /**
     * Flash price must be lower than the product's regular price.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $prices = Product::whereIn('id', collect($this->items)->pluck('product_id'))->pluck('price', 'id');

                foreach ($this->items ?? [] as $i => $row) {
                    $regular = $prices[$row['product_id']] ?? null;
                    if ($regular !== null && (float) $row['price'] >= (float) $regular) {
                        $validator->errors()->add("items.{$i}.price", 'Flash price must be lower than the regular price ('.money($regular).').');
                    }
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Add at least one product to the flash sale.',
            'items.*.product_id.distinct' => 'The same product is added twice.',
        ];
    }
}
