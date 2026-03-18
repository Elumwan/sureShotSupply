<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $productNames = [
            'Hot Shoe Cover',
            'Braided Wrist Strap',
            'Leather Neck Strap',
            'Lens Cleaning Kit',
            'Cold Shoe Thumb Grip',
            'Soft Shutter Release Button',
            'Peak Camera Wrist Cuff',
        ];

        $name = fake()->randomElement($productNames);

        return [
            'name' => $name,
            'slug' => null,
            'description' => fake()->sentence(12),
            'price' => fake()->randomElement([800, 1200, 1800, 2400, 3200, 4500]),
            'stock_quantity' => fake()->numberBetween(5, 150),
            'image_path' => null,
            'is_active' => true,
        ];
    }
}
