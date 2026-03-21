<?php

use App\Mail\NewOrderAlert;
use App\Mail\OrderConfirmation;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Mail;

it('sends order confirmation to the customer when the webhook fires', function () {
    Mail::fake();

    config()->set('services.stripe.webhook_secret', 'whsec_test_secret');
    config()->set('mail.owner_email', 'owner@example.com');

    $product = Product::factory()->create([
        'is_active' => true,
        'stock_quantity' => 5,
        'price' => 1800,
    ]);

    $order = Order::factory()->create([
        'stripe_session_id' => 'cs_test_mail_customer',
        'customer_email' => '',
        'status' => 'pending',
        'total' => 1800,
    ]);

    $payload = [
        'type' => 'checkout.session.completed',
        'data' => [
            'object' => [
                'id' => 'cs_test_mail_customer',
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
            ],
        ],
    ];

    postOrderMailWebhookPayload($this, $payload)->assertOk();

    $order->refresh();

    Mail::assertSent(OrderConfirmation::class, function (OrderConfirmation $mail) use ($order): bool {
        return $mail->hasTo($order->customer_email) && $mail->order->is($order);
    });
});

it('sends a new order alert to the owner when the webhook fires', function () {
    Mail::fake();

    config()->set('services.stripe.webhook_secret', 'whsec_test_secret');
    config()->set('mail.owner_email', 'owner@example.com');

    $product = Product::factory()->create([
        'is_active' => true,
        'stock_quantity' => 5,
        'price' => 1800,
    ]);

    $order = Order::factory()->create([
        'stripe_session_id' => 'cs_test_mail_owner',
        'customer_email' => '',
        'status' => 'pending',
        'total' => 1800,
    ]);

    $payload = [
        'type' => 'checkout.session.completed',
        'data' => [
            'object' => [
                'id' => 'cs_test_mail_owner',
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
            ],
        ],
    ];

    postOrderMailWebhookPayload($this, $payload)->assertOk();

    Mail::assertSent(NewOrderAlert::class, function (NewOrderAlert $mail) use ($order): bool {
        return $mail->hasTo(config('mail.owner_email')) && $mail->order->is($order->fresh());
    });
});

function postOrderMailWebhookPayload($testCase, array $payload)
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
