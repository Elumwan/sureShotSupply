<?php

use App\Models\CameraListing;
use App\Models\Product;
use App\Models\SiteSetting;

it('home page returns 200', function () {
    SiteSetting::set('hero_title', 'Gear for those who shoot first.');
    CameraListing::factory()->create(['status' => 'available', 'is_featured' => true]);
    Product::factory()->create(['is_active' => true]);

    $this->get('/')->assertOk();
});

it('cameras page returns 200', function () {
    CameraListing::factory()->create(['status' => 'available']);

    $this->get('/cameras')->assertOk();
});

it('camera detail page returns 200 for available camera', function () {
    $camera = CameraListing::factory()->create(['status' => 'available']);

    $this->get("/cameras/{$camera->slug}")->assertOk();
});

it('camera detail page returns 404 for sold or draft camera', function () {
    $soldCamera = CameraListing::factory()->create(['status' => 'sold']);
    $draftCamera = CameraListing::factory()->create(['status' => 'draft']);

    $this->get("/cameras/{$soldCamera->slug}")->assertNotFound();
    $this->get("/cameras/{$draftCamera->slug}")->assertNotFound();
});

it('shop page returns 200', function () {
    Product::factory()->create(['is_active' => true]);

    $this->get('/shop')->assertOk();
});

it('product detail page returns 200 for active product', function () {
    $product = Product::factory()->create(['is_active' => true]);

    $this->get("/shop/{$product->slug}")->assertOk();
});

it('contact page returns 200', function () {
    $this->get('/contact')->assertOk();
});

it('contact form submission with valid data returns redirect', function () {
    $response = $this->post('/contact', [
        'name' => 'Alex Mercer',
        'email' => 'alex@example.com',
        'message' => 'Interested in the featured camera.',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Thanks for your message. We will be in touch.');
});

it('contact form submission with missing fields returns validation errors', function () {
    $response = $this->post('/contact', []);

    $response->assertSessionHasErrors(['name', 'email', 'message']);
});
