<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class EmailSetting extends Model
{
    protected $fillable = [
        'company_id',
        'type',
        'name',
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',
        'from_email',
        'from_name',
        'reply_to_email',
        'reply_to_name',
        'enabled_notifications',
        'email_signature',
        'company_logo_url',
        'is_active',
        'is_verified',
        'last_tested_at',
        'test_results',
        'emails_sent_today',
        'emails_sent_month',
        'last_reset_date',
    ];

    protected $casts = [
        'enabled_notifications' => 'array',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'last_tested_at' => 'datetime',
        'last_reset_date' => 'date',
        'smtp_port' => 'integer',
        'emails_sent_today' => 'integer',
        'emails_sent_month' => 'integer',
    ];

    protected $hidden = [
        'smtp_password',
    ];

    /**
     * Relationships
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Accessors & Mutators
     */
    public function setSmtpPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['smtp_password'] = Crypt::encryptString($value);
        }
    }

    public function getSmtpPasswordAttribute($value)
    {
        if ($value) {
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeSystem($query)
    {
        return $query->where('type', 'system');
    }

    public function scopeCompany($query)
    {
        return $query->where('type', 'company');
    }

    /**
     * Static Methods
     */
    public static function getActiveForCompany($companyId)
    {
        return static::forCompany($companyId)->active()->first() 
            ?: static::system()->active()->first();
    }

    public static function getSystemDefault()
    {
        return static::system()->active()->first();
    }

    /**
     * Configure Laravel Mail with this email setting
     */
    public function configureMailer()
    {
        Config::set([
            'mail.default' => 'smtp',
            'mail.mailers.smtp' => [
                'transport' => 'smtp',
                'host' => $this->smtp_host,
                'port' => $this->smtp_port,
                'encryption' => $this->smtp_encryption,
                'username' => $this->smtp_username,
                'password' => $this->smtp_password,
                'timeout' => null,
                'local_domain' => env('MAIL_EHLO_DOMAIN'),
            ],
            'mail.from' => [
                'address' => $this->from_email,
                'name' => $this->from_name,
            ],
        ]);
    }

    /**
     * Test email configuration
     */
    public function testConnection($testEmail = null)
    {
        try {
            $this->configureMailer();
            
            $testEmail = $testEmail ?: $this->from_email;
            
            Mail::raw('This is a test email to verify your email configuration.', function ($message) use ($testEmail) {
                $message->to($testEmail)
                        ->subject('Email Configuration Test - ' . now()->format('Y-m-d H:i:s'));
            });

            $this->update([
                'is_verified' => true,
                'last_tested_at' => now(),
                'test_results' => 'Connection successful',
            ]);

            return ['success' => true, 'message' => 'Test email sent successfully!'];
            
        } catch (\Exception $e) {
            $this->update([
                'is_verified' => false,
                'last_tested_at' => now(),
                'test_results' => $e->getMessage(),
            ]);

            return ['success' => false, 'message' => 'Test failed: ' . $e->getMessage()];
        }
    }

    /**
     * Check if a notification type is enabled
     */
    public function isNotificationEnabled($type)
    {
        $enabledNotifications = $this->enabled_notifications ?: [];
        return in_array($type, $enabledNotifications);
    }

    /**
     * Increment email usage counters
     */
    public function incrementUsage()
    {
        $today = now()->toDateString();
        
        if ($this->last_reset_date?->toDateString() !== $today) {
            $this->update([
                'emails_sent_today' => 1,
                'emails_sent_month' => $this->emails_sent_month + 1,
                'last_reset_date' => $today,
            ]);
        } else {
            $this->increment('emails_sent_today');
            $this->increment('emails_sent_month');
        }
    }

    /**
     * Available notification types
     */
    public static function getNotificationTypes()
    {
        return [
            // Company notifications
            'invoice_created' => 'Invoice Created - Send to Client',
            'invoice_sent' => 'Invoice Sent - Client Notification',
            'invoice_paid' => 'Invoice Paid - Internal Notification',
            'invoice_overdue' => 'Invoice Overdue - Client Reminder',
            
            'project_variation_created' => 'Project Variation Created - Send to Client',
            'project_variation_approved' => 'Project Variation Approved - Client Notification',
            'project_completed' => 'Project Completed - Client Notification',
            
            'operative_invoice_submitted' => 'Operative Invoice Submitted - Manager Notification',
            'operative_invoice_approved' => 'Operative Invoice Approved - Operative Notification',
            'operative_invoice_rejected' => 'Operative Invoice Rejected - Operative Notification',
            
            'task_assigned' => 'Task Assigned - Operative Notification',
            'task_completed' => 'Task Completed - Manager Notification',
            'task_overdue' => 'Task Overdue - Manager/Operative Notification',
            
            'document_expiry_warning' => 'Document Expiry Warning - Admin Notification',
            'insurance_expiry_warning' => 'Insurance Expiry Warning - Admin Notification',
            
            // System notifications (SuperAdmin)
            'subscription_expiry_warning' => 'Subscription Expiry Warning - Company Notification',
            'subscription_expired' => 'Subscription Expired - Company Notification',
            'subscription_renewed' => 'Subscription Renewed - Company Confirmation',
            'payment_failed' => 'Payment Failed - Company Notification',
            'payment_successful' => 'Payment Successful - Company Receipt',
            
            'new_company_registered' => 'New Company Registered - Admin Notification',
            'company_trial_ending' => 'Company Trial Ending - Admin Notification',
            'system_backup_completed' => 'System Backup Completed - Admin Notification',
            'system_maintenance_scheduled' => 'System Maintenance Scheduled - All Companies',
        ];
    }

    /**
     * Get notification types by category
     */
    public static function getNotificationsByCategory()
    {
        return [
            'Invoicing' => [
                'invoice_created' => 'Invoice Created - Send to Client',
                'invoice_sent' => 'Invoice Sent - Client Notification',
                'invoice_paid' => 'Invoice Paid - Internal Notification',
                'invoice_overdue' => 'Invoice Overdue - Client Reminder',
            ],
            'Projects' => [
                'project_variation_created' => 'Project Variation Created - Send to Client',
                'project_variation_approved' => 'Project Variation Approved - Client Notification',
                'project_completed' => 'Project Completed - Client Notification',
            ],
            'Operative Management' => [
                'operative_invoice_submitted' => 'Operative Invoice Submitted - Manager Notification',
                'operative_invoice_approved' => 'Operative Invoice Approved - Operative Notification',
                'operative_invoice_rejected' => 'Operative Invoice Rejected - Operative Notification',
                'task_assigned' => 'Task Assigned - Operative Notification',
                'task_completed' => 'Task Completed - Manager Notification',
                'task_overdue' => 'Task Overdue - Manager/Operative Notification',
            ],
            'Compliance' => [
                'document_expiry_warning' => 'Document Expiry Warning - Admin Notification',
                'insurance_expiry_warning' => 'Insurance Expiry Warning - Admin Notification',
            ],
            'Subscriptions' => [
                'subscription_expiry_warning' => 'Subscription Expiry Warning - Company Notification',
                'subscription_expired' => 'Subscription Expired - Company Notification',
                'subscription_renewed' => 'Subscription Renewed - Company Confirmation',
                'payment_failed' => 'Payment Failed - Company Notification',
                'payment_successful' => 'Payment Successful - Company Receipt',
            ],
            'System' => [
                'new_company_registered' => 'New Company Registered - Admin Notification',
                'company_trial_ending' => 'Company Trial Ending - Admin Notification',
                'system_backup_completed' => 'System Backup Completed - Admin Notification',
                'system_maintenance_scheduled' => 'System Maintenance Scheduled - All Companies',
            ],
        ];
    }
}
