<?php

namespace App\Http\Controllers;

use App\Models\CameraListing;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function index(CartService $cartService): Response
    {
        return Inertia::render('Cart', [
            'items' => $cartService->items(),
            'total' => $cartService->total(),
        ]);
    }

    public function add(Request $request, CartService $cartService): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:product,camera'],
            'id' => ['required', 'integer'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $this->findPurchasable($data['type'], (int) $data['id']);

        $cartService->add($data['type'], (int) $data['id'], (int) ($data['quantity'] ?? 1));

        return back()->with('success', 'Added to bag.');
    }

    public function remove(Request $request, CartService $cartService): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:product,camera'],
            'id' => ['required', 'integer'],
        ]);

        $cartService->remove($data['type'], (int) $data['id']);

        return back()->with('success', 'Removed from bag.');
    }

    public function update(Request $request, CartService $cartService): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:product,camera'],
            'id' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        $cartService->update($data['type'], (int) $data['id'], (int) $data['quantity']);

        return back()->with('success', 'Bag updated.');
    }

    protected function findPurchasable(string $type, int $id): Product | CameraListing
    {
        return match ($type) {
            'product' => Product::query()
                ->whereKey($id)
                ->where('is_active', true)
                ->firstOrFail(),
            'camera' => CameraListing::query()
                ->whereKey($id)
                ->where('status', 'available')
                ->firstOrFail(),
        };
    }
}
