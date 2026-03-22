<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\WebhookController;
use App\Models\CameraListing;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/webhook/stripe', [WebhookController::class, 'handle']);

Route::get('/', function () {
    return inertia('Home', [
        'featuredCamera' => CameraListing::with('media')->featured()->available()->first(),
        'cameras' => CameraListing::with('media')->available()->limit(3)->get(),
        'products' => Product::with('media')->where('is_active', true)->get(),
    ]);
});

Route::get('/cameras', function () {
    return inertia('Cameras', [
        'featuredCamera' => CameraListing::with('media')->featured()->available()->first(),
        'cameras' => CameraListing::with('media')->available()->paginate(12),
    ]);
});

Route::get('/cameras/{slug}', function (string $slug) {
    $camera = CameraListing::with('media')
        ->where('slug', $slug)
        ->where('status', 'available')
        ->firstOrFail();

    return inertia('CameraDetail', [
        'camera' => $camera,
    ]);
});

Route::get('/shop', function () {
    return inertia('Shop', [
        'products' => Product::with('media')->where('is_active', true)->get(),
    ]);
});

Route::get('/shop/{slug}', function (string $slug) {
    $product = Product::with('media')
        ->where('slug', $slug)
        ->where('is_active', true)
        ->firstOrFail();

    return inertia('ProductDetail', [
        'product' => $product,
    ]);
});

Route::get('/contact', function () {
    return inertia('Contact');
});

Route::post('/contact', function (Request $request) {
    $request->validate([
        'name' => 'required|string|max:100',
        'email' => 'required|email',
        'message' => 'required|string|max:2000',
    ]);

    return back()->with('success', 'Thanks for your message. We will be in touch.');
});

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');

Route::post('/checkout/session', [CheckoutController::class, 'createSession'])->name('checkout.session');
Route::get('/checkout/session-status', [CheckoutController::class, 'sessionStatus'])->name('checkout.session-status');
Route::post('/checkout/shipping-rate', [CheckoutController::class, 'calculateShippingRate'])->name('checkout.shipping-rate');
Route::get('/checkout/return', fn () => inertia('CheckoutReturn'))->name('checkout.return');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
