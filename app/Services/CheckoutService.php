<?php

namespace App\Services;

use Stripe\Checkout\Session;
use Stripe\Stripe;

class CheckoutService
{
    public function __construct(protected ShippingService $shippingService) {}

    public function createSession(array $cartItems, string $successUrl, string $cancelUrl): Session
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $lineItems = collect($cartItems)
            ->map(fn (array $item): array => [
                'price_data' => [
                    'currency' => 'aud',
                    'unit_amount' => $item['price'],
                    'product_data' => [
                        'name' => $item['name'],
                    ],
                ],
                'quantity' => $item['quantity'],
            ])
            ->values()
            ->all();

        return Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $successUrl.'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $cancelUrl,
            'shipping_address_collection' => [
                'allowed_countries' => $this->shippingService->allowedCountries(),
            ],
            'shipping_options' => $this->shippingService->shippingOptions(),
            'metadata' => [
                'cart' => json_encode($cartItems, JSON_THROW_ON_ERROR),
            ],
        ]);
    }
}
