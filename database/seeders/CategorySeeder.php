<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $tree = [
            ['Groceries', 'bi-basket', true, ['Rice & Grains', 'Oil & Ghee', 'Spices', 'Lentils & Pulses']],
            ['Beverages', 'bi-cup-hot', true, ['Tea & Coffee', 'Juice & Drinks']],
            ['Snacks & Biscuits', 'bi-cookie', true, ['Biscuits', 'Chips & Crackers']],
            ['Dairy & Breakfast', 'bi-egg-fried', true, ['Milk & Powder', 'Breakfast Cereals']],
            ['Personal Care', 'bi-droplet-half', true, ['Soap & Body Wash', 'Hair Care', 'Oral Care']],
            ['Household Cleaning', 'bi-bucket', false, ['Laundry', 'Dishwash', 'Floor & Surface']],
        ];

        foreach ($tree as $order => [$name, $icon, $featured, $children]) {
            $parent = Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'icon' => $icon, 'is_featured' => $featured, 'sort_order' => $order + 1, 'status' => true]
            );

            foreach ($children as $childOrder => $childName) {
                Category::updateOrCreate(
                    ['slug' => Str::slug($childName)],
                    ['name' => $childName, 'parent_id' => $parent->id, 'sort_order' => $childOrder + 1, 'status' => true]
                );
            }
        }
    }
}
