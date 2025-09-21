<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Client;
use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects
     */
    public function index(Request $request)
    {
        // Company admins and superadmins get full company visibility
        if (auth()->user()->isCompanyAdmin() || auth()->user()->isSuperAdmin()) {
            $query = Project::forCompany()->with(['site', 'client', 'manager', 'tasks']);
        } else {
            $query = Project::forCompany()->visibleToUser()->with(['site', 'client', 'manager', 'tasks']);
        }

        // Apply archived filter
        if ($request->filled('archived')) {
            if ($request->archived === '1') {
                $query->where('is_active', false);
            } else {
                $query->where('is_active', true);
            }
        } else {
            // By default, only show active projects
            $query->where('is_active', true);
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('site_id')) {
            $query->where('site_id', $request->site_id);
        }

        $projects = $query->latest()->paginate(15);
        
        // Get filter options
        $sites = \App\Models\Site::forCompany()->orderBy('name')->get();

        return view('projects.index', compact('projects', 'sites'));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        
        // Check if creating from within a site
        $selectedSite = null;
        $selectedClient = null;
        $fromSite = false;
        
        if ($request->has('site_id')) {
            $selectedSite = Site::forCompany($user->company_id)
                                ->with('client')
                                ->findOrFail($request->site_id);
            $selectedClient = $selectedSite->client;
            $fromSite = true;
        }
        
        // Get clients, sites, and managers from same company
        $clients = Client::forCompany($user->company_id)
                         ->where('is_active', true)
                         ->orderBy('company_name')
                         ->get();
        
        $sites = Site::forCompany($user->company_id)
                    ->where('status', 'active')
                    ->with('activeManagers')
                    ->orderBy('name')
                    ->get();
        
        $managers = User::forCompany($user->company_id)
                       ->where('is_active', true)
                       ->whereIn('role', [User::ROLE_COMPANY_ADMIN, User::ROLE_PROJECT_MANAGER, User::ROLE_SITE_MANAGER])
                       ->orderBy('name')
                       ->get();

        return view('projects.create', compact('clients', 'sites', 'managers', 'selectedSite', 'selectedClient', 'fromSite'));
    }

    /**
     * Store a newly created project.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'client_id' => 'required|exists:clients,id',
            'site_id' => 'required|exists:sites,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'budget' => 'nullable|numeric|min:0',
            'address' => 'nullable|string',
            'postcode' => 'required|string|max:20',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'priority' => 'required|in:low,medium,high,urgent',
            'managers' => 'nullable|array',
            'managers.*' => 'exists:users,id'
        ]);

        // Ensure client and site belong to same company
        $client = Client::forCompany($user->company_id)->findOrFail($validated['client_id']);
        $site = Site::forCompany($user->company_id)->findOrFail($validated['site_id']);

        // Add company_id to the project
        $validated['company_id'] = $user->company_id;
        $validated['status'] = Project::STATUS_PLANNING;
        $validated['progress'] = 0;

        $project = Project::create($validated);

        // Generate coordinates from postcode
        $project->updateCoordinatesFromPostcode();

        // Assign managers to the project
        if ($request->has('managers') && is_array($request->managers)) {
            foreach ($request->managers as $index => $managerId) {
                if ($managerId) {
                    // Verify manager belongs to company
                    $manager = User::forCompany($user->company_id)->findOrFail($managerId);
                    
                    // Assign to many-to-many managers table
                    $project->managers()->attach($managerId, [
                        'role' => $index === 0 ? 'primary' : 'secondary',
                        'is_active' => true
                    ]);

                    // Also add to project users table for backward compatibility
                    if (!$project->users()->where('user_id', $managerId)->exists()) {
                        $project->users()->attach($managerId, [
                            'role' => 'manager',
                            'joined_at' => now(),
                        ]);
                    }
                }
            }
        }

        // Determine redirect destination based on context
        if ($request->has('site_id') || $project->site_id) {
            // If project was created from a site context, redirect back to the site
            return redirect()->route('sites.show', $project->site_id)
                            ->with('success', 'Project created successfully.');
        }
        
        // Otherwise, redirect to general projects index
        return redirect()->route('projects.index')
                        ->with('success', 'Project created successfully.');
    }

    /**
     * Get managers for a specific site (AJAX endpoint)
     */
    public function getManagersForSite(Request $request)
    {
        $siteId = $request->get('site_id');
        
        if (!$siteId) {
            return response()->json(['managers' => []]);
        }

        $site = Site::forCompany()
                   ->with('activeManagers')
                   ->find($siteId);

        if (!$site) {
            return response()->json(['managers' => []]);
        }

        $managers = $site->activeManagers->map(function($manager) {
            return [
                'id' => $manager->id,
                'name' => $manager->name,
                'email' => $manager->email,
                'role' => $manager->pivot->role, // primary or secondary
            ];
        });

        return response()->json(['managers' => $managers]);
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project)
    {
        $user = auth()->user();
        
        // Load relationships
        $project->load([
            'client', 
            'manager', 
            'users', 
            'tasks.assignedUser', 
            'tasks.taskCategory', 
            'documents',
            'projectExpenses',
            'projectDocuments',
            'projectVariations',
            'projectSnaggings',
            'site'
        ]);

        // Ensure the project belongs to the current company
        if ($project->company_id !== $user->company_id) {
            abort(403, 'Access denied to this project.');
        }

        // Company admin/superadmin can view any company project
        if ($user->isCompanyAdmin() || $user->isSuperAdmin()) {
            return view('projects.show', [
                'project' => $project,
                'availableUsers' => User::forCompany($user->company_id)
                    ->where('is_active', true)
                    ->whereNotIn('id', $project->users->pluck('id'))
                    ->get(),
                'financial_stats' => $this->calculateProjectFinancialStats($project),
                'project_stats' => [
                    'total_tasks' => $project->tasks->count(),
                    'completed_tasks' => $project->tasks->where('status', 'completed')->count(),
                    'in_progress_tasks' => $project->tasks->where('status', 'in_progress')->count(),
                    'overdue_tasks' => $project->tasks->filter(fn($t) => $t->due_date && $t->due_date->isPast() && $t->status !== 'completed')->count(),
                    'team_members' => $project->users->count()
                ],
            ]);
        }

        // Site managers and project managers can view projects in sites they manage
        if (in_array($user->role, ['site_manager', 'project_manager'])) {
            // Check if user is a manager of this project's site using the many-to-many relationship
            $canAccessSite = false;
            
            if ($project->site) {
                $canAccessSite = $project->site->activeManagers()
                    ->where('users.id', $user->id)
                    ->exists();
            }
            
            if (!$canAccessSite) {
                abort(403, 'Access denied. You can only view projects in sites allocated to you.');
            }
            
            return view('projects.show', [
                'project' => $project,
                'availableUsers' => User::forCompany($user->company_id)
                    ->where('is_active', true)
                    ->whereNotIn('id', $project->users->pluck('id'))
                    ->get(),
                'financial_stats' => $this->calculateProjectFinancialStats($project),
                'project_stats' => [
                    'total_tasks' => $project->tasks->count(),
                    'completed_tasks' => $project->tasks->where('status', 'completed')->count(),
                    'in_progress_tasks' => $project->tasks->where('status', 'in_progress')->count(),
                    'overdue_tasks' => $project->tasks->filter(fn($t) => $t->due_date && $t->due_date->isPast() && $t->status !== 'completed')->count(),
                    'team_members' => $project->users->count()
                ],
            ]);
        }

        // Check if user can view this project
        $isOnTeam = $project->users->contains($user->id);
        $hasAssignedTasks = $project->tasks->contains(fn($task) => (int)$task->assigned_to === (int)$user->id);
        $isClientOfProject = $project->client && ($project->client->contact_email === $user->email || $project->client->owner_user_id === $user->id);

        // Allow contractors/subcontractors/operatives to view all company projects by default
        $isInternalNonAdmin = in_array($user->role, [
            User::ROLE_CONTRACTOR,
            User::ROLE_SUBCONTRACTOR ?? 'subcontractor',
            User::ROLE_OPERATIVE ?? 'operative',
        ], true);

        if (!$user->canManageProjects() && !$isOnTeam && !$hasAssignedTasks && !$isClientOfProject && !$isInternalNonAdmin) {
            abort(403, 'Access denied to this project.');
        }

        // Get available users for adding to team (from same company)
        $availableUsers = User::forCompany($user->company_id)
                             ->where('is_active', true)
                             ->whereNotIn('id', $project->users->pluck('id'))
                             ->get();

        // Calculate financial statistics
        $financial_stats = $this->calculateProjectFinancialStats($project);

        // Calculate project statistics
        $project_stats = [
            'total_tasks' => $project->tasks->count(),
            'completed_tasks' => $project->tasks->where('status', 'completed')->count(),
            'in_progress_tasks' => $project->tasks->where('status', 'in_progress')->count(),
            'overdue_tasks' => $project->tasks->filter(function($task) {
                return $task->due_date && $task->due_date->isPast() && $task->status !== 'completed';
            })->count(),
            'team_members' => $project->users->count()
        ];

        return view('projects.show', compact('project', 'availableUsers', 'financial_stats', 'project_stats'));
    }

    /**
     * Show the form for editing the project.
     */
    public function edit(string $id, Request $request)
    {
        $user = auth()->user();
        
        $project = Project::forCompany($user->company_id)
                          ->with(['site.client', 'client'])
                          ->findOrFail($id);
        
        // Check if editing from within a site context OR if project belongs to a site
        // If project has a site, client should be read-only to maintain data integrity
        $fromSite = $request->has('from_site') || 
                   ($request->headers->get('referer') && str_contains($request->headers->get('referer'), '/sites/')) ||
                   ($project->site_id !== null); // If project belongs to a site, make client read-only
        
        $selectedClient = $project->client;
        
        $clients = Client::forCompany($user->company_id)
                         ->where('is_active', true)
                         ->orderBy('company_name')
                         ->get();
        
        $sites = Site::forCompany($user->company_id)
                    ->where('status', 'active')
                    ->with('activeManagers')
                    ->orderBy('name')
                    ->get();
        
        $managers = User::forCompany($user->company_id)
                       ->where('is_active', true)
                       ->whereIn('role', [User::ROLE_COMPANY_ADMIN, User::ROLE_PROJECT_MANAGER, User::ROLE_SITE_MANAGER])
                       ->orderBy('name')
                       ->get();

        return view('projects.edit', compact('project', 'clients', 'sites', 'managers', 'fromSite', 'selectedClient'));
    }

    /**
     * Update the specified project.
     */
    public function update(Request $request, string $id)
    {
        $user = auth()->user();
        
        $project = Project::forCompany($user->company_id)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'client_id' => 'required|exists:clients,id',
            'manager_id' => 'required|exists:users,id',
            'status' => 'required|in:planning,in_progress,on_hold,completed,cancelled',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'budget' => 'nullable|numeric|min:0',
            'address' => 'nullable|string',
            'postcode' => 'required|string|max:20',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'priority' => 'required|in:low,medium,high,urgent',
            'progress' => 'required|integer|min:0|max:100',
        ]);

        // Ensure client and manager belong to same company
        $client = Client::forCompany($user->company_id)->findOrFail($validated['client_id']);
        $manager = User::forCompany($user->company_id)->findOrFail($validated['manager_id']);

        // Check if postcode changed and update coordinates
        $oldPostcode = $project->postcode;
        $project->update($validated);
        
        if ($oldPostcode !== $validated['postcode']) {
            $project->updateCoordinatesFromPostcode();
        }

        // Determine redirect destination based on context
        if ($request->has('from_site') || ($request->headers->get('referer') && str_contains($request->headers->get('referer'), '/sites/'))) {
            // If project was edited from a site context, redirect back to the site
            return redirect()->route('sites.show', $project->site_id)
                            ->with('success', 'Project updated successfully.');
        }

        return redirect()->route('projects.show', $project)
                        ->with('success', 'Project updated successfully.');
    }

    /**
     * Update project status directly
     */
    public function updateStatus(Request $request, string $id)
    {
        $user = auth()->user();
        $project = Project::forCompany($user->company_id)->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:not_started,in_progress,on_hold,completed,cancelled'
        ]);

        $oldStatus = $project->status;
        $project->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 
            "Project '{$project->name}' status updated from " . 
            ucfirst(str_replace('_', ' ', $oldStatus)) . " to " . 
            ucfirst(str_replace('_', ' ', $validated['status']))
        );
    }

    /**
     * Update project status via GET request (simpler approach)
     */
    public function updateStatusGet(string $id, string $status)
    {
        $user = auth()->user();
        $project = Project::forCompany($user->company_id)->findOrFail($id);

        // Validate status
        if (!in_array($status, ['not_started', 'in_progress', 'on_hold', 'completed', 'cancelled'])) {
            return redirect()->back()->with('error', 'Invalid status provided');
        }

        $oldStatus = $project->status;
        $project->update(['status' => $status]);

        return redirect()->back()->with('success', 
            "Project '{$project->name}' status updated from " . 
            ucfirst(str_replace('_', ' ', $oldStatus)) . " to " . 
            ucfirst(str_replace('_', ' ', $status))
        );
    }

    /**
     * Remove the specified project.
     */
    public function destroy(string $id)
    {
        $user = auth()->user();
        
        $project = Project::forCompany($user->company_id)->findOrFail($id);

        // Check if project has active tasks
        if ($project->tasks()->whereIn('status', ['pending', 'in_progress', 'review'])->exists()) {
            return back()->with('error', 'Cannot delete project with active tasks.');
        }

        $projectName = $project->name;
        $project->delete();

        return redirect()->route('projects.index')
                        ->with('success', "Project '{$projectName}' deleted successfully.");
    }

    /**
     * Add a user to the project team.
     */
    public function addUser(Request $request, string $id)
    {
        $user = auth()->user();
        
        $project = Project::forCompany($user->company_id)->findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:member,contractor,subcontractor',
        ]);

        // Ensure user belongs to same company
        $teamUser = User::forCompany($user->company_id)->findOrFail($validated['user_id']);

        // Check if user is already on the team
        if ($project->users->contains($teamUser->id)) {
            return back()->with('error', 'User is already on the project team.');
        }

        $project->users()->attach($teamUser->id, [
            'role' => $validated['role'],
            'joined_at' => now(),
        ]);

        return back()->with('success', 'User added to project team successfully.');
    }

    /**
     * Remove a user from the project team.
     */
    public function removeUser(Request $request, string $id)
    {
        $user = auth()->user();
        
        $project = Project::forCompany($user->company_id)->findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Ensure user belongs to same company
        $teamUser = User::forCompany($user->company_id)->findOrFail($validated['user_id']);

        // Don't allow removing the project manager
        if ($teamUser->id === $project->manager_id) {
            return back()->with('error', 'Cannot remove the project manager from the team.');
        }

        $project->users()->detach($teamUser->id);

        return back()->with('success', 'User removed from project team successfully.');
    }

    /**
     * Calculate financial statistics for a project
     */
    private function calculateProjectFinancialStats(Project $project)
    {
        // Project budget
        $project_budget = $project->budget ?? 0;
        
        // Task costs
        $total_estimated_task_costs = $project->total_estimated_cost;
        $total_actual_task_costs = $project->total_actual_cost;
        
        // Actual project costs (project expenses + invoices for this project + task costs)
        $total_project_expenses = $project->projectExpenses()
            ->where('status', 'approved')
            ->sum('amount') ?? 0;
            
        $total_invoices_paid = \App\Models\Invoice::where('project_id', $project->id)
            ->where('status', 'paid')
            ->sum('total_amount') ?? 0;
            
        $actual_costs = $total_project_expenses + $total_invoices_paid + $total_actual_task_costs;
        
        // Overall tasks progress (average of all task progress)
        $total_tasks = $project->tasks->count();
        $overall_progress = $total_tasks > 0 
            ? $project->tasks->avg('progress') ?? 0 
            : 0;
            
        // Remaining budget
        $remaining_budget = $project_budget - $actual_costs;
        
        // Calculate status indicators
        $is_over_budget = $actual_costs > $project_budget && $project_budget > 0;
        $has_overdue_tasks = $project->tasks->filter(function($task) {
            return $task->due_date && $task->due_date->isPast() && $task->status !== 'completed';
        })->count() > 0;
        
        // Budget utilization
        $budget_utilization = $project_budget > 0 ? (($actual_costs / $project_budget) * 100) : 0;
        
        // Cost variance (actual vs estimated for tasks)
        $task_cost_variance = $total_actual_task_costs - $total_estimated_task_costs;
        
        return [
            'project_budget' => $project_budget,
            'actual_costs' => $actual_costs,
            'total_expenses' => $total_project_expenses,
            'total_invoices_paid' => $total_invoices_paid,
            'total_estimated_task_costs' => $total_estimated_task_costs,
            'total_actual_task_costs' => $total_actual_task_costs,
            'task_cost_variance' => $task_cost_variance,
            'overall_progress' => round($overall_progress, 1),
            'remaining_budget' => $remaining_budget,
            'budget_utilization' => $budget_utilization,
            'is_over_budget' => $is_over_budget,
            'has_overdue_tasks' => $has_overdue_tasks,
            'project_status' => $project->status
        ];
    }

    /**
     * Archive the specified project
     */
    public function archive(Project $project)
    {
        // Ensure the project belongs to the current company
        if ($project->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        // Only allow company admins and project managers to archive projects
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Access denied. You do not have permission to archive projects.');
        }

        $project->update(['is_active' => false]);

        return redirect()->back()->with('success', "Project '{$project->name}' has been archived successfully.");
    }

    /**
     * Unarchive the specified project
     */
    public function unarchive(Project $project)
    {
        // Ensure the project belongs to the current company
        if ($project->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        // Only allow company admins and project managers to unarchive projects
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Access denied. You do not have permission to unarchive projects.');
        }

        $project->update(['is_active' => true]);

        return redirect()->back()->with('success', "Project '{$project->name}' has been unarchived successfully.");
    }

    /**
     * Bulk archive all completed projects
     */
    public function bulkArchiveCompleted()
    {
        // Only allow company admins to bulk archive projects
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Access denied. You do not have permission to bulk archive projects.');
        }

        $completedProjects = Project::forCompany()
            ->where('status', 'completed')
            ->where('is_active', true)
            ->get();

        $count = $completedProjects->count();

        if ($count > 0) {
            Project::forCompany()
                ->where('status', 'completed')
                ->where('is_active', true)
                ->update(['is_active' => false]);

            return redirect()->route('projects.index')->with('success', "Successfully archived {$count} completed " . \Illuminate\Support\Str::plural('project', $count) . ".");
        } else {
            return redirect()->route('projects.index')->with('info', 'No completed projects found to archive.');
        }
    }
}
