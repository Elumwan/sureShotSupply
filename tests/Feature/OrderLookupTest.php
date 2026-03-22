<?php

use App\Models\Order;
use Illuminate\Testing\TestResponse;
use Inertia\Testing\AssertableInertia as Assert;

it('returns 200 for the order lookup page', function () {
    $this->get('/order-lookup')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('OrderLookup'));
});

it('returns order data for a valid reference and email', function () {
    $order = Order::factory()->create([
        'customer_email' => 'customer@example.com',
        'status' => 'paid',
        'total' => 5500,
        'shipping_address' => [
            'line1' => '1 Street',
            'line2' => 'Unit 2',
            'city' => 'Brisbane',
            'state' => 'QLD',
            'postal_code' => '4000',
            'country' => 'AU',
        ],
        'shipping_rate_label' => 'Australia',
        'shipping_country' => 'AU',
        'created_at' => now()->setDate(2026, 3, 23),
    ]);

    $order->items()->create([
        'itemable_type' => App\Models\Product::class,
        'itemable_id' => 1,
        'name' => 'Hot Shoe Cover',
        'price' => 1500,
        'quantity' => 2,
    ]);

    $this->post('/order-lookup', [
        'reference' => $order->reference,
        'email' => 'customer@example.com',
    ])->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('OrderLookup')
            ->where('order.reference', $order->reference)
            ->where('order.status', 'paid')
            ->where('order.created_at', '23 March 2026')
            ->where('order.total', 5500)
            ->where('order.shipping_rate_label', 'Australia')
            ->where('order.shipping_country', 'AU')
            ->where('order.items.0.name', 'Hot Shoe Cover')
            ->where('order.items.0.quantity', 2)
            ->where('order.items.0.price', 1500)
            ->where('order.items.0.subtotal', 3000)
            ->where('order.shipping_address.city', 'Brisbane'));
});

it('returns an error for a valid reference with the wrong email', function () {
    $order = Order::factory()->create([
        'customer_email' => 'customer@example.com',
        'status' => 'paid',
    ]);

    postOrderLookup($this, $order->reference, 'wrong@example.com')
        ->assertRedirect()
        ->assertSessionHas('error', 'No order found matching those details.');
});

it('returns an error for a non existent reference', function () {
    postOrderLookup($this, 'SSS-999999', 'missing@example.com')
        ->assertRedirect()
        ->assertSessionHas('error', 'No order found matching those details.');
});

it('returns a processing error for a pending order', function () {
    $order = Order::factory()->create([
        'customer_email' => 'customer@example.com',
        'status' => 'pending',
    ]);

    postOrderLookup($this, $order->reference, 'customer@example.com')
        ->assertRedirect()
        ->assertSessionHas('error', 'Your order is still being processed. Please check back shortly.');
});

it('matches customer email case insensitively', function () {
    $order = Order::factory()->create([
        'customer_email' => 'customer@example.com',
        'status' => 'paid',
    ]);

    $this->post('/order-lookup', [
        'reference' => strtolower($order->reference),
        'email' => 'CUSTOMER@EXAMPLE.COM',
    ])->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('OrderLookup')
            ->where('order.reference', $order->reference));
});

function postOrderLookup($testCase, string $reference, string $email): TestResponse
{
    return $testCase->from('/order-lookup')->post('/order-lookup', [
        'reference' => $reference,
        'email' => $email,
    ]);
}
