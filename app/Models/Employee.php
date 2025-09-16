<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'user_id',
        'employee_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'nationality',
        'gender',
        'address',
        'city',
        'state',
        'zip_code',
        'postcode',
        'country',
        'role',
        'department',
        'job_title',
        'hire_date',
        'termination_date',
        'employment_status',
        'employment_type',
        'salary',
        'salary_type',
        'skills',
        'certifications',
        'qualifications',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'notes',
        'avatar',
        'is_active',
        'cis_number',
        'cis_status',
        'day_rate',
        'cis_applicable',
        'cis_rate',
        // Work Documentation
        'national_insurance_number',
        'utr_number',
        'cscs_card_type',
        'cscs_card_number',
        'cscs_card_expiry',
        'right_to_work_uk',
        'passport_id_provided',
        // Bank Details
        'bank_name',
        'account_holder_name',
        'sort_code',
        'account_number',
        // Trade and Qualifications
        'primary_trade',
        'years_experience',
        'other_cards_licenses'
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'hire_date' => 'date',
            'termination_date' => 'date',
            'cscs_card_expiry' => 'date',
            'salary' => 'decimal:2',
            'day_rate' => 'decimal:2',
            'cis_rate' => 'decimal:2',
            'skills' => 'array',
            'certifications' => 'array',
            'qualifications' => 'array',
            'other_cards_licenses' => 'array',
            'is_active' => 'boolean',
            'cis_applicable' => 'boolean',
            'right_to_work_uk' => 'boolean',
            'passport_id_provided' => 'boolean'
        ];
    }

    // Role constants
    const ROLE_SITE_MANAGER = 'site_manager';
    const ROLE_CONTRACT_MANAGER = 'contract_manager';
    const ROLE_QUANTITY_SURVEYOR = 'quantity_surveyor';
    const ROLE_PROJECT_COORDINATOR = 'project_coordinator';
    const ROLE_SAFETY_OFFICER = 'safety_officer';
    const ROLE_QUALITY_INSPECTOR = 'quality_inspector';
    const ROLE_PROCUREMENT_MANAGER = 'procurement_manager';
    const ROLE_CONSTRUCTION_SUPERVISOR = 'construction_supervisor';
    const ROLE_ARCHITECT = 'architect';
    const ROLE_ENGINEER = 'engineer';
    const ROLE_FOREMAN = 'foreman';
    const ROLE_ADMIN_ASSISTANT = 'admin_assistant';

    // Employment status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_TERMINATED = 'terminated';
    const STATUS_ON_LEAVE = 'on_leave';

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sites(): BelongsToMany
    {
        return $this->belongsToMany(Site::class, 'employee_site_allocations')
                    ->withPivot(['allocated_from', 'allocated_until', 'allocation_type', 'responsibilities', 'allocation_percentage', 'status', 'notes'])
                    ->withTimestamps();
    }

    public function activeSites(): BelongsToMany
    {
        return $this->sites()->wherePivot('status', 'active');
    }

    public function siteAllocations(): HasMany
    {
        return $this->hasMany(EmployeeSiteAllocation::class);
    }

    public function activeSiteAllocations(): HasMany
    {
        return $this->siteAllocations()->where('status', 'active');
    }

    public function assignedAssets(): HasMany
    {
        return $this->hasMany(Asset::class, 'assignee_id');
    }

    public function documents()
    {
        // Get documents uploaded by this employee's user account
        return Document::where('uploaded_by', $this->user_id);
    }

    public function expenses()
    {
        // Get expenses submitted by this employee's user account
        return Expense::where('user_id', $this->user_id);
    }

    public function invoices()
    {
        // Get invoices from projects where this employee is assigned
        return Invoice::whereHas('project.users', function($query) {
            $query->where('user_id', $this->user_id);
        });
    }

    public function cisPayments(): HasMany
    {
        return $this->hasMany(CisPayment::class);
    }

    public function cisPaymentsThisYear(): HasMany
    {
        $currentYear = now()->year;
        return $this->cisPayments()->whereYear('payment_date', $currentYear);
    }

    /**
     * Document attachments for this employee
     */
    public function documentAttachments()
    {
        return $this->morphMany(DocumentAttachment::class, 'attachable');
    }

    // Scopes
    public function scopeForCompany($query, $companyId = null)
    {
        $companyId = $companyId ?: auth()->user()->company_id;
        return $query->where('company_id', $companyId);
    }

    public function scopeActive($query)
    {
        return $query->where('employment_status', self::STATUS_ACTIVE)
                    ->where('is_active', true);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getRoleDisplayAttribute(): string
    {
        return match($this->role) {
            self::ROLE_SITE_MANAGER => 'Site Manager',
            self::ROLE_CONTRACT_MANAGER => 'Contract Manager',
            self::ROLE_QUANTITY_SURVEYOR => 'Quantity Surveyor',
            self::ROLE_PROJECT_COORDINATOR => 'Project Coordinator',
            self::ROLE_SAFETY_OFFICER => 'Safety Officer',
            self::ROLE_QUALITY_INSPECTOR => 'Quality Inspector',
            self::ROLE_PROCUREMENT_MANAGER => 'Procurement Manager',
            self::ROLE_CONSTRUCTION_SUPERVISOR => 'Construction Supervisor',
            self::ROLE_ARCHITECT => 'Architect',
            self::ROLE_ENGINEER => 'Engineer',
            self::ROLE_FOREMAN => 'Foreman',
            self::ROLE_ADMIN_ASSISTANT => 'Admin Assistant',
            default => ucfirst(str_replace('_', ' ', $this->role))
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->employment_status) {
            self::STATUS_ACTIVE => 'success',
            self::STATUS_INACTIVE => 'warning',
            self::STATUS_TERMINATED => 'danger',
            self::STATUS_ON_LEAVE => 'info',
            default => 'secondary'
        };
    }

    public function getRoleColorAttribute(): string
    {
        return match($this->role) {
            self::ROLE_SITE_MANAGER => 'primary',
            self::ROLE_CONTRACT_MANAGER => 'success',
            self::ROLE_QUANTITY_SURVEYOR => 'info',
            self::ROLE_PROJECT_COORDINATOR => 'warning',
            self::ROLE_SAFETY_OFFICER => 'danger',
            self::ROLE_QUALITY_INSPECTOR => 'secondary',
            self::ROLE_PROCUREMENT_MANAGER => 'dark',
            self::ROLE_CONSTRUCTION_SUPERVISOR => 'primary',
            self::ROLE_ARCHITECT => 'info',
            self::ROLE_ENGINEER => 'success',
            self::ROLE_FOREMAN => 'warning',
            self::ROLE_ADMIN_ASSISTANT => 'light',
            default => 'secondary'
        };
    }

    public function getFullAddressAttribute(): ?string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->zip_code
        ]);
        
        return !empty($parts) ? implode(', ', $parts) : null;
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name) . '&color=fff&background=4f46e5';
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->employment_status === self::STATUS_ACTIVE && $this->is_active;
    }

    public function isAllocatedToSite(Site $site): bool
    {
        return $this->activeSites()->where('sites.id', $site->id)->exists();
    }

    public function getAllocationForSite(Site $site)
    {
        return $this->activeSiteAllocations()
                   ->where('site_id', $site->id)
                   ->first();
    }

    public static function getRoleOptions(): array
    {
        return [
            self::ROLE_SITE_MANAGER => 'Site Manager',
            self::ROLE_CONTRACT_MANAGER => 'Contract Manager',
            self::ROLE_QUANTITY_SURVEYOR => 'Quantity Surveyor',
            self::ROLE_PROJECT_COORDINATOR => 'Project Coordinator',
            self::ROLE_SAFETY_OFFICER => 'Safety Officer',
            self::ROLE_QUALITY_INSPECTOR => 'Quality Inspector',
            self::ROLE_PROCUREMENT_MANAGER => 'Procurement Manager',
            self::ROLE_CONSTRUCTION_SUPERVISOR => 'Construction Supervisor',
            self::ROLE_ARCHITECT => 'Architect',
            self::ROLE_ENGINEER => 'Engineer',
            self::ROLE_FOREMAN => 'Foreman',
            self::ROLE_ADMIN_ASSISTANT => 'Admin Assistant',
        ];
    }

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_TERMINATED => 'Terminated',
            self::STATUS_ON_LEAVE => 'On Leave',
        ];
    }
}
