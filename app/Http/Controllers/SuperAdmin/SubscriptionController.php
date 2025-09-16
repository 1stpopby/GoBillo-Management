<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Company;
use App\Models\MembershipPlan;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
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
    public function index(Request $request)
    {
        $query = Subscription::with(['company', 'membershipPlan']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('plan')) {
            $query->whereHas('membershipPlan', function ($q) use ($request) {
                $q->where('slug', $request->plan);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('company', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $subscriptions = $query->latest()->paginate(15);

        // Get filter options
        $plans = MembershipPlan::active()->ordered()->get();
        $statuses = ['active', 'trial', 'cancelled', 'past_due', 'suspended'];

        return view('superadmin.subscriptions.index', compact('subscriptions', 'plans', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companies = Company::whereDoesntHave('subscriptions', function ($query) {
            $query->whereIn('status', ['active', 'trial']);
        })->orderBy('name')->get();

        $plans = MembershipPlan::active()->ordered()->get();

        return view('superadmin.subscriptions.create', compact('companies', 'plans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'membership_plan_id' => 'required|exists:membership_plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
            'status' => 'required|in:trial,active',
            'starts_at' => 'required|date',
            'trial_ends_at' => 'nullable|date|after:starts_at',
        ]);

        $company = Company::findOrFail($request->company_id);
        $plan = MembershipPlan::findOrFail($request->membership_plan_id);

        // Check if company already has an active subscription
        $existingSubscription = $company->subscriptions()
            ->whereIn('status', ['active', 'trial'])
            ->first();

        if ($existingSubscription) {
            return redirect()->back()
                            ->withErrors(['company_id' => 'Company already has an active subscription.']);
        }

        $amount = $request->billing_cycle === 'yearly' ? $plan->yearly_price : $plan->monthly_price;
        
        $subscriptionData = [
            'company_id' => $request->company_id,
            'membership_plan_id' => $request->membership_plan_id,
            'status' => $request->status,
            'billing_cycle' => $request->billing_cycle,
            'starts_at' => $request->starts_at,
            'amount' => $amount,
            'setup_fee' => $plan->setup_fee,
            'currency' => 'GBP',
        ];

        if ($request->status === 'trial' && $request->trial_ends_at) {
            $subscriptionData['trial_ends_at'] = $request->trial_ends_at;
        }

        if ($request->status === 'active') {
            $nextBilling = $request->billing_cycle === 'yearly' 
                ? now()->addYear() 
                : now()->addMonth();
            $subscriptionData['next_billing_date'] = $nextBilling;
        }

        $subscription = Subscription::create($subscriptionData);
        $subscription->updateUsage();

        return redirect()->route('superadmin.subscriptions.index')
                        ->with('success', 'Subscription created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Subscription $subscription)
    {
        $subscription->load(['company', 'membershipPlan']);
        
        return view('superadmin.subscriptions.show', compact('subscription'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subscription $subscription)
    {
        $plans = MembershipPlan::active()->ordered()->get();
        
        return view('superadmin.subscriptions.edit', compact('subscription', 'plans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subscription $subscription)
    {
        $request->validate([
            'membership_plan_id' => 'required|exists:membership_plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
            'status' => 'required|in:trial,active,cancelled,suspended',
            'notes' => 'nullable|string',
        ]);

        $plan = MembershipPlan::findOrFail($request->membership_plan_id);
        $amount = $request->billing_cycle === 'yearly' ? $plan->yearly_price : $plan->monthly_price;

        $subscription->update([
            'membership_plan_id' => $request->membership_plan_id,
            'billing_cycle' => $request->billing_cycle,
            'status' => $request->status,
            'amount' => $amount,
            'notes' => $request->notes,
        ]);

        $subscription->updateUsage();

        return redirect()->route('superadmin.subscriptions.index')
                        ->with('success', 'Subscription updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subscription $subscription)
    {
        $subscription->delete();

        return redirect()->route('superadmin.subscriptions.index')
                        ->with('success', 'Subscription deleted successfully.');
    }

    /**
     * Cancel subscription
     */
    public function cancel(Subscription $subscription, Request $request)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $subscription->cancel($request->reason);

        return redirect()->route('superadmin.subscriptions.index')
                        ->with('success', 'Subscription cancelled successfully.');
    }

    /**
     * Reactivate subscription
     */
    public function reactivate(Subscription $subscription)
    {
        $subscription->reactivate();

        return redirect()->route('superadmin.subscriptions.index')
                        ->with('success', 'Subscription reactivated successfully.');
    }

    /**
     * Suspend subscription
     */
    public function suspend(Subscription $subscription, Request $request)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $subscription->suspend($request->reason);

        return redirect()->route('superadmin.subscriptions.index')
                        ->with('success', 'Subscription suspended successfully.');
    }
}