<?php

use App\Models\CameraListing;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return inertia('Home', [
        'featuredCamera' => CameraListing::featured()->available()->first(),
        'cameras' => CameraListing::available()->limit(3)->get(),
        'products' => Product::where('is_active', true)->get(),
    ]);
});

Route::get('/cameras', function () {
    return inertia('Cameras', [
        'featuredCamera' => CameraListing::featured()->available()->first(),
        'cameras' => CameraListing::available()->paginate(12),
    ]);
});

Route::get('/cameras/{slug}', function (string $slug) {
    $camera = CameraListing::where('slug', $slug)
        ->where('status', 'available')
        ->firstOrFail();

    return inertia('CameraDetail', [
        'camera' => $camera,
    ]);
});

Route::get('/shop', function () {
    return inertia('Shop', [
        'products' => Product::where('is_active', true)->get(),
    ]);
});

Route::get('/shop/{slug}', function (string $slug) {
    $product = Product::where('slug', $slug)
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
