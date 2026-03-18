<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CameraListing extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\CameraListingFactory> */
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'slug',
        'make',
        'model',
        'year',
        'type',
        'condition',
        'description',
        'includes',
        'price',
        'status',
        'is_featured',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'status' => 'string',
    ];

    protected $appends = [
        'primary_image',
        'full_images',
        'featured_image',
    ];

    protected static function booted(): void
    {
        static::creating(function (CameraListing $cameraListing): void {
            if (! $cameraListing->slug) {
                $cameraListing->slug = static::generateUniqueSlug($cameraListing->name);
            }
        });
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', 'available');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
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

    public function getFeaturedImageAttribute(): ?string
    {
        return $this->getFirstMediaUrl('images', 'featured')
            ?: $this->getFirstMediaUrl('images')
            ?: null;
    }

    public function getFullImagesAttribute(): array
    {
        return $this->getMedia('images')
            ->map(fn (Media $media): array => [
                'thumb' => $media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : $media->getUrl(),
                'full' => $media->getUrl(),
                'featured' => $media->hasGeneratedConversion('featured') ? $media->getUrl('featured') : $media->getUrl(),
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
            ->fit(Fit::Crop, 800, 600)
            ->performOnCollections('images')
            ->nonQueued();

        $this->addMediaConversion('featured')
            ->fit(Fit::Crop, 1200, 800)
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
