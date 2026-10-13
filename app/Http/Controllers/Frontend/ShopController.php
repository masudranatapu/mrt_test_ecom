<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
        // Header search can narrow results with ?category=slug
        $category = $request->category
            ? Category::active()->where('slug', $request->category)->first()
            : null;

        return $this->listing($request, $category);
    }

    public function category(Request $request, Category $category): View
    {
        abort_unless($category->status, 404);

        return $this->listing($request, $category);
    }

    private function listing(Request $request, ?Category $category = null): View
    {
        // A parent category also shows its sub-categories' products
        $categoryIds = $category
            ? $category->children()->active()->pluck('id')->push($category->id)
            : null;

        $products = Product::active()
            ->with('category')
            ->when($categoryIds, fn ($q) => $q->whereIn('category_id', $categoryIds))
            ->when($request->q, function ($query, $term) {
                $query->where(fn ($q) => $q->where('name', 'like', "%{$term}%")
                    ->orWhere('sku', 'like', "%{$term}%")
                    ->orWhere('short_description', 'like', "%{$term}%"));
            })
            ->when($request->boolean('in_stock'), fn ($q) => $q->inStock())
            ->when($request->boolean('on_sale'), fn ($q) => $q->whereNotNull('sale_price')->whereColumn('sale_price', '<', 'price'))
            ->tap(fn ($q) => match ($request->sort) {
                'price_asc' => $q->orderByRaw('COALESCE(NULLIF(sale_price, 0), price) asc'),
                'price_desc' => $q->orderByRaw('COALESCE(NULLIF(sale_price, 0), price) desc'),
                'popular' => $q->orderByDesc('sold_qty'),
                'name' => $q->orderBy('name'),
                default => $q->latest(),
            })
            ->paginate(config('shop.products_per_page'))
            ->withQueryString();

        $sidebarCategories = Category::active()->root()
            ->with(['children' => fn ($q) => $q->active()])
            ->orderBy('sort_order')
            ->get();

        return view('frontend.shop.index', compact('products', 'category', 'sidebarCategories'));
    }
}
