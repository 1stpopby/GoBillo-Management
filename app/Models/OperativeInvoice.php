<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OperativeInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'operative_id',
        'manager_id',
        'site_id',
        'project_id',
        'invoice_number',
        'status',
        'week_starting',
        'week_ending',
        'total_hours',
        'day_rate',
        'gross_amount',
        'cis_applicable',
        'cis_rate',
        'cis_deduction',
        'net_amount',
        'notes',
        'submitted_at',
        'approved_at',
        'paid_at',
        'paid_by',
    ];

    protected $casts = [
        'week_starting' => 'date',
        'week_ending' => 'date',
        'total_hours' => 'decimal:2',
        'day_rate' => 'decimal:2',
        'gross_amount' => 'decimal:2',
        'cis_applicable' => 'boolean',
        'cis_rate' => 'decimal:2',
        'cis_deduction' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_APPROVED = 'approved';
    const STATUS_PAID = 'paid';
    const STATUS_REJECTED = 'rejected';

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function operative()
    {
        return $this->belongsTo(User::class, 'operative_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function items()
    {
        return $this->hasMany(OperativeInvoiceItem::class)->orderBy('work_date');
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    // Scopes
    public function scopeForCompany($query, $companyId = null)
    {
        $companyId = $companyId ?? auth()->user()->company_id ?? null;
        return $query->where('company_id', $companyId);
    }

    public function scopeForOperative($query, $operativeId = null)
    {
        $operativeId = $operativeId ?? auth()->id();
        return $query->where('operative_id', $operativeId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Accessors
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'secondary',
            self::STATUS_SUBMITTED => 'warning',
            self::STATUS_APPROVED => 'success',
            self::STATUS_PAID => 'primary',
            self::STATUS_REJECTED => 'danger',
            default => 'secondary',
        };
    }

    public function getWeekPeriodAttribute(): string
    {
        return $this->week_starting->format('M j') . ' - ' . $this->week_ending->format('M j, Y');
    }

    // Methods
    public function calculateTotals(): void
    {
        $this->total_hours = $this->items()->sum('hours_worked');
        $this->gross_amount = $this->items()->sum('total_amount');
        
        if ($this->cis_applicable && $this->cis_rate > 0) {
            $this->cis_deduction = $this->gross_amount * ($this->cis_rate / 100);
        } else {
            $this->cis_deduction = 0;
        }
        
        $this->net_amount = $this->gross_amount - $this->cis_deduction;
        $this->save();
    }

    public function submit(): void
    {
        $this->update([
            'status' => self::STATUS_SUBMITTED,
            'submitted_at' => now(),
        ]);
    }

    public function approve(): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_at' => now(),
        ]);
    }

    public function reject(): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
        ]);
    }

    public function markAsPaid(): void
    {
        $this->update([
            'status' => self::STATUS_PAID,
            'paid_at' => now(),
        ]);
    }

    // Generate invoice number
    public static function generateInvoiceNumber($companyId): string
    {
        $year = now()->year;
        $month = str_pad(now()->month, 2, '0', STR_PAD_LEFT);
        
        $lastInvoice = self::where('company_id', $companyId)
                          ->where('invoice_number', 'like', "OP-{$year}{$month}-%")
                          ->orderBy('invoice_number', 'desc')
                          ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return "OP-{$year}{$month}-{$nextNumber}";
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (!$invoice->invoice_number) {
                $invoice->invoice_number = self::generateInvoiceNumber($invoice->company_id);
            }
        });
    }
}