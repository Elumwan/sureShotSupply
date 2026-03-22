<?php

namespace App\Services;

use App\Models\SiteSetting;

class ShippingService
{
    protected const REGIONS = [
        'Australia' => ['AU'],
        'New Zealand' => ['NZ'],
        'United Kingdom' => ['GB'],
        'United States & Canada' => ['US', 'CA'],
        'Europe' => [
            'AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE',
            'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL', 'PL', 'PT',
            'RO', 'SK', 'SI', 'ES', 'SE', 'NO', 'CH', 'IS',
        ],
        'Asia Pacific' => [
            'JP', 'CN', 'HK', 'SG', 'MY', 'TH', 'IN', 'KR', 'TW', 'PH', 'ID',
            'VN', 'NZ', 'AU', 'PK', 'BD', 'LK', 'NP',
        ],
    ];

    protected const SETTINGS = [
        'Australia' => 'shipping_australia',
        'New Zealand' => 'shipping_new_zealand',
        'United Kingdom' => 'shipping_uk',
        'United States & Canada' => 'shipping_us_canada',
        'Europe' => 'shipping_europe',
        'Asia Pacific' => 'shipping_asia_pacific',
        'Rest of World' => 'shipping_rest_of_world',
    ];

    protected const DEFAULTS = [
        'shipping_australia' => 1000,
        'shipping_new_zealand' => 1500,
        'shipping_uk' => 2500,
        'shipping_us_canada' => 2500,
        'shipping_europe' => 3000,
        'shipping_asia_pacific' => 3000,
        'shipping_rest_of_world' => 3500,
    ];

    public function getRateForCountry(string $countryCode): int
    {
        $label = $this->getLabelForCountry($countryCode);
        $settingKey = self::SETTINGS[$label];

        return (int) SiteSetting::get($settingKey, self::DEFAULTS[$settingKey]);
    }

    public function getLabelForCountry(string $countryCode): string
    {
        $countryCode = strtoupper(trim($countryCode));

        foreach (self::REGIONS as $label => $codes) {
            if (in_array($countryCode, $codes, true)) {
                return $label;
            }
        }

        return 'Rest of World';
    }

    public function allowedCountries(): array
    {
        return [
            'AU', 'NZ', 'GB', 'US', 'CA', 'AT', 'BE', 'BG', 'HR', 'CY', 'CZ',
            'DK', 'EE', 'FI', 'FR', 'DE', 'GR', 'HU', 'IE', 'IT', 'LV', 'LT',
            'LU', 'MT', 'NL', 'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE', 'NO',
            'CH', 'IS', 'JP', 'CN', 'HK', 'SG', 'MY', 'TH', 'IN', 'KR', 'TW',
            'PH', 'ID', 'VN', 'PK', 'BD', 'LK', 'NP', 'AE', 'BR', 'MX', 'ZA',
            'CL', 'AR', 'IL', 'TR',
        ];
    }

    public function shippingOptions(): array
    {
        return collect(self::SETTINGS)
            ->map(fn (string $settingKey, string $label): array => [
                'shipping_rate_data' => [
                    'type' => 'fixed_amount',
                    'fixed_amount' => [
                        'amount' => (int) SiteSetting::get($settingKey, self::DEFAULTS[$settingKey]),
                        'currency' => 'aud',
                    ],
                    'display_name' => $label,
                ],
            ])
            ->values()
            ->all();
    }
}
