<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('site_settings')
            ->whereIn('key', [
                'shipping_uk',
                'shipping_us_canada',
                'shipping_europe',
                'shipping_asia_pacific',
            ])
            ->delete();

        $timestamp = Carbon::now();

        DB::table('site_settings')->upsert(
            [
                [
                    'key' => 'shipping_australia',
                    'value' => '1000',
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ],
                [
                    'key' => 'shipping_new_zealand',
                    'value' => '1500',
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ],
                [
                    'key' => 'shipping_uk_us_canada',
                    'value' => '2500',
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ],
                [
                    'key' => 'shipping_europe_asia_pacific',
                    'value' => '3000',
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ],
                [
                    'key' => 'shipping_rest_of_world',
                    'value' => '3500',
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ],
            ],
            ['key'],
            ['value', 'updated_at']
        );
    }

    public function down(): void
    {
        DB::table('site_settings')
            ->whereIn('key', [
                'shipping_uk_us_canada',
                'shipping_europe_asia_pacific',
            ])
            ->delete();

        $timestamp = Carbon::now();

        DB::table('site_settings')->upsert(
            [
                [
                    'key' => 'shipping_uk',
                    'value' => '2500',
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ],
                [
                    'key' => 'shipping_us_canada',
                    'value' => '2500',
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ],
                [
                    'key' => 'shipping_europe',
                    'value' => '3000',
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ],
                [
                    'key' => 'shipping_asia_pacific',
                    'value' => '3000',
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ],
            ],
            ['key'],
            ['value', 'updated_at']
        );
    }
};
