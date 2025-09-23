<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'invoice_id',
        'payment_method_id',
        'payment_number',
        'status',
        'amount',
        'processing_fee',
        'net_amount',
        'currency',
        'payment_type',
        'provider_transaction_id',
        'provider_response',
        'payment_gateway',
        'notes',
        'processed_at',
        'failed_at',
        'failure_reason',
        'refunded_at',
        'refund_amount',
        'refund_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processing_fee' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'provider_response' => 'array',
        'processed_at' => 'datetime',
        'failed_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_CANCELLED = 'cancelled';

    // Payment type constants
    const TYPE_FULL_PAYMENT = 'full_payment';
    const TYPE_PARTIAL_PAYMENT = 'partial_payment';
    const TYPE_DEPOSIT = 'deposit';

    /**
     * Get the company that owns the payment
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the invoice this payment belongs to
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the payment method used
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Scope for company isolation
     */
    public function scopeForCompany($query, $companyId = null)
    {
        $companyId = $companyId ?? auth()->user()->company_id ?? null;
        
        if (auth()->user() && auth()->user()->isSuperAdmin()) {
            return $query; // Superadmin can see all payments
        }
        
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope for completed payments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope for pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Check if payment is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if payment failed
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Generate payment number
     */
    public static function generatePaymentNumber(): string
    {
        $prefix = 'PAY';
        $year = date('Y');
        $month = date('m');
        
        // Get the latest payment number for this month
        $lastPayment = self::where('payment_number', 'LIKE', "{$prefix}-{$year}{$month}-%")
            ->orderBy('payment_number', 'desc')
            ->first();
        
        if ($lastPayment) {
            $lastNumber = (int) substr($lastPayment->payment_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return sprintf('%s-%s%s-%04d', $prefix, $year, $month, $newNumber);
    }
}