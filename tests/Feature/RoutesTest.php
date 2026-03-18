<?php

use App\Models\CameraListing;
use App\Models\Product;

it('loads the inertia home page', function () {
    CameraListing::factory()->create([
        'name' => 'Canon AE-1 Program',
        'status' => 'available',
        'is_featured' => true,
    ]);

    Product::factory()->create([
        'name' => 'Wrist Strap',
        'is_active' => true,
    ]);

    $response = $this->get('/');

    $response->assertOk()
        ->assertSee('SureShotSupply')
        ->assertSee('Canon AE-1 Program')
        ->assertSee('Wrist Strap');
});

it('loads the filament admin login page', function () {
    $this->get('/admin/login')->assertOk();
});
