<?php

namespace App\Http\Controllers;

use App\Models\HireRequest;
use App\Models\Site;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HireController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('company.access');
    }

    /**
     * Display a listing of hire requests
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = HireRequest::forCompany($user->company_id)
            ->with(['requestedBy', 'site', 'project', 'approvedBy', 'assignedTo']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('urgency')) {
            $query->where('urgency', $request->urgency);
        }

        if ($request->filled('position_type')) {
            $query->where('position_type', $request->position_type);
        }

        if ($request->filled('site_id')) {
            $query->where('site_id', $request->site_id);
        }

        if ($request->filled('requested_by')) {
            $query->where('requested_by', $request->requested_by);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('required_skills', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $hireRequests = $query->paginate(20)->withQueryString();

        // Get filter options
        $sites = Site::forCompany($user->company_id)->get();
        $managers = User::where('company_id', $user->company_id)
            ->whereIn('role', [User::ROLE_COMPANY_ADMIN, User::ROLE_SITE_MANAGER])
            ->get();

        // Get statistics
        $stats = $this->getHireStatistics($user->company_id);

        return view('hire.index', compact('hireRequests', 'sites', 'managers', 'stats'));
    }

    /**
     * Show the form for creating a new hire request
     */
    public function create()
    {
        $user = auth()->user();
        
        $sites = Site::forCompany($user->company_id)->get();
        $projects = Project::forCompany($user->company_id)->get();

        return view('hire.create', compact('sites', 'projects'));
    }

    /**
     * Store a newly created hire request
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'position_type' => 'required|string|in:' . implode(',', array_keys(HireRequest::getPositionTypeOptions())),
            'employment_type' => 'required|string|in:' . implode(',', array_keys(HireRequest::getEmploymentTypeOptions())),
            'quantity' => 'required|integer|min:1|max:100',
            'urgency' => 'required|string|in:' . implode(',', array_keys(HireRequest::getUrgencyOptions())),
            'site_id' => 'nullable|exists:sites,id',
            'project_id' => 'nullable|exists:projects,id',
            'required_skills' => 'nullable|string',
            'required_qualifications' => 'nullable|string',
            'required_certifications' => 'nullable|string',
            'min_experience_years' => 'nullable|integer|min:0|max:50',
            'offered_rate' => 'nullable|numeric|min:0|max:9999.99',
            'rate_type' => 'required|string|in:' . implode(',', array_keys(HireRequest::getRateTypeOptions())),
            'benefits' => 'nullable|string',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'deadline' => 'nullable|date|after_or_equal:today',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $user->company_id;
        $validated['requested_by'] = $user->id;
        $validated['status'] = HireRequest::STATUS_PENDING_APPROVAL;

        $hireRequest = HireRequest::create($validated);

        return redirect()->route('hire.show', $hireRequest)
            ->with('success', 'Hire request created successfully and submitted for approval!');
    }

    /**
     * Display the specified hire request
     */
    public function show(HireRequest $hireRequest)
    {
        // Ensure user can access this hire request
        if ($hireRequest->company_id !== auth()->user()->company_id) {
            abort(404);
        }

        $hireRequest->load(['requestedBy', 'site', 'project', 'approvedBy', 'assignedTo']);

        return view('hire.show', compact('hireRequest'));
    }

    /**
     * Show the form for editing the hire request
     */
    public function edit(HireRequest $hireRequest)
    {
        // Ensure user can access this hire request
        if ($hireRequest->company_id !== auth()->user()->company_id) {
            abort(404);
        }

        // Check if user can edit
        if (!$hireRequest->canBeEdited()) {
            return redirect()->route('hire.show', $hireRequest)
                ->with('error', 'This hire request cannot be edited in its current status.');
        }

        $user = auth()->user();
        $sites = Site::forCompany($user->company_id)->get();
        $projects = Project::forCompany($user->company_id)->get();

        return view('hire.edit', compact('hireRequest', 'sites', 'projects'));
    }

    /**
     * Update the specified hire request
     */
    public function update(Request $request, HireRequest $hireRequest)
    {
        // Ensure user can access this hire request
        if ($hireRequest->company_id !== auth()->user()->company_id) {
            abort(404);
        }

        // Check if user can edit
        if (!$hireRequest->canBeEdited()) {
            return redirect()->route('hire.show', $hireRequest)
                ->with('error', 'This hire request cannot be edited in its current status.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'position_type' => 'required|string|in:' . implode(',', array_keys(HireRequest::getPositionTypeOptions())),
            'employment_type' => 'required|string|in:' . implode(',', array_keys(HireRequest::getEmploymentTypeOptions())),
            'quantity' => 'required|integer|min:1|max:100',
            'urgency' => 'required|string|in:' . implode(',', array_keys(HireRequest::getUrgencyOptions())),
            'site_id' => 'nullable|exists:sites,id',
            'project_id' => 'nullable|exists:projects,id',
            'required_skills' => 'nullable|string',
            'required_qualifications' => 'nullable|string',
            'required_certifications' => 'nullable|string',
            'min_experience_years' => 'nullable|integer|min:0|max:50',
            'offered_rate' => 'nullable|numeric|min:0|max:9999.99',
            'rate_type' => 'required|string|in:' . implode(',', array_keys(HireRequest::getRateTypeOptions())),
            'benefits' => 'nullable|string',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'deadline' => 'nullable|date|after_or_equal:today',
            'notes' => 'nullable|string',
        ]);

        $hireRequest->update($validated);

        return redirect()->route('hire.show', $hireRequest)
            ->with('success', 'Hire request updated successfully!');
    }

    /**
     * Remove the specified hire request
     */
    public function destroy(HireRequest $hireRequest)
    {
        // Ensure user can access this hire request
        if ($hireRequest->company_id !== auth()->user()->company_id) {
            abort(404);
        }

        // Check if user can delete (only drafts can be deleted)
        if ($hireRequest->status !== HireRequest::STATUS_DRAFT) {
            return redirect()->route('hire.show', $hireRequest)
                ->with('error', 'Only draft hire requests can be deleted.');
        }

        $hireRequest->delete();

        return redirect()->route('hire.index')
            ->with('success', 'Hire request deleted successfully!');
    }

    /**
     * Approve a hire request
     */
    public function approve(HireRequest $hireRequest)
    {
        // Ensure user can access this hire request
        if ($hireRequest->company_id !== auth()->user()->company_id) {
            abort(404);
        }

        // Check permissions
        $user = auth()->user();
        if (!$user->isCompanyAdmin() && !$user->canManageProjects()) {
            abort(403, 'You do not have permission to approve hire requests.');
        }

        if (!$hireRequest->canBeApproved()) {
            return redirect()->route('hire.show', $hireRequest)
                ->with('error', 'This hire request cannot be approved in its current status.');
        }

        $hireRequest->update([
            'status' => HireRequest::STATUS_APPROVED,
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        return redirect()->route('hire.show', $hireRequest)
            ->with('success', 'Hire request approved successfully!');
    }

    /**
     * Reject a hire request
     */
    public function reject(Request $request, HireRequest $hireRequest)
    {
        // Ensure user can access this hire request
        if ($hireRequest->company_id !== auth()->user()->company_id) {
            abort(404);
        }

        // Check permissions
        $user = auth()->user();
        if (!$user->isCompanyAdmin() && !$user->canManageProjects()) {
            abort(403, 'You do not have permission to reject hire requests.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $hireRequest->update([
            'status' => HireRequest::STATUS_CANCELLED,
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->route('hire.show', $hireRequest)
            ->with('success', 'Hire request rejected.');
    }

    /**
     * Mark hire request as filled
     */
    public function markFilled(HireRequest $hireRequest)
    {
        // Ensure user can access this hire request
        if ($hireRequest->company_id !== auth()->user()->company_id) {
            abort(404);
        }

        // Check permissions
        $user = auth()->user();
        if (!$user->isCompanyAdmin() && !$user->canManageProjects()) {
            abort(403, 'You do not have permission to mark hire requests as filled.');
        }

        $hireRequest->update([
            'status' => HireRequest::STATUS_FILLED,
            'hired_count' => $hireRequest->quantity, // Assume all positions were filled
        ]);

        return redirect()->route('hire.show', $hireRequest)
            ->with('success', 'Hire request marked as filled!');
    }

    /**
     * Get hiring statistics for the company
     */
    private function getHireStatistics($companyId)
    {
        return [
            'total_requests' => HireRequest::forCompany($companyId)->count(),
            'pending_approval' => HireRequest::forCompany($companyId)->where('status', HireRequest::STATUS_PENDING_APPROVAL)->count(),
            'active_requests' => HireRequest::forCompany($companyId)->active()->count(),
            'filled_requests' => HireRequest::forCompany($companyId)->where('status', HireRequest::STATUS_FILLED)->count(),
            'urgent_requests' => HireRequest::forCompany($companyId)->urgent()->count(),
            'overdue_requests' => HireRequest::forCompany($companyId)->overdue()->count(),
        ];
    }

    /**
     * Get projects for a specific site (AJAX endpoint)
     */
    public function getProjectsForSite(Request $request)
    {
        $siteId = $request->get('site_id');
        
        if (!$siteId) {
            return response()->json([]);
        }

        $projects = Project::where('site_id', $siteId)
            ->forCompany(auth()->user()->company_id)
            ->select('id', 'name')
            ->get();

        return response()->json($projects);
    }
}
