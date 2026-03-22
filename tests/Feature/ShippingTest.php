<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Services\ShippingService;
use Illuminate\Support\Facades\Mail;

it('returns the australia rate from site settings for au', function () {
    SiteSetting::set('shipping_australia', '1234');

    expect(app(ShippingService::class)->getRateForCountry('AU'))->toBe(1234)
        ->and(app(ShippingService::class)->getLabelForCountry('AU'))->toBe('Australia');
});

it('returns the uk, us and canada rate for gb, us and ca', function () {
    SiteSetting::set('shipping_uk_us_canada', '2555');

    expect(app(ShippingService::class)->getRateForCountry('GB'))->toBe(2555)
        ->and(app(ShippingService::class)->getRateForCountry('US'))->toBe(2555)
        ->and(app(ShippingService::class)->getRateForCountry('CA'))->toBe(2555)
        ->and(app(ShippingService::class)->getLabelForCountry('US'))->toBe('UK, US & Canada');
});

it('returns the europe and asia pacific rate for fr and jp', function () {
    SiteSetting::set('shipping_europe_asia_pacific', '3111');

    expect(app(ShippingService::class)->getRateForCountry('FR'))->toBe(3111)
        ->and(app(ShippingService::class)->getRateForCountry('JP'))->toBe(3111)
        ->and(app(ShippingService::class)->getLabelForCountry('FR'))->toBe('Europe & Asia Pacific');
});

it('returns the rest of world rate for unmapped countries', function () {
    SiteSetting::set('shipping_rest_of_world', '3999');

    expect(app(ShippingService::class)->getRateForCountry('ZW'))->toBe(3999)
        ->and(app(ShippingService::class)->getLabelForCountry('ZW'))->toBe('Rest of World');
});

it('uses specific australia and new zealand matches before europe and asia pacific', function () {
    expect(app(ShippingService::class)->getLabelForCountry('AU'))->toBe('Australia')
        ->and(app(ShippingService::class)->getLabelForCountry('NZ'))->toBe('New Zealand');
});

it('returns exactly five stripe shipping options with the consolidated labels', function () {
    $shippingOptions = app(ShippingService::class)->shippingOptions();

    expect($shippingOptions)->toHaveCount(5)
        ->and(collect($shippingOptions)->pluck('shipping_rate_data.display_name')->all())->toBe([
            'Australia',
            'New Zealand',
            'UK, US & Canada',
            'Europe & Asia Pacific',
            'Rest of World',
        ]);
});

it('stores shipping country and rate label on a completed checkout webhook', function () {
    Mail::fake();

    config()->set('services.stripe.webhook_secret', 'whsec_test_secret');

    $product = Product::factory()->create([
        'is_active' => true,
        'stock_quantity' => 5,
        'price' => 1800,
    ]);

    $order = Order::factory()->create([
        'stripe_session_id' => 'cs_test_shipping',
        'status' => 'pending',
        'total' => 1800,
    ]);

    $payload = [
        'type' => 'checkout.session.completed',
        'data' => [
            'object' => [
                'id' => 'cs_test_shipping',
                'payment_intent' => 'pi_test_shipping',
                'amount_total' => 2800,
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

    postShippingWebhookPayload($this, $payload)->assertOk();

    $order->refresh();

    expect($order->shipping_country)->toBe('AU')
        ->and($order->shipping_rate_label)->toBe('Australia')
        ->and($order->total)->toBe(2800);
});

function postShippingWebhookPayload($testCase, array $payload)
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
