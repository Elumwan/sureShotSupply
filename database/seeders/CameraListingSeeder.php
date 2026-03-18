<?php

namespace Database\Seeders;

use App\Models\CameraListing;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CameraListingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CameraListing::updateOrCreate(
            ['name' => 'Canon AE-1 Program'],
            [
                'slug' => Str::slug('Canon AE-1 Program'),
                'make' => 'Canon',
                'model' => 'AE-1 Program',
                'year' => 1981,
                'type' => '35mm SLR',
                'condition' => 'good',
                'description' => 'Fully working AE-1 Program with clean meter and smooth advance.',
                'includes' => '50mm f/1.8 lens, original strap',
                'price' => 32000,
                'status' => 'available',
                'is_featured' => true,
            ]
        );
    }
}
