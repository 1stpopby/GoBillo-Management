<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class OperativeDataForm extends Model
{
    protected $fillable = [
        'company_id',
        'share_token',
        'status',
        
        // Personal Information
        'full_name',
        'date_of_birth',
        'nationality',
        'mobile_number',
        'email_address',
        'home_address',
        'postcode',
        
        // Emergency Contact
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_number',
        
        // Work Documentation
        'national_insurance_number',
        'utr_number',
        'cscs_card_type',
        'cscs_card_number',
        'cscs_card_expiry',
        'cscs_card_front_image',
        'cscs_card_back_image',
        'right_to_work_uk',
        'passport_id_provided',
        
        // Bank Details
        'bank_name',
        'account_holder_name',
        'sort_code',
        'account_number',
        
        // Trade and Qualifications
        'primary_trade',
        'years_experience',
        'qualifications_certifications',
        'other_cards_licenses',
        
        // Declaration
        'declaration_confirmed',
        'submitted_at',
        'approved_at',
        'rejected_at',
        'approved_by',
        'rejection_reason',
        
        // Account Creation
        'account_created',
        'account_created_at',
        'account_created_by',
        'created_user_id',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'cscs_card_expiry' => 'date',
        'right_to_work_uk' => 'boolean',
        'passport_id_provided' => 'boolean',
        'declaration_confirmed' => 'boolean',
        'account_created' => 'boolean',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'account_created_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->share_token)) {
                $model->share_token = Str::random(32);
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function accountCreatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'account_created_by');
    }

    public function createdUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_APPROVED => 'success',
            self::STATUS_REJECTED => 'danger',
            default => 'secondary',
        };
    }

    public function getShareUrlAttribute(): string
    {
        return url("/operative-data-form/{$this->share_token}");
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }
}
