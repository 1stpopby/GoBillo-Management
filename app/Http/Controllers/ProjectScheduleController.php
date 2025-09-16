<?php

namespace App\Http\Controllers;

use App\Models\ProjectSchedule;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProjectScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $view = $request->get('view', 'calendar'); // calendar, gantt, timeline, list
        
        // Get projects for filter
        $projects = Project::forCompany($user->company_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
        
        // Get team members for assignment filter
        $teamMembers = User::where('company_id', $user->company_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
        
        // Build query
        $query = ProjectSchedule::forCompany($user->company_id)
            ->with(['project', 'assignedTo', 'subtasks']);
        
        // Apply filters
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }
        
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        
        if ($request->filled('date_range')) {
            $dateRange = explode(' to ', $request->date_range);
            if (count($dateRange) == 2) {
                $query->where(function($q) use ($dateRange) {
                    $q->whereBetween('start_date', [Carbon::parse($dateRange[0]), Carbon::parse($dateRange[1])])
                      ->orWhereBetween('end_date', [Carbon::parse($dateRange[0]), Carbon::parse($dateRange[1])]);
                });
            }
        }
        
        // Get schedules based on view type
        if ($view === 'list') {
            $schedules = $query->rootTasks()
                ->orderBy('start_date')
                ->orderBy('order_index')
                ->paginate(20);
        } else {
            $schedules = $query->get();
        }
        
        // Get statistics
        $stats = [
            'total_tasks' => ProjectSchedule::forCompany($user->company_id)->count(),
            'in_progress' => ProjectSchedule::forCompany($user->company_id)->where('status', 'in_progress')->count(),
            'completed' => ProjectSchedule::forCompany($user->company_id)->where('status', 'completed')->count(),
            'overdue' => ProjectSchedule::forCompany($user->company_id)->overdue()->count(),
            'upcoming' => ProjectSchedule::forCompany($user->company_id)->upcoming(7)->count(),
            'milestones' => ProjectSchedule::forCompany($user->company_id)->milestones()->count(),
        ];
        
        // Calculate overall progress
        $totalProgress = ProjectSchedule::forCompany($user->company_id)
            ->whereNull('parent_task_id')
            ->avg('progress') ?? 0;
        
        return view('project-schedules.index', compact(
            'schedules',
            'projects',
            'teamMembers',
            'stats',
            'totalProgress',
            'view'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        
        $projects = Project::forCompany($user->company_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
        
        $teamMembers = User::where('company_id', $user->company_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
        
        $parentTasks = [];
        if ($request->filled('project_id')) {
            $parentTasks = ProjectSchedule::forProject($request->project_id)
                ->whereNull('parent_task_id')
                ->select('id', 'task_name')
                ->orderBy('order_index')
                ->get();
        }
        
        return view('project-schedules.create', compact('projects', 'teamMembers', 'parentTasks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'task_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'priority' => 'required|in:low,medium,high,critical',
            'assigned_to' => 'nullable|exists:users,id',
            'parent_task_id' => 'nullable|exists:project_schedules,id',
            'dependencies' => 'nullable|array',
            'dependencies.*' => 'exists:project_schedules,id',
            'is_milestone' => 'boolean',
            'estimated_hours' => 'nullable|numeric|min:0',
            'resources' => 'nullable|array',
            'color' => 'nullable|string|size:7',
        ]);
        
        $validated['company_id'] = auth()->user()->company_id;
        $validated['status'] = ProjectSchedule::STATUS_NOT_STARTED;
        $validated['progress'] = 0;
        
        // Get the next order index
        $maxOrder = ProjectSchedule::where('project_id', $validated['project_id'])
            ->where('parent_task_id', $validated['parent_task_id'] ?? null)
            ->max('order_index') ?? -1;
        $validated['order_index'] = $maxOrder + 1;
        
        $schedule = ProjectSchedule::create($validated);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Task created successfully',
                'data' => $schedule->load(['project', 'assignedTo'])
            ]);
        }
        
        return redirect()->route('project-schedules.index')
            ->with('success', 'Task created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProjectSchedule $projectSchedule)
    {
        $projectSchedule->load(['project', 'assignedTo', 'parentTask', 'subtasks.assignedTo']);
        
        // Get dependent tasks
        $dependentTasks = [];
        if ($projectSchedule->dependencies) {
            $dependentTasks = ProjectSchedule::whereIn('id', $projectSchedule->dependencies)->get();
        }
        
        // Get tasks that depend on this task
        $dependingTasks = $projectSchedule->dependentTasks();
        
        return view('project-schedules.show', compact('projectSchedule', 'dependentTasks', 'dependingTasks'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProjectSchedule $projectSchedule)
    {
        $user = auth()->user();
        
        $projects = Project::forCompany($user->company_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
        
        $teamMembers = User::where('company_id', $user->company_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
        
        $parentTasks = ProjectSchedule::forProject($projectSchedule->project_id)
            ->whereNull('parent_task_id')
            ->where('id', '!=', $projectSchedule->id)
            ->select('id', 'task_name')
            ->orderBy('order_index')
            ->get();
        
        $availableDependencies = ProjectSchedule::forProject($projectSchedule->project_id)
            ->where('id', '!=', $projectSchedule->id)
            ->select('id', 'task_name')
            ->orderBy('start_date')
            ->get();
        
        return view('project-schedules.edit', compact('projectSchedule', 'projects', 'teamMembers', 'parentTasks', 'availableDependencies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProjectSchedule $projectSchedule)
    {
        $validated = $request->validate([
            'task_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'progress' => 'nullable|numeric|min:0|max:100',
            'status' => 'nullable|in:not_started,in_progress,completed,delayed,on_hold,cancelled',
            'priority' => 'required|in:low,medium,high,critical',
            'assigned_to' => 'nullable|exists:users,id',
            'parent_task_id' => 'nullable|exists:project_schedules,id',
            'dependencies' => 'nullable|array',
            'dependencies.*' => 'exists:project_schedules,id',
            'is_milestone' => 'boolean',
            'estimated_hours' => 'nullable|numeric|min:0',
            'actual_hours' => 'nullable|numeric|min:0',
            'resources' => 'nullable|array',
            'color' => 'nullable|string|size:7',
            'notes' => 'nullable|string',
        ]);
        
        // Prevent circular dependencies
        if (isset($validated['parent_task_id']) && $validated['parent_task_id'] == $projectSchedule->id) {
            return back()->withErrors(['parent_task_id' => 'A task cannot be its own parent']);
        }
        
        $projectSchedule->update($validated);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Task updated successfully',
                'data' => $projectSchedule->load(['project', 'assignedTo'])
            ]);
        }
        
        return redirect()->route('project-schedules.index')
            ->with('success', 'Task updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProjectSchedule $projectSchedule)
    {
        // Update subtasks to have no parent
        $projectSchedule->subtasks()->update(['parent_task_id' => null]);
        
        // Remove this task from dependencies of other tasks
        $dependentTasks = $projectSchedule->dependentTasks();
        foreach ($dependentTasks as $task) {
            $dependencies = array_filter($task->dependencies ?? [], function($id) use ($projectSchedule) {
                return $id != $projectSchedule->id;
            });
            $task->update(['dependencies' => array_values($dependencies)]);
        }
        
        $projectSchedule->delete();
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Task deleted successfully'
            ]);
        }
        
        return redirect()->route('project-schedules.index')
            ->with('success', 'Task deleted successfully');
    }

    /**
     * Update task progress
     */
    public function updateProgress(Request $request, ProjectSchedule $projectSchedule)
    {
        $validated = $request->validate([
            'progress' => 'required|numeric|min:0|max:100',
        ]);
        
        $projectSchedule->updateProgress($validated['progress']);
        
        return response()->json([
            'success' => true,
            'message' => 'Progress updated successfully',
            'data' => $projectSchedule
        ]);
    }

    /**
     * Update task status
     */
    public function updateStatus(Request $request, ProjectSchedule $projectSchedule)
    {
        $validated = $request->validate([
            'status' => 'required|in:not_started,in_progress,completed,delayed,on_hold,cancelled',
        ]);
        
        $projectSchedule->update(['status' => $validated['status']]);
        
        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'data' => $projectSchedule
        ]);
    }

    /**
     * Reorder tasks
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'tasks' => 'required|array',
            'tasks.*.id' => 'required|exists:project_schedules,id',
            'tasks.*.order_index' => 'required|integer|min:0',
        ]);
        
        DB::transaction(function () use ($validated) {
            foreach ($validated['tasks'] as $task) {
                ProjectSchedule::where('id', $task['id'])
                    ->update(['order_index' => $task['order_index']]);
            }
        });
        
        return response()->json([
            'success' => true,
            'message' => 'Tasks reordered successfully'
        ]);
    }

    /**
     * Get Gantt chart data
     */
    public function ganttData(Request $request)
    {
        $user = auth()->user();
        
        $query = ProjectSchedule::forCompany($user->company_id)
            ->with(['project', 'assignedTo']);
        
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }
        
        $schedules = $query->get();
        
        $ganttData = [
            'data' => $schedules->map(fn($s) => $s->getGanttData())->toArray(),
            'links' => $this->getGanttLinks($schedules),
        ];
        
        return response()->json($ganttData);
    }

    /**
     * Get calendar events
     */
    public function calendarEvents(Request $request)
    {
        $user = auth()->user();
        
        $query = ProjectSchedule::forCompany($user->company_id)
            ->with(['project', 'assignedTo']);
        
        if ($request->filled('start') && $request->filled('end')) {
            $query->where(function($q) use ($request) {
                $q->whereBetween('start_date', [$request->start, $request->end])
                  ->orWhereBetween('end_date', [$request->start, $request->end]);
            });
        }
        
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }
        
        $schedules = $query->get();
        
        $events = $schedules->map(fn($s) => $s->getCalendarEvent())->toArray();
        
        return response()->json($events);
    }

    /**
     * Get timeline data
     */
    public function timelineData(Request $request)
    {
        $user = auth()->user();
        
        $query = ProjectSchedule::forCompany($user->company_id)
            ->with(['project', 'assignedTo'])
            ->rootTasks();
        
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }
        
        $schedules = $query->orderBy('start_date')->get();
        
        $timelineData = $schedules->map(function ($schedule) {
            return [
                'id' => $schedule->id,
                'content' => $schedule->task_name,
                'start' => $schedule->start_date->format('Y-m-d'),
                'end' => $schedule->end_date->format('Y-m-d'),
                'group' => $schedule->project_id,
                'className' => 'status-' . $schedule->status,
                'style' => 'background-color: ' . $schedule->color,
                'type' => $schedule->is_milestone ? 'point' : 'range',
            ];
        });
        
        return response()->json($timelineData);
    }

    /**
     * Helper method to get Gantt links
     */
    private function getGanttLinks($schedules)
    {
        $links = [];
        
        foreach ($schedules as $schedule) {
            if ($schedule->dependencies) {
                foreach ($schedule->dependencies as $dependencyId) {
                    $links[] = [
                        'id' => uniqid(),
                        'source' => $dependencyId,
                        'target' => $schedule->id,
                        'type' => '0', // finish-to-start
                    ];
                }
            }
        }
        
        return $links;
    }
}


