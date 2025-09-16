<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Carbon\Carbon;

class CompanyModule extends Pivot
{
    protected $table = 'company_modules';

    protected $fillable = [
        'company_id',
        'module_id',
        'is_enabled',
        'settings',
        'enabled_at',
        'expires_at',
        'monthly_price',
        'yearly_price',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'settings' => 'array',
        'enabled_at' => 'datetime',
        'expires_at' => 'datetime',
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
    ];

    /**
     * Get the company
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the module
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Check if module access is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if module is currently active
     */
    public function isActive(): bool
    {
        return $this->is_enabled && !$this->isExpired();
    }

    /**
     * Get days until expiration
     */
    public function getDaysUntilExpirationAttribute(): ?int
    {
        if (!$this->expires_at) {
            return null;
        }

        return max(0, now()->diffInDays($this->expires_at, false));
    }

    /**
     * Enable the module
     */
    public function enable(?Carbon $expiresAt = null): void
    {
        $this->update([
            'is_enabled' => true,
            'enabled_at' => now(),
            'expires_at' => $expiresAt,
        ]);
    }

    /**
     * Disable the module
     */
    public function disable(): void
    {
        $this->update([
            'is_enabled' => false,
            'enabled_at' => null,
            'expires_at' => null,
        ]);
    }

    /**
     * Update module settings
     */
    public function updateSettings(array $settings): void
    {
        $currentSettings = $this->settings ?? [];
        $this->update([
            'settings' => array_merge($currentSettings, $settings)
        ]);
    }
} 