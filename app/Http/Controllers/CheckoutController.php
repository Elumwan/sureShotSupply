<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\ShippingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Stripe\Checkout\Session as StripeCheckoutSession;
use Stripe\Stripe;

class CheckoutController extends Controller
{
    public function createSession(CartService $cartService, CheckoutService $checkoutService): JsonResponse
    {
        $cartItems = $this->cartItems($cartService);

        if ($cartItems === []) {
            return response()->json([
                'message' => 'Your bag is empty.',
            ], 422);
        }

        $session = $checkoutService->createSession(
            $cartItems,
            route('checkout.return'),
        );

        Order::query()->create([
            'stripe_session_id' => $session->id,
            'customer_name' => '',
            'customer_email' => '',
            'status' => 'pending',
            'total' => $cartService->total(),
        ]);

        return response()->json([
            'clientSecret' => $session->client_secret,
            'sessionId' => $session->id,
        ]);
    }

    public function sessionStatus(Request $request, CheckoutService $checkoutService): JsonResponse
    {
        $sessionId = $request->string('session_id')->toString();

        if ($sessionId === '') {
            return response()->json([
                'message' => 'A session_id query parameter is required.',
            ], 422);
        }

        $session = $checkoutService->retrieveSession($sessionId);

        return response()->json([
            'status' => $session->status,
            'customer_email' => data_get($session, 'customer_details.email'),
        ]);
    }

    public function calculateShippingRate(
        Request $request,
        ShippingService $shippingService,
        CheckoutService $checkoutService
    ): JsonResponse
    {
        $data = $request->validate([
            'session_id' => ['required', 'string'],
            'country' => ['nullable', 'string'],
            'shipping_details' => ['nullable', 'array'],
        ]);

        $shippingDetails = $data['shipping_details'] ?? null;
        $country = data_get($shippingDetails, 'address.country') ?? $data['country'] ?? null;

        if (! is_string($country) || trim($country) === '') {
            return response()->json([
                'message' => 'A shipping_details.address.country or country value is required.',
            ], 422);
        }

        $rate = $shippingService->getRateForCountry($country);
        $label = $shippingService->getLabelForCountry($country);

        try {
            if ($shippingDetails === null) {
                $checkoutService->updateShippingRate($data['session_id'], $rate, $label);
            } else {
                Stripe::setApiKey(config('services.stripe.secret'));

                StripeCheckoutSession::update($data['session_id'], [
                    'collected_information' => [
                        'shipping_details' => [
                            'name' => $shippingDetails['name'] ?? '',
                            'address' => [
                                'line1' => $shippingDetails['address']['line1'] ?? '',
                                'line2' => $shippingDetails['address']['line2'] ?? null,
                                'city' => $shippingDetails['address']['city'] ?? '',
                                'state' => $shippingDetails['address']['state'] ?? null,
                                'postal_code' => $shippingDetails['address']['postal_code'] ?? '',
                                'country' => $shippingDetails['address']['country'] ?? '',
                            ],
                        ],
                    ],
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
        } catch (\Throwable) {
            return response()->json([
                'success' => false,
            ], 422);
        }

        return response()->json([
            'success' => true,
            'rate' => $rate,
            'label' => $label,
        ]);
    }

    public function success(Request $request): RedirectResponse | Response
    {
        $sessionId = $request->string('session_id')->toString();

        if ($sessionId === '') {
            return redirect('/');
        }

        $order = Order::query()
            ->with('items')
            ->where('stripe_session_id', $sessionId)
            ->first();

        if (! $order) {
            return redirect('/');
        }

        return Inertia::render('CheckoutSuccess', [
            'order' => $order,
            'processing' => $order->status === 'pending',
        ]);
    }

    public function cancel(): Response
    {
        return Inertia::render('CheckoutCancel');
    }

    protected function cartItems(CartService $cartService): array
    {
        return collect($cartService->items())
            ->map(fn (array $item): array => [
                'type' => $item['type'],
                'id' => $item['id'],
                'name' => $item['model']->name,
                'price' => $item['price'],
                'quantity' => $item['quantity'],
            ])
            ->values()
            ->all();
    }
}
