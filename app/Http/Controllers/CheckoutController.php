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
            'country' => ['required', 'string'],
        ]);

        $rate = $shippingService->getRateForCountry($data['country']);
        $label = $shippingService->getLabelForCountry($data['country']);

        try {
            $checkoutService->updateShippingRate($data['session_id'], $rate, $label);
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
