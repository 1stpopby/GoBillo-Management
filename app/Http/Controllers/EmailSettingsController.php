<?php

namespace App\Http\Controllers;

use App\Models\EmailSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmailSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'company.access']);
        
        // Only allow company staff (not operatives) to access email settings
        $this->middleware(function ($request, $next) {
            if (auth()->user()->isOperative()) {
                abort(403, 'Access denied. Only company staff can access email settings.');
            }
            return $next($request);
        });
    }

    /**
     * Display email settings
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get existing email setting for this company or create default
        $emailSetting = EmailSetting::forCompany($user->company_id)->first();
        
        if (!$emailSetting) {
            $emailSetting = new EmailSetting([
                'company_id' => $user->company_id,
                'type' => 'company',
                'name' => 'Company Email Settings',
                'smtp_port' => 587,
                'smtp_encryption' => 'tls',
                'from_name' => $user->company->name,
                'enabled_notifications' => [],
            ]);
        }

        $notificationCategories = EmailSetting::getNotificationsByCategory();
        
        // Remove system notifications for company admin
        unset($notificationCategories['System']);
        unset($notificationCategories['Subscriptions']);

        return view('email-settings.index', compact('emailSetting', 'notificationCategories'));
    }

    /**
     * Store or update email settings
     */
    public function store(Request $request)
    {
        // Only company admins can update email settings
        if (!auth()->user()->isCompanyAdmin()) {
            return redirect()->route('email-settings.index')
                            ->with('error', 'Only Company Administrators can modify email settings.');
        }

        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'smtp_host' => 'required|string|max:255',
            'smtp_port' => 'required|integer|min:1|max:65535',
            'smtp_username' => 'required|string|max:255',
            'smtp_password' => 'nullable|string|max:255',
            'smtp_encryption' => 'required|in:tls,ssl,none',
            'from_email' => 'required|email|max:255',
            'from_name' => 'required|string|max:255',
            'reply_to_email' => 'nullable|email|max:255',
            'reply_to_name' => 'nullable|string|max:255',
            'enabled_notifications' => 'nullable|array',
            'enabled_notifications.*' => 'string',
            'email_signature' => 'nullable|string|max:1000',
            'company_logo_url' => 'nullable|url|max:255',
            'is_active' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->route('email-settings.index')
                            ->withErrors($validator)
                            ->withInput();
        }

        $data = $request->all();
        $data['company_id'] = $user->company_id;
        $data['type'] = 'company';
        
        // Handle encryption field
        if ($data['smtp_encryption'] === 'none') {
            $data['smtp_encryption'] = null;
        }

        // Handle boolean fields
        $data['is_active'] = $request->has('is_active');

        // Find existing setting or create new
        $emailSetting = EmailSetting::forCompany($user->company_id)->first();
        
        if ($emailSetting) {
            // Don't update password if not provided
            if (empty($data['smtp_password'])) {
                unset($data['smtp_password']);
            }
            $emailSetting->update($data);
        } else {
            $emailSetting = EmailSetting::create($data);
        }

        return redirect()->route('email-settings.index')
                        ->with('success', 'Email settings saved successfully!');
    }

    /**
     * Test email configuration
     */
    public function test(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        $user = auth()->user();
        $emailSetting = EmailSetting::forCompany($user->company_id)->first();

        if (!$emailSetting) {
            return response()->json([
                'success' => false,
                'message' => 'No email configuration found. Please save your settings first.',
            ]);
        }

        $result = $emailSetting->testConnection($request->test_email);

        return response()->json($result);
    }

    /**
     * Get email usage statistics
     */
    public function usage()
    {
        $user = auth()->user();
        $emailSetting = EmailSetting::forCompany($user->company_id)->first();

        if (!$emailSetting) {
            return response()->json([
                'emails_sent_today' => 0,
                'emails_sent_month' => 0,
                'last_tested' => null,
                'is_verified' => false,
            ]);
        }

        return response()->json([
            'emails_sent_today' => $emailSetting->emails_sent_today,
            'emails_sent_month' => $emailSetting->emails_sent_month,
            'last_tested' => $emailSetting->last_tested_at?->format('M j, Y g:i A'),
            'is_verified' => $emailSetting->is_verified,
            'test_results' => $emailSetting->test_results,
        ]);
    }

    /**
     * Preview email template
     */
    public function preview(Request $request)
    {
        $request->validate([
            'template_type' => 'required|string',
        ]);

        $templateType = $request->template_type;
        $user = auth()->user();
        
        // Mock data for preview
        $mockData = $this->getMockDataForTemplate($templateType, $user);
        
        try {
            $view = "emails.templates.{$templateType}";
            $html = view($view, $mockData)->render();
            
            return response()->json([
                'success' => true,
                'html' => $html,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Template not found: ' . $templateType,
            ]);
        }
    }

    /**
     * Get mock data for email template preview
     */
    private function getMockDataForTemplate($templateType, $user)
    {
        $company = $user->company;
        
        switch ($templateType) {
            case 'invoice_created':
                return [
                    'invoice' => (object) [
                        'invoice_number' => 'INV-2025-001',
                        'total_amount' => 2500.00,
                        'due_date' => now()->addDays(30)->format('M j, Y'),
                    ],
                    'client' => (object) [
                        'company_name' => 'Sample Client Ltd',
                        'contact_name' => 'John Smith',
                    ],
                    'company' => $company,
                ];
                
            case 'project_variation_created':
                return [
                    'variation' => (object) [
                        'reference' => 'VAR-001',
                        'title' => 'Additional electrical work',
                        'amount' => 850.00,
                        'description' => 'Installation of additional power outlets in office area',
                    ],
                    'project' => (object) [
                        'name' => 'Office Building Construction',
                        'reference' => 'PROJ-2025-001',
                    ],
                    'client' => (object) [
                        'company_name' => 'Sample Client Ltd',
                        'contact_name' => 'John Smith',
                    ],
                    'company' => $company,
                ];
                
            case 'task_assigned':
                return [
                    'task' => (object) [
                        'title' => 'Install electrical fixtures',
                        'description' => 'Install all electrical fixtures in the main office area',
                        'due_date' => now()->addDays(7)->format('M j, Y'),
                        'priority' => 'high',
                    ],
                    'project' => (object) [
                        'name' => 'Office Building Construction',
                    ],
                    'assignee' => (object) [
                        'name' => 'Mike Johnson',
                        'email' => 'mike@example.com',
                    ],
                    'company' => $company,
                ];
                
            default:
                return [
                    'company' => $company,
                    'user' => $user,
                ];
        }
    }
}
