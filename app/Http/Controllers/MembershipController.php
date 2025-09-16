<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class MembershipController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (Auth::user()->isOperative()) {
                abort(403, 'Access denied. Operatives cannot access membership settings.');
            }
            return $next($request);
        });
    }

    /**
     * Display the membership page.
     */
    public function index(): View
    {
        $company = Auth::user()->company;
        
        // Calculate usage statistics
        $userUsage = [
            'current' => $company->users()->count(),
            'limit' => $this->getUserLimit($company->subscription_plan ?? 'trial'),
            'percentage' => $this->calculateUsagePercentage(
                $company->users()->count(),
                $this->getUserLimit($company->subscription_plan ?? 'trial')
            )
        ];

        $projectUsage = [
            'current' => $company->projects()->count(),
            'limit' => $this->getProjectLimit($company->subscription_plan ?? 'trial'),
            'percentage' => $this->calculateUsagePercentage(
                $company->projects()->count(),
                $this->getProjectLimit($company->subscription_plan ?? 'trial')
            )
        ];

        $availablePlans = self::getAvailablePlans();
        
        return view('membership.index', compact('company', 'userUsage', 'projectUsage', 'availablePlans'));
    }

    /**
     * Update membership settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $company = Auth::user()->company;

        // Only allow company admins to update membership settings
        if (!Auth::user()->isCompanyAdmin()) {
            return redirect()->back()->with('error', 'Only company administrators can update membership settings.');
        }

        $validatedData = $request->validate([
            'billing_contact_email' => 'nullable|email|max:255',
            'premium_support' => 'nullable|boolean',
            'advanced_reporting' => 'nullable|boolean',
            'api_access' => 'nullable|boolean',
        ]);

        // Handle checkbox values
        $validatedData['premium_support'] = $request->has('premium_support');
        $validatedData['advanced_reporting'] = $request->has('advanced_reporting');
        $validatedData['api_access'] = $request->has('api_access');

        $company->update($validatedData);

        return redirect()->back()->with('success', 'Membership settings updated successfully!');
    }

    /**
     * Get user limit from company record (set by SuperAdmin).
     */
    private function getUserLimit(string $plan): int
    {
        return Auth::user()->company->max_users ?? 5;
    }

    /**
     * Get project limit from company record (set by SuperAdmin).
     */
    private function getProjectLimit(string $plan): int
    {
        return Auth::user()->company->max_projects ?? 10;
    }

    /**
     * Get available subscription plans.
     */
    public static function getAvailablePlans(): array
    {
        return [
            'trial' => [
                'name' => 'Trial',
                'description' => '30-day free trial',
                'price' => 0,
                'features' => [
                    'Limited users (set by admin)',
                    'Limited projects (set by admin)',
                    'Basic support',
                    '30 days trial period'
                ]
            ],
            'basic' => [
                'name' => 'Basic',
                'description' => 'Perfect for small teams',
                'price' => 49,
                'features' => [
                    'User limit set by admin',
                    'Project limit set by admin', 
                    'Email support',
                    'Basic reporting'
                ]
            ],
            'professional' => [
                'name' => 'Professional',
                'description' => 'Best for growing businesses',
                'price' => 99,
                'features' => [
                    'User limit set by admin',
                    'Project limit set by admin',
                    'Priority support',
                    'Advanced reporting',
                    'API access'
                ]
            ],
            'enterprise' => [
                'name' => 'Enterprise',
                'description' => 'For large organizations',
                'price' => 199,
                'features' => [
                    'User limit set by admin',
                    'Project limit set by admin',
                    '24/7 dedicated support',
                    'Custom reporting',
                    'Full API access',
                    'Custom integrations'
                ]
            ]
        ];
    }

    /**
     * Calculate usage percentage.
     */
    private function calculateUsagePercentage(int $current, int $limit): int
    {
        if ($limit >= 999999) { // Unlimited
            return 0;
        }
        
        return $limit > 0 ? min(100, intval(($current / $limit) * 100)) : 0;
    }
}
