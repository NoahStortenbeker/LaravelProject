<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductSize;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $userId = User::query()->value('id') ?? User::factory()->create()->id;

        $sets = [
            ['category' => 'Men Clothing', 'type' => 'apparel', 'values' => ['XS', 'S', 'M', 'L', 'XL', 'XXL']],
            ['category' => 'Women Clothing', 'type' => 'apparel', 'values' => ['XXS', 'XS', 'S', 'M', 'L', 'XL']],
            ['category' => 'Men Pants', 'type' => 'pants', 'values' => array_map('strval', range(28, 40))],
            ['category' => 'Women Jeans', 'type' => 'pants', 'values' => array_map('strval', range(24, 32))],
            ['category' => 'Men Shoes', 'type' => 'shoes', 'values' => array_map('strval', range(39, 47))],
            ['category' => 'Women Shoes', 'type' => 'shoes', 'values' => array_map('strval', range(35, 42))],
            ['category' => 'Kids Apparel', 'type' => 'kids', 'values' => ['2Y', '3Y', '4Y', '5Y', '6Y', '7Y', '8Y', '10Y', '12Y', '14Y']],
            ['category' => 'Watches', 'type' => 'other', 'values' => ['ONE SIZE']],
            ['category' => 'Accessories', 'type' => 'other', 'values' => ['ONE SIZE']],
        ];

        $names = [
            'Essential Tee', 'Classic Hoodie', 'Denim Jeans', 'Cargo Pants', 'Running Sneakers', 'Chelsea Boots',
            'Summer Dress', 'Pleated Skirt', 'Leather Jacket', 'Bomber Jacket', 'Cardigan', 'Sweatpants',
            'Trench Coat', 'Puffer Jacket', 'Linen Shirt', 'Silk Blouse', 'Mini Skirt', 'Midi Dress',
            'Backpack', 'Crossbody Bag', 'Beanie', 'Cap', 'Scarf', 'Sunglasses', 'Wrist Watch', 'Bracelet',
        ];

        for ($i = 0; $i < 50; $i++) {
            $set = $sets[array_rand($sets)];
            $name = $names[array_rand($names)];
            $sku = (string) random_int(100000, 999999);
            $price = number_format(random_int(1500, 30000) / 100, 2, '.', '');
            $isOnline = random_int(1, 100) <= 85;
            $discount = [0, 0, 10, 20, 30][array_rand([0, 0, 10, 20, 30])];
            $seed = $sku.$i;
            $images = [
                "https://picsum.photos/seed/{$seed}-1/800/800",
                "https://picsum.photos/seed/{$seed}-2/800/800",
            ];

            $product = Product::create([
                'category' => $set['category'],
                'name' => $name,
                'sku' => $sku,
                'price' => $price,
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                'images' => $images,
                'is_online' => $isOnline,
                'discount_percent' => $discount,
                'created_by' => $userId,
            ]);

            $values = $set['values'];
            shuffle($values);
            $take = max(3, min(count($values), random_int(3, 6)));
            $selected = array_slice($values, 0, $take);
            foreach ($selected as $label) {
                ProductSize::create([
                    'product_id' => $product->id,
                    'size_label' => (string) $label,
                    'size_type' => $set['type'],
                    'amount' => random_int(1, 15),
                ]);
            }
        }
    }
}
