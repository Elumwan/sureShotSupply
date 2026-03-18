<?php

use App\Models\Product;

it('a product can be created with valid attributes', function () {
    $product = Product::create([
        'name' => 'Camera Strap',
        'description' => 'Reliable woven strap.',
        'price' => 2400,
        'stock_quantity' => 25,
        'image_path' => null,
        'is_active' => true,
    ]);

    expect($product->exists)->toBeTrue()
        ->and($product->name)->toBe('Camera Strap');
});

it('a product slug is auto-generated from its name', function () {
    $product = Product::create([
        'name' => 'Soft Shutter Button',
        'price' => 1200,
        'stock_quantity' => 10,
        'is_active' => true,
    ]);

    expect($product->slug)->toBe('soft-shutter-button');
});

it('price formatted accessor returns correct dollar value', function () {
    $product = Product::factory()->create(['price' => 1800]);

    expect($product->price_formatted)->toBe(18.0);
});

it('a product can be marked inactive', function () {
    $product = Product::factory()->create(['is_active' => false]);

    expect($product->is_active)->toBeFalse();
});
