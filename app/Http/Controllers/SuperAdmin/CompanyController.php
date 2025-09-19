<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
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
        $query = Company::withCount(['users', 'projects', 'clients'])
            ->with(['activeSubscription.membershipPlan']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('plan')) {
            $query->whereHas('activeSubscription.membershipPlan', function($q) use ($request) {
                $q->where('slug', $request->plan);
            });
        }

        $companies = $query->latest()->paginate(12);

        return view('superadmin.companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('superadmin.companies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:50',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'required|string|max:2',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string|max:1000',
            'subscription_plan' => 'required|in:trial,basic,professional,enterprise',
            'max_users' => 'required|integer|min:1|max:1000',
            'max_projects' => 'required|integer|min:1|max:10000',
            
            // Admin user fields
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:8|confirmed',
        ]);

        // Create company
        $company = Company::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zip_code' => $request->zip_code,
            'country' => $request->country,
            'website' => $request->website,
            'description' => $request->description,
            'status' => Company::STATUS_ACTIVE,
            'subscription_plan' => $request->subscription_plan,
            'trial_ends_at' => $request->subscription_plan === 'trial' ? now()->addDays(30) : null,
            'max_users' => $request->max_users,
            'max_projects' => $request->max_projects,
        ]);

        // Create company admin user
        User::create([
            'name' => $request->admin_name,
            'email' => $request->admin_email,
            'password' => Hash::make($request->admin_password),
            'role' => User::ROLE_COMPANY_ADMIN,
            'company_id' => $company->id,
            'is_active' => true,
        ]);

        return redirect()->route('superadmin.companies.index')
                        ->with('success', 'Company created successfully with admin user.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        $company->load([
            'users' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'projects' => function ($query) {
                $query->with('client')->orderBy('created_at', 'desc');
            }
        ]);
        
        $company->loadCount(['users', 'projects', 'clients', 'tasks']);

        return view('superadmin.companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        $company->loadCount(['users', 'projects', 'clients', 'tasks']);
        return view('superadmin.companies.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('companies')->ignore($company->id)],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:50',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'required|string|max:2',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive,suspended',
            'subscription_plan' => 'required|in:trial,basic,professional,enterprise',
            'trial_ends_at' => 'nullable|date|after:today',
            'subscription_ends_at' => 'nullable|date|after:today',
            'max_users' => 'required|integer|min:1|max:1000',
            'max_projects' => 'required|integer|min:1|max:10000',
        ]);

        $company->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zip_code' => $request->zip_code,
            'country' => $request->country,
            'website' => $request->website,
            'description' => $request->description,
            'status' => $request->status,
            'subscription_plan' => $request->subscription_plan,
            'trial_ends_at' => $request->trial_ends_at,
            'subscription_ends_at' => $request->subscription_ends_at,
            'max_users' => $request->max_users,
            'max_projects' => $request->max_projects,
        ]);

        return redirect()->route('superadmin.companies.show', $company)
                        ->with('success', 'Company updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        $companyName = $company->name;
        $company->delete();

        return redirect()->route('superadmin.companies.index')
                        ->with('success', "Company '{$companyName}' deleted successfully.");
    }

    /**
     * Suspend a company
     */
    public function suspend(Company $company)
    {
        $company->update(['status' => Company::STATUS_SUSPENDED]);

        return back()->with('success', "Company '{$company->name}' has been suspended.");
    }

    /**
     * Activate a company
     */
    public function activate(Company $company)
    {
        $company->update(['status' => Company::STATUS_ACTIVE]);

        return back()->with('success', "Company '{$company->name}' has been activated.");
    }
}
