<?php

namespace App\Services;

use Stripe\Checkout\Session;
use Stripe\Stripe;

class CheckoutService
{
    public function __construct(protected ShippingService $shippingService) {}

    public function createSession(array $cartItems, string $returnUrl): Session
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
            'ui_mode' => 'embedded',
            'return_url' => $returnUrl . '?session_id={CHECKOUT_SESSION_ID}',
            'permissions' => [
                'update_shipping_details' => 'server_only',
            ],
            'shipping_address_collection' => [
                'allowed_countries' => $this->shippingService->allowedCountries(),
            ],
            'shipping_options' => [
                [
                    'shipping_rate_data' => [
                        'type' => 'fixed_amount',
                        'fixed_amount' => [
                            'amount' => 0,
                            'currency' => 'aud',
                        ],
                        'display_name' => 'Calculating...',
                    ],
                ],
            ],
            'metadata' => [
                'cart' => json_encode($cartItems, JSON_THROW_ON_ERROR),
            ],
        ]);
    }

    public function retrieveSession(string $sessionId): Session
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        return Session::retrieve($sessionId);
    }

    public function updateShippingRate(string $sessionId, int $rate, string $label): void
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        Session::update($sessionId, [
            'shipping_options' => [
                [
                    'shipping_rate_data' => [
                        'type' => 'fixed_amount',
                        'fixed_amount' => [
                            'amount' => $rate,
                            'currency' => 'aud',
                        ],
                        'display_name' => $label,
                    ],
                ],
            ],
        ]);
    }
}
