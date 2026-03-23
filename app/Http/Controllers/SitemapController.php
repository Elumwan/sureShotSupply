<?php

namespace App\Http\Controllers;

use App\Models\CameraListing;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $today = now()->toDateString();

        $urls = [
            ['loc' => url('/'), 'lastmod' => $today],
            ['loc' => url('/cameras'), 'lastmod' => $today],
            ['loc' => url('/shop'), 'lastmod' => $today],
            ['loc' => url('/contact'), 'lastmod' => $today],
            ...CameraListing::query()
                ->where('status', 'available')
                ->get(['slug', 'updated_at'])
                ->map(fn (CameraListing $camera): array => [
                    'loc' => url("/cameras/{$camera->slug}"),
                    'lastmod' => optional($camera->updated_at)->toDateString() ?? $today,
                ])
                ->all(),
            ...Product::query()
                ->where('is_active', true)
                ->where('stock_quantity', '>', 0)
                ->get(['slug', 'updated_at'])
                ->map(fn (Product $product): array => [
                    'loc' => url("/shop/{$product->slug}"),
                    'lastmod' => optional($product->updated_at)->toDateString() ?? $today,
                ])
                ->all(),
        ];

        $xml = view('sitemap', ['urls' => $urls])->render();

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
}
