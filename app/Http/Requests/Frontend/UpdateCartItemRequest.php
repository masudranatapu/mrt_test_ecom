<?php

namespace App\Http\Requests\Frontend;

use App\Services\CartService;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCartItemRequest extends FormRequest
{
    /**
     * Only the owner of the cart (customer or guest token) may change the item.
     */
    public function authorize(CartService $cart): bool
    {
        return $cart->owns($this->route('item'));
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'min:1', 'max:'.config('shop.max_qty_per_item')],
        ];
    }
}
