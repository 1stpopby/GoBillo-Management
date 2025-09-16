<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'membership_plan_id',
        'status',
        'billing_cycle',
        'starts_at',
        'ends_at',
        'trial_ends_at',
        'cancelled_at',
        'amount',
        'setup_fee',
        'currency',
        'stripe_subscription_id',
        'stripe_customer_id',
        'stripe_price_id',
        'stripe_data',
        'current_users',
        'current_sites',
        'current_projects',
        'current_storage_gb',
        'next_billing_date',
        'last_payment_date',
        'last_payment_amount',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'next_billing_date' => 'datetime',
        'last_payment_date' => 'datetime',
        'amount' => 'decimal:2',
        'setup_fee' => 'decimal:2',
        'last_payment_amount' => 'decimal:2',
        'current_storage_gb' => 'decimal:2',
        'stripe_data' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Relationships
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function membershipPlan()
    {
        return $this->belongsTo(MembershipPlan::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeTrial($query)
    {
        return $query->where('status', 'trial');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeDueForBilling($query)
    {
        return $query->where('next_billing_date', '<=', now())
                    ->whereIn('status', ['active', 'trial']);
    }

    public function scopeExpiringSoon($query, $days = 7)
    {
        return $query->where('ends_at', '<=', now()->addDays($days))
                    ->where('ends_at', '>', now())
                    ->whereIn('status', ['active', 'trial']);
    }

    /**
     * Accessors
     */
    public function getIsActiveAttribute()
    {
        return $this->status === 'active';
    }

    public function getIsTrialAttribute()
    {
        return $this->status === 'trial';
    }

    public function getIsCancelledAttribute()
    {
        return $this->status === 'cancelled';
    }

    public function getIsExpiredAttribute()
    {
        return $this->ends_at && $this->ends_at->isPast();
    }

    public function getTrialDaysRemainingAttribute()
    {
        if (!$this->trial_ends_at || $this->status !== 'trial') {
            return 0;
        }
        
        return max(0, $this->trial_ends_at->diffInDays(now(), false));
    }

    public function getDaysUntilBillingAttribute()
    {
        if (!$this->next_billing_date) {
            return null;
        }
        
        return $this->next_billing_date->diffInDays(now(), false);
    }

    public function getUsagePercentageAttribute()
    {
        $plan = $this->membershipPlan;
        if (!$plan) return [];
        
        return [
            'users' => $plan->max_users > 0 ? round(($this->current_users / $plan->max_users) * 100, 1) : 0,
            'sites' => $plan->max_sites > 0 ? round(($this->current_sites / $plan->max_sites) * 100, 1) : 0,
            'projects' => $plan->max_projects > 0 ? round(($this->current_projects / $plan->max_projects) * 100, 1) : 0,
            'storage' => $plan->max_storage_gb > 0 ? round(($this->current_storage_gb / $plan->max_storage_gb) * 100, 1) : 0,
        ];
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'active' => 'success',
            'trial' => 'info',
            'cancelled' => 'secondary',
            'past_due' => 'warning',
            'suspended' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Methods
     */
    public function updateUsage()
    {
        $company = $this->company;
        
        $this->update([
            'current_users' => $company->users()->count(),
            'current_sites' => $company->sites()->count(),
            'current_projects' => $company->projects()->count(),
            'current_storage_gb' => $this->calculateStorageUsage(),
        ]);
    }

    public function isOverLimit()
    {
        $plan = $this->membershipPlan;
        if (!$plan) return false;
        
        return ($plan->max_users > 0 && $this->current_users > $plan->max_users) ||
               ($plan->max_sites > 0 && $this->current_sites > $plan->max_sites) ||
               ($plan->max_projects > 0 && $this->current_projects > $plan->max_projects) ||
               ($plan->max_storage_gb > 0 && $this->current_storage_gb > $plan->max_storage_gb);
    }

    public function cancel($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'notes' => $reason ? "Cancelled: {$reason}" : null,
        ]);
    }

    public function reactivate()
    {
        $this->update([
            'status' => 'active',
            'cancelled_at' => null,
        ]);
    }

    public function suspend($reason = null)
    {
        $this->update([
            'status' => 'suspended',
            'notes' => $reason ? "Suspended: {$reason}" : null,
        ]);
    }

    private function calculateStorageUsage()
    {
        // Calculate actual storage usage for the company
        $company = $this->company;
        if (!$company) return 0;

        // Calculate storage from various sources
        $storage = 0;

        // Document storage (if documents table has file_size column)
        if (Schema::hasColumn('documents', 'file_size')) {
            $storage += $company->documents()->sum('file_size') / (1024 * 1024 * 1024); // Convert bytes to GB
        }

        // Project files storage (if exists)
        if (Schema::hasTable('project_files') && Schema::hasColumn('project_files', 'file_size')) {
            $storage += DB::table('project_files')
                ->join('projects', 'project_files.project_id', '=', 'projects.id')
                ->where('projects.company_id', $company->id)
                ->sum('project_files.file_size') / (1024 * 1024 * 1024);
        }

        // Fallback: estimate based on company activity
        if ($storage == 0) {
            // Rough estimate: 10MB per user + 50MB per project + 5MB per task
            $estimatedBytes = ($company->users()->count() * 10 * 1024 * 1024) + 
                            ($company->projects()->count() * 50 * 1024 * 1024) + 
                            ($company->tasks()->count() * 5 * 1024 * 1024);
            $storage = $estimatedBytes / (1024 * 1024 * 1024); // Convert to GB
        }

        return round($storage, 2);
    }

    /**
     * Static methods
     */
    public static function createTrial($company, $membershipPlan, $trialDays = null)
    {
        $trialDays = $trialDays ?? $membershipPlan->trial_days;
        
        return static::create([
            'company_id' => $company->id,
            'membership_plan_id' => $membershipPlan->id,
            'status' => 'trial',
            'billing_cycle' => 'monthly',
            'starts_at' => now(),
            'trial_ends_at' => now()->addDays($trialDays),
            'amount' => 0,
            'currency' => 'GBP',
        ]);
    }

    public static function createSubscription($company, $membershipPlan, $billingCycle = 'monthly')
    {
        $amount = $billingCycle === 'yearly' ? $membershipPlan->yearly_price : $membershipPlan->monthly_price;
        $nextBilling = $billingCycle === 'yearly' ? now()->addYear() : now()->addMonth();
        
        return static::create([
            'company_id' => $company->id,
            'membership_plan_id' => $membershipPlan->id,
            'status' => 'active',
            'billing_cycle' => $billingCycle,
            'starts_at' => now(),
            'amount' => $amount,
            'setup_fee' => $membershipPlan->setup_fee,
            'currency' => 'GBP',
            'next_billing_date' => $nextBilling,
        ]);
    }
}