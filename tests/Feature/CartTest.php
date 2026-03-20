<?php

use App\Models\CameraListing;
use App\Models\Product;
use App\Services\CartService;

it('items can be added to the cart', function () {
    $product = Product::factory()->create([
        'is_active' => true,
        'stock_quantity' => 10,
        'price' => 1800,
    ]);

    $this->post('/cart/add', [
        'type' => 'product',
        'id' => $product->id,
        'quantity' => 2,
    ])->assertRedirect();

    $items = app(CartService::class)->items();

    expect($items)->toHaveCount(1)
        ->and($items[0]['quantity'])->toBe(2);
});

it('camera listings can only have quantity 1', function () {
    $camera = CameraListing::factory()->create([
        'status' => 'available',
        'price' => 32000,
    ]);

    app(CartService::class)->add('camera', $camera->id, 3);

    expect(app(CartService::class)->items()[0]['quantity'])->toBe(1);
});

it('products respect stock quantity limits', function () {
    $product = Product::factory()->create([
        'is_active' => true,
        'stock_quantity' => 3,
    ]);

    app(CartService::class)->add('product', $product->id, 8);

    expect(app(CartService::class)->items()[0]['quantity'])->toBe(3);
});

it('items can be removed from the cart', function () {
    $product = Product::factory()->create([
        'is_active' => true,
        'stock_quantity' => 5,
    ]);

    app(CartService::class)->add('product', $product->id, 1);

    $this->post('/cart/remove', [
        'type' => 'product',
        'id' => $product->id,
    ])->assertRedirect();

    expect(app(CartService::class)->items())->toBe([]);
});

it('cart total calculates correctly in cents', function () {
    $product = Product::factory()->create([
        'is_active' => true,
        'stock_quantity' => 5,
        'price' => 1800,
    ]);

    $camera = CameraListing::factory()->create([
        'status' => 'available',
        'price' => 32000,
    ]);

    app(CartService::class)->add('product', $product->id, 2);
    app(CartService::class)->add('camera', $camera->id, 1);

    expect(app(CartService::class)->total())->toBe(35600);
});

it('sold camera listings are removed from cart automatically', function () {
    $camera = CameraListing::factory()->create([
        'status' => 'available',
    ]);

    app(CartService::class)->add('camera', $camera->id, 1);

    $camera->update(['status' => 'sold']);

    expect(app(CartService::class)->items())->toBe([])
        ->and(app(CartService::class)->count())->toBe(0);
});

it('cart count is correct after adding multiple items', function () {
    $product = Product::factory()->create([
        'is_active' => true,
        'stock_quantity' => 10,
    ]);

    $camera = CameraListing::factory()->create([
        'status' => 'available',
    ]);

    app(CartService::class)->add('product', $product->id, 3);
    app(CartService::class)->add('camera', $camera->id, 1);

    expect(app(CartService::class)->count())->toBe(4);
});
