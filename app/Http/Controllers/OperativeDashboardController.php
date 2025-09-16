<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\OperativeInvoice;
use App\Models\Task;
use App\Models\Project;
use App\Models\Site;
use App\Models\Client;
use App\Models\TimeEntry;
use App\Services\GeolocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OperativeDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $activeTab = $request->get('tab', 'invoices');

        // Get user's operative invoices
        $myInvoices = OperativeInvoice::where('company_id', $user->company_id)
            ->where('operative_id', $user->id)
            ->with(['site', 'project', 'manager'])
            ->latest()
            ->limit(10)
            ->get();

        // Get user's tasks
        $myTasks = Task::where('company_id', $user->company_id)
            ->where('assigned_user_id', $user->id)
            ->with(['project.site', 'taskCategory'])
            ->orderByRaw("
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
            ")
            ->limit(15)
            ->get();

        // Get reports data
        $reportsData = $this->getReportsData($user);

        // Get current active time entry
        $activeTimeEntry = TimeEntry::getActiveForUser($user->id);

        // Get today's hours
        $todayHours = TimeEntry::getTodayHoursForUser($user->id);

        // Get this week's hours
        $weekHours = TimeEntry::getWeekHoursForUser($user->id);

        // Get recent time entries
        $recentTimeEntries = TimeEntry::forUser($user->id)
            ->with(['project', 'site', 'task'])
            ->orderBy('clock_in', 'desc')
            ->limit(10)
            ->get();

        // Get available projects and sites for clock in
        $availableProjects = Project::forCompany($user->company_id)
            ->whereIn('status', ['planning', 'in_progress'])
            ->orderBy('name')
            ->get();

        $availableSites = Site::forCompany($user->company_id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('operative-dashboard.index', compact(
            'myInvoices',
            'myTasks',
            'reportsData',
            'activeTab',
            'activeTimeEntry',
            'todayHours',
            'weekHours',
            'recentTimeEntries',
            'availableProjects',
            'availableSites'
        ));
    }

    private function getReportsData($user)
    {
        $companyId = $user->company_id;
        
        // Invoice statistics
        $invoiceStats = [
            'total_invoices' => Invoice::where('company_id', $companyId)->count(),
            'pending_invoices' => Invoice::where('company_id', $companyId)->where('status', 'pending')->count(),
            'paid_invoices' => Invoice::where('company_id', $companyId)->where('status', 'paid')->count(),
            'overdue_invoices' => Invoice::where('company_id', $companyId)
                ->where('status', 'pending')
                ->where('due_date', '<', now())
                ->count(),
            'total_revenue' => Invoice::where('company_id', $companyId)
                ->where('status', 'paid')
                ->sum('total_amount'),
            'pending_revenue' => Invoice::where('company_id', $companyId)
                ->where('status', 'pending')
                ->sum('total_amount'),
        ];

        // Task statistics
        $taskStats = [
            'total_tasks' => Task::where('company_id', $companyId)->count(),
            'my_tasks' => Task::where('company_id', $companyId)->where('assigned_user_id', $user->id)->count(),
            'completed_tasks' => Task::where('company_id', $companyId)->where('status', 'completed')->count(),
            'overdue_tasks' => Task::where('company_id', $companyId)
                ->where('status', '!=', 'completed')
                ->where('due_date', '<', now())
                ->count(),
            'urgent_tasks' => Task::where('company_id', $companyId)
                ->where('status', '!=', 'completed')
                ->where('priority', 'urgent')
                ->count(),
        ];

        // Project statistics
        $projectStats = [
            'total_projects' => Project::where('company_id', $companyId)->count(),
            'active_projects' => Project::where('company_id', $companyId)->where('status', 'active')->count(),
            'completed_projects' => Project::where('company_id', $companyId)->where('status', 'completed')->count(),
            'on_hold_projects' => Project::where('company_id', $companyId)->where('status', 'on_hold')->count(),
        ];

        // Site statistics
        $siteStats = [
            'total_sites' => Site::where('company_id', $companyId)->count(),
            'active_sites' => Site::where('company_id', $companyId)->where('status', 'active')->count(),
        ];

        // Recent activity
        $recentActivity = collect()
            ->merge(
                Invoice::where('company_id', $companyId)
                    ->with('client')
                    ->latest()
                    ->limit(5)
                    ->get()
                    ->map(function ($invoice) {
                        return [
                            'type' => 'invoice',
                            'icon' => 'bi-receipt',
                            'color' => 'success',
                            'title' => 'Invoice #' . $invoice->invoice_number,
                            'description' => 'Created for ' . $invoice->client->name,
                            'date' => $invoice->created_at,
                            'amount' => $invoice->total_amount,
                        ];
                    })
            )
            ->merge(
                Task::where('company_id', $companyId)
                    ->with('project')
                    ->latest()
                    ->limit(5)
                    ->get()
                    ->map(function ($task) {
                        return [
                            'type' => 'task',
                            'icon' => 'bi-check-square',
                            'color' => 'primary',
                            'title' => $task->title,
                            'description' => 'Project: ' . ($task->project->name ?? 'N/A'),
                            'date' => $task->created_at,
                            'priority' => $task->priority,
                        ];
                    })
            )
            ->sortByDesc('date')
            ->take(10);

        return [
            'invoices' => $invoiceStats,
            'tasks' => $taskStats,
            'projects' => $projectStats,
            'sites' => $siteStats,
            'recent_activity' => $recentActivity,
        ];
    }

    /**
     * Clock in functionality with location validation
     */
    public function clockIn(Request $request)
    {
        $user = auth()->user();

        // Check if user already has an active time entry
        $activeEntry = TimeEntry::getActiveForUser($user->id);
        if ($activeEntry) {
            return response()->json([
                'success' => false,
                'message' => 'You are already clocked in. Please clock out first.',
                'active_entry' => $activeEntry
            ], 422);
        }

        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'site_id' => 'nullable|exists:sites,id',
            'task_id' => 'nullable|exists:tasks,id',
            'notes' => 'nullable|string|max:500',
            'location' => 'nullable|string|max:255',
            'operative_latitude' => 'required|numeric|between:-90,90',
            'operative_longitude' => 'required|numeric|between:-180,180',
        ]);

        try {
            // Get the project and validate it has a postcode
            $project = Project::findOrFail($request->project_id);
            
            if (!$project->postcode) {
                return response()->json([
                    'success' => false,
                    'message' => 'This project does not have a postcode set. Please contact your manager to update the project location.',
                ], 422);
            }

            // Ensure project has coordinates
            if (!$project->hasValidCoordinates()) {
                $project->updateCoordinatesFromPostcode();
                $project->refresh();
                
                if (!$project->hasValidCoordinates()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unable to determine project location. Please contact your manager.',
                    ], 422);
                }
            }

            // Validate operative location
            $geolocationService = app(GeolocationService::class);
            $locationValidation = $geolocationService->validateOperativeLocation(
                $request->operative_latitude,
                $request->operative_longitude,
                $project->latitude,
                $project->longitude
            );

            if (!$locationValidation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $locationValidation['error'],
                    'distance' => $locationValidation['distance'],
                    'max_distance' => $locationValidation['max_distance'],
                    'location_error' => true
                ], 422);
            }

            // Get operative's address from coordinates
            $operativeAddress = $geolocationService->getAddressFromCoordinates(
                $request->operative_latitude,
                $request->operative_longitude
            );

            $timeEntry = TimeEntry::create([
                'company_id' => $user->company_id,
                'user_id' => $user->id,
                'project_id' => $request->project_id,
                'site_id' => $request->site_id,
                'task_id' => $request->task_id,
                'clock_in' => now(),
                'notes' => $request->notes,
                'location' => $request->location,
                'latitude' => $request->operative_latitude, // Legacy field
                'longitude' => $request->operative_longitude, // Legacy field
                'operative_latitude' => $request->operative_latitude,
                'operative_longitude' => $request->operative_longitude,
                'operative_location_address' => $operativeAddress,
                'project_latitude' => $project->latitude,
                'project_longitude' => $project->longitude,
                'distance_from_project' => $locationValidation['distance'],
                'location_validated' => true,
                'status' => 'active',
            ]);

            // Load relationships for response
            $timeEntry->load(['project', 'site', 'task']);

            return response()->json([
                'success' => true,
                'message' => 'Successfully clocked in!',
                'time_entry' => $timeEntry,
                'clock_in_time' => $timeEntry->clock_in->format('g:i A'),
                'distance' => $locationValidation['distance'],
                'location_validated' => true,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clock in. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clock out functionality with location validation
     */
    public function clockOut(Request $request)
    {
        $user = auth()->user();

        // Get the active time entry
        $activeEntry = TimeEntry::getActiveForUser($user->id);
        if (!$activeEntry) {
            return response()->json([
                'success' => false,
                'message' => 'No active time entry found. Please clock in first.',
            ], 422);
        }

        $request->validate([
            'notes' => 'nullable|string|max:500',
            'operative_latitude' => 'required|numeric|between:-90,90',
            'operative_longitude' => 'required|numeric|between:-180,180',
        ]);

        try {
            // Validate location if project has coordinates
            if ($activeEntry->project_latitude && $activeEntry->project_longitude) {
                $geolocationService = app(GeolocationService::class);
                $locationValidation = $geolocationService->validateOperativeLocation(
                    $request->operative_latitude,
                    $request->operative_longitude,
                    $activeEntry->project_latitude,
                    $activeEntry->project_longitude
                );

                if (!$locationValidation['valid']) {
                    return response()->json([
                        'success' => false,
                        'message' => $locationValidation['error'],
                        'distance' => $locationValidation['distance'],
                        'max_distance' => $locationValidation['max_distance'],
                        'location_error' => true
                    ], 422);
                }
            }

            // Clock out
            $activeEntry->clockOut($request->notes);

            // Load relationships for response
            $activeEntry->load(['project', 'site', 'task']);

            return response()->json([
                'success' => true,
                'message' => 'Successfully clocked out!',
                'time_entry' => $activeEntry,
                'clock_out_time' => $activeEntry->clock_out->format('g:i A'),
                'duration' => $activeEntry->duration_formatted,
                'total_hours_today' => TimeEntry::getTodayHoursForUser($user->id),
                'location_validated' => true,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clock out. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current status
     */
    public function getTimeStatus()
    {
        $user = auth()->user();
        $activeEntry = TimeEntry::getActiveForUser($user->id);

        return response()->json([
            'is_clocked_in' => !is_null($activeEntry),
            'active_entry' => $activeEntry ? $activeEntry->load(['project', 'site', 'task']) : null,
            'today_hours' => TimeEntry::getTodayHoursForUser($user->id),
            'week_hours' => TimeEntry::getWeekHoursForUser($user->id),
        ]);
    }
}

