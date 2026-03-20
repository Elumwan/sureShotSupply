<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function redirect(Request $request, CartService $cartService, CheckoutService $checkoutService)
    {
        $cartItems = collect($cartService->items())
            ->map(fn (array $item): array => [
                'type' => $item['type'],
                'id' => $item['id'],
                'name' => $item['model']->name,
                'price' => $item['price'],
                'quantity' => $item['quantity'],
            ])
            ->values()
            ->all();

        if ($cartItems === []) {
            return redirect('/cart')->with('error', 'Your bag is empty.');
        }

        $session = $checkoutService->createSession(
            $cartItems,
            route('checkout.success'),
            route('checkout.cancel'),
        );

        Order::query()->create([
            'stripe_session_id' => $session->id,
            'customer_name' => '',
            'customer_email' => '',
            'status' => 'pending',
            'total' => $cartService->total(),
        ]);

        return Inertia::location($session->url);
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
}
