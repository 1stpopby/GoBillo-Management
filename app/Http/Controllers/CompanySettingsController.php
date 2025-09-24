<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\EmailSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;

class CompanySettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'company.access']);
        
        // Only allow company staff (not operatives) to access settings
        $this->middleware(function ($request, $next) {
            if (auth()->user()->isOperative()) {
                abort(403, 'Access denied. Only company staff can access settings.');
            }
            return $next($request);
        });
    }

    /**
     * Display company settings
     */
    public function index()
    {
        $company = auth()->user()->company;
        
        return view('settings.index', compact('company'));
    }

    /**
     * Update company settings
     */
    public function update(Request $request)
    {
        // Only company admins can update settings
        if (!auth()->user()->isCompanyAdmin()) {
            return redirect()->route('settings.index')
                            ->with('error', 'Only Company Administrators can modify settings.');
        }

        $company = auth()->user()->company;

        $request->validate([
            // Basic Company Info
            'name' => 'required|string|max:255',
            'trading_name' => 'nullable|string|max:255',
            'company_number' => 'nullable|string|max:50',
            'vat_number' => 'nullable|string|max:50',
            'utr_number' => 'nullable|string|max:50',
            'business_type' => 'nullable|string|max:100',
            'incorporation_date' => 'nullable|date',
            'industry_sector' => 'nullable|string|max:100',

            // Contact Details
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'primary_contact_name' => 'nullable|string|max:255',
            'primary_contact_title' => 'nullable|string|max:100',
            'primary_contact_email' => 'nullable|email|max:255',
            'primary_contact_phone' => 'nullable|string|max:20',
            'secondary_contact_name' => 'nullable|string|max:255',
            'secondary_contact_title' => 'nullable|string|max:100',
            'secondary_contact_email' => 'nullable|email|max:255',
            'secondary_contact_phone' => 'nullable|string|max:20',

            // Addresses
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:2',
            'registered_address' => 'nullable|string|max:500',
            'registered_city' => 'nullable|string|max:100',
            'registered_state' => 'nullable|string|max:100',
            'registered_zip_code' => 'nullable|string|max:20',
            'registered_country' => 'nullable|string|max:2',

            // Banking
            'bank_name' => 'nullable|string|max:255',
            'bank_account_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_sort_code' => 'nullable|string|max:10',
            'iban' => 'nullable|string|max:50',
            'swift_code' => 'nullable|string|max:20',

            // Insurance
            'public_liability_insurer' => 'nullable|string|max:255',
            'public_liability_policy_number' => 'nullable|string|max:100',
            'public_liability_expiry' => 'nullable|date',
            'public_liability_amount' => 'nullable|numeric|min:0',
            'employers_liability_insurer' => 'nullable|string|max:255',
            'employers_liability_policy_number' => 'nullable|string|max:100',
            'employers_liability_expiry' => 'nullable|date',
            'employers_liability_amount' => 'nullable|numeric|min:0',

            // Health & Safety
            'health_safety_policy_date' => 'nullable|date',
            'risk_assessment_policy_date' => 'nullable|date',

            // Certifications
            'construction_line_number' => 'nullable|string|max:100',
            'construction_line_expiry' => 'nullable|date',
            'chas_number' => 'nullable|string|max:100',
            'chas_expiry' => 'nullable|date',
            'safe_contractor_number' => 'nullable|string|max:100',
            'safe_contractor_expiry' => 'nullable|date',

            // Descriptions
            'description' => 'nullable|string',
            'business_description' => 'nullable|string',

            // Settings
            'timezone' => 'required|string|max:50',
            'currency' => 'required|string|max:3',

            // Membership
            'subscription_plan' => 'nullable|string|max:100',
            'subscription_status' => 'nullable|string|max:50',
            'subscription_start_date' => 'nullable|date',
            'subscription_end_date' => 'nullable|date|after:subscription_start_date',
            'billing_cycle' => 'nullable|in:monthly,quarterly,annually',
            'monthly_fee' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|in:credit_card,debit_card,bank_transfer,direct_debit',
            'next_billing_date' => 'nullable|date',
            'billing_contact_email' => 'nullable|email|max:255',
            'premium_support' => 'nullable|boolean',
            'advanced_reporting' => 'nullable|boolean',
            'api_access' => 'nullable|boolean',

            // File uploads
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'health_safety_policy' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'risk_assessment_policy' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $data = $request->except(['logo', 'health_safety_policy', 'risk_assessment_policy', 'services_offered']);

        // Handle services offered
        $servicesOffered = [];
        if ($request->has('services_offered')) {
            $servicesOffered = array_filter($request->input('services_offered', []));
        }
        $data['services_offered'] = $servicesOffered;

        // Handle boolean fields
        $data['is_vat_registered'] = $request->has('is_vat_registered');
        $data['is_cis_registered'] = $request->has('is_cis_registered');
        $data['gdpr_compliant'] = $request->has('gdpr_compliant');
        
        if ($data['gdpr_compliant'] && !$company->gdpr_compliant) {
            $data['gdpr_compliance_date'] = now()->toDateString();
        }

        // Handle file uploads
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            
            $logoPath = $request->file('logo')->store('company-logos', 'public');
            $data['logo'] = $logoPath;
        }

        if ($request->hasFile('health_safety_policy')) {
            // Delete old file
            if ($company->health_safety_policy) {
                Storage::disk('public')->delete($company->health_safety_policy);
            }
            
            $policyPath = $request->file('health_safety_policy')->store('company-policies', 'public');
            $data['health_safety_policy'] = $policyPath;
        }

        if ($request->hasFile('risk_assessment_policy')) {
            // Delete old file
            if ($company->risk_assessment_policy) {
                Storage::disk('public')->delete($company->risk_assessment_policy);
            }
            
            $policyPath = $request->file('risk_assessment_policy')->store('company-policies', 'public');
            $data['risk_assessment_policy'] = $policyPath;
        }

        // Update company
        $company->update($data);

        // Handle email settings if provided
        $this->handleEmailSettings($request, $company);

        return redirect()->route('settings.index')
                        ->with('success', 'Company settings updated successfully.');
    }

    /**
     * Handle email settings submission
     */
    private function handleEmailSettings(Request $request, Company $company)
    {
        // Check if email settings data is provided
        if (!$request->has('smtp_host')) {
            return;
        }

        // Validate email settings
        $emailValidator = Validator::make($request->all(), [
            'smtp_host' => 'required|string|max:255',
            'smtp_port' => 'required|integer|min:1|max:65535',
            'smtp_username' => 'required|string|max:255',
            'smtp_password' => 'nullable|string|max:255',
            'smtp_encryption' => 'nullable|in:tls,ssl',
            'from_email' => 'required|email|max:255',
            'from_name' => 'required|string|max:255',
            'reply_to_email' => 'nullable|email|max:255',
            'reply_to_name' => 'nullable|string|max:255',
            'enabled_notifications' => 'nullable|array',
            'enabled_notifications.*' => 'string',
            'email_signature' => 'nullable|string|max:1000',
            'logo_url' => 'nullable|url|max:500',
        ]);

        if ($emailValidator->fails()) {
            return redirect()->route('settings.index')
                ->withErrors($emailValidator, 'email_settings')
                ->withInput();
        }

        // Find or create email setting
        $emailSetting = $company->emailSettings()->first();
        if (!$emailSetting) {
            $emailSetting = new EmailSetting();
            $emailSetting->company_id = $company->id;
            $emailSetting->type = 'company';
            $emailSetting->name = $company->name . ' Email Settings';
        }

        // Prepare data
        $emailData = [
            'smtp_host' => $request->smtp_host,
            'smtp_port' => $request->smtp_port,
            'smtp_username' => $request->smtp_username,
            'smtp_encryption' => $request->smtp_encryption,
            'from_email' => $request->from_email,
            'from_name' => $request->from_name,
            'reply_to_email' => $request->reply_to_email,
            'reply_to_name' => $request->reply_to_name,
            'enabled_notifications' => json_encode($request->enabled_notifications ?? []),
            'email_signature' => $request->email_signature,
            'logo_url' => $request->logo_url,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'type' => 'company',
            'name' => $company->name . ' Email Settings',
        ];

        // Handle password (only update if provided)
        if ($request->filled('smtp_password')) {
            $emailData['smtp_password'] = $request->smtp_password;
        }

        // Update or create email setting
        $emailSetting->fill($emailData);
        $emailSetting->save();
    }

    /**
     * Upload company logo
     */
    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $company = auth()->user()->company;

        // Delete old logo
        if ($company->logo) {
            Storage::disk('public')->delete($company->logo);
        }

        // Store new logo
        $logoPath = $request->file('logo')->store('company-logos', 'public');
        $company->update(['logo' => $logoPath]);

        return response()->json([
            'success' => true,
            'logo_url' => Storage::url($logoPath),
            'message' => 'Logo uploaded successfully.'
        ]);
    }

    /**
     * Remove company logo
     */
    public function removeLogo()
    {
        $company = auth()->user()->company;

        if ($company->logo) {
            Storage::disk('public')->delete($company->logo);
            $company->update(['logo' => null]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Logo removed successfully.'
        ]);
    }

    /**
     * Test email configuration
     */
    public function testEmail(Request $request)
    {
        $request->validate([
            'smtp_host' => 'required|string',
            'smtp_port' => 'required|integer',
            'smtp_username' => 'required|string',
            'smtp_password' => 'required|string',
            'smtp_encryption' => 'nullable|string',
            'from_email' => 'required|email',
            'from_name' => 'required|string',
        ]);

        try {
            // Use the EmailSettingsController logic
            $emailController = new \App\Http\Controllers\EmailSettingsController();
            return $emailController->test($request);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get email usage statistics
     */
    public function emailUsage()
    {
        try {
            $emailController = new \App\Http\Controllers\EmailSettingsController();
            return $emailController->usage();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load usage statistics'
            ]);
        }
    }

    /**
     * Preview email template
     */
    public function previewEmail(Request $request)
    {
        try {
            $emailController = new \App\Http\Controllers\EmailSettingsController();
            return $emailController->preview($request);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate preview'
            ]);
        }
    }
}