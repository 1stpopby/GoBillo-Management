<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeSiteAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'site_id',
        'allocated_from',
        'allocated_until',
        'allocation_type',
        'responsibilities',
        'allocation_percentage',
        'status',
        'notes'
    ];

    protected function casts(): array
    {
        return [
            'allocated_from' => 'date',
            'allocated_until' => 'date',
            'allocation_percentage' => 'decimal:2'
        ];
    }

    // Constants
    const TYPE_PRIMARY = 'primary';
    const TYPE_SECONDARY = 'secondary';
    const TYPE_TEMPORARY = 'temporary';

    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeForSite($query, $siteId)
    {
        return $query->where('site_id', $siteId);
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopePrimary($query)
    {
        return $query->where('allocation_type', self::TYPE_PRIMARY);
    }

    // Accessors
    public function getTypeDisplayAttribute(): string
    {
        return match($this->allocation_type) {
            self::TYPE_PRIMARY => 'Primary',
            self::TYPE_SECONDARY => 'Secondary',
            self::TYPE_TEMPORARY => 'Temporary',
            default => ucfirst($this->allocation_type)
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'success',
            self::STATUS_COMPLETED => 'info',
            self::STATUS_CANCELLED => 'danger',
            default => 'secondary'
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->allocation_type) {
            self::TYPE_PRIMARY => 'primary',
            self::TYPE_SECONDARY => 'warning',
            self::TYPE_TEMPORARY => 'info',
            default => 'secondary'
        };
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isExpired(): bool
    {
        return $this->allocated_until && $this->allocated_until->isPast();
    }

    public static function getTypeOptions(): array
    {
        return [
            self::TYPE_PRIMARY => 'Primary',
            self::TYPE_SECONDARY => 'Secondary',
            self::TYPE_TEMPORARY => 'Temporary',
        ];
    }

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }
}
