<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class HireRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'requested_by',
        'site_id',
        'project_id',
        'title',
        'description',
        'position_type',
        'employment_type',
        'quantity',
        'urgency',
        'required_skills',
        'required_qualifications',
        'required_certifications',
        'min_experience_years',
        'offered_rate',
        'rate_type',
        'benefits',
        'start_date',
        'end_date',
        'deadline',
        'status',
        'approved_by',
        'approved_at',
        'assigned_to',
        'rejection_reason',
        'notes',
        'applications_count',
        'interviews_count',
        'hired_count',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'deadline' => 'date',
            'approved_at' => 'datetime',
            'offered_rate' => 'decimal:2',
            'quantity' => 'integer',
            'min_experience_years' => 'integer',
            'applications_count' => 'integer',
            'interviews_count' => 'integer',
            'hired_count' => 'integer',
        ];
    }

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING_APPROVAL = 'pending_approval';
    const STATUS_APPROVED = 'approved';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_FILLED = 'filled';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_EXPIRED = 'expired';

    // Position types
    const POSITION_OPERATIVE = 'operative';
    const POSITION_SUPERVISOR = 'supervisor';
    const POSITION_FOREMAN = 'foreman';
    const POSITION_MANAGER = 'manager';
    const POSITION_SPECIALIST = 'specialist';

    // Employment types
    const EMPLOYMENT_FULL_TIME = 'full_time';
    const EMPLOYMENT_PART_TIME = 'part_time';
    const EMPLOYMENT_CONTRACT = 'contract';
    const EMPLOYMENT_TEMPORARY = 'temporary';

    // Urgency levels
    const URGENCY_LOW = 'low';
    const URGENCY_NORMAL = 'normal';
    const URGENCY_HIGH = 'high';
    const URGENCY_URGENT = 'urgent';

    // Rate types
    const RATE_HOURLY = 'hourly';
    const RATE_DAILY = 'daily';
    const RATE_WEEKLY = 'weekly';
    const RATE_MONTHLY = 'monthly';

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Scopes
    public function scopeForCompany($query, $companyId = null)
    {
        $companyId = $companyId ?: (auth()->user()?->company_id ?? 1);
        return $query->where('company_id', $companyId);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            self::STATUS_PENDING_APPROVAL,
            self::STATUS_APPROVED,
            self::STATUS_IN_PROGRESS
        ]);
    }

    public function scopeUrgent($query)
    {
        return $query->where('urgency', self::URGENCY_URGENT);
    }

    public function scopeOverdue($query)
    {
        return $query->where('deadline', '<', now())
                    ->whereIn('status', [
                        self::STATUS_PENDING_APPROVAL,
                        self::STATUS_APPROVED,
                        self::STATUS_IN_PROGRESS
                    ]);
    }

    // Helper methods
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'secondary',
            self::STATUS_PENDING_APPROVAL => 'warning',
            self::STATUS_APPROVED => 'info',
            self::STATUS_IN_PROGRESS => 'primary',
            self::STATUS_FILLED => 'success',
            self::STATUS_CANCELLED => 'danger',
            self::STATUS_EXPIRED => 'dark',
            default => 'secondary'
        };
    }

    public function getStatusDisplayAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PENDING_APPROVAL => 'Pending Approval',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_FILLED => 'Filled',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_EXPIRED => 'Expired',
            default => ucfirst(str_replace('_', ' ', $this->status))
        };
    }

    public function getUrgencyColorAttribute(): string
    {
        return match($this->urgency) {
            self::URGENCY_LOW => 'success',
            self::URGENCY_NORMAL => 'info',
            self::URGENCY_HIGH => 'warning',
            self::URGENCY_URGENT => 'danger',
            default => 'secondary'
        };
    }

    public function getPositionTypeDisplayAttribute(): string
    {
        return match($this->position_type) {
            self::POSITION_OPERATIVE => 'Operative',
            self::POSITION_SUPERVISOR => 'Supervisor',
            self::POSITION_FOREMAN => 'Foreman',
            self::POSITION_MANAGER => 'Manager',
            self::POSITION_SPECIALIST => 'Specialist',
            default => ucfirst(str_replace('_', ' ', $this->position_type))
        };
    }

    public function isOverdue(): bool
    {
        return $this->deadline && 
               $this->deadline->isPast() && 
               in_array($this->status, [
                   self::STATUS_PENDING_APPROVAL,
                   self::STATUS_APPROVED,
                   self::STATUS_IN_PROGRESS
               ]);
    }

    public function canBeApproved(): bool
    {
        return $this->status === self::STATUS_PENDING_APPROVAL;
    }

    public function canBeEdited(): bool
    {
        return in_array($this->status, [
            self::STATUS_DRAFT,
            self::STATUS_PENDING_APPROVAL
        ]);
    }

    public function canBeCancelled(): bool
    {
        return !in_array($this->status, [
            self::STATUS_FILLED,
            self::STATUS_CANCELLED,
            self::STATUS_EXPIRED
        ]);
    }

    // Static methods for dropdowns
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PENDING_APPROVAL => 'Pending Approval',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_FILLED => 'Filled',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_EXPIRED => 'Expired',
        ];
    }

    public static function getPositionTypeOptions(): array
    {
        return [
            self::POSITION_OPERATIVE => 'Operative',
            self::POSITION_SUPERVISOR => 'Supervisor',
            self::POSITION_FOREMAN => 'Foreman',
            self::POSITION_MANAGER => 'Manager',
            self::POSITION_SPECIALIST => 'Specialist',
        ];
    }

    public static function getEmploymentTypeOptions(): array
    {
        return [
            self::EMPLOYMENT_FULL_TIME => 'Full Time',
            self::EMPLOYMENT_PART_TIME => 'Part Time',
            self::EMPLOYMENT_CONTRACT => 'Contract',
            self::EMPLOYMENT_TEMPORARY => 'Temporary',
        ];
    }

    public static function getUrgencyOptions(): array
    {
        return [
            self::URGENCY_LOW => 'Low',
            self::URGENCY_NORMAL => 'Normal',
            self::URGENCY_HIGH => 'High',
            self::URGENCY_URGENT => 'Urgent',
        ];
    }

    public static function getRateTypeOptions(): array
    {
        return [
            self::RATE_HOURLY => 'Hourly',
            self::RATE_DAILY => 'Daily',
            self::RATE_WEEKLY => 'Weekly',
            self::RATE_MONTHLY => 'Monthly',
        ];
    }
}
