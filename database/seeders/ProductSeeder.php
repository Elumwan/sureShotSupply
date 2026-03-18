<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Hot shoe cover',
                'description' => 'Protective hot shoe cover for mirrorless and film cameras.',
                'price' => 800,
                'stock_quantity' => 100,
            ],
            [
                'name' => 'Neck strap',
                'description' => 'Comfortable woven neck strap for everyday carry.',
                'price' => 2400,
                'stock_quantity' => 50,
            ],
            [
                'name' => 'Wrist strap',
                'description' => 'Compact wrist strap for lightweight camera setups.',
                'price' => 1800,
                'stock_quantity' => 50,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['name' => $product['name']],
                [
                    ...$product,
                    'slug' => Str::slug($product['name']),
                    'image_path' => null,
                    'is_active' => true,
                ]
            );
        }
    }
}
