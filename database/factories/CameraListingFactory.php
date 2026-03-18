<?php

namespace Database\Factories;

use App\Models\CameraListing;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CameraListing>
 */
class CameraListingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cameras = [
            ['make' => 'Canon', 'model' => 'AE-1 Program', 'type' => '35mm SLR'],
            ['make' => 'Nikon', 'model' => 'FM2', 'type' => '35mm SLR'],
            ['make' => 'Olympus', 'model' => 'OM-1', 'type' => '35mm SLR'],
            ['make' => 'Pentax', 'model' => '67', 'type' => 'Medium Format'],
            ['make' => 'Fujifilm', 'model' => 'X100V', 'type' => 'Digital'],
        ];

        $camera = fake()->randomElement($cameras);

        return [
            'name' => "{$camera['make']} {$camera['model']}",
            'slug' => null,
            'make' => $camera['make'],
            'model' => $camera['model'],
            'year' => fake()->optional()->numberBetween(1970, 2023),
            'type' => $camera['type'],
            'condition' => fake()->randomElement(['excellent', 'good', 'fair']),
            'description' => fake()->paragraph(),
            'includes' => fake()->randomElement([
                'Body cap, fresh batteries, and original strap',
                '50mm f/1.8 lens, original strap, and lens cap',
                'Camera body only',
                'Lens, strap, and fitted case',
            ]),
            'price' => fake()->numberBetween(18000, 145000),
            'status' => fake()->randomElement(['draft', 'available', 'sold']),
            'is_featured' => false,
        ];
    }
}
