<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Site;
use App\Models\User;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TimeTrackingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('company.access');
    }

    /**
     * Display the main time tracking dashboard
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $companyId = $user->company_id;
        
        // Get filter parameters
        $userFilter = $request->get('user_id');
        $projectFilter = $request->get('project_id');
        $dateFilter = $request->get('date_range', 'week');
        
        // Calculate date range
        $dateRange = $this->getDateRange($dateFilter);
        
        // Get users for filtering (only field workers)
        $users = User::where('company_id', $companyId)
            ->whereIn('role', ['operative', 'site_manager', 'project_manager'])
            ->orderBy('name')
            ->get();
        
        // Get projects for filtering
        $projects = Project::forCompany($companyId)->orderBy('name')->get();
        
        // Time tracking statistics
        $stats = $this->getTimeTrackingStats($companyId, $userFilter, $projectFilter, $dateRange);
        
        // Recent time entries
        $recentTimeEntries = $this->getRecentTimeEntries($companyId, $userFilter, $projectFilter, $dateRange);
        
        // Active timers (users currently clocked in)
        $activeTimers = $this->getActiveTimers($companyId);
        
        // Top performers (by hours this period)
        $topPerformers = $this->getTopPerformers($companyId, $dateRange);
        
        // Project time breakdown
        $projectTimeBreakdown = $this->getProjectTimeBreakdown($companyId, $dateRange);
        
        return view('time-tracking.index', compact(
            'users',
            'projects',
            'stats',
            'recentTimeEntries',
            'activeTimers',
            'topPerformers',
            'projectTimeBreakdown',
            'userFilter',
            'projectFilter',
            'dateFilter'
        ));
    }

    /**
     * Show timesheets view
     */
    public function timesheets(Request $request)
    {
        $user = auth()->user();
        $companyId = $user->company_id;
        
        // Get filter parameters
        $userFilter = $request->get('user_id');
        $weekStart = $request->get('week_start', now()->startOfWeek()->toDateString());
        
        // Calculate week range
        $weekStartDate = Carbon::parse($weekStart);
        $weekEndDate = $weekStartDate->copy()->endOfWeek();
        
        // Get users for filtering
        $users = User::where('company_id', $companyId)
            ->whereIn('role', ['operative', 'site_manager', 'project_manager'])
            ->orderBy('name')
            ->get();
        
        // Get timesheets for the week
        $timesheets = $this->getWeeklyTimesheets($companyId, $userFilter, $weekStartDate, $weekEndDate);
        
        return view('time-tracking.timesheets', compact(
            'users',
            'timesheets',
            'userFilter',
            'weekStart',
            'weekStartDate',
            'weekEndDate'
        ));
    }

    /**
     * Show reports view
     */
    public function reports(Request $request)
    {
        $user = auth()->user();
        $companyId = $user->company_id;
        
        // Get filter parameters
        $reportType = $request->get('report_type', 'summary');
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());
        
        $dateRange = [
            'start' => Carbon::parse($startDate),
            'end' => Carbon::parse($endDate)
        ];
        
        // Generate reports based on type
        $reportData = $this->generateReport($companyId, $reportType, $dateRange);
        
        return view('time-tracking.reports', compact(
            'reportType',
            'startDate',
            'endDate',
            'reportData'
        ));
    }

    /**
     * Clock in/out functionality
     */
    public function clockInOut(Request $request)
    {
        $user = auth()->user();
        $companyId = $user->company_id;
        $action = $request->get('action'); // 'clock_in' or 'clock_out'
        $projectId = $request->get('project_id');
        $taskId = $request->get('task_id');
        $notes = $request->get('notes');
        
        // Mock implementation - in real app, this would create/update time entries
        $mockTimeEntry = (object)[
            'id' => rand(1000, 9999),
            'user_id' => $user->id,
            'project_id' => $projectId,
            'task_id' => $taskId,
            'clock_in' => $action === 'clock_in' ? now() : null,
            'clock_out' => $action === 'clock_out' ? now() : null,
            'notes' => $notes,
            'duration' => $action === 'clock_out' ? rand(1, 8) * 3600 : null, // Random hours in seconds
        ];
        
        return response()->json([
            'success' => true,
            'message' => $action === 'clock_in' ? 'Successfully clocked in' : 'Successfully clocked out',
            'time_entry' => $mockTimeEntry,
            'current_status' => $action === 'clock_in' ? 'clocked_in' : 'clocked_out'
        ]);
    }

    /**
     * Get time tracking statistics
     */
    private function getTimeTrackingStats($companyId, $userFilter, $projectFilter, $dateRange)
    {
        // Mock data - in real app, these would be calculated from actual time entries
        return [
            'total_hours' => rand(150, 300),
            'billable_hours' => rand(120, 250),
            'active_users' => rand(8, 15),
            'projects_tracked' => rand(5, 12),
            'overtime_hours' => rand(10, 30),
            'average_daily_hours' => rand(6, 8),
        ];
    }

    /**
     * Get recent time entries
     */
    private function getRecentTimeEntries($companyId, $userFilter, $projectFilter, $dateRange)
    {
        // Mock data - in real app, this would query actual time entries
        $projects = Project::forCompany($companyId)->get();
        $users = User::where('company_id', $companyId)
            ->whereIn('role', ['operative', 'site_manager', 'project_manager'])
            ->get();
        
        $entries = collect();
        
        for ($i = 0; $i < 15; $i++) {
            $user = $users->random();
            $project = $projects->random();
            $clockIn = now()->subDays(rand(0, 7))->setHour(rand(7, 9))->setMinute(rand(0, 59));
            $duration = rand(4, 10) * 3600; // 4-10 hours in seconds
            
            $entries->push((object)[
                'id' => rand(1000, 9999),
                'user' => $user,
                'project' => $project,
                'task' => 'General Work',
                'clock_in' => $clockIn,
                'clock_out' => $clockIn->copy()->addSeconds($duration),
                'duration' => $duration,
                'notes' => collect(['Foundation work', 'Electrical installation', 'Plumbing', 'Painting', 'Cleanup'])->random(),
                'is_billable' => rand(0, 1),
                'status' => collect(['approved', 'pending', 'rejected'])->random(),
            ]);
        }
        
        return $entries->sortByDesc('clock_in');
    }

    /**
     * Get active timers
     */
    private function getActiveTimers($companyId)
    {
        // Mock data - users currently clocked in
        $users = User::where('company_id', $companyId)
            ->whereIn('role', ['operative', 'site_manager', 'project_manager'])
            ->take(rand(3, 8))
            ->get();
        
        $projects = Project::forCompany($companyId)->get();
        
        return $users->map(function($user) use ($projects) {
            $clockIn = now()->subHours(rand(1, 8))->subMinutes(rand(0, 59));
            return (object)[
                'user' => $user,
                'project' => $projects->random(),
                'clock_in' => $clockIn,
                'duration' => now()->diffInSeconds($clockIn),
                'location' => collect(['Downtown Site', 'Residential Complex', 'Office Building', 'Warehouse'])->random(),
            ];
        });
    }

    /**
     * Get top performers
     */
    private function getTopPerformers($companyId, $dateRange)
    {
        $users = User::where('company_id', $companyId)
            ->whereIn('role', ['operative', 'site_manager', 'project_manager'])
            ->take(10)
            ->get();
        
        return $users->map(function($user) {
            return (object)[
                'user' => $user,
                'total_hours' => rand(30, 60),
                'billable_hours' => rand(25, 55),
                'projects_count' => rand(2, 6),
                'efficiency_rate' => rand(85, 98),
            ];
        })->sortByDesc('total_hours');
    }

    /**
     * Get project time breakdown
     */
    private function getProjectTimeBreakdown($companyId, $dateRange)
    {
        $projects = Project::forCompany($companyId)->take(8)->get();
        
        return $projects->map(function($project) {
            return (object)[
                'project' => $project,
                'total_hours' => rand(20, 100),
                'billable_hours' => rand(15, 90),
                'team_members' => rand(2, 8),
                'completion_rate' => rand(15, 95),
            ];
        })->sortByDesc('total_hours');
    }

    /**
     * Get weekly timesheets
     */
    private function getWeeklyTimesheets($companyId, $userFilter, $weekStart, $weekEnd)
    {
        $users = User::where('company_id', $companyId)
            ->whereIn('role', ['operative', 'site_manager', 'project_manager'])
            ->when($userFilter, function($q) use ($userFilter) {
                return $q->where('id', $userFilter);
            })
            ->get();
        
        $projects = Project::forCompany($companyId)->get();
        
        return $users->map(function($user) use ($weekStart, $weekEnd, $projects) {
            $days = [];
            $totalHours = 0;
            
            for ($date = $weekStart->copy(); $date->lte($weekEnd); $date->addDay()) {
                $dayHours = $date->isWeekend() ? 0 : rand(0, 10);
                $totalHours += $dayHours;
                
                $days[] = (object)[
                    'date' => $date->copy(),
                    'hours' => $dayHours,
                    'project' => $dayHours > 0 ? $projects->random() : null,
                    'notes' => $dayHours > 0 ? collect(['Regular work', 'Overtime', 'Site visit', 'Meeting'])->random() : null,
                ];
            }
            
            return (object)[
                'user' => $user,
                'days' => $days,
                'total_hours' => $totalHours,
                'regular_hours' => min($totalHours, 40),
                'overtime_hours' => max(0, $totalHours - 40),
                'status' => collect(['pending', 'approved', 'submitted'])->random(),
            ];
        });
    }

    /**
     * Generate reports
     */
    private function generateReport($companyId, $reportType, $dateRange)
    {
        switch ($reportType) {
            case 'summary':
                return $this->generateSummaryReport($companyId, $dateRange);
            case 'detailed':
                return $this->generateDetailedReport($companyId, $dateRange);
            case 'project':
                return $this->generateProjectReport($companyId, $dateRange);
            default:
                return [];
        }
    }

    /**
     * Generate summary report
     */
    private function generateSummaryReport($companyId, $dateRange)
    {
        return [
            'total_hours' => rand(500, 1000),
            'billable_hours' => rand(400, 900),
            'total_cost' => rand(15000, 30000),
            'average_hourly_rate' => rand(25, 45),
            'projects_count' => rand(8, 15),
            'users_count' => rand(10, 20),
        ];
    }

    /**
     * Generate detailed report
     */
    private function generateDetailedReport($companyId, $dateRange)
    {
        // Mock detailed time entries
        return $this->getRecentTimeEntries($companyId, null, null, $dateRange);
    }

    /**
     * Generate project report
     */
    private function generateProjectReport($companyId, $dateRange)
    {
        return $this->getProjectTimeBreakdown($companyId, $dateRange);
    }

    /**
     * Get date range based on filter
     */
    private function getDateRange($filter)
    {
        switch ($filter) {
            case 'today':
                return [
                    'start' => now()->startOfDay(),
                    'end' => now()->endOfDay()
                ];
            case 'week':
                return [
                    'start' => now()->startOfWeek(),
                    'end' => now()->endOfWeek()
                ];
            case 'month':
                return [
                    'start' => now()->startOfMonth(),
                    'end' => now()->endOfMonth()
                ];
            case 'quarter':
                return [
                    'start' => now()->startOfQuarter(),
                    'end' => now()->endOfQuarter()
                ];
            default:
                return [
                    'start' => now()->startOfWeek(),
                    'end' => now()->endOfWeek()
                ];
        }
    }
}
