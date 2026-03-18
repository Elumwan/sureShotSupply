<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock_quantity',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'primary_image',
        'full_images',
    ];

    protected static function booted(): void
    {
        static::creating(function (Product $product): void {
            if (! $product->slug) {
                $product->slug = static::generateUniqueSlug($product->name);
            }
        });
    }

    public function getPriceFormattedAttribute(): float
    {
        return $this->price / 100;
    }

    public function getPrimaryImageAttribute(): ?string
    {
        return $this->getFirstMediaUrl('images', 'thumb')
            ?: $this->getFirstMediaUrl('images')
            ?: null;
    }

    public function getFullImagesAttribute(): array
    {
        return $this->getMedia('images')
            ->map(fn (Media $media): array => [
                'thumb' => $media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : $media->getUrl(),
                'full' => $media->getUrl(),
            ])
            ->toArray();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->useDisk('public')
            ->acceptsMimeTypes([
                'image/jpeg',
                'image/png',
                'image/webp',
            ]);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        if (! function_exists('imagecreatefromstring')) {
            return;
        }

        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 600, 600)
            ->performOnCollections('images')
            ->nonQueued();
    }

    protected static function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 2;

        while (static::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
