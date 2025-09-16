<?php

namespace App\Http\Controllers;

use App\Models\TaskCategory;
use Illuminate\Http\Request;

class TaskCategoryController extends Controller
{
    public function __construct()
    {
        // Only company admins and super admins can manage task categories
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->canManageProjects()) {
                abort(403, 'Access denied. Only administrators can manage task categories.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of task categories
     */
    public function index()
    {
        $categories = TaskCategory::forCompany()
                                ->ordered()
                                ->withCount('tasks')
                                ->get();

        return view('task-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new task category
     */
    public function create()
    {
        return view('task-categories.create');
    }

    /**
     * Store a newly created task category
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:task_categories,name,NULL,id,company_id,' . auth()->user()->company_id,
            'description' => 'nullable|string',
            'color' => 'required|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        $category = TaskCategory::create([
            'company_id' => auth()->user()->company_id,
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,
            'icon' => $request->icon,
            'sort_order' => $request->sort_order ?? 0
        ]);

        return redirect()->route('task-categories.index')
                        ->with('success', 'Task category created successfully.');
    }

    /**
     * Display the specified task category
     */
    public function show(TaskCategory $taskCategory)
    {
        // Ensure the category belongs to the current company
        if ($taskCategory->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $taskCategory->load(['tasks.project.site', 'tasks.assignedUser']);

        return view('task-categories.show', compact('taskCategory'));
    }

    /**
     * Show the form for editing the task category
     */
    public function edit(TaskCategory $taskCategory)
    {
        // Ensure the category belongs to the current company
        if ($taskCategory->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        return view('task-categories.edit', compact('taskCategory'));
    }

    /**
     * Update the specified task category
     */
    public function update(Request $request, TaskCategory $taskCategory)
    {
        // Ensure the category belongs to the current company
        if ($taskCategory->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:task_categories,name,' . $taskCategory->id . ',id,company_id,' . auth()->user()->company_id,
            'description' => 'nullable|string',
            'color' => 'required|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $taskCategory->update([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,
            'icon' => $request->icon,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->boolean('is_active', true)
        ]);

        return redirect()->route('task-categories.index')
                        ->with('success', 'Task category updated successfully.');
    }

    /**
     * Remove the specified task category
     */
    public function destroy(TaskCategory $taskCategory)
    {
        // Ensure the category belongs to the current company
        if ($taskCategory->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        // Check if category has tasks
        if ($taskCategory->tasks()->exists()) {
            return redirect()->route('task-categories.index')
                            ->with('error', 'Cannot delete task category with existing tasks. Reassign or delete the tasks first.');
        }

        $taskCategory->delete();

        return redirect()->route('task-categories.index')
                        ->with('success', 'Task category deleted successfully.');
    }

    /**
     * Toggle the active status of a task category
     */
    public function toggle(TaskCategory $taskCategory)
    {
        // Ensure the category belongs to the current company
        if ($taskCategory->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $taskCategory->update([
            'is_active' => !$taskCategory->is_active
        ]);

        $status = $taskCategory->is_active ? 'activated' : 'deactivated';

        return redirect()->route('task-categories.index')
                        ->with('success', "Task category {$status} successfully.");
    }

    /**
     * Reorder task categories
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:task_categories,id',
            'categories.*.sort_order' => 'required|integer|min:0'
        ]);

        foreach ($request->categories as $categoryData) {
            $category = TaskCategory::forCompany()->find($categoryData['id']);
            if ($category) {
                $category->update(['sort_order' => $categoryData['sort_order']]);
            }
        }

        return response()->json(['success' => true]);
    }
}
