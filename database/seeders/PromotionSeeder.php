<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\Category;
use App\Models\FlashSale;
use App\Models\Product;
use Illuminate\Database\Seeder;

class PromotionSeeder extends Seeder
{
    /**
     * Demo: one running flash sale, one running campaign and one upcoming campaign.
     */
    public function run(): void
    {
        if (! FlashSale::exists()) {
            $flash = FlashSale::create([
                'title' => 'Weekend Flash Sale',
                'starts_at' => now()->subHour(),
                'ends_at' => now()->addDays(2)->setTime(23, 59),
                'status' => true,
            ]);

            // ~25% off the regular price, some with a quantity limit
            Product::active()->inStock()->where('is_featured', true)->limit(6)->get()
                ->each(fn (Product $product, $i) => $flash->items()->create([
                    'product_id' => $product->id,
                    'price' => round($product->price * 0.75),
                    'quantity_limit' => $i % 2 === 0 ? 30 : null,
                    'sold' => $i % 2 === 0 ? rand(5, 20) : 0,
                ]));
        }

        if (! Campaign::exists()) {
            $groceries = Category::where('slug', 'groceries')->first();
            $groceryIds = $groceries
                ? Product::active()->whereIn('category_id', $groceries->children()->pluck('id')->push($groceries->id))->pluck('id')
                : collect();

            Campaign::create([
                'title' => 'Mega Grocery Week',
                'slug' => 'mega-grocery-week',
                'subtitle' => 'Rice, oil, spices and lentils at special prices',
                'description' => 'Stock up your kitchen! Every grocery product in this campaign is discounted for one week only.',
                'discount_percent' => 15,
                'starts_at' => now()->subDay(),
                'ends_at' => now()->addDays(5)->setTime(23, 59),
                'is_featured' => true,
                'status' => true,
            ])->products()->sync($groceryIds);

            $care = Category::where('slug', 'personal-care')->first();
            $careIds = $care
                ? Product::active()->whereIn('category_id', $care->children()->pluck('id')->push($care->id))->pluck('id')
                : collect();

            Campaign::create([
                'title' => 'Personal Care Fest',
                'slug' => 'personal-care-fest',
                'subtitle' => 'Soap, shampoo and oral care — 20% off',
                'description' => 'Our biggest personal care sale of the season.',
                'discount_percent' => 20,
                'starts_at' => now()->addDays(3)->setTime(10, 0),
                'ends_at' => now()->addDays(10)->setTime(23, 59),
                'is_featured' => true,
                'status' => true,
            ])->products()->sync($careIds);
        }
    }
}
