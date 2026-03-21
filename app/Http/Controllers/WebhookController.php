<?php

namespace App\Http\Controllers;

use App\Mail\NewOrderAlert;
use App\Mail\OrderConfirmation;
use App\Models\CameraListing;
use App\Models\Order;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use UnexpectedValueException;

class WebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                (string) $request->header('Stripe-Signature'),
                (string) config('services.stripe.webhook_secret'),
            );
        } catch (UnexpectedValueException | SignatureVerificationException) {
            return response()->json(['message' => 'Invalid webhook signature.'], 400);
        }

        match ($event->type) {
            'checkout.session.completed' => $this->handleCheckoutCompleted($event->data->object),
            'checkout.session.expired' => $this->handleCheckoutExpired($event->data->object),
            default => null,
        };

        return response()->json(['received' => true]);
    }

    protected function handleCheckoutCompleted(object $session): void
    {
        $order = Order::query()
            ->where('stripe_session_id', $session->id)
            ->first();

        if (! $order) {
            return;
        }

        $shipping = $session->shipping_details->address ?? null;
        $cartItems = json_decode($session->metadata->cart ?? '[]', true);

        $order->update([
            'status' => 'paid',
            'stripe_payment_intent' => $session->payment_intent ?? null,
            'customer_name' => $session->customer_details->name ?? '',
            'customer_email' => $session->customer_details->email ?? '',
            'shipping_address' => $shipping ? [
                'line1' => $shipping->line1 ?? null,
                'line2' => $shipping->line2 ?? null,
                'city' => $shipping->city ?? null,
                'state' => $shipping->state ?? null,
                'postal_code' => $shipping->postal_code ?? null,
                'country' => $shipping->country ?? null,
            ] : null,
        ]);

        if ($order->items()->doesntExist()) {
            foreach ($cartItems as $item) {
                $itemableType = $item['type'] === 'camera' ? CameraListing::class : Product::class;

                $order->items()->create([
                    'itemable_type' => $itemableType,
                    'itemable_id' => $item['id'],
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                ]);

                if ($item['type'] === 'camera') {
                    CameraListing::query()
                        ->whereKey($item['id'])
                        ->update(['status' => 'sold']);
                } else {
                    $product = Product::query()->find($item['id']);

                    if ($product) {
                        $product->decrement('stock_quantity', min($product->stock_quantity, (int) $item['quantity']));
                    }
                }
            }
        }

        $order->load('items');

        if (filled($order->customer_email)) {
            Mail::to($order->customer_email)->send(new OrderConfirmation($order));
        }

        if (filled(config('mail.owner_email'))) {
            Mail::to(config('mail.owner_email'))->send(new NewOrderAlert($order));
        }

        try {
            Session::forget(CartService::SESSION_KEY);
        } catch (\Throwable) {
            // Webhook requests may not have an active session store.
        }
    }

    protected function handleCheckoutExpired(object $session): void
    {
        Order::query()
            ->where('stripe_session_id', $session->id)
            ->update(['status' => 'failed']);
    }
}
