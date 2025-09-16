<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_REVIEW = 'review';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    protected $fillable = [
        'company_id',
        'project_id',
        'task_category_id',
        'assigned_to',
        'created_by',
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'start_date',
        'completed_at',
        'estimated_time',
        'estimated_time_unit',
        'estimated_cost',
        'actual_time',
        'actual_time_unit',
        'actual_cost',
        'delay_days',
        'delay_reason',
        'original_due_date',
        'is_delayed',
        'is_on_hold',
        'on_hold_reason',
        'on_hold_date',
        'on_hold_removed_date',
        'delay_applied_date',
        'delay_removed_date',
        'delay_applied_by',
        'on_hold_applied_by',
        'notes'
    ];

    protected $casts = [
        'due_date' => 'date',
        'start_date' => 'date',
        'completed_at' => 'datetime',
        'estimated_time' => 'decimal:2',
        'estimated_cost' => 'decimal:2',
        'actual_time' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'original_due_date' => 'date',
        'is_delayed' => 'boolean',
        'is_on_hold' => 'boolean',
        'on_hold_date' => 'datetime',
        'on_hold_removed_date' => 'datetime',
        'delay_applied_date' => 'datetime',
        'delay_removed_date' => 'datetime'
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function taskCategory(): BelongsTo
    {
        return $this->belongsTo(TaskCategory::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function delayAppliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delay_applied_by');
    }

    public function onHoldAppliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'on_hold_applied_by');
    }

    // Scopes
    public function scopeForCompany($query, $companyId = null)
    {
        $companyId = $companyId ?? auth()->user()?->company_id ?? null;
        
        if ($companyId === null) {
            // If no company ID is available, don't filter (for superadmin or console contexts)
            return $query;
        }
        
        return $query->where('company_id', $companyId);
    }

    public function scopeForUser($query, $userId = null)
    {
        $userId = $userId ?? auth()->id();
        return $query->where('assigned_to', $userId);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('task_category_id', $categoryId);
    }

    // Accessors
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date && 
               $this->due_date->isPast() && 
               $this->status !== self::STATUS_COMPLETED;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'secondary',
            self::STATUS_IN_PROGRESS => 'primary',
            self::STATUS_REVIEW => 'warning',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_CANCELLED => 'danger',
            default => 'secondary'
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'low' => 'success',
            'medium' => 'info',
            'high' => 'warning',
            'urgent' => 'danger',
            default => 'secondary'
        };
    }

    public function getIsCurrentlyDelayedAttribute(): bool
    {
        return $this->is_delayed && !$this->delay_removed_date;
    }

    public function getIsCurrentlyOnHoldAttribute(): bool
    {
        return $this->is_on_hold && !$this->on_hold_removed_date;
    }

    public function getIsDelayedOrOnHoldAttribute(): bool
    {
        return $this->is_currently_delayed || $this->is_currently_on_hold;
    }

    public function getDelayHoldStatusColorAttribute(): string
    {
        if ($this->is_currently_on_hold) {
            return 'danger'; // Red for on hold
        }
        if ($this->is_currently_delayed) {
            return 'warning'; // Orange for delayed
        }
        return $this->status_color;
    }

    /**
     * Get all valid status values
     */
    public static function getValidStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_IN_PROGRESS,
            self::STATUS_REVIEW,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
        ];
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_REVIEW => 'Review',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
            default => ucfirst(str_replace('_', ' ', $this->status))
        };
    }

    // Helper methods
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
            'progress' => 100
        ]);
    }

    public function getSite()
    {
        return $this->project?->site;
    }

    public function getClient()
    {
        return $this->project?->getEffectiveClient();
    }

    // Time formatting helpers
    public function getFormattedEstimatedTimeAttribute(): string
    {
        if (!$this->estimated_time) {
            return 'Not set';
        }
        
        $unit = $this->estimated_time_unit === 'days' ? 'day' : 'hour';
        $plural = $this->estimated_time > 1 ? $unit . 's' : $unit;
        
        return number_format($this->estimated_time, ($this->estimated_time == floor($this->estimated_time)) ? 0 : 1) . ' ' . $plural;
    }

    public function getFormattedActualTimeAttribute(): string
    {
        if (!$this->actual_time) {
            return 'Not recorded';
        }
        
        $unit = $this->actual_time_unit === 'days' ? 'day' : 'hour';
        $plural = $this->actual_time > 1 ? $unit . 's' : $unit;
        
        return number_format($this->actual_time, ($this->actual_time == floor($this->actual_time)) ? 0 : 1) . ' ' . $plural;
    }

    // Convert time to hours for calculations
    public function getEstimatedTimeInHoursAttribute(): float
    {
        if (!$this->estimated_time) {
            return 0;
        }
        
        return $this->estimated_time_unit === 'days' 
            ? $this->estimated_time * 8 // Assume 8 hours per day
            : $this->estimated_time;
    }

    public function getActualTimeInHoursAttribute(): float
    {
        if (!$this->actual_time) {
            return 0;
        }
        
        return $this->actual_time_unit === 'days' 
            ? $this->actual_time * 8 // Assume 8 hours per day
            : $this->actual_time;
    }
}