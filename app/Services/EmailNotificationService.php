<?php

namespace App\Services;

use App\Models\EmailSetting;
use App\Models\Company;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class EmailNotificationService
{
    /**
     * Send an email notification
     */
    public function send(string $notificationType, array $data, array $recipients, ?int $companyId = null)
    {
        try {
            // Get appropriate email configuration
            $emailSetting = $this->getEmailConfiguration($companyId);
            
            if (!$emailSetting) {
                Log::warning("No email configuration found for notification: {$notificationType}");
                return false;
            }

            // Check if notification is enabled
            if (!$emailSetting->isNotificationEnabled($notificationType)) {
                Log::info("Notification {$notificationType} is disabled");
                return false;
            }

            // Configure mail with the email setting
            $emailSetting->configureMailer();

            // Get email template
            $template = $this->getTemplate($notificationType);
            if (!$template) {
                Log::error("Email template not found for notification: {$notificationType}");
                return false;
            }

            // Send email to each recipient
            $sent = 0;
            $failed = 0;

            foreach ($recipients as $recipient) {
                try {
                    $emailData = array_merge($data, [
                        'recipient' => $recipient,
                        'company' => $companyId ? Company::find($companyId) : null,
                    ]);

                    Mail::send($template, $emailData, function ($message) use ($recipient, $notificationType) {
                        $message->to($recipient['email'], $recipient['name'] ?? '')
                                ->subject($this->getSubject($notificationType, $recipient));
                    });

                    $sent++;
                    
                } catch (\Exception $e) {
                    Log::error("Failed to send email to {$recipient['email']}: " . $e->getMessage());
                    $failed++;
                }
            }

            // Update usage statistics
            if ($sent > 0) {
                $emailSetting->emails_sent_today += $sent;
                $emailSetting->emails_sent_month += $sent;
                $emailSetting->save();
            }

            Log::info("Email notification {$notificationType}: {$sent} sent, {$failed} failed");
            
            return [
                'success' => $sent > 0,
                'sent' => $sent,
                'failed' => $failed,
            ];

        } catch (\Exception $e) {
            Log::error("Email notification service error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send invoice created notification
     */
    public function sendInvoiceCreated($invoice, $client, ?int $companyId = null)
    {
        return $this->send('invoice_created', [
            'invoice' => $invoice,
            'client' => $client,
        ], [
            ['email' => $client->email, 'name' => $client->name]
        ], $companyId);
    }

    /**
     * Send project variation notification
     */
    public function sendProjectVariationCreated($variation, $project, $client, ?int $companyId = null)
    {
        return $this->send('project_variation_created', [
            'variation' => $variation,
            'project' => $project,
            'client' => $client,
        ], [
            ['email' => $client->email, 'name' => $client->name]
        ], $companyId);
    }

    /**
     * Send task assigned notification
     */
    public function sendTaskAssigned($task, $project, $assignee, ?int $companyId = null)
    {
        return $this->send('task_assigned', [
            'task' => $task,
            'project' => $project,
            'assignee' => $assignee,
        ], [
            ['email' => $assignee->email, 'name' => $assignee->name]
        ], $companyId);
    }

    /**
     * Send subscription expiry warning (system notification)
     */
    public function sendSubscriptionExpiryWarning($company, $subscription)
    {
        return $this->send('subscription_expiry_warning', [
            'company' => $company,
            'subscription' => $subscription,
            'renewal_url' => route('billing.renew', $subscription->id),
        ], [
            ['email' => $company->email, 'name' => $company->name]
        ]);
    }

    /**
     * Send operative invoice notifications
     */
    public function sendOperativeInvoiceSubmitted($invoice, $manager, ?int $companyId = null)
    {
        return $this->send('operative_invoice_submitted', [
            'invoice' => $invoice,
            'operative' => $invoice->operative,
            'manager' => $manager,
        ], [
            ['email' => $manager->email, 'name' => $manager->name]
        ], $companyId);
    }

    public function sendOperativeInvoiceApproved($invoice, ?int $companyId = null)
    {
        return $this->send('operative_invoice_approved', [
            'invoice' => $invoice,
            'operative' => $invoice->operative,
        ], [
            ['email' => $invoice->operative->email, 'name' => $invoice->operative->name]
        ], $companyId);
    }

    public function sendOperativeInvoiceRejected($invoice, $reason, ?int $companyId = null)
    {
        return $this->send('operative_invoice_rejected', [
            'invoice' => $invoice,
            'operative' => $invoice->operative,
            'reason' => $reason,
        ], [
            ['email' => $invoice->operative->email, 'name' => $invoice->operative->name]
        ], $companyId);
    }

    /**
     * Get appropriate email configuration
     */
    protected function getEmailConfiguration(?int $companyId = null): ?EmailSetting
    {
        if ($companyId) {
            return EmailSetting::getActiveForCompany($companyId);
        }
        
        return EmailSetting::getSystemDefault();
    }

    /**
     * Get email template path
     */
    protected function getTemplate(string $notificationType): ?string
    {
        $template = "emails.templates.{$notificationType}";
        
        if (View::exists($template)) {
            return $template;
        }
        
        return null;
    }

    /**
     * Get email subject based on notification type
     */
    protected function getSubject(string $notificationType, array $recipient): string
    {
        $subjects = [
            'invoice_created' => 'New Invoice Created - Action Required',
            'invoice_sent' => 'Invoice Sent - Payment Due',
            'invoice_paid' => 'Payment Received - Thank You',
            'invoice_overdue' => 'Invoice Overdue - Immediate Action Required',
            
            'project_variation_created' => 'Project Variation - Approval Required',
            'project_variation_approved' => 'Project Variation Approved',
            'project_completed' => 'Project Completed Successfully',
            
            'operative_invoice_submitted' => 'New Operative Invoice - Review Required',
            'operative_invoice_approved' => 'Your Invoice Has Been Approved',
            'operative_invoice_rejected' => 'Invoice Rejected - Action Required',
            
            'task_assigned' => 'New Task Assigned',
            'task_completed' => 'Task Completed',
            'task_overdue' => 'Task Overdue - Attention Required',
            
            'document_expiry_warning' => 'Document Expiring Soon',
            'insurance_expiry_warning' => 'Insurance Expiring Soon',
            
            'subscription_expiry_warning' => 'Subscription Expiring Soon - Action Required',
            'subscription_expired' => 'Subscription Expired',
            'subscription_renewed' => 'Subscription Renewed Successfully',
            'payment_failed' => 'Payment Failed - Action Required',
            'payment_successful' => 'Payment Successful - Thank You',
            
            'new_company_registered' => 'New Company Registration',
            'company_trial_ending' => 'Company Trial Ending',
            'system_backup_completed' => 'System Backup Completed',
            'system_maintenance_scheduled' => 'Scheduled Maintenance Notification',
        ];

        return $subjects[$notificationType] ?? 'Notification from ProMax Team';
    }

    /**
     * Queue email notification for later processing
     */
    public function queue(string $notificationType, array $data, array $recipients, ?int $companyId = null)
    {
        // This would typically dispatch a job to the queue
        // For now, we'll just send immediately
        return $this->send($notificationType, $data, $recipients, $companyId);
    }

    /**
     * Test email configuration
     */
    public function testConfiguration(?int $companyId = null, string $testEmail = null): array
    {
        $emailSetting = $this->getEmailConfiguration($companyId);
        
        if (!$emailSetting) {
            return [
                'success' => false,
                'message' => 'No email configuration found',
            ];
        }

        return $emailSetting->testConnection($testEmail);
    }

    /**
     * Get email usage statistics
     */
    public function getUsageStats(?int $companyId = null): array
    {
        $emailSetting = $this->getEmailConfiguration($companyId);
        
        if (!$emailSetting) {
            return [
                'emails_sent_today' => 0,
                'emails_sent_month' => 0,
                'is_verified' => false,
            ];
        }

        return [
            'emails_sent_today' => $emailSetting->emails_sent_today,
            'emails_sent_month' => $emailSetting->emails_sent_month,
            'is_verified' => $emailSetting->is_verified,
            'last_tested' => $emailSetting->last_tested_at,
        ];
    }
}
