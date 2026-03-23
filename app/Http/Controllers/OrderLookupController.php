<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderLookupController extends Controller
{
    public function show(): Response
    {
        return Inertia::render('OrderLookup');
    }

    public function lookup(Request $request): RedirectResponse | Response
    {
        $data = $request->validate([
            'reference' => ['required', 'string'],
            'email' => ['required', 'email'],
        ]);

        $orderId = $this->orderIdFromReference($data['reference']);

        $order = $orderId === null
            ? null
            : Order::query()
                ->with('items')
                ->whereKey($orderId)
                ->whereRaw('LOWER(customer_email) = ?', [strtolower($data['email'])])
                ->first();

        if (! $order) {
            return back()->with('error', 'No order found matching those details.');
        }

        if ($order->status === 'pending') {
            return back()->with('error', 'Your order is still being processed. Please check back shortly.');
        }

        return Inertia::render('OrderLookup', [
            'order' => [
                'reference' => $order->reference,
                'status' => $order->status,
                'created_at' => $order->created_at?->format('j F Y'),
                'total' => $order->total,
                'items' => $order->items->map(fn ($item) => [
                    'name' => $item->name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'subtotal' => $item->price * $item->quantity,
                ])->values()->all(),
                'shipping_address' => $order->shipping_address,
                'shipping_cost' => $order->total - $order->items->sum(fn ($item) => $item->price * $item->quantity),
                'shipping_rate_label' => $order->shipping_rate_label,
                'shipping_country' => $order->shipping_country,
            ],
        ]);
    }

    protected function orderIdFromReference(string $reference): ?int
    {
        if (! preg_match('/^SSS-(\d{6})$/', strtoupper(trim($reference)), $matches)) {
            return null;
        }

        $orderId = (int) ltrim($matches[1], '0');

        return $orderId > 0 ? $orderId : null;
    }
}
