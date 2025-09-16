<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ToolHireRequest extends Model
{
    use HasFactory;

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'id';
    }

    protected $fillable = [
        'company_id', 'requested_by', 'site_id', 'project_id', 'title', 'description',
        'tool_category', 'tool_type', 'tool_name', 'quantity', 'urgency',
        'hire_start_date', 'hire_end_date', 'hire_duration_days', 'delivery_method',
        'delivery_address', 'special_requirements', 'estimated_daily_rate',
        'estimated_total_cost', 'actual_daily_rate', 'actual_total_cost',
        'deposit_amount', 'insurance_required', 'insurance_cost', 'preferred_supplier',
        'supplier_name', 'supplier_contact', 'supplier_notes', 'status',
        'approved_by', 'approved_at', 'assigned_to', 'rejection_reason',
        'actual_delivery_date', 'actual_return_date', 'condition_on_delivery',
        'condition_on_return', 'damage_notes', 'damage_charges', 'notes', 'attachments',
    ];

    protected function casts(): array
    {
        return [
            'hire_start_date' => 'date', 'hire_end_date' => 'date',
            'actual_delivery_date' => 'date', 'actual_return_date' => 'date',
            'approved_at' => 'datetime', 'estimated_daily_rate' => 'decimal:2',
            'estimated_total_cost' => 'decimal:2', 'actual_daily_rate' => 'decimal:2',
            'actual_total_cost' => 'decimal:2', 'deposit_amount' => 'decimal:2',
            'insurance_cost' => 'decimal:2', 'damage_charges' => 'decimal:2',
            'quantity' => 'integer', 'hire_duration_days' => 'integer',
            'insurance_required' => 'boolean', 'attachments' => 'array',
        ];
    }

    // Constants
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING_APPROVAL = 'pending_approval';
    const STATUS_APPROVED = 'approved';
    const STATUS_QUOTED = 'quoted';
    const STATUS_ORDERED = 'ordered';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_IN_USE = 'in_use';
    const STATUS_RETURNED = 'returned';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    const CATEGORY_EXCAVATION = 'excavation';
    const CATEGORY_POWER_TOOLS = 'power_tools';
    const CATEGORY_LIFTING = 'lifting';
    const CATEGORY_SAFETY = 'safety';
    const CATEGORY_MEASURING = 'measuring';
    const CATEGORY_CUTTING = 'cutting';
    const CATEGORY_CONCRETE = 'concrete';
    const CATEGORY_SCAFFOLDING = 'scaffolding';
    const CATEGORY_GENERATORS = 'generators';
    const CATEGORY_COMPACTION = 'compaction';
    const CATEGORY_ACCESS = 'access';
    const CATEGORY_PUMPING = 'pumping';

    const URGENCY_LOW = 'low';
    const URGENCY_NORMAL = 'normal';
    const URGENCY_HIGH = 'high';
    const URGENCY_URGENT = 'urgent';

    const DELIVERY_PICKUP = 'pickup';
    const DELIVERY_STANDARD = 'delivery';
    const DELIVERY_SITE = 'site_delivery';

    // Relationships
    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function requestedBy(): BelongsTo { return $this->belongsTo(User::class, 'requested_by'); }
    public function site(): BelongsTo { return $this->belongsTo(Site::class); }
    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function approvedBy(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
    public function assignedTo(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to'); }

    // Scopes
    public function scopeForCompany($query, $companyId = null)
    {
        $companyId = $companyId ?: (auth()->user()?->company_id ?? 1);
        return $query->where('company_id', $companyId);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            self::STATUS_PENDING_APPROVAL, self::STATUS_APPROVED, self::STATUS_QUOTED,
            self::STATUS_ORDERED, self::STATUS_DELIVERED, self::STATUS_IN_USE
        ]);
    }

    public function scopeUrgent($query) { return $query->where('urgency', self::URGENCY_URGENT); }

    public function scopeOverdue($query)
    {
        return $query->where('hire_start_date', '<', now())
                    ->whereIn('status', [self::STATUS_PENDING_APPROVAL, self::STATUS_APPROVED, self::STATUS_QUOTED, self::STATUS_ORDERED]);
    }

    // Helper methods
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'secondary',
            self::STATUS_PENDING_APPROVAL => 'warning',
            self::STATUS_APPROVED => 'info',
            self::STATUS_QUOTED => 'primary',
            self::STATUS_ORDERED => 'info',
            self::STATUS_DELIVERED, self::STATUS_IN_USE => 'success',
            self::STATUS_RETURNED => 'secondary',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_CANCELLED => 'danger',
            default => 'secondary'
        };
    }

    public function getStatusDisplayAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PENDING_APPROVAL => 'Pending Approval',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_QUOTED => 'Quoted',
            self::STATUS_ORDERED => 'Ordered',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_IN_USE => 'In Use',
            self::STATUS_RETURNED => 'Returned',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
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

    public function getCategoryDisplayAttribute(): string
    {
        return match($this->tool_category) {
            self::CATEGORY_EXCAVATION => 'Excavation',
            self::CATEGORY_POWER_TOOLS => 'Power Tools',
            self::CATEGORY_LIFTING => 'Lifting Equipment',
            self::CATEGORY_SAFETY => 'Safety Equipment',
            self::CATEGORY_MEASURING => 'Measuring Tools',
            self::CATEGORY_CUTTING => 'Cutting Tools',
            self::CATEGORY_CONCRETE => 'Concrete Equipment',
            self::CATEGORY_SCAFFOLDING => 'Scaffolding',
            self::CATEGORY_GENERATORS => 'Generators & Power',
            self::CATEGORY_COMPACTION => 'Compaction Equipment',
            self::CATEGORY_ACCESS => 'Access Equipment',
            self::CATEGORY_PUMPING => 'Pumping Equipment',
            default => ucfirst(str_replace('_', ' ', $this->tool_category))
        };
    }

    // Business logic
    public function isOverdue(): bool
    {
        return $this->hire_start_date && $this->hire_start_date->isPast() && 
               in_array($this->status, [self::STATUS_PENDING_APPROVAL, self::STATUS_APPROVED, self::STATUS_QUOTED, self::STATUS_ORDERED]);
    }

    public function canBeApproved(): bool { return $this->status === self::STATUS_PENDING_APPROVAL; }
    public function canBeEdited(): bool { return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_PENDING_APPROVAL]); }
    public function canBeCancelled(): bool { return !in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_CANCELLED]); }

    // Auto calculations
    protected static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            if ($model->hire_start_date && $model->hire_end_date) {
                $model->hire_duration_days = $model->hire_start_date->diffInDays($model->hire_end_date) + 1;
            }
            if ($model->estimated_daily_rate && $model->hire_duration_days) {
                $model->estimated_total_cost = $model->estimated_daily_rate * $model->hire_duration_days;
                if ($model->insurance_cost) $model->estimated_total_cost += $model->insurance_cost;
            }
        });
    }

    // Static options
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft', self::STATUS_PENDING_APPROVAL => 'Pending Approval',
            self::STATUS_APPROVED => 'Approved', self::STATUS_QUOTED => 'Quoted',
            self::STATUS_ORDERED => 'Ordered', self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_IN_USE => 'In Use', self::STATUS_RETURNED => 'Returned',
            self::STATUS_COMPLETED => 'Completed', self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public static function getCategoryOptions(): array
    {
        return [
            self::CATEGORY_EXCAVATION => 'Excavation', self::CATEGORY_POWER_TOOLS => 'Power Tools',
            self::CATEGORY_LIFTING => 'Lifting Equipment', self::CATEGORY_SAFETY => 'Safety Equipment',
            self::CATEGORY_MEASURING => 'Measuring Tools', self::CATEGORY_CUTTING => 'Cutting Tools',
            self::CATEGORY_CONCRETE => 'Concrete Equipment', self::CATEGORY_SCAFFOLDING => 'Scaffolding',
            self::CATEGORY_GENERATORS => 'Generators & Power', self::CATEGORY_COMPACTION => 'Compaction Equipment',
            self::CATEGORY_ACCESS => 'Access Equipment', self::CATEGORY_PUMPING => 'Pumping Equipment',
        ];
    }

    public static function getUrgencyOptions(): array
    {
        return [self::URGENCY_LOW => 'Low', self::URGENCY_NORMAL => 'Normal', self::URGENCY_HIGH => 'High', self::URGENCY_URGENT => 'Urgent'];
    }

    public static function getDeliveryMethodOptions(): array
    {
        return [self::DELIVERY_PICKUP => 'Pickup from Supplier', self::DELIVERY_STANDARD => 'Standard Delivery', self::DELIVERY_SITE => 'Direct Site Delivery'];
    }
}
