<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Carbon\Carbon;

class DocumentAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'attachable_type',
        'attachable_id',
        'document_type',
        'document_name',
        'original_filename',
        'file_path',
        'file_size',
        'mime_type',
        'issue_date',
        'expiry_date',
        'document_number',
        'issuing_authority',
        'notes',
        'status',
        'requires_renewal',
        'notification_sent',
        'notification_sent_at',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'expiry_date' => 'date',
            'notification_sent_at' => 'date',
            'requires_renewal' => 'boolean',
            'notification_sent' => 'boolean',
        ];
    }

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_EXPIRED = 'expired';
    const STATUS_EXPIRING_SOON = 'expiring_soon';
    const STATUS_ARCHIVED = 'archived';

    // Document types
    const TYPE_CSCS_CARD = 'cscs_card';
    const TYPE_DRIVING_LICENSE = 'driving_license';
    const TYPE_PASSPORT = 'passport';
    const TYPE_VISA = 'visa';
    const TYPE_RIGHT_TO_WORK = 'right_to_work';
    const TYPE_INSURANCE = 'insurance';
    const TYPE_CERTIFICATION = 'certification';
    const TYPE_TRAINING = 'training';
    const TYPE_HEALTH_SAFETY = 'health_safety';
    const TYPE_OTHER = 'other';

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Scopes
    public function scopeForCompany($query, $companyId = null)
    {
        $companyId = $companyId ?: (auth()->user()?->company_id ?? 1);
        return $query->where('company_id', $companyId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_EXPIRED);
    }

    public function scopeExpiringSoon($query)
    {
        return $query->where('status', self::STATUS_EXPIRING_SOON);
    }

    public function scopeRequiringNotification($query)
    {
        return $query->where('notification_sent', false)
                    ->where(function($q) {
                        $q->where('status', self::STATUS_EXPIRED)
                          ->orWhere('status', self::STATUS_EXPIRING_SOON);
                    });
    }

    // Helper methods
    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isExpiringSoon($days = 30): bool
    {
        return $this->expiry_date && 
               $this->expiry_date->isFuture() && 
               $this->expiry_date->diffInDays(now()) <= $days;
    }

    public function updateStatus(): void
    {
        if ($this->expiry_date) {
            if ($this->isExpired()) {
                $this->status = self::STATUS_EXPIRED;
            } elseif ($this->isExpiringSoon()) {
                $this->status = self::STATUS_EXPIRING_SOON;
            } else {
                $this->status = self::STATUS_ACTIVE;
            }
            $this->save();
        }
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'success',
            self::STATUS_EXPIRED => 'danger',
            self::STATUS_EXPIRING_SOON => 'warning',
            self::STATUS_ARCHIVED => 'secondary',
            default => 'secondary'
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_EXPIRED => 'Expired',
            self::STATUS_EXPIRING_SOON => 'Expiring Soon',
            self::STATUS_ARCHIVED => 'Archived',
            default => 'Unknown'
        };
    }

    public function getDocumentTypeDisplayAttribute(): string
    {
        return match($this->document_type) {
            self::TYPE_CSCS_CARD => 'CSCS Card',
            self::TYPE_DRIVING_LICENSE => 'Driving License',
            self::TYPE_PASSPORT => 'Passport',
            self::TYPE_VISA => 'Visa',
            self::TYPE_RIGHT_TO_WORK => 'Right to Work',
            self::TYPE_INSURANCE => 'Insurance',
            self::TYPE_CERTIFICATION => 'Certification',
            self::TYPE_TRAINING => 'Training Certificate',
            self::TYPE_HEALTH_SAFETY => 'Health & Safety',
            self::TYPE_OTHER => 'Other',
            default => ucfirst(str_replace('_', ' ', $this->document_type))
        };
    }

    public function getFileSizeHumanAttribute(): string
    {
        $bytes = (int) $this->file_size;
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        
        return $bytes . ' bytes';
    }

    public static function getDocumentTypes(): array
    {
        return [
            self::TYPE_CSCS_CARD => 'CSCS Card',
            self::TYPE_DRIVING_LICENSE => 'Driving License',
            self::TYPE_PASSPORT => 'Passport',
            self::TYPE_VISA => 'Visa',
            self::TYPE_RIGHT_TO_WORK => 'Right to Work',
            self::TYPE_INSURANCE => 'Insurance',
            self::TYPE_CERTIFICATION => 'Certification',
            self::TYPE_TRAINING => 'Training Certificate',
            self::TYPE_HEALTH_SAFETY => 'Health & Safety',
            self::TYPE_OTHER => 'Other',
        ];
    }
}