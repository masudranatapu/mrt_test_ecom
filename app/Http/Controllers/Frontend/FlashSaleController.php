<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\FlashSale;
use Illuminate\View\View;

class FlashSaleController extends Controller
{
    public function index(): View
    {
        $flashSale = FlashSale::running()->with(['items' => fn ($q) => $q->with('product.category')->orderBy('price')])->orderBy('ends_at')->first()
            ?? FlashSale::upcoming()->with(['items.product.category'])->orderBy('starts_at')->first();

        $items = $flashSale?->items->filter(fn ($item) => $item->product?->status) ?? collect();

        return view('frontend.flash-sale', compact('flashSale', 'items'));
    }
}
