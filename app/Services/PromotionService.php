<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\FlashSaleItem;
use App\Models\Product;

/**
 * Works out the best running promotion (flash sale or campaign) for a product.
 * Loaded once per request (registered as a scoped singleton).
 */
class PromotionService
{
    /** @var array<int, FlashSaleItem>|null */
    private ?array $flashItems = null;

    /** @var array<int, Campaign>|null */
    private ?array $campaigns = null;

    /**
     * Best promotion for the product, or null when none is cheaper than its normal price.
     *
     * @return array{type: string, price: float, ends_at: \Illuminate\Support\Carbon, label: string, flash_item?: FlashSaleItem, campaign?: Campaign}|null
     */
    public function bestFor(Product $product): ?array
    {
        $base = $product->regularSellingPrice();
        $offers = [];

        if ($item = $this->flashItems()[$product->id] ?? null) {
            $offers[] = [
                'type' => 'flash',
                'price' => (float) $item->price,
                'ends_at' => $item->flashSale->ends_at,
                'label' => 'Flash Sale',
                'flash_item' => $item,
            ];
        }

        if ($campaign = $this->campaigns()[$product->id] ?? null) {
            $offers[] = [
                'type' => 'campaign',
                'price' => round((float) $product->price * (100 - $campaign->discount_percent) / 100, 2),
                'ends_at' => $campaign->ends_at,
                'label' => $campaign->title,
                'campaign' => $campaign,
            ];
        }

        $best = collect($offers)->sortBy('price')->first();

        return $best && $best['price'] < $base ? $best : null;
    }

    /**
     * Product id => running flash sale item (still within its quantity limit).
     *
     * @return array<int, FlashSaleItem>
     */
    private function flashItems(): array
    {
        return $this->flashItems ??= FlashSaleItem::query()
            ->with('flashSale')
            ->whereHas('flashSale', fn ($q) => $q->running())
            ->where(fn ($q) => $q->whereNull('quantity_limit')->orWhereColumn('sold', '<', 'quantity_limit'))
            ->orderBy('price')
            ->get()
            ->unique('product_id')
            ->keyBy('product_id')
            ->all();
    }

    /**
     * Product id => running campaign with the biggest discount.
     *
     * @return array<int, Campaign>
     */
    private function campaigns(): array
    {
        if ($this->campaigns !== null) {
            return $this->campaigns;
        }

        $map = [];
        foreach (Campaign::running()->with('products:id')->orderByDesc('discount_percent')->get() as $campaign) {
            foreach ($campaign->products as $product) {
                $map[$product->id] ??= $campaign;
            }
        }

        return $this->campaigns = $map;
    }
}
