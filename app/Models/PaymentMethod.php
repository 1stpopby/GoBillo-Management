<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'type',
        'provider',
        'configuration',
        'is_active',
        'is_default',
        'processing_fee_percentage',
        'processing_fee_fixed',
        'currency',
    ];

    protected $casts = [
        'configuration' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'processing_fee_percentage' => 'decimal:2',
        'processing_fee_fixed' => 'decimal:2',
    ];

    // Type constants
    const TYPE_CREDIT_CARD = 'credit_card';
    const TYPE_BANK_TRANSFER = 'bank_transfer';
    const TYPE_DIGITAL_WALLET = 'digital_wallet';
    const TYPE_CHECK = 'check';
    const TYPE_CASH = 'cash';

    // Provider constants
    const PROVIDER_STRIPE = 'stripe';
    const PROVIDER_PAYPAL = 'paypal';
    const PROVIDER_SQUARE = 'square';
    const PROVIDER_MANUAL = 'manual';

    /**
     * Get the company that owns the payment method
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get payments using this method
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Scope for company isolation
     */
    public function scopeForCompany($query, $companyId = null)
    {
        $companyId = $companyId ?? auth()->user()->company_id ?? null;
        
        if (auth()->user() && auth()->user()->isSuperAdmin()) {
            return $query; // Superadmin can see all payment methods
        }
        
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope for active payment methods
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Calculate processing fee for an amount
     */
    public function calculateProcessingFee($amount): float
    {
        $percentageFee = ($amount * $this->processing_fee_percentage) / 100;
        return $percentageFee + $this->processing_fee_fixed;
    }

    /**
     * Calculate net amount after processing fees
     */
    public function calculateNetAmount($amount): float
    {
        return $amount - $this->calculateProcessingFee($amount);
    }

    /**
     * Check if this is a manual payment method
     */
    public function isManual(): bool
    {
        return $this->provider === self::PROVIDER_MANUAL;
    }

    /**
     * Check if this payment method supports online payments
     */
    public function supportsOnlinePayments(): bool
    {
        return in_array($this->provider, [
            self::PROVIDER_STRIPE,
            self::PROVIDER_PAYPAL,
            self::PROVIDER_SQUARE,
        ]);
    }

    /**
     * Get configuration value
     */
    public function getConfigValue($key, $default = null)
    {
        return data_get($this->configuration, $key, $default);
    }

    /**
     * Set configuration value
     */
    public function setConfigValue($key, $value): void
    {
        $config = $this->configuration ?? [];
        data_set($config, $key, $value);
        $this->configuration = $config;
        $this->save();
    }

    /**
     * Get default payment methods for a company
     */
    public static function getDefaultMethods(): array
    {
        return [
            [
                'name' => 'Cash',
                'type' => self::TYPE_CASH,
                'provider' => self::PROVIDER_MANUAL,
                'configuration' => [],
                'processing_fee_percentage' => 0,
                'processing_fee_fixed' => 0,
            ],
            [
                'name' => 'Check',
                'type' => self::TYPE_CHECK,
                'provider' => self::PROVIDER_MANUAL,
                'configuration' => [],
                'processing_fee_percentage' => 0,
                'processing_fee_fixed' => 0,
            ],
            [
                'name' => 'Bank Transfer',
                'type' => self::TYPE_BANK_TRANSFER,
                'provider' => self::PROVIDER_MANUAL,
                'configuration' => [],
                'processing_fee_percentage' => 0,
                'processing_fee_fixed' => 0,
            ],
            [
                'name' => 'Credit Card (Stripe)',
                'type' => self::TYPE_CREDIT_CARD,
                'provider' => self::PROVIDER_STRIPE,
                'configuration' => [
                    'publishable_key' => '',
                    'secret_key' => '',
                    'webhook_secret' => '',
                ],
                'processing_fee_percentage' => 2.9,
                'processing_fee_fixed' => 0.30,
                'is_active' => false, // Requires configuration
            ],
        ];
    }

    /**
     * Create default payment methods for a company
     */
    public static function createDefaultMethods($companyId): void
    {
        $defaultMethods = self::getDefaultMethods();
        
        foreach ($defaultMethods as $methodData) {
            self::create(array_merge($methodData, [
                'company_id' => $companyId,
            ]));
        }

        // Set cash as default
        self::where('company_id', $companyId)
            ->where('name', 'Cash')
            ->update(['is_default' => true]);
    }
} 