<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class CisPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'employee_id',
        'user_id',
        'payee_type',
        'employment_status',
        'project_id',
        'payment_reference',
        'payment_date',
        'period_start',
        'period_end',
        'gross_amount',
        'materials_cost',
        'labour_amount',
        'cis_rate',
        'cis_deduction',
        'net_payment',
        'other_deductions',
        'deduction_notes',
        'status',
        'verification_status',
        'verified_at',
        'verified_by',
        'cis_return_id',
        'included_in_return',
        'description',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'period_start' => 'date',
            'period_end' => 'date',
            'verified_at' => 'datetime',
            'gross_amount' => 'decimal:2',
            'materials_cost' => 'decimal:2',
            'labour_amount' => 'decimal:2',
            'cis_rate' => 'decimal:2',
            'cis_deduction' => 'decimal:2',
            'net_payment' => 'decimal:2',
            'other_deductions' => 'decimal:2',
            'included_in_return' => 'boolean',
            'metadata' => 'array',
        ];
    }

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_VERIFIED = 'verified';
    const STATUS_PAID = 'paid';
    const STATUS_RETURNED = 'returned';

    // Verification status constants
    const VERIFICATION_PENDING = 'pending';
    const VERIFICATION_VERIFIED = 'verified';
    const VERIFICATION_REJECTED = 'rejected';

    // CIS rates
    const RATE_REGISTERED = 20.00;
    const RATE_UNREGISTERED = 30.00;
    
    // Payee types
    const PAYEE_TYPE_EMPLOYEE = 'employee';
    const PAYEE_TYPE_USER = 'user';
    
    // Employment status
    const EMPLOYMENT_EMPLOYED = 'employed';
    const EMPLOYMENT_SELF_EMPLOYED = 'self_employed';

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function cisReturn(): BelongsTo
    {
        return $this->belongsTo(CisReturn::class);
    }
    
    // Helper methods
    public function getPayee()
    {
        return $this->payee_type === self::PAYEE_TYPE_USER ? $this->user : $this->employee;
    }
    
    public function getPayeeName()
    {
        $payee = $this->getPayee();
        return $payee ? ($this->payee_type === self::PAYEE_TYPE_USER ? $payee->name : $payee->full_name) : 'Unknown';
    }
    
    public function isForUser()
    {
        return $this->payee_type === self::PAYEE_TYPE_USER;
    }
    
    public function isForEmployee()
    {
        return $this->payee_type === self::PAYEE_TYPE_EMPLOYEE;
    }
    
    public function isSelfEmployed()
    {
        return $this->employment_status === self::EMPLOYMENT_SELF_EMPLOYED;
    }

    // Scopes
    public function scopeForCompany($query, $companyId = null)
    {
        $companyId = $companyId ?: (auth()->user()?->company_id ?? 1);
        return $query->where('company_id', $companyId);
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeInPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeNotInReturn($query)
    {
        return $query->where('included_in_return', false);
    }

    public function scopeVerified($query)
    {
        return $query->where('status', self::STATUS_VERIFIED);
    }

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_VERIFIED => 'Verified',
            self::STATUS_PAID => 'Paid',
            self::STATUS_RETURNED => 'Returned',
            default => 'Unknown'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'secondary',
            self::STATUS_VERIFIED => 'warning',
            self::STATUS_PAID => 'success',
            self::STATUS_RETURNED => 'info',
            default => 'secondary'
        };
    }

    public function getVerificationStatusLabelAttribute(): string
    {
        return match($this->verification_status) {
            self::VERIFICATION_PENDING => 'Pending',
            self::VERIFICATION_VERIFIED => 'Verified',
            self::VERIFICATION_REJECTED => 'Rejected',
            default => 'Unknown'
        };
    }

    public function getDeductionRatePercentageAttribute(): string
    {
        return number_format($this->cis_rate, 1) . '%';
    }

    // Helper methods
    public function calculateDeduction(): void
    {
        $this->labour_amount = $this->gross_amount - $this->materials_cost;
        $this->cis_deduction = ($this->labour_amount * $this->cis_rate) / 100;
        $this->net_payment = $this->gross_amount - $this->cis_deduction - $this->other_deductions;
    }

    public function isOverdue(): bool
    {
        if ($this->status === self::STATUS_PAID) {
            return false;
        }
        
        // Payments should be made within 7 days of verification
        return $this->verified_at && $this->verified_at->addDays(7)->isPast();
    }

    public function getTaxMonth(): int
    {
        return $this->payment_date->month;
    }

    public function getTaxYear(): int
    {
        // UK tax year runs April to March
        $paymentDate = $this->payment_date;
        if ($paymentDate->month >= 4) {
            return $paymentDate->year;
        }
        return $paymentDate->year - 1;
    }

    public function canBeIncludedInReturn(): bool
    {
        return $this->status === self::STATUS_VERIFIED && !$this->included_in_return;
    }

    // Static methods
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_VERIFIED => 'Verified',
            self::STATUS_PAID => 'Paid',
            self::STATUS_RETURNED => 'Returned',
        ];
    }

    public static function getVerificationStatusOptions(): array
    {
        return [
            self::VERIFICATION_PENDING => 'Pending',
            self::VERIFICATION_VERIFIED => 'Verified',
            self::VERIFICATION_REJECTED => 'Rejected',
        ];
    }

    public static function getCisRateForEmployee(Employee $employee): float
    {
        // Return appropriate CIS rate based on employee registration status
        if ($employee->cis_status === 'verified') {
            return self::RATE_REGISTERED;
        }
        return self::RATE_UNREGISTERED;
    }
}