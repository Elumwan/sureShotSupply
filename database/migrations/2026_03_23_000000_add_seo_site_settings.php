<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $timestamp = now();

        DB::table('site_settings')->insertOrIgnore([
            [
                'key' => 'seo_site_name',
                'value' => 'SureShotSupply',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'key' => 'seo_default_title',
                'value' => 'SureShotSupply — Cameras & Accessories',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'key' => 'seo_default_description',
                'value' => 'Hand-picked second-hand cameras and premium accessories. Shop SureShotSupply.',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'key' => 'seo_og_image',
                'value' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'key' => 'seo_twitter_handle',
                'value' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('site_settings')->whereIn('key', [
            'seo_site_name',
            'seo_default_title',
            'seo_default_description',
            'seo_og_image',
            'seo_twitter_handle',
        ])->delete();
    }
};
