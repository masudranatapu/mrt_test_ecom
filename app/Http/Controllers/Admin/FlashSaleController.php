<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFlashSaleRequest;
use App\Http\Requests\Admin\UpdateFlashSaleRequest;
use App\Models\FlashSale;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FlashSaleController extends Controller
{
    public function index(): View
    {
        $flashSales = FlashSale::withCount('items')->withSum('items', 'sold')->latest('starts_at')->paginate(15);

        return view('admin.flash-sales.index', compact('flashSales'));
    }

    public function create(): View
    {
        return view('admin.flash-sales.create', [
            'flashSale' => new FlashSale(['status' => true, 'starts_at' => now()->startOfHour(), 'ends_at' => now()->startOfHour()->addDay()]),
            'products' => $this->productOptions(),
        ]);
    }

    public function store(StoreFlashSaleRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $flashSale = FlashSale::create($request->safe()->only(['title', 'starts_at', 'ends_at', 'status']));
            $flashSale->items()->createMany($request->validated('items'));
        });

        return redirect()->route('admin.flash-sales.index')->with('success', 'Flash sale created.');
    }

    public function edit(FlashSale $flashSale): View
    {
        return view('admin.flash-sales.edit', [
            'flashSale' => $flashSale->load('items'),
            'products' => $this->productOptions(),
        ]);
    }

    public function update(UpdateFlashSaleRequest $request, FlashSale $flashSale): RedirectResponse
    {
        DB::transaction(function () use ($request, $flashSale) {
            $flashSale->update($request->safe()->only(['title', 'starts_at', 'ends_at', 'status']));

            $items = collect($request->validated('items'))->keyBy('product_id');

            // Keep the "sold" counter of products that stay in the sale
            $flashSale->items()->whereNotIn('product_id', $items->keys())->delete();
            foreach ($items as $productId => $row) {
                $flashSale->items()->updateOrCreate(['product_id' => $productId], [
                    'price' => $row['price'],
                    'quantity_limit' => $row['quantity_limit'] ?? null,
                ]);
            }
        });

        return redirect()->route('admin.flash-sales.index')->with('success', 'Flash sale updated.');
    }

    public function destroy(FlashSale $flashSale): RedirectResponse
    {
        $flashSale->delete();

        return back()->with('success', 'Flash sale deleted.');
    }

    private function productOptions()
    {
        return Product::active()->orderBy('name')->get(['id', 'name', 'price', 'stock_qty']);
    }
}
