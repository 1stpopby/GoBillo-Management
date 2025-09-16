<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SiteController extends Controller
{
    /**
     * Display a listing of sites
     */
    public function index(Request $request)
    {
        $query = Site::forCompany()->with(['client', 'projects']);

        // For site managers, only show sites they manage
        if (in_array(auth()->user()->role, ['site_manager', 'project_manager'])) {
            $query->where('manager_id', auth()->id());
        }

        // Apply archived filter
        if ($request->filled('archived')) {
            if ($request->archived === '1') {
                $query->where('is_active', false);
            } else {
                $query->where('is_active', true);
            }
        } else {
            // By default, only show active sites
            $query->where('is_active', true);
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($clientQuery) use ($search) {
                      $clientQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $sites = $query->latest()->paginate(12);

        // Get filter options
        $clients = Client::forCompany()->orderBy('company_name')->get();

        return view('sites.index', compact('sites', 'clients'));
    }

    /**
     * Show the form for creating a new site
     */
    public function create()
    {
        $clients = Client::forCompany()->orderBy('company_name')->get();
        $managers = User::forCompany()
            ->whereIn('role', [User::ROLE_COMPANY_ADMIN, User::ROLE_PROJECT_MANAGER, User::ROLE_SITE_MANAGER])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('sites.create', compact('clients', 'managers'));
    }

    /**
     * Store a newly created site
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'client_id' => 'required|exists:clients,id',
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:50',
            'zip_code' => 'nullable|string|max:20',
            'total_budget' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'expected_completion_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:planning,active,on_hold,completed,cancelled',
            'priority' => 'required|in:low,medium,high,urgent',
            'site_manager_contact' => 'nullable|string|max:255',
            'site_manager_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'managers' => 'nullable|array',
            'managers.*' => 'exists:users,id'
        ]);

        // Verify client belongs to the same company
        $client = Client::forCompany()->findOrFail($request->client_id);

        $site = Site::create([
            'company_id' => auth()->user()->company_id,
            'client_id' => $client->id,
            'name' => $request->name,
            'description' => $request->description,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zip_code' => $request->zip_code,
            'total_budget' => $request->total_budget,
            'start_date' => $request->start_date,
            'expected_completion_date' => $request->expected_completion_date,
            'status' => $request->status,
            'priority' => $request->priority,
            'site_manager_contact' => $request->site_manager_contact,
            'site_manager_phone' => $request->site_manager_phone,
            'notes' => $request->notes
        ]);

        // Assign managers to the site
        if ($request->has('managers') && is_array($request->managers)) {
            foreach ($request->managers as $index => $managerId) {
                if ($managerId) {
                    $site->managers()->attach($managerId, [
                        'role' => $index === 0 ? 'primary' : 'secondary',
                        'is_active' => true
                    ]);
                }
            }
        }

        return redirect()->route('sites.show', $site)
                        ->with('success', 'Site created successfully.');
    }

    /**
     * Display the specified site
     */
    public function show(Site $site)
    {
        // Ensure the site belongs to the current company
        if ($site->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        // For site managers, only allow access to sites they manage
        if (in_array(auth()->user()->role, ['site_manager', 'project_manager'])) {
            if ($site->manager_id !== auth()->id()) {
                abort(403, 'Access denied. You can only view sites allocated to you.');
            }
        }

        $site->load(['client', 'projects.tasks', 'projects.users', 'projects.manager']);

        // Get site statistics
        $stats = [
            'total_projects' => $site->getTotalProjectsCount(),
            'active_projects' => $site->getActiveProjectsCount(),
            'completed_projects' => $site->getCompletedProjectsCount(),
            'total_tasks' => $site->getTotalTasksCount()
        ];

        // Calculate financial statistics
        $financial_stats = $this->calculateFinancialStats($site);

        return view('sites.show', compact('site', 'stats', 'financial_stats'));
    }

    /**
     * Show the form for editing the site
     */
    public function edit(Site $site)
    {
        // Ensure the site belongs to the current company
        if ($site->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $clients = Client::forCompany()->orderBy('company_name')->get();
        $managers = User::forCompany()
            ->whereIn('role', [User::ROLE_COMPANY_ADMIN, User::ROLE_PROJECT_MANAGER, User::ROLE_SITE_MANAGER])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('sites.edit', compact('site', 'clients', 'managers'));
    }

    /**
     * Update the specified site
     */
    public function update(Request $request, Site $site)
    {
        // Ensure the site belongs to the current company
        if ($site->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'client_id' => 'required|exists:clients,id',
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:50',
            'zip_code' => 'nullable|string|max:20',
            'total_budget' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'expected_completion_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:planning,active,on_hold,completed,cancelled',
            'priority' => 'required|in:low,medium,high,urgent',
            'site_manager_contact' => 'nullable|string|max:255',
            'site_manager_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'managers' => 'nullable|array',
            'managers.*' => 'exists:users,id'
        ]);

        // Verify client belongs to the same company
        $client = Client::forCompany()->findOrFail($request->client_id);

        $site->update([
            'client_id' => $client->id,
            'name' => $request->name,
            'description' => $request->description,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zip_code' => $request->zip_code,
            'total_budget' => $request->total_budget,
            'start_date' => $request->start_date,
            'expected_completion_date' => $request->expected_completion_date,
            'status' => $request->status,
            'priority' => $request->priority,
            'site_manager_contact' => $request->site_manager_contact,
            'site_manager_phone' => $request->site_manager_phone,
            'notes' => $request->notes
        ]);

        // Update manager assignments
        $site->managers()->detach(); // Remove all existing manager assignments
        
        if ($request->has('managers') && is_array($request->managers)) {
            foreach ($request->managers as $index => $managerId) {
                if ($managerId) {
                    $site->managers()->attach($managerId, [
                        'role' => $index === 0 ? 'primary' : 'secondary',
                        'is_active' => true
                    ]);
                }
            }
        }

        return redirect()->route('sites.show', $site)
                        ->with('success', 'Site updated successfully.');
    }

    /**
     * Remove the specified site
     */
    public function destroy(Site $site)
    {
        // Ensure the site belongs to the current company
        if ($site->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        // Check if site has active projects
        if ($site->projects()->where('status', '!=', Project::STATUS_COMPLETED)->exists()) {
            return redirect()->route('sites.show', $site)
                            ->with('error', 'Cannot delete site with active projects. Complete or cancel all projects first.');
        }

        $site->delete();

        return redirect()->route('sites.index')
                        ->with('success', 'Site deleted successfully.');
    }

    /**
     * Calculate financial statistics for a site
     */
    private function calculateFinancialStats(Site $site)
    {
        // Site budget (set when creating the site) - for guidance only
        $site_budget = $site->total_budget ?? 0;
        
        // Total projects revenue (sum of all project budgets)
        $total_projects_revenue = $site->projects()->sum('budget') ?? 0;
        
        // Total direct costs (project expenses + invoices paid to subcontractors/operatives)
        $total_expenses = 0;
        $total_invoices_paid = 0;
        
        // Get project expenses for all projects in this site
        foreach ($site->projects as $project) {
            // Sum approved project expenses for this project
            $project_expenses = $project->projectExpenses()
                ->where('status', 'approved')
                ->sum('amount') ?? 0;
            $total_expenses += $project_expenses;
            
            // Sum paid invoices for this project (assuming invoices table exists)
            // This would include payments to subcontractors, operatives, managers, etc.
            $project_invoices = \App\Models\Invoice::where('project_id', $project->id)
                ->where('status', 'paid')
                ->sum('total') ?? 0;
            $total_invoices_paid += $project_invoices;
        }
        
        $total_direct_costs = $total_expenses + $total_invoices_paid;
        
        // Calculate remaining budget based on projects revenue vs direct costs (actual business logic)
        $remaining_budget = $total_projects_revenue - $total_direct_costs;
        
        // Calculate profit margin based on projects revenue (actual revenue)
        $profit_margin = $total_projects_revenue > 0 ? (($remaining_budget / $total_projects_revenue) * 100) : 0;
        
        // Calculate budget utilization based on projects revenue
        $budget_utilization = $total_projects_revenue > 0 ? (($total_direct_costs / $total_projects_revenue) * 100) : 0;
        
        // Calculate variance between site budget and projects revenue (for guidance)
        $budget_variance = $total_projects_revenue - $site_budget;
        $budget_variance_percentage = $site_budget > 0 ? (($budget_variance / $site_budget) * 100) : 0;
        
        return [
            'site_budget' => $site_budget,
            'total_projects_revenue' => $total_projects_revenue,
            'total_direct_costs' => $total_direct_costs,
            'total_expenses' => $total_expenses,
            'total_invoices_paid' => $total_invoices_paid,
            'remaining_budget' => $remaining_budget,
            'profit_margin' => $profit_margin,
            'budget_utilization' => $budget_utilization,
            'budget_variance' => $budget_variance,
            'budget_variance_percentage' => $budget_variance_percentage
        ];
    }

    /**
     * Display sites allocated to the current manager
     */
    public function managerSites(Request $request)
    {
        $user = auth()->user();
        
        // Only site managers and above can access this
        if (!in_array($user->role, ['site_manager', 'project_manager', 'company_admin'])) {
            abort(403, 'Access denied. Only managers can access this section.');
        }

        // Get sites where the user is the manager (direct assignment)
        $query = Site::forCompany()
            ->with(['client', 'projects', 'manager'])
            ->where('manager_id', $user->id);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($clientQuery) use ($search) {
                      $clientQuery->where('company_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $sites = $query->latest()->paginate(12);

        // Get filter options for manager's sites only
        $clientIds = $sites->pluck('client_id')->unique();
        $clients = Client::forCompany()->whereIn('id', $clientIds)->orderBy('company_name')->get();

        return view('sites.index', compact('sites', 'clients'));
    }

    /**
     * Archive the specified site
     */
    public function archive(Site $site)
    {
        // Ensure the site belongs to the current company
        if ($site->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        // Only allow company admins and site managers to archive sites
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Access denied. You do not have permission to archive sites.');
        }

        $site->update(['is_active' => false]);

        return redirect()->back()->with('success', "Site '{$site->name}' has been archived successfully.");
    }

    /**
     * Unarchive the specified site
     */
    public function unarchive(Site $site)
    {
        // Ensure the site belongs to the current company
        if ($site->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        // Only allow company admins and site managers to unarchive sites
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Access denied. You do not have permission to unarchive sites.');
        }

        $site->update(['is_active' => true]);

        return redirect()->back()->with('success', "Site '{$site->name}' has been unarchived successfully.");
    }

    /**
     * Bulk archive all completed sites
     */
    public function bulkArchiveCompleted()
    {
        // Only allow company admins to bulk archive sites
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Access denied. You do not have permission to bulk archive sites.');
        }

        $completedSites = Site::forCompany()
            ->where('status', 'completed')
            ->where('is_active', true)
            ->get();

        $count = $completedSites->count();

        if ($count > 0) {
            Site::forCompany()
                ->where('status', 'completed')
                ->where('is_active', true)
                ->update(['is_active' => false]);

            return redirect()->route('sites.index')->with('success', "Successfully archived {$count} completed " . Str::plural('site', $count) . ".");
        } else {
            return redirect()->route('sites.index')->with('info', 'No completed sites found to archive.');
        }
    }

}
