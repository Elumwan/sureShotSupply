<?php

use App\Models\CameraListing;

it('a camera listing can be created with valid attributes', function () {
    $listing = CameraListing::create([
        'name' => 'Canon AE-1 Program',
        'make' => 'Canon',
        'model' => 'AE-1 Program',
        'year' => 1981,
        'type' => '35mm SLR',
        'condition' => 'good',
        'description' => 'Fully working camera body.',
        'includes' => '50mm lens',
        'price' => 32000,
        'status' => 'available',
        'is_featured' => true,
    ]);

    expect($listing->exists)->toBeTrue()
        ->and($listing->make)->toBe('Canon');
});

it('a camera listing slug is auto-generated', function () {
    $listing = CameraListing::create([
        'name' => 'Nikon FM2',
        'make' => 'Nikon',
        'model' => 'FM2',
        'type' => '35mm SLR',
        'condition' => 'excellent',
        'price' => 54000,
        'status' => 'available',
        'is_featured' => false,
    ]);

    expect($listing->slug)->toBe('nikon-fm2');
});

it('only listings with status available are returned by scopeAvailable', function () {
    CameraListing::factory()->create(['status' => 'available']);
    CameraListing::factory()->create(['status' => 'draft']);
    CameraListing::factory()->create(['status' => 'sold']);

    expect(CameraListing::available()->count())->toBe(1);
});

it('scopeFeatured returns only listings where is_featured is true', function () {
    CameraListing::factory()->create(['status' => 'available', 'is_featured' => true]);
    CameraListing::factory()->create(['status' => 'available', 'is_featured' => false]);

    expect(CameraListing::featured()->count())->toBe(1);
});

it('price formatted accessor returns correct dollar value', function () {
    $listing = CameraListing::factory()->create(['price' => 32000]);

    expect($listing->price_formatted)->toBe(320.0);
});
