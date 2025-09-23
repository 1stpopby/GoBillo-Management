<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;

class TaskController extends Controller
{
    /**
     * Display a listing of tasks
     */
    public function index(Request $request)
    {
        $query = Task::forCompany()->with(['project.site', 'assignedUser', 'taskCategory']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if (auth()->user()->isCompanyAdmin() || auth()->user()->isSuperAdmin() || auth()->user()->canViewAllTasks()) {
            // full access
        } else {
            $query->forUser();
        }

        // Custom sorting: completed tasks at bottom, then by due date and priority
        $tasks = $query->orderByRaw("
            CASE 
                WHEN status = 'completed' THEN 1 
                ELSE 0 
            END ASC,
            CASE 
                WHEN due_date IS NULL THEN 1 
                ELSE 0 
            END ASC,
            due_date ASC,
            CASE 
                WHEN priority = 'urgent' THEN 1
                WHEN priority = 'high' THEN 2
                WHEN priority = 'medium' THEN 3
                WHEN priority = 'low' THEN 4
                ELSE 5
            END ASC,
            created_at DESC
        ")->paginate(15);
        
        // Get filter options
        $projects = Project::forCompany()->orderBy('name')->get();

        return view('tasks.index', compact('tasks', 'projects'));
    }

    /**
     * Show the form for creating a new task
     */
    public function create(Request $request)
    {
        // Check if creating from within a project
        $selectedProject = null;
        $fromProject = false;
        
        if ($request->has('project')) {
            $selectedProject = Project::forCompany()
                                     ->with(['site', 'client'])
                                     ->findOrFail($request->project);
            $fromProject = true;
        }
        
        $projects = Project::forCompany()
                          ->where('status', '!=', 'cancelled')
                          ->with('site')
                          ->orderBy('name')
                          ->get();
                          
        $users = User::forCompany()
                    ->where('is_active', true)
                    ->where('role', User::ROLE_OPERATIVE)
                    ->orderBy('name')
                    ->get();
        
        $taskCategories = \App\Models\TaskCategory::forCompany()
                                                 ->active()
                                                 ->ordered()
                                                 ->get();

        return view('tasks.create', compact('projects', 'users', 'taskCategories', 'selectedProject', 'fromProject'));
    }

    /**
     * Store a newly created task
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'task_category_id' => 'nullable|exists:task_categories,id',
            'assigned_to' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'status' => 'required|in:' . implode(',', Task::getValidStatuses()),
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'estimated_time' => 'nullable|numeric|min:0',
            'estimated_time_unit' => 'nullable|in:hours,days',
            'estimated_cost' => 'nullable|numeric|min:0',
            'actual_time' => 'nullable|numeric|min:0',
            'actual_time_unit' => 'nullable|in:hours,days',
            'actual_cost' => 'nullable|numeric|min:0',
            'progress' => 'nullable|integer|min:0|max:100',
            'notes' => 'nullable|string'
        ]);

        // Verify project belongs to the same company
        $project = Project::forCompany()->findOrFail($request->project_id);

        // Verify assigned user belongs to the same company (if provided)
        if ($request->assigned_to) {
            User::forCompany()->findOrFail($request->assigned_to);
        }

        // Verify task category belongs to the same company (if provided)
        if ($request->task_category_id) {
            \App\Models\TaskCategory::forCompany()->findOrFail($request->task_category_id);
        }

        $task = Task::create([
            'company_id' => auth()->user()->company_id,
            'project_id' => $project->id,
            'task_category_id' => $request->task_category_id,
            'assigned_to' => $request->assigned_to,
            'created_by' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'priority' => $request->priority,
            'due_date' => $request->due_date,
            'start_date' => $request->start_date,
            'estimated_time' => $request->estimated_time,
            'estimated_time_unit' => $request->estimated_time_unit ?? 'hours',
            'estimated_cost' => $request->estimated_cost,
            'actual_time' => $request->actual_time,
            'actual_time_unit' => $request->actual_time_unit ?? 'hours',
            'actual_cost' => $request->actual_cost,
            'progress' => $request->progress ?? 0,
            'notes' => $request->notes
        ]);

        return redirect(route('projects.show', $task->project_id) . '#tasks')
                        ->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task, Request $request)
    {
        $user = auth()->user();
        
        // Load relationships
        $task->load(['project', 'assignedUser', 'createdBy', 'documents', 'delayAppliedBy', 'onHoldAppliedBy']);
        
        // Check company access
        if ($task->company_id !== $user->company_id && !$user->isSuperAdmin()) {
            abort(404, 'Task not found.');
        }

        // Check if user can view this task
        if (!($user->isCompanyAdmin() || $user->isSuperAdmin() || $user->canViewAllTasks()) && $task->assigned_to !== $user->id) {
            abort(403, 'Access denied to this task.');
        }

        // Check if this is an AJAX request
        $isAjax = $request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest';

        // If AJAX request, return modal content only
        if ($isAjax) {
            return view('tasks.modal-content', compact('task'));
        }

        // Otherwise return full page view
        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(Task $task, Request $request)
    {
        $user = auth()->user();
        
        // Load relationships
        $task->load(['project.site', 'project.client']);
        
        // Check company access
        if ($task->company_id !== $user->company_id && !$user->isSuperAdmin()) {
            abort(404, 'Task not found.');
        }
        
        // Check if editing from within a project context
        // If task belongs to a project, make project field read-only to maintain data integrity
        $fromProject = $request->has('from_project') || 
                      ($request->headers->get('referer') && str_contains($request->headers->get('referer'), '/projects/')) ||
                      ($task->project_id !== null); // If task belongs to a project, make project read-only
        
        $selectedProject = $task->project;
        
        // Get projects and users from same company
        $projects = Project::forCompany($user->company_id)
                          ->where('status', '!=', 'cancelled')
                          ->with('site')
                          ->orderBy('name')
                          ->get();
        
        $users = User::forCompany($user->company_id)
                    ->where('is_active', true)
                    ->where('role', User::ROLE_OPERATIVE)
                    ->orderBy('name')
                    ->get();
                    
        $taskCategories = \App\Models\TaskCategory::forCompany()
                                                 ->active()
                                                 ->ordered()
                                                 ->get();

        return view('tasks.edit', compact('task', 'projects', 'users', 'taskCategories', 'fromProject', 'selectedProject'));
    }

    /**
     * Update the specified task.
     */
    public function update(Request $request, Task $task)
    {
        $user = auth()->user();
        
        // Check company access
        if ($task->company_id !== $user->company_id && !$user->isSuperAdmin()) {
            abort(404, 'Task not found.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'required|exists:projects,id',
            'task_category_id' => 'nullable|exists:task_categories,id',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:' . implode(',', Task::getValidStatuses()),
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
            'estimated_time' => 'nullable|numeric|min:0',
            'estimated_time_unit' => 'nullable|in:hours,days',
            'estimated_cost' => 'nullable|numeric|min:0',
            'actual_time' => 'nullable|numeric|min:0',
            'actual_time_unit' => 'nullable|in:hours,days',
            'actual_cost' => 'nullable|numeric|min:0',
        ]);

        // Ensure project belongs to same company
        $project = Project::forCompany($user->company_id)->findOrFail($validated['project_id']);
        
        // Ensure assigned user belongs to same company (if provided)
        if ($validated['assigned_to']) {
            $assignedUser = User::forCompany($user->company_id)->findOrFail($validated['assigned_to']);
        }

        // Verify task category belongs to the same company (if provided)
        if ($validated['task_category_id']) {
            \App\Models\TaskCategory::forCompany($user->company_id)->findOrFail($validated['task_category_id']);
        }

        // Set default time units if not provided
        if (isset($validated['estimated_time']) && !isset($validated['estimated_time_unit'])) {
            $validated['estimated_time_unit'] = 'hours';
        }
        if (isset($validated['actual_time']) && !isset($validated['actual_time_unit'])) {
            $validated['actual_time_unit'] = 'hours';
        }

        $task->update($validated);

        // Update project progress automatically based on task completion
        $this->updateProjectProgress($project);

        return redirect(route('projects.show', $task->project_id) . '#tasks')
                        ->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified task.
     */
    public function destroy(Task $task)
    {
        $user = auth()->user();
        
        // Check company access
        if ($task->company_id !== $user->company_id && !$user->isSuperAdmin()) {
            abort(404, 'Task not found.');
        }
        
        // Check if user has permission to delete tasks
        if (!$user->canManageProjects()) {
            abort(403, 'You do not have permission to delete tasks.');
        }
        
        // Load project relationship
        $task->load('project');

        $taskTitle = $task->title;
        $projectId = $task->project_id;
        $task->delete();

        // If task belongs to a project, redirect back to the project
        if ($projectId) {
            $redirectUrl = route('projects.show', $projectId);
            
            // If request came from project tasks tab, add fragment to stay on tasks tab
            if (request()->has('return_to_tab') && request()->return_to_tab === 'tasks') {
                $redirectUrl .= '#tasks';
            }
            
            return redirect($redirectUrl)
                            ->with('success', "Task '{$taskTitle}' deleted successfully.");
        }

        // Otherwise, redirect to tasks index
        return redirect()->route('tasks.index')
                        ->with('success', "Task '{$taskTitle}' deleted successfully.");
    }

    /**
     * Update task status directly
     */
    public function updateStatus(Request $request, Task $task)
    {
        $user = auth()->user();
        
        // Check company access
        if ($task->company_id !== $user->company_id && !$user->isSuperAdmin()) {
            abort(404, 'Task not found.');
        }

        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', Task::getValidStatuses())
        ]);

        $oldStatus = $task->status;
        $task->update(['status' => $validated['status']]);

        // Update project progress automatically
        $this->updateProjectProgress($task->project);

        // If AJAX request, return JSON response
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Task '{$task->title}' status updated from " . 
                    ucfirst(str_replace('_', ' ', $oldStatus)) . " to " . 
                    ucfirst(str_replace('_', ' ', $validated['status'])) .
                    ". Project progress updated automatically."
            ]);
        }

        return redirect()->back()->with('success', 
            "Task '{$task->title}' status updated from " . 
            ucfirst(str_replace('_', ' ', $oldStatus)) . " to " . 
            ucfirst(str_replace('_', ' ', $validated['status'])) .
            ". Project progress updated automatically."
        );
    }

    /**
     * Update task status via GET request (simpler approach)
     */
    public function updateStatusGet(Task $task, string $status)
    {
        $user = auth()->user();
        
        // Check company access
        if ($task->company_id !== $user->company_id && !$user->isSuperAdmin()) {
            abort(404, 'Task not found.');
        }

        // Validate status
        if (!in_array($status, Task::getValidStatuses())) {
            return redirect()->back()->with('error', 'Invalid status provided');
        }

        $oldStatus = $task->status;
        $task->update(['status' => $status]);

        // Update project progress automatically
        $this->updateProjectProgress($task->project);

        return redirect()->back()->with('success', 
            "Task '{$task->title}' status updated from " . 
            ucfirst(str_replace('_', ' ', $oldStatus)) . " to " . 
            ucfirst(str_replace('_', ' ', $status)) .
            ". Project progress updated automatically."
        );
    }

    /**
     * Update project progress based on task completion
     */
    private function updateProjectProgress(Project $project)
    {
        $totalTasks = $project->tasks()->count();
        
        if ($totalTasks === 0) {
            $project->update(['progress' => 0]);
            return;
        }

        $completedTasks = $project->tasks()->where('status', 'completed')->count();
        $progress = round(($completedTasks / $totalTasks) * 100);
        
        $project->update(['progress' => $progress]);

        // Auto-update project status based on progress
        if ($progress === 100) {
            $project->update(['status' => 'completed']);
        } elseif ($progress > 0 && $project->status === 'planning') {
            $project->update(['status' => 'in_progress']);
        }

        // Update site progress if project belongs to a site
        // Site progress is now automatically calculated as average of project progress
        // No need to manually update site since it uses dynamic attribute calculation
    }

    /**
     * Get task attachments (AJAX endpoint)
     */
    public function getAttachments(Task $task)
    {
        $this->authorize('view', $task);
        
        $attachments = $task->documents()->with('uploadedBy')->get()->map(function ($document) {
            return [
                'id' => $document->id,
                'name' => $document->name,
                'original_name' => $document->original_name,
                'file_size' => $document->file_size,
                'formatted_file_size' => $document->formatted_file_size,
                'mime_type' => $document->mime_type,
                'extension' => $document->extension,
                'uploaded_by' => $document->uploadedBy->name ?? 'Unknown',
                'uploaded_at' => $document->created_at->format('M j, Y'),
                'is_image' => $document->isImage(),
            ];
        });
        
        return response()->json([
            'attachments' => $attachments,
            'count' => $attachments->count(),
        ]);
    }

    /**
     * Get updated progress data for projects and sites (AJAX endpoint)
     */
    public function getProgressData(Request $request)
    {
        $user = auth()->user();
        $companyId = $user->company_id;

        $data = [];

        // Get project progress if project_id is provided
        if ($request->has('project_id')) {
            $project = Project::where('company_id', $companyId)
                             ->findOrFail($request->project_id);
            
            $data['project'] = [
                'id' => $project->id,
                'progress' => $project->progress,
                'status' => $project->status,
                'completed_tasks' => $project->tasks()->where('status', 'completed')->count(),
                'total_tasks' => $project->tasks()->count(),
            ];

            // Include site progress if project belongs to a site
            if ($project->site_id) {
                $site = $project->site;
                $data['site'] = [
                    'id' => $site->id,
                    'progress' => $site->progress, // This uses the updated calculation
                    'status' => $site->status,
                ];
            }
        }

        // Get site progress if site_id is provided
        if ($request->has('site_id')) {
            $site = \App\Models\Site::where('company_id', $companyId)
                                   ->findOrFail($request->site_id);
            
            $data['site'] = [
                'id' => $site->id,
                'progress' => $site->progress,
                'status' => $site->status,
                'completed_projects' => $site->projects()->where('status', 'completed')->count(),
                'total_projects' => $site->projects()->count(),
            ];
        }

        // Get dashboard statistics if requested
        if ($request->has('dashboard')) {
            $activeProjects = Project::where('company_id', $companyId)
                                    ->whereNotIn('status', ['completed', 'cancelled'])
                                    ->with('site')
                                    ->get()
                                    ->map(function($project) {
                                        return [
                                            'id' => $project->id,
                                            'name' => $project->name,
                                            'progress' => $project->progress,
                                            'site_name' => $project->site->name ?? 'No Site',
                                        ];
                                    });

            $activeSites = \App\Models\Site::where('company_id', $companyId)
                                          ->where('is_active', true)
                                          ->get()
                                          ->map(function($site) {
                                              return [
                                                  'id' => $site->id,
                                                  'name' => $site->name,
                                                  'progress' => $site->progress,
                                                  'status' => $site->status,
                                              ];
                                          });

            $data['dashboard'] = [
                'active_projects' => $activeProjects,
                'active_sites' => $activeSites,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get task delay information (AJAX endpoint)
     */
    public function getDelays(Task $task)
    {
        $this->authorize('view', $task);
        
        return response()->json([
            'is_delayed' => $task->is_delayed,
            'delay_days' => $task->delay_days,
            'delay_reason' => $task->delay_reason,
            'original_due_date' => $task->original_due_date ? $task->original_due_date->format('M j, Y') : null,
            'current_due_date' => $task->due_date ? $task->due_date->format('M j, Y') : null,
            'due_date_changed' => $task->original_due_date && $task->due_date && !$task->original_due_date->equalTo($task->due_date),
        ]);
    }

    /**
     * Apply delay to task
     */
    public function applyDelay(Request $request, Task $task)
    {
        $user = auth()->user();
        
        // Check company access
        if ($task->company_id !== $user->company_id && !$user->isSuperAdmin()) {
            abort(404, 'Task not found.');
        }

        // Check permissions
        if (!($user->canManageTasks() || in_array($user->role, ['site_manager', 'project_manager']))) {
            abort(403, 'You do not have permission to apply delays to tasks.');
        }

        $validated = $request->validate([
            'delay_days' => 'required|integer|min:1|max:365',
            'delay_reason' => 'required|string|max:1000'
        ]);

        // Apply delay
        $task->update([
            'is_delayed' => true,
            'delay_days' => $validated['delay_days'],
            'delay_reason' => $validated['delay_reason'],
            'delay_applied_date' => now(),
            'delay_applied_by' => $user->id,
            'delay_removed_date' => null, // Clear any previous removal
        ]);

        return response()->json([
            'success' => true,
            'message' => "Task delayed by {$validated['delay_days']} days successfully."
        ]);
    }

    /**
     * Remove delay from task
     */
    public function removeDelay(Task $task)
    {
        $user = auth()->user();
        
        // Check company access
        if ($task->company_id !== $user->company_id && !$user->isSuperAdmin()) {
            abort(404, 'Task not found.');
        }

        // Check permissions
        if (!($user->canManageTasks() || in_array($user->role, ['site_manager', 'project_manager']))) {
            abort(403, 'You do not have permission to remove delays from tasks.');
        }

        // Remove delay
        $task->update([
            'is_delayed' => false,
            'delay_removed_date' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task delay removed successfully.'
        ]);
    }

    /**
     * Apply on hold to task
     */
    public function applyOnHold(Request $request, Task $task)
    {
        $user = auth()->user();
        
        // Check company access
        if ($task->company_id !== $user->company_id && !$user->isSuperAdmin()) {
            abort(404, 'Task not found.');
        }

        // Check permissions
        if (!($user->canManageTasks() || in_array($user->role, ['site_manager', 'project_manager']))) {
            abort(403, 'You do not have permission to put tasks on hold.');
        }

        $validated = $request->validate([
            'on_hold_reason' => 'required|string|max:1000'
        ]);

        // Apply on hold
        $task->update([
            'is_on_hold' => true,
            'on_hold_reason' => $validated['on_hold_reason'],
            'on_hold_date' => now(),
            'on_hold_applied_by' => $user->id,
            'on_hold_removed_date' => null, // Clear any previous removal
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task put on hold successfully.'
        ]);
    }

    /**
     * Remove on hold from task
     */
    public function removeOnHold(Task $task)
    {
        $user = auth()->user();
        
        // Check company access
        if ($task->company_id !== $user->company_id && !$user->isSuperAdmin()) {
            abort(404, 'Task not found.');
        }

        // Check permissions
        if (!($user->canManageTasks() || in_array($user->role, ['site_manager', 'project_manager']))) {
            abort(403, 'You do not have permission to remove hold from tasks.');
        }

        // Remove on hold
        $task->update([
            'is_on_hold' => false,
            'on_hold_removed_date' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task hold removed successfully.'
        ]);
    }
}