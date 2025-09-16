<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectSnagging;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectSnaggingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Project $project)
    {
        $snaggings = $project->projectSnaggings()
            ->with(['reporter', 'assignee', 'resolver', 'task'])
            ->latest()
            ->get();

        return response()->json($snaggings);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Project $project)
    {
        $tasks = $project->tasks()->select('id', 'title')->get();
        $users = User::forCompany()->select('id', 'name')->get();
        
        return response()->json([
            'item_number' => ProjectSnagging::generateItemNumber($project->id),
            'categories' => [
                'defect' => 'Defect',
                'incomplete' => 'Incomplete Work',
                'damage' => 'Damage',
                'quality' => 'Quality Issue',
                'safety' => 'Safety Concern',
                'compliance' => 'Compliance Issue',
                'other' => 'Other'
            ],
            'severities' => [
                'low' => 'Low',
                'medium' => 'Medium',
                'high' => 'High',
                'critical' => 'Critical'
            ],
            'tasks' => $tasks,
            'users' => $users
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Project $project)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'location' => 'required|string|max:255',
                'category' => 'required|in:defect,incomplete,damage,quality,safety,compliance,other',
                'severity' => 'required|in:low,medium,high,critical',
                'identified_date' => 'required|date',
                'target_completion_date' => 'nullable|date|after:identified_date',
                'task_id' => 'nullable|exists:tasks,id',
                'assigned_to' => 'nullable|exists:users,id',
                'trade_responsible' => 'nullable|string|max:255',
                'cost_to_fix' => 'nullable|numeric|min:0',
                'client_reported' => 'nullable|boolean',
                'photos_before' => 'nullable|array|max:5',
                'photos_before.*' => 'file|mimes:jpg,jpeg,png|max:10240' // 10MB per photo
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        try {
            $validated['project_id'] = $project->id;
            $validated['company_id'] = auth()->user()->company_id;
            $validated['reported_by'] = auth()->id();
            $validated['item_number'] = ProjectSnagging::generateItemNumber($project->id);
            $validated['client_reported'] = $validated['client_reported'] ?? false;

            // Handle photo uploads
            if ($request->hasFile('photos_before')) {
                $photosPaths = [];
                foreach ($request->file('photos_before') as $photo) {
                    $filename = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                    $path = $photo->storeAs('snagging-photos/' . $project->id, $filename, 'public');
                    $photosPaths[] = $path;
                }
                $validated['photos_before'] = $photosPaths;
            }

            $snagging = ProjectSnagging::create($validated);
            $snagging->load(['reporter', 'assignee', 'resolver', 'task']);

            return response()->json([
                'success' => true,
                'message' => 'Snagging item created successfully',
                'snagging' => $snagging
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating snagging item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project, ProjectSnagging $snagging)
    {
        $snagging->load(['reporter', 'assignee', 'resolver', 'task']);
        return view('project-snagging.show', compact('project', 'snagging'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project, ProjectSnagging $snagging)
    {
        $snagging->load(['reporter', 'assignee', 'resolver', 'task']);
        $tasks = $project->tasks()->select('id', 'title')->get();
        $users = User::forCompany()->select('id', 'name')->get();
        
        return response()->json([
            'snagging' => $snagging,
            'categories' => [
                'defect' => 'Defect',
                'incomplete' => 'Incomplete Work',
                'damage' => 'Damage',
                'quality' => 'Quality Issue',
                'safety' => 'Safety Concern',
                'compliance' => 'Compliance Issue',
                'other' => 'Other'
            ],
            'severities' => [
                'low' => 'Low',
                'medium' => 'Medium',
                'high' => 'High',
                'critical' => 'Critical'
            ],
            'tasks' => $tasks,
            'users' => $users
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project, ProjectSnagging $snagging)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'category' => 'required|in:defect,incomplete,damage,quality,safety,compliance,other',
            'severity' => 'required|in:low,medium,high,critical',
            'identified_date' => 'required|date',
            'target_completion_date' => 'nullable|date|after:identified_date',
            'task_id' => 'nullable|exists:tasks,id',
            'assigned_to' => 'nullable|exists:users,id',
            'trade_responsible' => 'nullable|string|max:255',
            'cost_to_fix' => 'nullable|numeric|min:0',
            'client_reported' => 'boolean',
            'resolution_notes' => 'nullable|string',
            'photos_before' => 'nullable|array|max:5',
            'photos_before.*' => 'file|mimes:jpg,jpeg,png|max:10240',
            'photos_after' => 'nullable|array|max:5',
            'photos_after.*' => 'file|mimes:jpg,jpeg,png|max:10240'
        ]);

        // Handle photo uploads
        if ($request->hasFile('photos_before')) {
            // Delete old photos
            if ($snagging->photos_before) {
                foreach ($snagging->photos_before as $oldPhoto) {
                    Storage::disk('public')->delete($oldPhoto);
                }
            }

            $photosPaths = [];
            foreach ($request->file('photos_before') as $photo) {
                $filename = time() . '_before_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                $path = $photo->storeAs('snagging-photos/' . $project->id, $filename, 'public');
                $photosPaths[] = $path;
            }
            $validated['photos_before'] = $photosPaths;
        }

        if ($request->hasFile('photos_after')) {
            // Delete old after photos
            if ($snagging->photos_after) {
                foreach ($snagging->photos_after as $oldPhoto) {
                    Storage::disk('public')->delete($oldPhoto);
                }
            }

            $photosPaths = [];
            foreach ($request->file('photos_after') as $photo) {
                $filename = time() . '_after_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                $path = $photo->storeAs('snagging-photos/' . $project->id, $filename, 'public');
                $photosPaths[] = $path;
            }
            $validated['photos_after'] = $photosPaths;
        }

        $snagging->update($validated);
        $snagging->load(['reporter', 'assignee', 'resolver', 'task']);

        return response()->json([
            'success' => true,
            'message' => 'Snagging item updated successfully',
            'snagging' => $snagging
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, ProjectSnagging $snagging)
    {
        // Delete associated photos
        if ($snagging->photos_before) {
            foreach ($snagging->photos_before as $photo) {
                Storage::disk('public')->delete($photo);
            }
        }

        if ($snagging->photos_after) {
            foreach ($snagging->photos_after as $photo) {
                Storage::disk('public')->delete($photo);
            }
        }

        $snagging->delete();

        return response()->json([
            'success' => true,
            'message' => 'Snagging item deleted successfully'
        ]);
    }

    /**
     * Resolve a snagging item
     */
    public function resolve(Request $request, Project $project, ProjectSnagging $snagging)
    {
        $validated = $request->validate([
            'resolution_notes' => 'required|string',
            'photos_after' => 'nullable|array|max:5',
            'photos_after.*' => 'file|mimes:jpg,jpeg,png|max:10240'
        ]);

        $updateData = [
            'status' => 'resolved',
            'resolution_notes' => $validated['resolution_notes'],
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
            'actual_completion_date' => now()->toDateString()
        ];

        // Handle after photos
        if ($request->hasFile('photos_after')) {
            $photosPaths = [];
            foreach ($request->file('photos_after') as $photo) {
                $filename = time() . '_after_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                $path = $photo->storeAs('snagging-photos/' . $project->id, $filename, 'public');
                $photosPaths[] = $path;
            }
            $updateData['photos_after'] = $photosPaths;
        }

        $snagging->update($updateData);
        $snagging->load(['reporter', 'assignee', 'resolver', 'task']);

        return response()->json([
            'success' => true,
            'message' => 'Snagging item resolved successfully',
            'snagging' => $snagging
        ]);
    }

    /**
     * Close a snagging item
     */
    public function close(Project $project, ProjectSnagging $snagging)
    {
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Unauthorized to close snagging items');
        }

        if ($snagging->status !== 'resolved') {
            return response()->json([
                'success' => false,
                'message' => 'Only resolved items can be closed'
            ], 422);
        }

        $snagging->update([
            'status' => 'closed'
        ]);

        $snagging->load(['reporter', 'assignee', 'resolver', 'task']);

        return response()->json([
            'success' => true,
            'message' => 'Snagging item closed successfully',
            'snagging' => $snagging
        ]);
    }
}
