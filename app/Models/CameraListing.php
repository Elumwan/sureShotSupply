<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class CameraListing extends Model
{
    /** @use HasFactory<\Database\Factories\CameraListingFactory> */
    use HasFactory;

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
