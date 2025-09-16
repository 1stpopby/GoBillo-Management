<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'contact_person',
        'email',
        'phone',
        'address',
        'website',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($vendor) {
            if (!$vendor->slug) {
                $vendor->slug = Str::slug($vendor->name);
            }
        });

        static::updating(function ($vendor) {
            if ($vendor->isDirty('name') && !$vendor->isDirty('slug')) {
                $vendor->slug = Str::slug($vendor->name);
            }
        });
    }

    // Relationships
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeWithAssetCount(Builder $query): Builder
    {
        return $query->withCount('assets');
    }

    // Accessors
    public function getAssetsCountAttribute(): int
    {
        return $this->assets()->count();
    }
}