<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\StockService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * [category slug, name, unit, price, sale price, stock, sold, best before (months from now) or null, featured]
     */
    private array $products = [
        ['rice-grains', 'Premium Miniket Rice 5kg', 'pack', 420, 395, 120, 340, 12, true],
        ['rice-grains', 'Chinigura Aromatic Rice 1kg', 'pack', 165, null, 64, 210, 12, false],
        ['rice-grains', 'Atta (Whole Wheat Flour) 2kg', 'pack', 140, 130, 38, 95, 6, false],
        ['oil-ghee', 'Fortified Soybean Oil 5L', 'bottle', 920, 880, 75, 410, 18, true],
        ['oil-ghee', 'Pure Mustard Oil 1L', 'bottle', 310, null, 22, 160, 12, false],
        ['oil-ghee', 'Pure Cow Ghee 400g', 'jar', 690, 650, 8, 45, 9, false],
        ['spices', 'Turmeric Powder 200g', 'pack', 95, null, 140, 280, 18, false],
        ['spices', 'Red Chilli Powder 200g', 'pack', 110, 99, 55, 190, 18, false],
        ['spices', 'Garam Masala 100g', 'pack', 85, null, 0, 120, 12, false],
        ['lentils-pulses', 'Red Lentil (Masur Dal) 1kg', 'pack', 135, null, 90, 260, 10, false],
        ['lentils-pulses', 'Chickpeas (Boot) 500g', 'pack', 70, 65, 46, 75, 10, false],
        ['tea-coffee', 'Premium Black Tea 400g', 'pack', 230, 215, 200, 520, 24, true],
        ['tea-coffee', 'Instant Coffee Jar 100g', 'jar', 480, null, 18, 88, 18, false],
        ['juice-drinks', 'Mango Juice 1L', 'bottle', 120, null, 60, 175, 6, false],
        ['juice-drinks', 'Orange Drink Powder 500g', 'pack', 260, 240, 5, 64, 12, false],
        ['biscuits', 'Butter Cookies Tin 400g', 'tin', 350, 320, 33, 140, 8, true],
        ['biscuits', 'Cream Crackers 300g', 'pack', 60, null, 150, 430, 6, false],
        ['chips-crackers', 'Potato Chips Salted 100g', 'pack', 45, null, 85, 300, 4, false],
        ['chips-crackers', 'Spicy Chanachur 350g', 'pack', 90, 85, 9, 220, 5, false],
        ['milk-powder', 'Full Cream Milk Powder 1kg', 'pack', 880, 850, 42, 190, 12, true],
        ['milk-powder', 'UHT Liquid Milk 1L', 'pack', 110, null, 70, 260, 3, false],
        ['breakfast-cereals', 'Corn Flakes 500g', 'box', 390, 360, 27, 70, 9, false],
        ['breakfast-cereals', 'Rolled Oats 1kg', 'pack', 450, null, 15, 58, 12, false],
        ['soap-body-wash', 'Herbal Beauty Soap 100g (Pack of 4)', 'pack', 220, 199, 110, 230, null, false],
        ['soap-body-wash', 'Antibacterial Body Wash 250ml', 'bottle', 340, null, 36, 85, null, false],
        ['hair-care', 'Anti-Dandruff Shampoo 340ml', 'bottle', 520, 480, 48, 132, null, true],
        ['hair-care', 'Coconut Hair Oil 200ml', 'bottle', 150, null, 7, 175, 24, false],
        ['oral-care', 'Herbal Toothpaste 150g', 'tube', 130, 120, 95, 300, 24, false],
        ['laundry', 'Washing Powder 1kg', 'pack', 160, 150, 80, 340, null, false],
        ['laundry', 'Liquid Detergent 1L', 'bottle', 380, null, 0, 60, null, false],
        ['dishwash', 'Dishwashing Liquid 500ml', 'bottle', 145, 135, 64, 280, null, false],
        ['floor-surface', 'Floor Cleaner Lemon 1L', 'bottle', 210, null, 29, 90, null, false],
    ];

    public function run(StockService $stock): void
    {
        $adminId = User::value('id');
        $categories = Category::pluck('id', 'slug');

        foreach ($this->products as [$categorySlug, $name, $unit, $price, $salePrice, $stockQty, $sold, $bestBefore, $featured]) {
            $slug = Str::slug($name);

            if (Product::withTrashed()->where('slug', $slug)->exists()) {
                continue;
            }

            $product = Product::create([
                'category_id' => $categories[$categorySlug],
                'name' => $name,
                'slug' => $slug,
                'sku' => 'TMP-' . uniqid(),
                'short_description' => "Quality {$name} for everyday use. Carefully packed and delivered fresh.",
                'description' => "{$name} sourced from trusted suppliers and stored under proper conditions.\n\n"
                    . "- 100% genuine product\n- Hygienically packed\n- Best price guaranteed",
                'price' => $price,
                'sale_price' => $salePrice,
                'unit' => $unit,
                'stock_qty' => 0,
                'sold_qty' => $sold,
                'low_stock_alert' => 10,
                'best_before_date' => $bestBefore ? now()->addMonths($bestBefore)->toDateString() : null,
                'is_featured' => $featured,
                'status' => true,
            ]);

            $product->update(['sku' => 'PRD-' . str_pad($product->id, 5, '0', STR_PAD_LEFT)]);

            if ($stockQty > 0) {
                $stock->adjust($product, StockMovement::TYPE_IN, $stockQty, 'initial', 'Opening stock', $adminId);
            }
        }
    }
}
