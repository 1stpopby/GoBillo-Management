<?php

namespace App\Http\Controllers;

use App\Models\HealthSafetyRams;
use App\Models\HealthSafetyToolboxTalk;
use App\Models\HealthSafetyIncident;
use App\Models\HealthSafetyInduction;
use App\Models\HealthSafetyFormTemplate;
use App\Models\HealthSafetyFormSubmission;
use App\Models\HealthSafetyObservation;
use App\Models\Site;
use App\Models\Project;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;

class HealthSafetyController extends Controller
{
    /**
     * Display the main Health & Safety dashboard
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $companyId = $user->company_id;
        
        // Get statistics
        $stats = [
            'active_rams' => HealthSafetyRams::where('company_id', $companyId)
                ->where('status', 'approved')
                ->where('valid_until', '>=', now())
                ->count(),
            
            'toolbox_talks_month' => HealthSafetyToolboxTalk::where('company_id', $companyId)
                ->whereMonth('conducted_at', now()->month)
                ->whereYear('conducted_at', now()->year)
                ->count(),
            
            'open_incidents' => HealthSafetyIncident::where('company_id', $companyId)
                ->whereIn('status', ['reported', 'under_investigation'])
                ->count(),
            
            'active_inductions' => HealthSafetyInduction::where('company_id', $companyId)
                ->where('status', 'active')
                ->where('valid_until', '>=', now())
                ->count(),
            
            'pending_forms' => HealthSafetyFormSubmission::where('company_id', $companyId)
                ->where('status', 'submitted')
                ->count(),
            
            'safety_observations' => HealthSafetyObservation::where('company_id', $companyId)
                ->where('status', 'open')
                ->count(),
        ];
        
        // Calculate incident rate (incidents per 100 employees per month)
        $employeeCount = Employee::where('company_id', $companyId)->count();
        $monthlyIncidents = HealthSafetyIncident::where('company_id', $companyId)
            ->whereMonth('occurred_at', now()->month)
            ->whereYear('occurred_at', now()->year)
            ->count();
        $stats['incident_rate'] = $employeeCount > 0 ? round(($monthlyIncidents / $employeeCount) * 100, 2) : 0;
        
        // Get recent activities
        $recentActivities = collect();
        
        // Recent RAMS
        $recentRams = HealthSafetyRams::where('company_id', $companyId)
            ->with(['site', 'createdBy'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($ram) {
                return [
                    'type' => 'rams',
                    'title' => $ram->title,
                    'description' => "RAMS created for " . ($ram->site ? $ram->site->name : 'General'),
                    'user' => $ram->createdBy->name,
                    'date' => $ram->created_at,
                    'icon' => 'bi-file-earmark-text',
                    'color' => 'primary'
                ];
            });
        
        // Recent Toolbox Talks
        $recentTalks = HealthSafetyToolboxTalk::where('company_id', $companyId)
            ->with(['site', 'conductedBy'])
            ->latest('conducted_at')
            ->limit(5)
            ->get()
            ->map(function ($talk) {
                return [
                    'type' => 'toolbox',
                    'title' => $talk->title,
                    'description' => "Toolbox talk conducted at " . ($talk->site ? $talk->site->name : $talk->location),
                    'user' => $talk->conductedBy->name,
                    'date' => $talk->conducted_at,
                    'icon' => 'bi-megaphone',
                    'color' => 'info'
                ];
            });
        
        // Recent Incidents
        $recentIncidents = HealthSafetyIncident::where('company_id', $companyId)
            ->with(['site', 'reportedBy'])
            ->latest('occurred_at')
            ->limit(5)
            ->get()
            ->map(function ($incident) {
                return [
                    'type' => 'incident',
                    'title' => ucfirst($incident->type) . ' - ' . $incident->incident_number,
                    'description' => Str::limit($incident->description, 100),
                    'user' => $incident->reportedBy->name,
                    'date' => $incident->occurred_at,
                    'icon' => 'bi-exclamation-triangle',
                    'color' => $incident->severity === 'major' || $incident->severity === 'fatal' ? 'danger' : 'warning'
                ];
            });
        
        $recentActivities = $recentRams->concat($recentTalks)->concat($recentIncidents)
            ->sortByDesc('date')
            ->take(10);
        
        // Get sites for filter
        $sites = Site::where('company_id', $companyId)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
        
        // Get upcoming items
        $upcomingItems = collect();
        
        // RAMS expiring soon
        $expiringRams = HealthSafetyRams::where('company_id', $companyId)
            ->where('status', 'approved')
            ->whereBetween('valid_until', [now(), now()->addDays(30)])
            ->with('site')
            ->get()
            ->map(function ($ram) {
                return [
                    'type' => 'RAMS Expiring',
                    'title' => $ram->title,
                    'location' => $ram->site ? $ram->site->name : 'General',
                    'date' => $ram->valid_until,
                    'days_remaining' => now()->diffInDays($ram->valid_until),
                    'color' => 'warning'
                ];
            });
        
        // Inductions expiring soon
        $expiringInductions = HealthSafetyInduction::where('company_id', $companyId)
            ->where('status', 'active')
            ->whereBetween('valid_until', [now(), now()->addDays(30)])
            ->with('site')
            ->get()
            ->map(function ($induction) {
                return [
                    'type' => 'Induction Expiring',
                    'title' => $induction->inductee_name,
                    'location' => $induction->site ? $induction->site->name : 'General',
                    'date' => $induction->valid_until,
                    'days_remaining' => now()->diffInDays($induction->valid_until),
                    'color' => 'info'
                ];
            });
        
        $upcomingItems = $expiringRams->concat($expiringInductions)
            ->sortBy('days_remaining')
            ->take(10);
        
        return view('health-safety.index', compact(
            'stats',
            'recentActivities',
            'sites',
            'upcomingItems'
        ));
    }
    
    /**
     * RAMS Management
     */
    public function rams(Request $request)
    {
        $user = auth()->user();
        $query = HealthSafetyRams::where('company_id', $user->company_id)
            ->with(['site', 'project', 'createdBy', 'approvedBy']);
        
        // Apply filters
        if ($request->filled('site_id')) {
            $query->where('site_id', $request->site_id);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('risk_level')) {
            $query->where('risk_level', $request->risk_level);
        }
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('reference_number', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        
        $ramsList = $query->latest()->paginate(20);
        
        $sites = Site::where('company_id', $user->company_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
        
        $projects = Project::forCompany($user->company_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
        
        return view('health-safety.rams.index', compact('ramsList', 'sites', 'projects'));
    }
    
    public function createRams()
    {
        $user = auth()->user();
        
        $sites = Site::where('company_id', $user->company_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
        
        $projects = Project::forCompany($user->company_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
        
        $users = User::where('company_id', $user->company_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
        
        return view('health-safety.rams.create', compact('sites', 'projects', 'users'));
    }
    
    public function storeRams(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'reference_number' => 'nullable|string|max:100',
            'site_id' => 'nullable|exists:sites,id',
            'project_id' => 'nullable|exists:projects,id',
            'task_description' => 'required|string',
            'risk_assessments' => 'required|array|min:1',
            'risk_assessments.*.hazards' => 'required|string',
            'risk_assessments.*.risk_level' => 'required|in:low,medium,high,very_high',
            'risk_assessments.*.likelihood' => 'nullable|in:rare,unlikely,possible,likely,almost_certain',
            'risk_assessments.*.severity' => 'nullable|in:negligible,minor,moderate,major,catastrophic',
            'risk_assessments.*.control_measures' => 'nullable|string',
            'control_measures' => 'required|string',
            'sequence_of_work' => 'required|string',
            'ppe_required' => 'nullable|string',
            'training_required' => 'nullable|string',
            'emergency_procedures' => 'nullable|string',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
            'status' => 'nullable|in:draft,pending_approval,approved,rejected',
            'approved_by' => 'nullable|exists:users,id',
            'file_path' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'notes' => 'nullable|string'
        ]);
        
        // Set default values
        $validated['company_id'] = auth()->user()->company_id;
        $validated['created_by'] = auth()->id();
        $validated['reference_number'] = $validated['reference_number'] ?? 'RAMS-' . date('Ymd') . '-' . rand(1000, 9999);
        $validated['status'] = $validated['status'] ?? 'draft';
        
        // Handle file upload
        if ($request->hasFile('file_path')) {
            $validated['file_path'] = $request->file('file_path')->store('health-safety/rams', 'public');
        }
        
        // Process risk assessments - combine them into arrays for storage
        $riskAssessments = $validated['risk_assessments'];
        $validated['hazards'] = array_column($riskAssessments, 'hazards');
        $validated['risk_levels'] = array_column($riskAssessments, 'risk_level');
        $validated['likelihoods'] = array_column($riskAssessments, 'likelihood');
        $validated['severities'] = array_column($riskAssessments, 'severity');
        $validated['risk_control_measures'] = array_column($riskAssessments, 'control_measures');
        
        // Determine overall risk level (highest from all assessments)
        $riskLevels = array_column($riskAssessments, 'risk_level');
        $riskHierarchy = ['low' => 1, 'medium' => 2, 'high' => 3, 'very_high' => 4];
        $maxRiskLevel = 'low';
        foreach ($riskLevels as $level) {
            if ($riskHierarchy[$level] > $riskHierarchy[$maxRiskLevel]) {
                $maxRiskLevel = $level;
            }
        }
        $validated['risk_level'] = $maxRiskLevel;
        
        // Remove the risk_assessments array as it's not a direct column
        unset($validated['risk_assessments']);
        
        $rams = HealthSafetyRams::create($validated);
        
        return redirect()->route('health-safety.rams')
            ->with('success', 'RAMS document created successfully with ' . count($riskAssessments) . ' risk assessment(s).');
    }
    
    /**
     * Toolbox Talks Management
     */
    public function toolboxTalks(Request $request)
    {
        $user = auth()->user();
        $query = HealthSafetyToolboxTalk::where('company_id', $user->company_id)
            ->with(['site', 'project', 'conductedBy']);
        
        if ($request->filled('site_id')) {
            $query->where('site_id', $request->site_id);
        }
        
        if ($request->filled('date_from')) {
            $query->where('conducted_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('conducted_at', '<=', $request->date_to);
        }
        
        $talks = $query->latest('conducted_at')->paginate(20);
        
        $sites = Site::where('company_id', $user->company_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
        
        return view('health-safety.toolbox-talks.index', compact('talks', 'sites'));
    }
    
    public function createToolboxTalk()
    {
        $user = auth()->user();
        
        $sites = Site::where('company_id', $user->company_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
        
        $projects = Project::forCompany($user->company_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
        
        $employees = Employee::where('company_id', $user->company_id)
            ->select('id', 'first_name', 'last_name', 'job_title')
            ->orderBy('first_name')
            ->get();
        
        $users = User::where('company_id', $user->company_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
        
        return view('health-safety.toolbox-talks.create', compact('sites', 'projects', 'employees', 'users'));
    }
    
    public function storeToolboxTalk(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'site_id' => 'nullable|exists:sites,id',
            'project_id' => 'nullable|exists:projects,id',
            'topics_covered' => 'required|string',
            'key_points' => 'nullable|string',
            'conducted_at' => 'required|date_time',
            'duration_minutes' => 'required|integer|min:1',
            'location' => 'required|string|max:255',
            'attendees' => 'nullable|array',
            'weather_conditions' => 'nullable|in:clear,cloudy,rain,snow,wind,other',
            'notes' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf,doc,docx|max:10240'
        ]);
        
        $validated['company_id'] = auth()->user()->company_id;
        $validated['conducted_by'] = auth()->id();
        $validated['reference_number'] = 'TBT-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        $validated['attendee_count'] = count($validated['attendees'] ?? []);
        
        if ($request->hasFile('document')) {
            $validated['document_path'] = $request->file('document')->store('health-safety/toolbox-talks', 'public');
        }
        
        $talk = HealthSafetyToolboxTalk::create($validated);
        
        return redirect()->route('health-safety.toolbox-talks')
            ->with('success', 'Toolbox talk recorded successfully');
    }
    
    /**
     * Incidents Management
     */
    public function incidents(Request $request)
    {
        $user = auth()->user();
        $query = HealthSafetyIncident::where('company_id', $user->company_id)
            ->with(['site', 'project', 'reportedBy', 'investigatedBy']);
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $incidents = $query->latest('occurred_at')->paginate(20);
        
        $sites = Site::where('company_id', $user->company_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
        
        return view('health-safety.incidents.index', compact('incidents', 'sites'));
    }
    
    public function createIncident()
    {
        $user = auth()->user();
        
        $sites = Site::where('company_id', $user->company_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
        
        $projects = Project::forCompany($user->company_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
        
        $users = User::where('company_id', $user->company_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
        
        return view('health-safety.incidents.create', compact('sites', 'projects', 'users'));
    }
    
    public function storeIncident(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:accident,near_miss,dangerous_occurrence,environmental,property_damage',
            'severity' => 'required|in:minor,moderate,serious,major,fatal',
            'site_id' => 'nullable|exists:sites,id',
            'project_id' => 'nullable|exists:projects,id',
            'occurred_at' => 'required|date_time',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
            'involved_persons' => 'nullable|array',
            'witnesses' => 'nullable|array',
            'immediate_actions' => 'nullable|string',
            'first_aid_given' => 'boolean',
            'medical_treatment_required' => 'boolean',
            'reported_to_hse' => 'boolean',
            'reportable_riddor' => 'boolean',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120'
        ]);
        
        $validated['company_id'] = auth()->user()->company_id;
        $validated['reported_by'] = auth()->id();
        $validated['incident_number'] = 'INC-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        $validated['status'] = 'reported';
        
        if ($request->hasFile('attachments')) {
            $attachmentPaths = [];
            foreach ($request->file('attachments') as $file) {
                $attachmentPaths[] = $file->store('health-safety/incidents', 'public');
            }
            $validated['attachments'] = $attachmentPaths;
        }
        
        $incident = HealthSafetyIncident::create($validated);
        
        return redirect()->route('health-safety.incidents')
            ->with('success', 'Incident reported successfully. Incident number: ' . $incident->incident_number);
    }
    
    /**
     * Site Inductions Management
     */
    public function inductions(Request $request)
    {
        $user = auth()->user();
        $query = HealthSafetyInduction::where('company_id', $user->company_id)
            ->with(['site', 'employee', 'inductedBy']);
        
        if ($request->filled('site_id')) {
            $query->where('site_id', $request->site_id);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $inductions = $query->latest('inducted_at')->paginate(20);
        
        $sites = Site::where('company_id', $user->company_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
        
        return view('health-safety.inductions.index', compact('inductions', 'sites'));
    }
    
    public function createInduction()
    {
        $user = auth()->user();
        
        $sites = Site::where('company_id', $user->company_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
        
        $employees = Employee::where('company_id', $user->company_id)
            ->select('id', 'first_name', 'last_name')
            ->orderBy('first_name')
            ->get();
        
        return view('health-safety.inductions.create', compact('sites', 'employees'));
    }
    
    public function storeInduction(Request $request)
    {
        $validated = $request->validate([
            'site_id' => 'nullable|exists:sites,id',
            'employee_id' => 'nullable|exists:employees,id',
            'inductee_name' => 'required|string|max:255',
            'inductee_company' => 'nullable|string|max:255',
            'inductee_role' => 'nullable|string|max:255',
            'inductee_phone' => 'nullable|string|max:20',
            'inductee_email' => 'nullable|email|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'inducted_at' => 'required|date_format:Y-m-d\TH:i',
            'topics_covered' => 'nullable|array',
            'documents_provided' => 'nullable|array',
            'site_rules_acknowledged' => 'boolean',
            'emergency_procedures_understood' => 'boolean',
            'ppe_requirements_understood' => 'boolean',
            'hazards_communicated' => 'boolean',
            'valid_until' => 'required|date|after:inducted_at',
            'notes' => 'nullable|string'
        ]);
        
        $validated['company_id'] = auth()->user()->company_id;
        $validated['inducted_by'] = auth()->id();
        $validated['certificate_number'] = 'IND-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        $validated['status'] = 'active';
        
        // Convert datetime string to Carbon instance for proper handling
        $validated['inducted_at'] = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['inducted_at']);
        
        $induction = HealthSafetyInduction::create($validated);
        
        return redirect()->route('health-safety.inductions')
            ->with('success', 'Site induction completed successfully. Certificate: ' . $induction->certificate_number);
    }
    
    public function showInduction(HealthSafetyInduction $induction)
    {
        // Ensure user can only view inductions from their company
        if ($induction->company_id !== auth()->user()->company_id) {
            abort(404);
        }
        
        $induction->load(['site', 'employee', 'inductedBy']);
        
        return view('health-safety.inductions.show', compact('induction'));
    }
    
    public function downloadCertificate(HealthSafetyInduction $induction)
    {
        // Ensure user can only download certificates from their company
        if ($induction->company_id !== auth()->user()->company_id) {
            abort(404);
        }
        
        $induction->load(['site', 'inductedBy']);
        
        // For now, return the HTML certificate view
        // In production, you would use a PDF library like dompdf or wkhtmltopdf
        return view('health-safety.inductions.certificate', compact('induction'));
    }
    
    public function renewInduction(HealthSafetyInduction $induction)
    {
        // Ensure user can only renew inductions from their company
        if ($induction->company_id !== auth()->user()->company_id) {
            abort(404);
        }
        
        $induction->update([
            'valid_until' => now()->addYear(),
            'status' => 'active',
            'renewed_at' => now(),
            'renewed_by' => auth()->id()
        ]);
        
        return redirect()->route('health-safety.inductions')
            ->with('success', 'Induction renewed successfully. New expiry date: ' . $induction->valid_until->format('M j, Y'));
    }
    
    public function suspendInduction(HealthSafetyInduction $induction)
    {
        // Ensure user can only suspend inductions from their company
        if ($induction->company_id !== auth()->user()->company_id) {
            abort(404);
        }
        
        $induction->update([
            'status' => 'suspended',
            'suspended_at' => now(),
            'suspended_by' => auth()->id()
        ]);
        
        return redirect()->route('health-safety.inductions')
            ->with('success', 'Induction suspended successfully.');
    }
    
    public function reactivateInduction(HealthSafetyInduction $induction)
    {
        // Ensure user can only reactivate inductions from their company
        if ($induction->company_id !== auth()->user()->company_id) {
            abort(404);
        }
        
        $induction->update([
            'status' => 'active',
            'reactivated_at' => now(),
            'reactivated_by' => auth()->id()
        ]);
        
        return redirect()->route('health-safety.inductions')
            ->with('success', 'Induction reactivated successfully.');
    }
    
    /**
     * Custom Forms Management
     */
    public function forms(Request $request)
    {
        $user = auth()->user();
        
        $templates = HealthSafetyFormTemplate::where('company_id', $user->company_id)
            ->where('is_active', true)
            ->with('createdBy')
            ->latest()
            ->get();
        
        $submissions = HealthSafetyFormSubmission::where('company_id', $user->company_id)
            ->with(['template', 'site', 'submittedBy'])
            ->latest('submitted_at')
            ->paginate(20);
        
        return view('health-safety.forms.index', compact('templates', 'submissions'));
    }
    
    public function createFormTemplate()
    {
        return view('health-safety.forms.create-template');
    }
    
    public function storeFormTemplate(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:inspection,permit,checklist,assessment,report,other',
            'fields' => 'required|json',
            'requires_signature' => 'boolean',
            'requires_photo' => 'boolean'
        ]);
        
        $validated['company_id'] = auth()->user()->company_id;
        $validated['created_by'] = auth()->id();
        $validated['code'] = 'FRM-' . strtoupper(Str::random(8));
        $validated['is_active'] = true;
        $validated['version'] = 1;
        
        $template = HealthSafetyFormTemplate::create($validated);
        
        return redirect()->route('health-safety.forms')
            ->with('success', 'Form template created successfully');
    }
    
    public function submitForm($templateId)
    {
        $user = auth()->user();
        
        $template = HealthSafetyFormTemplate::where('company_id', $user->company_id)
            ->where('id', $templateId)
            ->where('is_active', true)
            ->firstOrFail();
        
        $sites = Site::where('company_id', $user->company_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
        
        $projects = Project::forCompany($user->company_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
        
        return view('health-safety.forms.submit', compact('template', 'sites', 'projects'));
    }
    
    public function storeFormSubmission(Request $request, $templateId)
    {
        $template = HealthSafetyFormTemplate::where('company_id', auth()->user()->company_id)
            ->where('id', $templateId)
            ->where('is_active', true)
            ->firstOrFail();
        
        $validated = $request->validate([
            'site_id' => 'nullable|exists:sites,id',
            'project_id' => 'nullable|exists:projects,id',
            'form_data' => 'required|array',
            'photos' => 'nullable|array',
            'photos.*' => 'image|max:5120'
        ]);
        
        $validated['company_id'] = auth()->user()->company_id;
        $validated['template_id'] = $templateId;
        $validated['submitted_by'] = auth()->id();
        $validated['submitted_at'] = now();
        $validated['submission_number'] = 'SUB-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        $validated['status'] = 'submitted';
        
        if ($request->hasFile('photos')) {
            $photoPaths = [];
            foreach ($request->file('photos') as $photo) {
                $photoPaths[] = $photo->store('health-safety/forms/photos', 'public');
            }
            $validated['photos'] = $photoPaths;
        }
        
        $submission = HealthSafetyFormSubmission::create($validated);
        
        return redirect()->route('health-safety.forms')
            ->with('success', 'Form submitted successfully. Reference: ' . $submission->submission_number);
    }
    
    /**
     * Safety Observations Management
     */
    public function observations(Request $request)
    {
        $user = auth()->user();
        $query = HealthSafetyObservation::where('company_id', $user->company_id)
            ->with(['site', 'project', 'observedBy', 'assignedTo']);
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('site_id')) {
            $query->where('site_id', $request->site_id);
        }
        
        $observations = $query->latest('observed_at')->paginate(20);
        
        $sites = Site::where('company_id', $user->company_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
        
        return view('health-safety.observations.index', compact('observations', 'sites'));
    }
}
