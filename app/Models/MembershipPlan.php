<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'monthly_price',
        'yearly_price',
        'setup_fee',
        'max_users',
        'max_sites',
        'max_projects',
        'max_storage_gb',
        'has_time_tracking',
        'has_invoicing',
        'has_reporting',
        'has_api_access',
        'has_white_label',
        'has_advanced_permissions',
        'has_custom_fields',
        'has_integrations',
        'has_priority_support',
        'is_active',
        'is_featured',
        'is_trial_available',
        'trial_days',
        'sort_order',
        'stripe_price_id_monthly',
        'stripe_price_id_yearly',
        'stripe_product_id',
    ];

    protected $casts = [
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
        'setup_fee' => 'decimal:2',
        'has_time_tracking' => 'boolean',
        'has_invoicing' => 'boolean',
        'has_reporting' => 'boolean',
        'has_api_access' => 'boolean',
        'has_white_label' => 'boolean',
        'has_advanced_permissions' => 'boolean',
        'has_custom_fields' => 'boolean',
        'has_integrations' => 'boolean',
        'has_priority_support' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_trial_available' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function companies()
    {
        return $this->hasMany(Company::class, 'subscription_plan', 'slug');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('monthly_price');
    }

    /**
     * Accessors
     */
    public function getYearlySavingsAttribute()
    {
        if ($this->yearly_price > 0 && $this->monthly_price > 0) {
            $yearlyEquivalent = $this->monthly_price * 12;
            return $yearlyEquivalent - $this->yearly_price;
        }
        return 0;
    }

    public function getYearlySavingsPercentageAttribute()
    {
        if ($this->yearly_savings > 0 && $this->monthly_price > 0) {
            return round(($this->yearly_savings / ($this->monthly_price * 12)) * 100);
        }
        return 0;
    }

    public function getFormattedFeaturesAttribute()
    {
        $features = [];
        
        if ($this->max_users > 0) {
            $features[] = "Up to {$this->max_users} users";
        } else {
            $features[] = "Unlimited users";
        }
        
        if ($this->max_sites > 0) {
            $features[] = "Up to {$this->max_sites} sites";
        } else {
            $features[] = "Unlimited sites";
        }
        
        if ($this->max_projects > 0) {
            $features[] = "Up to {$this->max_projects} projects";
        } else {
            $features[] = "Unlimited projects";
        }
        
        $features[] = "{$this->max_storage_gb}GB storage";
        
        if ($this->has_time_tracking) $features[] = "Time tracking";
        if ($this->has_invoicing) $features[] = "Invoicing & billing";
        if ($this->has_reporting) $features[] = "Advanced reporting";
        if ($this->has_api_access) $features[] = "API access";
        if ($this->has_white_label) $features[] = "White label";
        if ($this->has_advanced_permissions) $features[] = "Advanced permissions";
        if ($this->has_custom_fields) $features[] = "Custom fields";
        if ($this->has_integrations) $features[] = "Third-party integrations";
        if ($this->has_priority_support) $features[] = "Priority support";
        
        return $features;
    }

    /**
     * Methods
     */
    public function canSupport($users, $sites, $projects = null)
    {
        if ($this->max_users > 0 && $users > $this->max_users) {
            return false;
        }
        
        if ($this->max_sites > 0 && $sites > $this->max_sites) {
            return false;
        }
        
        if ($projects && $this->max_projects > 0 && $projects > $this->max_projects) {
            return false;
        }
        
        return true;
    }

    public function getRecommendedForUsage($users, $sites, $projects = null)
    {
        return static::active()
            ->where(function($query) use ($users) {
                $query->where('max_users', 0)
                      ->orWhere('max_users', '>=', $users);
            })
            ->where(function($query) use ($sites) {
                $query->where('max_sites', 0)
                      ->orWhere('max_sites', '>=', $sites);
            })
            ->when($projects, function($query) use ($projects) {
                $query->where(function($q) use ($projects) {
                    $q->where('max_projects', 0)
                      ->orWhere('max_projects', '>=', $projects);
                });
            })
            ->ordered()
            ->first();
    }
}