<?php

use App\Models\SiteSetting;
use App\Services\ShippingService;

it('returns the australia rate from site settings for au', function () {
    SiteSetting::set('shipping_australia', '1234');

    expect(app(ShippingService::class)->getRateForCountry('AU'))->toBe(1234);
});

it('returns the new zealand rate from site settings for nz', function () {
    SiteSetting::set('shipping_new_zealand', '1567');

    expect(app(ShippingService::class)->getRateForCountry('NZ'))->toBe(1567);
});

it('returns the uk us and canada rate from site settings for gb us and ca', function () {
    SiteSetting::set('shipping_uk_us_canada', '2555');

    expect(app(ShippingService::class)->getRateForCountry('GB'))->toBe(2555)
        ->and(app(ShippingService::class)->getRateForCountry('US'))->toBe(2555)
        ->and(app(ShippingService::class)->getRateForCountry('CA'))->toBe(2555);
});

it('returns the europe and asia pacific rate from site settings for fr and jp', function () {
    SiteSetting::set('shipping_europe_asia_pacific', '3111');

    expect(app(ShippingService::class)->getRateForCountry('FR'))->toBe(3111)
        ->and(app(ShippingService::class)->getRateForCountry('JP'))->toBe(3111);
});

it('returns the rest of world rate from site settings for zw', function () {
    SiteSetting::set('shipping_rest_of_world', '3999');

    expect(app(ShippingService::class)->getRateForCountry('ZW'))->toBe(3999);
});

it('reads rates live from site settings when values change', function () {
    SiteSetting::set('shipping_australia', '1000');

    expect(app(ShippingService::class)->getRateForCountry('AU'))->toBe(1000);

    SiteSetting::set('shipping_australia', '1888');

    expect(app(ShippingService::class)->getRateForCountry('AU'))->toBe(1888);
});

it('returns australia for au', function () {
    expect(app(ShippingService::class)->getLabelForCountry('AU'))->toBe('Australia');
});

it('returns new zealand for nz', function () {
    expect(app(ShippingService::class)->getLabelForCountry('NZ'))->toBe('New Zealand');
});

it('returns uk us and canada for gb us and ca', function () {
    expect(app(ShippingService::class)->getLabelForCountry('GB'))->toBe('UK, US & Canada')
        ->and(app(ShippingService::class)->getLabelForCountry('US'))->toBe('UK, US & Canada')
        ->and(app(ShippingService::class)->getLabelForCountry('CA'))->toBe('UK, US & Canada');
});

it('returns europe and asia pacific for fr and jp', function () {
    expect(app(ShippingService::class)->getLabelForCountry('FR'))->toBe('Europe & Asia Pacific')
        ->and(app(ShippingService::class)->getLabelForCountry('JP'))->toBe('Europe & Asia Pacific');
});

it('returns rest of world for unmapped countries', function () {
    expect(app(ShippingService::class)->getLabelForCountry('ZW'))->toBe('Rest of World');
});

it('treats country codes as case insensitive', function () {
    expect(app(ShippingService::class)->getLabelForCountry('au'))->toBe('Australia')
        ->and(app(ShippingService::class)->getRateForCountry('au'))->toBe(1000);
});

it('returns five shipping options with fixed aud amounts and display names from site settings', function () {
    SiteSetting::set('shipping_australia', '1001');
    SiteSetting::set('shipping_new_zealand', '1502');
    SiteSetting::set('shipping_uk_us_canada', '2503');
    SiteSetting::set('shipping_europe_asia_pacific', '3004');
    SiteSetting::set('shipping_rest_of_world', '3505');

    $shippingOptions = app(ShippingService::class)->shippingOptions();

    expect($shippingOptions)->toHaveCount(5);

    foreach ($shippingOptions as $option) {
        expect($option['shipping_rate_data']['type'])->toBe('fixed_amount')
            ->and($option['shipping_rate_data']['fixed_amount']['currency'])->toBe('aud')
            ->and($option['shipping_rate_data']['display_name'])->not->toBeEmpty();
    }

    expect(collect($shippingOptions)->mapWithKeys(
        fn (array $option): array => [
            $option['shipping_rate_data']['display_name'] => $option['shipping_rate_data']['fixed_amount']['amount'],
        ]
    )->all())->toBe([
        'Australia' => 1001,
        'New Zealand' => 1502,
        'UK, US & Canada' => 2503,
        'Europe & Asia Pacific' => 3004,
        'Rest of World' => 3505,
    ]);
});

it('returns allowed countries as an array containing core shipping regions', function () {
    $allowedCountries = app(ShippingService::class)->allowedCountries();

    expect($allowedCountries)->toBeArray()
        ->and($allowedCountries)->toContain('AU', 'NZ', 'GB', 'US', 'CA')
        ->and($allowedCountries)->toContain('FR')
        ->and($allowedCountries)->toContain('JP');
});
