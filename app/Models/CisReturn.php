<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class CisReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'tax_year',
        'tax_month',
        'period_start',
        'period_end',
        'due_date',
        'total_subcontractors',
        'total_payments',
        'total_deductions',
        'total_materials',
        'status',
        'submitted_at',
        'hmrc_reference',
        'submission_response',
        'prepared_by',
        'submitted_by',
        'notes',
        'is_late',
        'penalty_amount',
        'is_correction',
        'corrects_return_id',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'due_date' => 'date',
            'submitted_at' => 'datetime',
            'total_payments' => 'decimal:2',
            'total_deductions' => 'decimal:2',
            'total_materials' => 'decimal:2',
            'penalty_amount' => 'decimal:2',
            'is_late' => 'boolean',
            'is_correction' => 'boolean',
            'submission_response' => 'array',
        ];
    }

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function preparedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function correctsReturn(): BelongsTo
    {
        return $this->belongsTo(CisReturn::class, 'corrects_return_id');
    }

    public function corrections(): HasMany
    {
        return $this->hasMany(CisReturn::class, 'corrects_return_id');
    }

    public function cisPayments(): HasMany
    {
        return $this->hasMany(CisPayment::class);
    }

    // Scopes
    public function scopeForCompany($query, $companyId = null)
    {
        $companyId = $companyId ?: (auth()->user()?->company_id ?? 1);
        return $query->where('company_id', $companyId);
    }

    public function scopeByTaxYear($query, $taxYear)
    {
        return $query->where('tax_year', $taxYear);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->whereIn('status', [self::STATUS_DRAFT]);
    }

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SUBMITTED => 'Submitted',
            self::STATUS_ACCEPTED => 'Accepted',
            self::STATUS_REJECTED => 'Rejected',
            default => 'Unknown'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'secondary',
            self::STATUS_SUBMITTED => 'warning',
            self::STATUS_ACCEPTED => 'success',
            self::STATUS_REJECTED => 'danger',
            default => 'secondary'
        };
    }

    public function getPeriodDescriptionAttribute(): string
    {
        return $this->period_start->format('M Y');
    }

    public function getFormattedDueDateAttribute(): string
    {
        return $this->due_date->format('j M Y');
    }

    // Helper methods
    public function isOverdue(): bool
    {
        return $this->due_date->isPast() && $this->status === self::STATUS_DRAFT;
    }

    public function canBeSubmitted(): bool
    {
        return $this->status === self::STATUS_DRAFT && $this->total_subcontractors > 0;
    }

    public function calculateTotals(): void
    {
        $payments = $this->cisPayments()->verified()->get();
        
        $this->total_subcontractors = $payments->pluck('employee_id')->unique()->count();
        $this->total_payments = $payments->sum('gross_amount');
        $this->total_deductions = $payments->sum('cis_deduction');
        $this->total_materials = $payments->sum('materials_cost');
        
        $this->save();
    }

    public function includePendingPayments(): int
    {
        $payments = CisPayment::forCompany()
            ->verified()
            ->notInReturn()
            ->whereBetween('payment_date', [$this->period_start, $this->period_end])
            ->get();

        $included = 0;
        foreach ($payments as $payment) {
            $payment->update([
                'cis_return_id' => $this->id,
                'included_in_return' => true,
            ]);
            $included++;
        }

        $this->calculateTotals();
        return $included;
    }

    public function generateHmrcSubmission(): array
    {
        $payments = $this->cisPayments()->with('employee')->get();
        
        $subcontractors = [];
        foreach ($payments as $payment) {
            $employee = $payment->employee;
            $subcontractors[] = [
                'name' => $employee->full_name,
                'cis_number' => $employee->cis_number,
                'payments' => [
                    [
                        'amount' => $payment->gross_amount,
                        'deduction' => $payment->cis_deduction,
                        'materials' => $payment->materials_cost,
                        'date' => $payment->payment_date->format('Y-m-d'),
                    ]
                ]
            ];
        }

        return [
            'period' => [
                'start' => $this->period_start->format('Y-m-d'),
                'end' => $this->period_end->format('Y-m-d'),
            ],
            'totals' => [
                'payments' => $this->total_payments,
                'deductions' => $this->total_deductions,
                'materials' => $this->total_materials,
            ],
            'subcontractors' => $subcontractors,
        ];
    }

    // Static methods
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SUBMITTED => 'Submitted',
            self::STATUS_ACCEPTED => 'Accepted',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }

    public static function createForPeriod(int $taxYear, int $taxMonth, int $companyId): self
    {
        // Calculate period dates
        $periodStart = Carbon::create($taxYear, $taxMonth, 1);
        $periodEnd = $periodStart->copy()->endOfMonth();
        $dueDate = $periodStart->copy()->addMonth()->day(19);

        return self::create([
            'company_id' => $companyId,
            'tax_year' => $taxYear,
            'tax_month' => $taxMonth,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'due_date' => $dueDate,
            'prepared_by' => auth()->id() ?? 1, // Default to user ID 1 if not authenticated
            'status' => self::STATUS_DRAFT,
        ]);
    }

    public static function getCurrentPeriod(): array
    {
        $now = now();
        $taxMonth = $now->month;
        $taxYear = $now->year;
        
        // Adjust for UK tax year (April to March)
        if ($taxMonth < 4) {
            $taxYear--;
        }

        return [
            'tax_year' => $taxYear,
            'tax_month' => $taxMonth,
        ];
    }
}