<?php

namespace App\Http\Controllers;

use App\Models\ToolHireRequest;
use App\Models\Site;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ToolHireController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('company.access');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = ToolHireRequest::forCompany($user->company_id)
            ->with(['requestedBy', 'site', 'project', 'approvedBy', 'assignedTo']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tool_category')) {
            $query->where('tool_category', $request->tool_category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('tool_name', 'like', "%{$search}%");
            });
        }

        $toolHireRequests = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        $sites = Site::forCompany($user->company_id)->get();
        $stats = $this->getToolHireStatistics($user->company_id);

        return view('tool-hire.index', compact('toolHireRequests', 'sites', 'stats'));
    }

    public function create()
    {
        $user = auth()->user();
        $sites = Site::forCompany($user->company_id)->get();
        $projects = Project::forCompany($user->company_id)->get();

        return view('tool-hire.create', compact('sites', 'projects'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'tool_category' => 'required|string',
            'quantity' => 'required|integer|min:1|max:100',
            'urgency' => 'required|string',
            'hire_start_date' => 'required|date|after_or_equal:today',
            'hire_end_date' => 'required|date|after:hire_start_date',
            'delivery_method' => 'required|string',
            'site_id' => 'nullable|exists:sites,id',
            'project_id' => 'nullable|exists:projects,id',
            'estimated_daily_rate' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $user->company_id;
        $validated['requested_by'] = $user->id;
        $validated['status'] = ToolHireRequest::STATUS_PENDING_APPROVAL;

        $toolHireRequest = ToolHireRequest::create($validated);

        return redirect()->route('tool-hire.show', $toolHireRequest)
            ->with('success', 'Tool hire request created successfully!');
    }

    public function show(ToolHireRequest $toolHireRequest)
    {
        if ($toolHireRequest->company_id !== auth()->user()->company_id) {
            abort(404);
        }

        $toolHireRequest->load(['requestedBy', 'site', 'project', 'approvedBy', 'assignedTo']);
        return view('tool-hire.show', compact('toolHireRequest'));
    }

    public function edit(ToolHireRequest $toolHireRequest)
    {
        if ($toolHireRequest->company_id !== auth()->user()->company_id) {
            abort(404);
        }

        if (!$toolHireRequest->canBeEdited()) {
            return redirect()->route('tool-hire.show', $toolHireRequest)
                ->with('error', 'This request cannot be edited.');
        }

        $user = auth()->user();
        $sites = Site::forCompany($user->company_id)->get();
        $projects = Project::forCompany($user->company_id)->get();

        return view('tool-hire.edit', compact('toolHireRequest', 'sites', 'projects'));
    }

    public function update(Request $request, ToolHireRequest $toolHireRequest)
    {
        if ($toolHireRequest->company_id !== auth()->user()->company_id) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'tool_category' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'hire_start_date' => 'required|date',
            'hire_end_date' => 'required|date|after:hire_start_date',
        ]);

        $toolHireRequest->update($validated);

        return redirect()->route('tool-hire.show', $toolHireRequest)
            ->with('success', 'Request updated successfully!');
    }

    public function destroy(ToolHireRequest $toolHireRequest)
    {
        if ($toolHireRequest->company_id !== auth()->user()->company_id) {
            abort(404);
        }

        if ($toolHireRequest->status !== ToolHireRequest::STATUS_DRAFT) {
            return redirect()->route('tool-hire.show', $toolHireRequest)
                ->with('error', 'Only draft requests can be deleted.');
        }

        $toolHireRequest->delete();
        return redirect()->route('tool-hire.index')->with('success', 'Request deleted!');
    }

    public function approve(ToolHireRequest $toolHireRequest)
    {
        if ($toolHireRequest->company_id !== auth()->user()->company_id) {
            abort(404);
        }

        $user = auth()->user();
        if (!$user->isCompanyAdmin() && !$user->canManageProjects()) {
            abort(403);
        }

        $toolHireRequest->update([
            'status' => ToolHireRequest::STATUS_APPROVED,
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        return redirect()->route('tool-hire.show', $toolHireRequest)
            ->with('success', 'Request approved!');
    }

    public function reject(Request $request, ToolHireRequest $toolHireRequest)
    {
        $request->validate(['rejection_reason' => 'required|string']);

        $toolHireRequest->update([
            'status' => ToolHireRequest::STATUS_CANCELLED,
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->route('tool-hire.show', $toolHireRequest)
            ->with('success', 'Request rejected.');
    }

    private function getToolHireStatistics($companyId)
    {
        return [
            'total_requests' => ToolHireRequest::forCompany($companyId)->count(),
            'pending_approval' => ToolHireRequest::forCompany($companyId)->where('status', ToolHireRequest::STATUS_PENDING_APPROVAL)->count(),
            'active_requests' => ToolHireRequest::forCompany($companyId)->active()->count(),
            'currently_hired' => ToolHireRequest::forCompany($companyId)->whereIn('status', [ToolHireRequest::STATUS_DELIVERED, ToolHireRequest::STATUS_IN_USE])->count(),
            'urgent_requests' => ToolHireRequest::forCompany($companyId)->urgent()->count(),
            'overdue_requests' => ToolHireRequest::forCompany($companyId)->overdue()->count(),
        ];
    }
}
