<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'company_number',
        'vat_number',
        'utr_number',
        'business_type',
        'incorporation_date',
        'email',
        'primary_contact_name',
        'primary_contact_title',
        'primary_contact_email',
        'primary_contact_phone',
        'secondary_contact_name',
        'secondary_contact_title',
        'secondary_contact_email',
        'secondary_contact_phone',
        'phone',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'registered_address',
        'registered_city',
        'registered_state',
        'registered_zip_code',
        'registered_country',
        'website',
        'logo',
        'description',
        'business_description',
        'services_offered',
        'trading_name',
        'industry_sector',
        'bank_name',
        'bank_account_name',
        'bank_account_number',
        'bank_sort_code',
        'iban',
        'swift_code',
        'public_liability_insurer',
        'public_liability_policy_number',
        'public_liability_expiry',
        'public_liability_amount',
        'employers_liability_insurer',
        'employers_liability_policy_number',
        'employers_liability_expiry',
        'employers_liability_amount',
        'health_safety_policy',
        'health_safety_policy_date',
        'risk_assessment_policy',
        'risk_assessment_policy_date',
        'construction_line_number',
        'construction_line_expiry',
        'chas_number',
        'chas_expiry',
        'safe_contractor_number',
        'safe_contractor_expiry',
        'status',
        'subscription_plan',
        'trial_ends_at',
        'subscription_ends_at',
        'max_users',
        'max_projects',
        'settings',
        'notification_preferences',
        'timezone',
        'currency',
        'is_vat_registered',
        'is_cis_registered',
        'gdpr_compliant',
        'gdpr_compliance_date',
    ];

    protected function casts(): array
    {
        return [
            'incorporation_date' => 'date',
            'trial_ends_at' => 'datetime',
            'subscription_ends_at' => 'datetime',
            'public_liability_expiry' => 'date',
            'employers_liability_expiry' => 'date',
            'health_safety_policy_date' => 'date',
            'risk_assessment_policy_date' => 'date',
            'construction_line_expiry' => 'date',
            'chas_expiry' => 'date',
            'safe_contractor_expiry' => 'date',
            'gdpr_compliance_date' => 'date',
            'public_liability_amount' => 'decimal:2',
            'employers_liability_amount' => 'decimal:2',
            'settings' => 'array',
            'services_offered' => 'array',
            'notification_preferences' => 'array',
            'is_vat_registered' => 'boolean',
            'is_cis_registered' => 'boolean',
            'gdpr_compliant' => 'boolean',
        ];
    }

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_SUSPENDED = 'suspended';

    // Subscription plan constants
    const PLAN_TRIAL = 'trial';
    const PLAN_BASIC = 'basic';
    const PLAN_PROFESSIONAL = 'professional';
    const PLAN_ENTERPRISE = 'enterprise';

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
                    ->whereIn('status', ['active', 'trial'])
                    ->latest();
    }

    public function emailSettings()
    {
        return $this->hasMany(EmailSetting::class);
    }

    public function activeEmailSetting()
    {
        return $this->hasOne(EmailSetting::class)->where('is_active', true);
    }

    /**
     * Get all modules available to this company
     */
    public function modules()
    {
        return $this->belongsToMany(Module::class, 'company_modules')
                    ->withPivot(['is_enabled', 'settings', 'enabled_at', 'expires_at', 'monthly_price', 'yearly_price'])
                    ->withTimestamps();
    }

    /**
     * Get enabled modules for this company
     */
    public function enabledModules()
    {
        return $this->modules()->wherePivot('is_enabled', true)
                    ->where(function($query) {
                        $query->wherePivot('expires_at', '>', now())
                              ->orWhereNull('company_modules.expires_at');
                    });
    }

    /**
     * Check if company has access to a specific module
     */
    public function hasModule(string $moduleName): bool
    {
        return $this->enabledModules()->where('name', $moduleName)->exists();
    }

    /**
     * Enable a module for this company
     */
    public function enableModule(Module $module, ?\Carbon\Carbon $expiresAt = null, array $settings = []): void
    {
        $this->modules()->syncWithoutDetaching([
            $module->id => [
                'is_enabled' => true,
                'enabled_at' => now(),
                'expires_at' => $expiresAt,
                'settings' => $settings,
                'monthly_price' => $module->price_monthly,
                'yearly_price' => $module->price_yearly,
            ]
        ]);
    }

    /**
     * Disable a module for this company
     */
    public function disableModule(Module $module): void
    {
        $this->modules()->updateExistingPivot($module->id, [
            'is_enabled' => false,
            'enabled_at' => null,
            'expires_at' => null,
        ]);
    }

    // Accessor methods
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isOnTrial()
    {
        return $this->subscription_plan === self::PLAN_TRIAL && 
               $this->trial_ends_at && 
               $this->trial_ends_at->isFuture();
    }

    public function isSubscriptionExpired()
    {
        return $this->subscription_ends_at && $this->subscription_ends_at->isPast();
    }

    public function canAddUsers()
    {
        return $this->users()->count() < $this->max_users;
    }

    public function canAddProjects()
    {
        return $this->projects()->count() < $this->max_projects;
    }

    public function getActiveUsersCountAttribute()
    {
        return $this->users()->where('is_active', true)->count();
    }

    public function getActiveProjectsCountAttribute()
    {
        return $this->projects()->whereIn('status', ['planning', 'in_progress'])->count();
    }

    public function getCompletedProjectsCountAttribute()
    {
        return $this->projects()->where('status', 'completed')->count();
    }

    /**
     * Get the currency symbol for this company
     */
    public function getCurrencySymbol(): string
    {
        return match($this->currency) {
            'GBP' => '£',
            'EUR' => '€',
            'USD' => '$',
            'CAD' => '$',
            'AUD' => '$',
            default => '£', // Default to GBP for UK construction companies
        };
    }

    /**
     * Format an amount with the company's currency symbol
     */
    public function formatCurrency(float $amount, int $decimals = 0): string
    {
        return $this->getCurrencySymbol() . number_format($amount, $decimals);
    }

    // Boot method to auto-generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($company) {
            if (empty($company->slug)) {
                $company->slug = Str::slug($company->name);
                
                // Ensure slug is unique
                $originalSlug = $company->slug;
                $counter = 1;
                while (static::where('slug', $company->slug)->exists()) {
                    $company->slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }
        });
    }
}
