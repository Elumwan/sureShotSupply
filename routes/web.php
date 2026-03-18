<?php

use App\Models\CameraListing;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return inertia('Home', [
        'featuredCamera' => CameraListing::featured()->available()->first(),
        'cameras' => CameraListing::available()->limit(6)->get(),
        'products' => Product::where('is_active', true)->get(),
    ]);
});
