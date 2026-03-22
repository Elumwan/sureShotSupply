<?php

use App\Models\CameraListing;
use App\Models\Order;
use App\Models\Product;
use App\Services\CartService;
use App\Services\CheckoutService;
use Stripe\Checkout\Session as StripeCheckoutSession;

afterEach(function () {
    \Mockery::close();
});

it('checkout redirects to stripe when cart has items', function () {
    $product = Product::factory()->create([
        'is_active' => true,
        'stock_quantity' => 5,
        'price' => 1800,
    ]);

    app(CartService::class)->add('product', $product->id, 1);

    $service = \Mockery::mock(CheckoutService::class);
    $service->shouldReceive('createSession')
        ->once()
        ->andReturn(StripeCheckoutSession::constructFrom([
            'id' => 'cs_test_embedded',
            'client_secret' => 'cs_test_client_secret',
        ]));

    $this->app->instance(CheckoutService::class, $service);

    $this->postJson('/checkout/session')
        ->assertOk()
        ->assertJson([
            'clientSecret' => 'cs_test_client_secret',
            'sessionId' => 'cs_test_embedded',
        ]);
});

it('checkout redirects back with error when cart is empty', function () {
    $this->postJson('/checkout/session')
        ->assertStatus(422)
        ->assertJson([
            'message' => 'Your bag is empty.',
        ]);
});

it('a pending order is created when checkout session is initiated and its status can be queried', function () {
    $product = Product::factory()->create([
        'is_active' => true,
        'stock_quantity' => 5,
        'price' => 1800,
    ]);

    app(CartService::class)->add('product', $product->id, 2);

    $service = \Mockery::mock(CheckoutService::class);
    $service->shouldReceive('createSession')
        ->once()
        ->andReturn(StripeCheckoutSession::constructFrom([
            'id' => 'cs_test_pending',
            'client_secret' => 'cs_test_pending_secret',
        ]));
    $service->shouldReceive('retrieveSession')
        ->once()
        ->with('cs_test_pending')
        ->andReturn(StripeCheckoutSession::constructFrom([
            'id' => 'cs_test_pending',
            'status' => 'open',
            'customer_details' => [
                'email' => 'alex@example.com',
            ],
        ]));

    $this->app->instance(CheckoutService::class, $service);

    $this->postJson('/checkout/session')->assertOk();

    expect(Order::query()->where('stripe_session_id', 'cs_test_pending')->exists())->toBeTrue();

    $this->getJson('/checkout/session-status?session_id=cs_test_pending')
        ->assertOk()
        ->assertJson([
            'status' => 'open',
            'customer_email' => 'alex@example.com',
        ]);
});

it('calculates and updates the dynamic shipping rate for a country', function () {
    $service = \Mockery::mock(CheckoutService::class);
    $service->shouldReceive('updateShippingRate')
        ->once()
        ->with('cs_test_shipping_rate', 2500, 'UK, US & Canada');

    $this->app->instance(CheckoutService::class, $service);

    $this->postJson('/checkout/shipping-rate', [
        'session_id' => 'cs_test_shipping_rate',
        'country' => 'GB',
    ])->assertOk()
        ->assertJson([
            'success' => true,
            'rate' => 2500,
            'label' => 'UK, US & Canada',
        ]);
});

it('returns 422 when dynamic shipping update fails', function () {
    $service = \Mockery::mock(CheckoutService::class);
    $service->shouldReceive('updateShippingRate')
        ->once()
        ->with('cs_test_shipping_rate', 1000, 'Australia')
        ->andThrow(new Exception('Stripe update failed'));

    $this->app->instance(CheckoutService::class, $service);

    $this->postJson('/checkout/shipping-rate', [
        'session_id' => 'cs_test_shipping_rate',
        'country' => 'AU',
    ])->assertStatus(422)
        ->assertJson([
            'success' => false,
        ]);
});

it('webhook checkout session completed marks order as paid', function () {
    config()->set('services.stripe.webhook_secret', 'whsec_test_secret');

    $product = Product::factory()->create([
        'is_active' => true,
        'stock_quantity' => 5,
        'price' => 1800,
    ]);

    $order = Order::factory()->create([
        'stripe_session_id' => 'cs_test_paid',
        'customer_name' => '',
        'customer_email' => '',
        'status' => 'pending',
        'total' => 1800,
    ]);

    $payload = [
        'type' => 'checkout.session.completed',
        'data' => [
            'object' => [
                'id' => 'cs_test_paid',
                'payment_intent' => 'pi_test_123',
                'metadata' => [
                    'cart' => json_encode([
                        [
                            'type' => 'product',
                            'id' => $product->id,
                            'name' => $product->name,
                            'price' => 1800,
                            'quantity' => 1,
                        ],
                    ]),
                ],
                'customer_details' => [
                    'name' => 'Alex Mercer',
                    'email' => 'alex@example.com',
                ],
                'shipping_details' => [
                    'address' => [
                        'line1' => '1 Street',
                        'line2' => null,
                        'city' => 'Brisbane',
                        'state' => 'QLD',
                        'postal_code' => '4000',
                        'country' => 'AU',
                    ],
                ],
            ],
        ],
    ];

    postWebhookPayload($this, $payload)->assertOk();

    $order->refresh();

    expect($order->status)->toBe('paid')
        ->and($order->stripe_payment_intent)->toBe('pi_test_123')
        ->and($order->items)->toHaveCount(1);
});

it('webhook checkout session completed marks camera listing as sold', function () {
    config()->set('services.stripe.webhook_secret', 'whsec_test_secret');

    $camera = CameraListing::factory()->create([
        'status' => 'available',
        'price' => 32000,
    ]);

    Order::factory()->create([
        'stripe_session_id' => 'cs_test_camera',
        'status' => 'pending',
        'total' => 32000,
    ]);

    $payload = [
        'type' => 'checkout.session.completed',
        'data' => [
            'object' => [
                'id' => 'cs_test_camera',
                'metadata' => [
                    'cart' => json_encode([
                        [
                            'type' => 'camera',
                            'id' => $camera->id,
                            'name' => $camera->name,
                            'price' => 32000,
                            'quantity' => 1,
                        ],
                    ]),
                ],
            ],
        ],
    ];

    postWebhookPayload($this, $payload)->assertOk();

    expect($camera->fresh()->status)->toBe('sold');
});

it('webhook checkout session completed decrements product stock', function () {
    config()->set('services.stripe.webhook_secret', 'whsec_test_secret');

    $product = Product::factory()->create([
        'is_active' => true,
        'stock_quantity' => 6,
        'price' => 1800,
    ]);

    Order::factory()->create([
        'stripe_session_id' => 'cs_test_stock',
        'status' => 'pending',
        'total' => 3600,
    ]);

    $payload = [
        'type' => 'checkout.session.completed',
        'data' => [
            'object' => [
                'id' => 'cs_test_stock',
                'metadata' => [
                    'cart' => json_encode([
                        [
                            'type' => 'product',
                            'id' => $product->id,
                            'name' => $product->name,
                            'price' => 1800,
                            'quantity' => 2,
                        ],
                    ]),
                ],
            ],
        ],
    ];

    postWebhookPayload($this, $payload)->assertOk();

    expect($product->fresh()->stock_quantity)->toBe(4);
});

it('webhook with invalid signature returns 400', function () {
    config()->set('services.stripe.webhook_secret', 'whsec_test_secret');

    $this->postJson('/webhook/stripe', ['type' => 'checkout.session.completed'], [
        'Stripe-Signature' => 'invalid',
    ])->assertStatus(400);
});

it('webhook checkout session expired marks order as failed', function () {
    config()->set('services.stripe.webhook_secret', 'whsec_test_secret');

    $order = Order::factory()->create([
        'stripe_session_id' => 'cs_test_expired',
        'status' => 'pending',
    ]);

    $payload = [
        'type' => 'checkout.session.expired',
        'data' => [
            'object' => [
                'id' => 'cs_test_expired',
            ],
        ],
    ];

    postWebhookPayload($this, $payload)->assertOk();

    expect($order->fresh()->status)->toBe('failed');
});

function postWebhookPayload($testCase, array $payload)
{
    $json = json_encode($payload, JSON_THROW_ON_ERROR);
    $timestamp = time();
    $signature = hash_hmac('sha256', "{$timestamp}.{$json}", (string) config('services.stripe.webhook_secret'));

    return $testCase->call(
        'POST',
        '/webhook/stripe',
        [],
        [],
        [],
        [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_STRIPE_SIGNATURE' => "t={$timestamp},v1={$signature}",
        ],
        $json,
    );
}
