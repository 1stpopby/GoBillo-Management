<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Estimate extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'client_id',
        'project_id',
        'estimate_number',
        'status',
        'issue_date',
        'valid_until',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'currency',
        'description',
        'terms',
        'notes',
        'sent_at',
        'approved_at',
        'rejected_at',
        'rejection_reason',
        'converted_to_project_id',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'valid_until' => 'date',
        'sent_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_SENT = 'sent';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_EXPIRED = 'expired';
    const STATUS_CONVERTED = 'converted';

    /**
     * Get the company that owns the estimate
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the client for this estimate
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the project for this estimate
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the project this estimate was converted to
     */
    public function convertedToProject()
    {
        return $this->belongsTo(Project::class, 'converted_to_project_id');
    }

    /**
     * Get the estimate items
     */
    public function items()
    {
        return $this->hasMany(EstimateItem::class)->orderBy('sort_order');
    }

    /**
     * Scope for company isolation
     */
    public function scopeForCompany($query, $companyId = null)
    {
        $companyId = $companyId ?? auth()->user()->company_id ?? null;
        
        if (auth()->user() && auth()->user()->isSuperAdmin()) {
            return $query; // Superadmin can see all estimates
        }
        
        return $query->where('company_id', $companyId);
    }

    /**
     * Check if estimate is expired
     */
    public function isExpired(): bool
    {
        return $this->status !== self::STATUS_APPROVED && 
               $this->status !== self::STATUS_REJECTED && 
               $this->status !== self::STATUS_CONVERTED && 
               $this->valid_until->isPast();
    }

    /**
     * Get status color for display
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'secondary',
            self::STATUS_SENT => 'primary',
            self::STATUS_APPROVED => 'success',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_EXPIRED => 'warning',
            self::STATUS_CONVERTED => 'info',
            default => 'secondary',
        };
    }

    /**
     * Get days until expiration
     */
    public function getDaysUntilExpirationAttribute(): int
    {
        return now()->diffInDays($this->valid_until, false);
    }

    /**
     * Generate next estimate number
     */
    public static function generateEstimateNumber($companyId): string
    {
        $year = now()->year;
        $month = str_pad(now()->month, 2, '0', STR_PAD_LEFT);
        
        $lastEstimate = self::where('company_id', $companyId)
                           ->where('estimate_number', 'like', "EST-{$year}{$month}-%")
                           ->orderBy('estimate_number', 'desc')
                           ->first();

        if ($lastEstimate) {
            $lastNumber = (int) substr($lastEstimate->estimate_number, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return "EST-{$year}{$month}-{$nextNumber}";
    }

    /**
     * Calculate totals
     */
    public function calculateTotals(): void
    {
        $this->subtotal = $this->items->sum(function($item) {
            return $item->total_price + $item->markup_amount;
        });
        $this->tax_amount = $this->subtotal * ($this->tax_rate / 100);
        $this->total_amount = $this->subtotal + $this->tax_amount - $this->discount_amount;
        $this->save();
    }

    /**
     * Mark as sent
     */
    public function markAsSent(): void
    {
        $this->update([
            'status' => self::STATUS_SENT,
            'sent_at' => now(),
        ]);
    }

    /**
     * Mark as approved
     */
    public function markAsApproved(): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_at' => now(),
        ]);
    }

    /**
     * Mark as rejected
     */
    public function markAsRejected($reason = null): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    /**
     * Convert to project
     */
    public function convertToProject($projectData): Project
    {
        $project = Project::create(array_merge($projectData, [
            'company_id' => $this->company_id,
            'client_id' => $this->client_id,
            'budget' => $this->total_amount,
            'status' => 'planning',
        ]));

        $this->update([
            'status' => self::STATUS_CONVERTED,
            'converted_to_project_id' => $project->id,
        ]);

        return $project;
    }

    /**
     * Check and update expired status
     */
    public function checkExpired(): void
    {
        if ($this->isExpired() && $this->status === self::STATUS_SENT) {
            $this->update(['status' => self::STATUS_EXPIRED]);
        }
    }

    /**
     * Get items grouped by category
     */
    public function getItemsByCategory()
    {
        return $this->items->groupBy('category');
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($estimate) {
            if (!$estimate->estimate_number) {
                $estimate->estimate_number = self::generateEstimateNumber($estimate->company_id);
            }
        });
    }
} 