<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\EmailSetting;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmailSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        
        // Only allow superadmin access
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isSuperAdmin()) {
                abort(403, 'Access denied. SuperAdmin access required.');
            }
            return $next($request);
        });
    }

    /**
     * Display system email settings
     */
    public function index()
    {
        // Get system email setting
        $emailSetting = EmailSetting::system()->first();
        
        if (!$emailSetting) {
            $emailSetting = new EmailSetting([
                'company_id' => null,
                'type' => 'system',
                'name' => 'System Email Settings',
                'smtp_port' => 587,
                'smtp_encryption' => 'tls',
                'from_name' => 'GoBillo System',
                'enabled_notifications' => [],
            ]);
        }

        $notificationCategories = EmailSetting::getNotificationsByCategory();
        
        // Only show system and subscription notifications for SuperAdmin
        $superAdminCategories = [
            'Subscriptions' => $notificationCategories['Subscriptions'],
            'System' => $notificationCategories['System'],
        ];

        // Get email usage statistics
        $totalCompanies = Company::count();
        $activeCompanies = Company::where('status', 'active')->count();

        return view('superadmin.email-settings.index', compact(
            'emailSetting', 
            'superAdminCategories',
            'totalCompanies',
            'activeCompanies'
        ));
    }

    /**
     * Store or update system email settings
     */
    public function store(Request $request)
    {
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
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('superadmin.email-settings.index')
                            ->withErrors($validator)
                            ->withInput();
        }

        $data = $request->all();
        $data['company_id'] = null;
        $data['type'] = 'system';
        
        // Handle encryption field
        if ($data['smtp_encryption'] === 'none') {
            $data['smtp_encryption'] = null;
        }

        // Handle boolean fields
        $data['is_active'] = $request->has('is_active');

        // Find existing setting or create new
        $emailSetting = EmailSetting::system()->first();
        
        if ($emailSetting) {
            // Don't update password if not provided
            if (empty($data['smtp_password'])) {
                unset($data['smtp_password']);
            }
            $emailSetting->update($data);
        } else {
            $emailSetting = EmailSetting::create($data);
        }

        return redirect()->route('superadmin.email-settings.index')
                        ->with('success', 'System email settings saved successfully!');
    }

    /**
     * Test system email configuration
     */
    public function test(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        $emailSetting = EmailSetting::system()->first();

        if (!$emailSetting) {
            return response()->json([
                'success' => false,
                'message' => 'No system email configuration found. Please save your settings first.',
            ]);
        }

        $result = $emailSetting->testConnection($request->test_email);

        return response()->json($result);
    }

    /**
     * Get system email usage statistics
     */
    public function usage()
    {
        $emailSetting = EmailSetting::system()->first();

        if (!$emailSetting) {
            return response()->json([
                'system_emails_sent_today' => 0,
                'system_emails_sent_month' => 0,
                'company_emails_sent_today' => 0,
                'company_emails_sent_month' => 0,
                'active_company_configs' => 0,
                'avg_emails_per_company' => 0,
                'last_tested' => null,
                'is_verified' => false,
            ]);
        }

        // Get additional system stats
        $companyEmailStats = EmailSetting::company()
            ->selectRaw('
                SUM(emails_sent_today) as total_today,
                SUM(emails_sent_month) as total_month,
                COUNT(*) as active_companies,
                AVG(emails_sent_month) as avg_per_company
            ')
            ->where('is_active', true)
            ->first();

        return response()->json([
            'system_emails_sent_today' => $emailSetting->emails_sent_today,
            'system_emails_sent_month' => $emailSetting->emails_sent_month,
            'company_emails_sent_today' => $companyEmailStats->total_today ?? 0,
            'company_emails_sent_month' => $companyEmailStats->total_month ?? 0,
            'active_company_configs' => $companyEmailStats->active_companies ?? 0,
            'avg_emails_per_company' => round($companyEmailStats->avg_per_company ?? 0, 1),
            'last_tested' => $emailSetting->last_tested_at?->format('M j, Y g:i A'),
            'is_verified' => $emailSetting->is_verified,
            'test_results' => $emailSetting->test_results,
        ]);
    }

    /**
     * Get company email settings overview
     */
    public function companyOverview()
    {
        $companies = Company::with(['emailSettings' => function($query) {
            $query->where('is_active', true);
        }])
        ->select('id', 'name', 'email', 'status')
        ->get()
        ->map(function($company) {
            $emailSetting = $company->emailSettings->first();
            
            return [
                'id' => $company->id,
                'name' => $company->name,
                'email' => $company->email,
                'status' => $company->status,
                'has_email_config' => !is_null($emailSetting),
                'email_verified' => $emailSetting ? $emailSetting->is_verified : false,
                'emails_sent_month' => $emailSetting ? $emailSetting->emails_sent_month : 0,
                'last_tested' => $emailSetting && $emailSetting->last_tested_at 
                    ? $emailSetting->last_tested_at->format('M j, Y') 
                    : null,
            ];
        });

        return response()->json($companies);
    }
}
