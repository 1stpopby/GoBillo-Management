<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class CompanyRegistrationController extends Controller
{
    /**
     * Show the get started page
     */
    public function showGetStarted()
    {
        $subscriptionPlans = $this->getSubscriptionPlans();
        return view('get-started', compact('subscriptionPlans'));
    }

    /**
     * Get available subscription plans
     */
    private function getSubscriptionPlans()
    {
        return [
            'trial' => [
                'name' => 'Free Trial',
                'price' => 0,
                'period' => '30 days',
                'description' => 'Perfect for trying out ProMax Team',
                'features' => [
                    'Up to 5 users',
                    'Up to 10 projects',
                    'Basic project management',
                    'Email support',
                    'Mobile app access'
                ],
                'max_users' => 5,
                'max_projects' => 10,
                'popular' => false,
                'button_text' => 'Start Free Trial',
                'button_class' => 'btn-outline-primary'
            ],
            'basic' => [
                'name' => 'Basic',
                'price' => 29,
                'period' => 'month',
                'description' => 'Great for small teams',
                'features' => [
                    'Up to 15 users',
                    'Up to 50 projects',
                    'Full project management',
                    'Time tracking',
                    'Basic reporting',
                    'Email support'
                ],
                'max_users' => 15,
                'max_projects' => 50,
                'popular' => false,
                'button_text' => 'Get Started',
                'button_class' => 'btn-outline-primary'
            ],
            'professional' => [
                'name' => 'Professional',
                'price' => 49,
                'period' => 'month',
                'description' => 'Perfect for growing businesses',
                'features' => [
                    'Unlimited users',
                    'Unlimited projects',
                    'Advanced project management',
                    'Time tracking & invoicing',
                    'Advanced reporting & analytics',
                    'Client portal',
                    'Priority support',
                    'API access'
                ],
                'max_users' => -1, // unlimited
                'max_projects' => -1, // unlimited
                'popular' => true,
                'button_text' => 'Get Started',
                'button_class' => 'btn-primary'
            ],
            'enterprise' => [
                'name' => 'Enterprise',
                'price' => null, // custom pricing
                'period' => 'custom',
                'description' => 'For large organizations',
                'features' => [
                    'Everything in Professional',
                    'Custom integrations',
                    'Dedicated account manager',
                    'Advanced security features',
                    'Custom training',
                    'SLA guarantees',
                    '24/7 phone support'
                ],
                'max_users' => -1, // unlimited
                'max_projects' => -1, // unlimited
                'popular' => false,
                'button_text' => 'Contact Sales',
                'button_class' => 'btn-outline-primary'
            ]
        ];
    }

    /**
     * Handle company registration
     */
    public function register(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255|unique:companies,name',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'terms' => 'required|accepted',
        ], [
            'company_name.unique' => 'A company with this name already exists.',
            'email.unique' => 'An account with this email already exists.',
            'terms.accepted' => 'You must agree to the Terms of Service and Privacy Policy.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        try {
            DB::beginTransaction();

            // Create the company
            $company = Company::create([
                'name' => $request->company_name,
                'slug' => Str::slug($request->company_name),
                'email' => $request->email,
                'phone' => $request->phone,
                'primary_contact_name' => $request->first_name . ' ' . $request->last_name,
                'primary_contact_email' => $request->email,
                'primary_contact_phone' => $request->phone,
                'status' => 'active',
                'subscription_plan' => 'trial',
                'trial_ends_at' => now()->addDays(30),
                'max_users' => 5, // Default trial limits
                'max_projects' => 10,
                'timezone' => 'UTC',
                'currency' => 'USD',
                'is_vat_registered' => false,
                'is_cis_registered' => false,
                'gdpr_compliant' => true,
                'gdpr_compliance_date' => now(),
            ]);

            // Create the company admin user
            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'company_id' => $company->id,
                'role' => User::ROLE_COMPANY_ADMIN,
                'is_active' => true,
                'email_verified_at' => now(), // Auto-verify for now
            ]);

            DB::commit();

            // Log the user in
            auth()->login($user);

            return redirect()->route('company.welcome')
                ->with('success', 'Welcome to ProMax Team! Your account has been created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'There was an error creating your account. Please try again.')
                ->withInput($request->except('password', 'password_confirmation'));
        }
    }

    /**
     * Show the welcome page after registration
     */
    public function showWelcome()
    {
        if (!auth()->check() || !auth()->user()->isCompanyAdmin()) {
            return redirect()->route('get-started');
        }

        $company = auth()->user()->company;
        
        return view('company.welcome', compact('company'));
    }

    /**
     * Complete the onboarding process
     */
    public function completeOnboarding(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
            'business_type' => 'nullable|string|max:100',
            'industry_sector' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $company = auth()->user()->company;
            
            $company->update([
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'zip_code' => $request->zip_code,
                'country' => $request->country ?? 'United States',
                'website' => $request->website,
                'business_type' => $request->business_type,
                'industry_sector' => $request->industry_sector,
                'description' => $request->description,
            ]);

            return redirect()->route('dashboard')
                ->with('success', 'Welcome to ProMax Team! Your company profile has been completed.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'There was an error updating your company profile. Please try again.')
                ->withInput();
        }
    }
}