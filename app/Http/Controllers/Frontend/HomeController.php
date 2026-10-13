<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\FlashSale;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $products = fn () => Product::active()->with('category');

        $featuredProducts = $products()->where('is_featured', true)->latest()->limit(8)->get();

        return view('frontend.home', [
            'featuredCategories' => Category::active()->root()->where('is_featured', true)
                ->withCount(['children'])
                ->orderBy('sort_order')
                ->get(),
            'heroProducts' => $featuredProducts->take(4),
            // Running or upcoming campaigns, shown with a countdown after the categories
            'campaigns' => Campaign::visible()->where('is_featured', true)->withCount('products')->orderBy('starts_at')->limit(2)->get(),
            'flashSale' => FlashSale::running()
                ->with(['items' => fn ($q) => $q->with('product.category')->orderBy('price')])
                ->orderBy('ends_at')
                ->first(),
            'newArrivals' => $products()->latest()->limit(8)->get(),
            'featuredProducts' => $featuredProducts,
            'bestSellers' => $products()->orderByDesc('sold_qty')->limit(8)->get(),
            'onSaleProducts' => $products()->whereNotNull('sale_price')->whereColumn('sale_price', '<', 'price')
                ->inStock()->latest()->limit(4)->get(),
        ]);
    }
}
