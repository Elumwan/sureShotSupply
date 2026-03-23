<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use App\Services\CartService;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $heroImage = SiteSetting::get('hero_image');

        return [
            ...parent::share($request),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'siteSettings' => [
                'heroImage' => $heroImage ? asset('storage/'.$heroImage) : null,
                'heroTitle' => SiteSetting::get('hero_title', 'Gear for those who shoot first.'),
                'heroSubtitle' => SiteSetting::get(
                    'hero_subtitle',
                    'Handpicked cameras and accessories for photographers who care about what they carry.'
                ),
            ],
            'seo' => [
                'site_name' => SiteSetting::get('seo_site_name', 'SureShotSupply'),
                'default_title' => SiteSetting::get('seo_default_title', 'SureShotSupply — Cameras & Accessories'),
                'default_description' => SiteSetting::get(
                    'seo_default_description',
                    'Hand-picked second-hand cameras and premium accessories. Shop SureShotSupply.'
                ),
                'og_image' => SiteSetting::get('seo_og_image'),
                'twitter_handle' => SiteSetting::get('seo_twitter_handle'),
            ],
            'cartCount' => app(CartService::class)->count(),
            'cartItems' => fn () => collect(app(CartService::class)->items())
                ->map(fn (array $item): array => [
                    'type' => $item['type'],
                    'id' => $item['id'],
                ])
                ->values()
                ->all(),
        ];
    }
}
