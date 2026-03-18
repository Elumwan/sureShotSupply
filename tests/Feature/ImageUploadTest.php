<?php

use App\Models\CameraListing;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('a product can have media attached', function () {
    Storage::fake('public');

    $product = Product::factory()->create();

    $product
        ->addMedia(UploadedFile::fake()->image('test.jpg', 400, 400))
        ->toMediaCollection('images');

    expect($product->getMedia('images'))->toHaveCount(1);
});

it('a camera listing can have media attached', function () {
    Storage::fake('public');

    $camera = CameraListing::factory()->create();

    $camera
        ->addMedia(UploadedFile::fake()->image('test.jpg', 400, 400))
        ->toMediaCollection('images');

    expect($camera->getMedia('images'))->toHaveCount(1);
});

it('primary image attribute returns null when no media exists', function () {
    $product = Product::factory()->create();
    $camera = CameraListing::factory()->create();

    expect($product->primary_image)->toBeNull()
        ->and($camera->primary_image)->toBeNull();
});

it('primary image attribute returns a url string when media exists', function () {
    Storage::fake('public');

    $product = Product::factory()->create();
    $camera = CameraListing::factory()->create();

    $product
        ->addMedia(UploadedFile::fake()->image('test.jpg', 400, 400))
        ->toMediaCollection('images');

    $camera
        ->addMedia(UploadedFile::fake()->image('test.jpg', 400, 400))
        ->toMediaCollection('images');

    expect($product->fresh()->primary_image)->toBeString()
        ->and($camera->fresh()->primary_image)->toBeString();
});

it('full images attribute returns an empty array when no media exists', function () {
    $product = Product::factory()->create();
    $camera = CameraListing::factory()->create();

    expect($product->full_images)->toBe([])
        ->and($camera->full_images)->toBe([]);
});

it('full images attribute returns an array of thumb and full urls when media exists', function () {
    Storage::fake('public');

    $product = Product::factory()->create();
    $camera = CameraListing::factory()->create();

    $product
        ->addMedia(UploadedFile::fake()->image('test.jpg', 400, 400))
        ->toMediaCollection('images');

    $camera
        ->addMedia(UploadedFile::fake()->image('test.jpg', 400, 400))
        ->toMediaCollection('images');

    expect($product->fresh()->full_images[0])->toMatchArray([
        'thumb' => $product->fresh()->full_images[0]['thumb'],
        'full' => $product->fresh()->full_images[0]['full'],
    ])->and($camera->fresh()->full_images[0])->toMatchArray([
        'thumb' => $camera->fresh()->full_images[0]['thumb'],
        'full' => $camera->fresh()->full_images[0]['full'],
    ]);
});
