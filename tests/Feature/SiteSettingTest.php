<?php

use App\Models\SiteSetting;

it('site setting set stores a value', function () {
    SiteSetting::set('hero_title', 'Quiet tools for active hands.');

    expect(SiteSetting::query()->where('key', 'hero_title')->value('value'))
        ->toBe('Quiet tools for active hands.');
});

it('site setting get retrieves a stored value', function () {
    SiteSetting::set('hero_subtitle', 'Handpicked cameras and accessories.');

    expect(SiteSetting::get('hero_subtitle'))
        ->toBe('Handpicked cameras and accessories.');
});

it('site setting get returns default when key does not exist', function () {
    expect(SiteSetting::get('missing_key', 'fallback value'))
        ->toBe('fallback value');
});
