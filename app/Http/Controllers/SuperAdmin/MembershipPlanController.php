<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MembershipPlanController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user() || !auth()->user()->isSuperAdmin()) {
                abort(403, 'Access denied: SuperAdmin only.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $plans = MembershipPlan::withCount(['companies', 'subscriptions'])
            ->ordered()
            ->get();

        return view('superadmin.plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('superadmin.plans.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:membership_plans',
            'description' => 'nullable|string',
            'monthly_price' => 'required|numeric|min:0',
            'yearly_price' => 'required|numeric|min:0',
            'setup_fee' => 'nullable|numeric|min:0',
            'max_users' => 'required|integer|min:0',
            'max_sites' => 'required|integer|min:0',
            'max_projects' => 'required|integer|min:0',
            'max_storage_gb' => 'required|integer|min:1',
            'trial_days' => 'required|integer|min:0|max:365',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = $request->all();
        
        // Convert feature checkboxes to booleans
        $features = [
            'has_time_tracking',
            'has_invoicing', 
            'has_reporting',
            'has_api_access',
            'has_white_label',
            'has_advanced_permissions',
            'has_custom_fields',
            'has_integrations',
            'has_priority_support',
            'is_active',
            'is_featured',
            'is_trial_available',
        ];

        foreach ($features as $feature) {
            $data[$feature] = $request->has($feature);
        }

        MembershipPlan::create($data);

        return redirect()->route('superadmin.plans.index')
                        ->with('success', 'Membership plan created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MembershipPlan $plan)
    {
        $plan->loadCount(['companies', 'subscriptions']);
        
        return view('superadmin.plans.show', compact('plan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MembershipPlan $plan)
    {
        return view('superadmin.plans.edit', compact('plan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MembershipPlan $plan)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:255', Rule::unique('membership_plans')->ignore($plan)],
            'description' => 'nullable|string',
            'monthly_price' => 'required|numeric|min:0',
            'yearly_price' => 'required|numeric|min:0',
            'setup_fee' => 'nullable|numeric|min:0',
            'max_users' => 'required|integer|min:0',
            'max_sites' => 'required|integer|min:0',
            'max_projects' => 'required|integer|min:0',
            'max_storage_gb' => 'required|integer|min:1',
            'trial_days' => 'required|integer|min:0|max:365',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = $request->all();
        
        // Convert feature checkboxes to booleans
        $features = [
            'has_time_tracking',
            'has_invoicing', 
            'has_reporting',
            'has_api_access',
            'has_white_label',
            'has_advanced_permissions',
            'has_custom_fields',
            'has_integrations',
            'has_priority_support',
            'is_active',
            'is_featured',
            'is_trial_available',
        ];

        foreach ($features as $feature) {
            $data[$feature] = $request->has($feature);
        }

        $plan->update($data);

        return redirect()->route('superadmin.plans.index')
                        ->with('success', 'Membership plan updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MembershipPlan $plan)
    {
        if ($plan->companies()->count() > 0) {
            return redirect()->route('superadmin.plans.index')
                            ->with('error', 'Cannot delete plan with active companies.');
        }

        $plan->delete();

        return redirect()->route('superadmin.plans.index')
                        ->with('success', 'Membership plan deleted successfully.');
    }

    /**
     * Toggle plan status
     */
    public function toggleStatus(MembershipPlan $plan)
    {
        $plan->update(['is_active' => !$plan->is_active]);

        $status = $plan->is_active ? 'activated' : 'deactivated';
        
        return redirect()->route('superadmin.plans.index')
                        ->with('success', "Plan {$status} successfully.");
    }
}