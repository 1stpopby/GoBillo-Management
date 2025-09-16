<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($location) {
            if (!$location->slug) {
                $location->slug = Str::slug($location->name);
            }
        });

        static::updating(function ($location) {
            if ($location->isDirty('name') && !$location->isDirty('slug')) {
                $location->slug = Str::slug($location->name);
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
    public function getFullAddressAttribute(): ?string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->zip_code,
            $this->country
        ]);
        
        return !empty($parts) ? implode(', ', $parts) : null;
    }

    public function getAssetsCountAttribute(): int
    {
        return $this->assets()->count();
    }
}