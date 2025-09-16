<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ModuleController extends Controller
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
     * Display a listing of all modules
     */
    public function index(Request $request)
    {
        $query = Module::query();

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('display_name', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $modules = $query->orderBy('category')->orderBy('sort_order')->paginate(20);
        
        $categories = Module::select('category')->distinct()->pluck('category');

        return view('superadmin.modules.index', compact('modules', 'categories'));
    }

    /**
     * Show the form for creating a new module
     */
    public function create()
    {
        return view('superadmin.modules.create');
    }

    /**
     * Store a newly created module
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:modules,name',
            'display_name' => 'required|string|max:200',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|string|max:50',
            'icon' => 'nullable|string|max:50',
            'price_monthly' => 'required|numeric|min:0|max:999.99',
            'price_yearly' => 'required|numeric|min:0|max:9999.99',
            'is_active' => 'boolean',
            'is_core' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        Module::create($request->all());

        return redirect()->route('superadmin.modules.index')
                        ->with('success', 'Module created successfully.');
    }

    /**
     * Display the specified module
     */
    public function show(Module $module)
    {
        $module->load(['companies' => function($query) {
            $query->withPivot(['is_enabled', 'enabled_at', 'expires_at', 'monthly_price', 'yearly_price']);
        }]);

        $enabledCompanies = $module->enabledCompanies()->count();
        $totalRevenue = $module->companies()
                              ->wherePivot('is_enabled', true)
                              ->sum('company_modules.monthly_price');

        return view('superadmin.modules.show', compact('module', 'enabledCompanies', 'totalRevenue'));
    }

    /**
     * Show the form for editing the specified module
     */
    public function edit(Module $module)
    {
        return view('superadmin.modules.edit', compact('module'));
    }

    /**
     * Update the specified module
     */
    public function update(Request $request, Module $module)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('modules')->ignore($module->id)],
            'display_name' => 'required|string|max:200',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|string|max:50',
            'icon' => 'nullable|string|max:50',
            'price_monthly' => 'required|numeric|min:0|max:999.99',
            'price_yearly' => 'required|numeric|min:0|max:9999.99',
            'is_active' => 'boolean',
            'is_core' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $module->update($request->all());

        return redirect()->route('superadmin.modules.show', $module)
                        ->with('success', 'Module updated successfully.');
    }

    /**
     * Remove the specified module
     */
    public function destroy(Module $module)
    {
        if ($module->is_core) {
            return back()->with('error', 'Cannot delete core modules.');
        }

        $moduleName = $module->display_name;
        $module->delete();

        return redirect()->route('superadmin.modules.index')
                        ->with('success', "Module '{$moduleName}' deleted successfully.");
    }

    /**
     * Show company module assignments
     */
    public function companies(Module $module)
    {
        $companies = Company::with(['modules' => function($query) use ($module) {
            $query->where('module_id', $module->id);
        }])->paginate(15);

        return view('superadmin.modules.companies', compact('module', 'companies'));
    }

    /**
     * Enable module for a company
     */
    public function enableForCompany(Request $request, Module $module)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'expires_at' => 'nullable|date|after:today',
        ]);

        $company = Company::findOrFail($request->company_id);
        $company->enableModule($module, $request->expires_at ? \Carbon\Carbon::parse($request->expires_at) : null);

        return back()->with('success', "Module '{$module->display_name}' enabled for {$company->name}.");
    }

    /**
     * Disable module for a company
     */
    public function disableForCompany(Request $request, Module $module)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
        ]);

        $company = Company::findOrFail($request->company_id);
        $company->disableModule($module);

        return back()->with('success', "Module '{$module->display_name}' disabled for {$company->name}.");
    }

    /**
     * Bulk enable/disable modules for companies
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'action' => 'required|in:enable,disable',
            'module_ids' => 'required|array',
            'module_ids.*' => 'exists:modules,id',
            'company_ids' => 'required|array',
            'company_ids.*' => 'exists:companies,id',
            'expires_at' => 'nullable|date|after:today',
        ]);

        $modules = Module::whereIn('id', $request->module_ids)->get();
        $companies = Company::whereIn('id', $request->company_ids)->get();
        $expiresAt = $request->expires_at ? \Carbon\Carbon::parse($request->expires_at) : null;

        foreach ($companies as $company) {
            foreach ($modules as $module) {
                if ($request->action === 'enable') {
                    $company->enableModule($module, $expiresAt);
                } else {
                    $company->disableModule($module);
                }
            }
        }

        $action = $request->action === 'enable' ? 'enabled' : 'disabled';
        return back()->with('success', "Modules {$action} for selected companies successfully.");
    }
} 