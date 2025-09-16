<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'category',
        'icon',
        'price_monthly',
        'price_yearly',
        'is_active',
        'is_core',
        'dependencies',
        'settings_schema',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_core' => 'boolean',
        'dependencies' => 'array',
        'settings_schema' => 'array',
        'price_monthly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
    ];

    // Categories
    const CATEGORY_CORE = 'core';
    const CATEGORY_FINANCIAL = 'financial';
    const CATEGORY_PROJECT_MANAGEMENT = 'project_management';
    const CATEGORY_FIELD_OPERATIONS = 'field_operations';
    const CATEGORY_COMMUNICATION = 'communication';
    const CATEGORY_REPORTING = 'reporting';
    const CATEGORY_INTEGRATION = 'integration';
    const CATEGORY_AI = 'ai';

    /**
     * Get companies that have this module enabled
     */
    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_modules')
                    ->withPivot(['is_enabled', 'settings', 'enabled_at', 'expires_at', 'monthly_price', 'yearly_price'])
                    ->withTimestamps();
    }

    /**
     * Get enabled companies for this module
     */
    public function enabledCompanies()
    {
        return $this->companies()->wherePivot('is_enabled', true);
    }

    /**
     * Check if module is available (active and dependencies met)
     */
    public function isAvailable(): bool
    {
        return $this->is_active;
    }

    /**
     * Get category display name
     */
    public function getCategoryDisplayAttribute(): string
    {
        return match($this->category) {
            self::CATEGORY_CORE => 'Core Features',
            self::CATEGORY_FINANCIAL => 'Financial Management',
            self::CATEGORY_PROJECT_MANAGEMENT => 'Project Management',
            self::CATEGORY_FIELD_OPERATIONS => 'Field Operations',
            self::CATEGORY_COMMUNICATION => 'Communication',
            self::CATEGORY_REPORTING => 'Reporting & Analytics',
            self::CATEGORY_INTEGRATION => 'Integrations',
            self::CATEGORY_AI => 'AI Features',
            default => ucfirst(str_replace('_', ' ', $this->category)),
        };
    }

    /**
     * Get yearly savings percentage
     */
    public function getYearlySavingsAttribute(): int
    {
        if ($this->price_monthly <= 0 || $this->price_yearly <= 0) {
            return 0;
        }
        
        $monthlyTotal = $this->price_monthly * 12;
        return round((($monthlyTotal - $this->price_yearly) / $monthlyTotal) * 100);
    }

    /**
     * Scope for active modules
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for core modules
     */
    public function scopeCore($query)
    {
        return $query->where('is_core', true);
    }

    /**
     * Scope for non-core modules
     */
    public function scopeAddon($query)
    {
        return $query->where('is_core', false);
    }

    /**
     * Scope by category
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }
} 