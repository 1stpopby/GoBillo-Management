<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'project_id',
        'user_id',
        'expense_number',
        'status',
        'category',
        'vendor',
        'description',
        'amount',
        'currency',
        'expense_date',
        'receipt_path',
        'payment_method',
        'is_billable',
        'is_reimbursable',
        'mileage',
        'mileage_rate',
        'notes',
        'submitted_at',
        'approved_at',
        'rejected_at',
        'reimbursed_at',
        'approved_by',
        'rejection_reason',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'reimbursed_at' => 'datetime',
        'amount' => 'decimal:2',
        'mileage' => 'decimal:2',
        'mileage_rate' => 'decimal:2',
        'is_billable' => 'boolean',
        'is_reimbursable' => 'boolean',
    ];

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_REIMBURSED = 'reimbursed';

    // Payment method constants
    const PAYMENT_CASH = 'cash';
    const PAYMENT_CREDIT_CARD = 'credit_card';
    const PAYMENT_COMPANY_CARD = 'company_card';
    const PAYMENT_CHECK = 'check';
    const PAYMENT_BANK_TRANSFER = 'bank_transfer';

    /**
     * Get the company that owns the expense
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the project for this expense
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the user who submitted the expense
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who approved the expense
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the expense category
     */
    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category', 'name');
    }

    /**
     * Scope for company isolation
     */
    public function scopeForCompany($query, $companyId = null)
    {
        $companyId = $companyId ?? auth()->user()->company_id ?? null;
        
        if (auth()->user() && auth()->user()->isSuperAdmin()) {
            return $query; // Superadmin can see all expenses
        }
        
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope for user's own expenses
     */
    public function scopeForUser($query, $userId = null)
    {
        $userId = $userId ?? auth()->id();
        return $query->where('user_id', $userId);
    }

    /**
     * Get status color for display
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'secondary',
            self::STATUS_SUBMITTED => 'primary',
            self::STATUS_APPROVED => 'success',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_REIMBURSED => 'info',
            default => 'secondary',
        };
    }

    /**
     * Generate next expense number
     */
    public static function generateExpenseNumber($companyId): string
    {
        $year = now()->year;
        $month = str_pad(now()->month, 2, '0', STR_PAD_LEFT);
        
        $lastExpense = self::where('company_id', $companyId)
                          ->where('expense_number', 'like', "EXP-{$year}{$month}-%")
                          ->orderBy('expense_number', 'desc')
                          ->first();

        if ($lastExpense) {
            $lastNumber = (int) substr($lastExpense->expense_number, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return "EXP-{$year}{$month}-{$nextNumber}";
    }

    /**
     * Get receipt URL
     */
    public function getReceiptUrlAttribute(): ?string
    {
        return $this->receipt_path ? Storage::url($this->receipt_path) : null;
    }

    /**
     * Check if expense has receipt
     */
    public function hasReceipt(): bool
    {
        return !empty($this->receipt_path) && Storage::exists($this->receipt_path);
    }

    /**
     * Calculate mileage amount if applicable
     */
    public function getMileageAmountAttribute(): float
    {
        if ($this->mileage && $this->mileage_rate) {
            return $this->mileage * $this->mileage_rate;
        }
        return 0;
    }

    /**
     * Get total amount (base amount + mileage if applicable)
     */
    public function getTotalAmountAttribute(): float
    {
        return $this->amount + $this->mileage_amount;
    }

    /**
     * Submit expense for approval
     */
    public function submit(): void
    {
        $this->update([
            'status' => self::STATUS_SUBMITTED,
            'submitted_at' => now(),
        ]);
    }

    /**
     * Approve expense
     */
    public function approve($approvedBy = null): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by' => $approvedBy ?? auth()->id(),
        ]);
    }

    /**
     * Reject expense
     */
    public function reject($reason = null): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    /**
     * Mark as reimbursed
     */
    public function markAsReimbursed(): void
    {
        $this->update([
            'status' => self::STATUS_REIMBURSED,
            'reimbursed_at' => now(),
        ]);
    }

    /**
     * Check if expense can be edited
     */
    public function canBeEdited(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_REJECTED]);
    }

    /**
     * Check if expense can be deleted
     */
    public function canBeDeleted(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_REJECTED]);
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($expense) {
            if (!$expense->expense_number) {
                $expense->expense_number = self::generateExpenseNumber($expense->company_id);
            }
        });

        static::deleting(function ($expense) {
            // Delete receipt file when expense is deleted
            if ($expense->receipt_path && Storage::exists($expense->receipt_path)) {
                Storage::delete($expense->receipt_path);
            }
        });
    }
} 