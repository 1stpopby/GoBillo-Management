<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Site;
use App\Models\User;
use App\Models\Task;
use App\Models\Document;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FieldOperationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('company.access');
    }

    /**
     * Display the main field operations dashboard
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $companyId = $user->company_id;
        
        // Get filter parameters
        $siteFilter = $request->get('site_id');
        $projectFilter = $request->get('project_id');
        $dateFilter = $request->get('date_range', 'today');
        
        // Calculate date range
        $dateRange = $this->getDateRange($dateFilter);
        
        // Get sites for filtering
        $sites = Site::forCompany($companyId)->orderBy('name')->get();
        
        // Get projects for filtering
        $projects = Project::forCompany($companyId)
            ->when($siteFilter, function($q) use ($siteFilter) {
                return $q->where('site_id', $siteFilter);
            })
            ->orderBy('name')
            ->get();
        
        // Field Operations Statistics
        $stats = $this->getFieldOperationsStats($companyId, $siteFilter, $projectFilter, $dateRange);
        
        // Active Projects
        $activeProjects = Project::forCompany($companyId)
            ->whereIn('status', ['planning', 'in_progress'])
            ->when($siteFilter, function($q) use ($siteFilter) {
                return $q->where('site_id', $siteFilter);
            })
            ->with(['site', 'client'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();
        
        // Recent Tasks (field-related)
        $recentTasks = Task::whereHas('project', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->when($siteFilter, function($q) use ($siteFilter) {
                return $q->whereHas('project', function($subQ) use ($siteFilter) {
                    $subQ->where('site_id', $siteFilter);
                });
            })
            ->when($projectFilter, function($q) use ($projectFilter) {
                return $q->where('project_id', $projectFilter);
            })
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->with(['project.site', 'assignedUser'])
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get();
        
        // Equipment Status (mock data for now)
        $equipmentStatus = $this->getEquipmentStatus($companyId);
        
        // Safety Incidents (mock data for now)
        $safetyIncidents = $this->getSafetyIncidents($companyId, $dateRange);
        
        return view('field-operations.index', compact(
            'sites',
            'projects', 
            'stats',
            'activeProjects',
            'recentTasks',
            'equipmentStatus',
            'safetyIncidents',
            'siteFilter',
            'projectFilter',
            'dateFilter'
        ));
    }

    /**
     * Equipment management
     */
    public function equipment(Request $request)
    {
        $user = auth()->user();
        $companyId = $user->company_id;
        
        // Mock equipment data for now
        $equipment = collect([
            (object)[
                'id' => 1,
                'name' => 'Excavator CAT 320',
                'type' => 'Heavy Machinery',
                'status' => 'active',
                'location' => 'Downtown Construction Site',
                'last_maintenance' => Carbon::now()->subDays(15),
                'next_maintenance' => Carbon::now()->addDays(15),
                'operator' => 'John Smith',
                'fuel_level' => 75,
                'hours_used' => 1250
            ],
            (object)[
                'id' => 2,
                'name' => 'Crane Liebherr LTM 1050',
                'type' => 'Heavy Machinery',
                'status' => 'maintenance',
                'location' => 'Equipment Yard',
                'last_maintenance' => Carbon::now()->subDays(2),
                'next_maintenance' => Carbon::now()->addDays(28),
                'operator' => null,
                'fuel_level' => 45,
                'hours_used' => 890
            ],
            (object)[
                'id' => 3,
                'name' => 'Concrete Mixer Volvo FM',
                'type' => 'Vehicle',
                'status' => 'active',
                'location' => 'Residential Complex Site',
                'last_maintenance' => Carbon::now()->subDays(8),
                'next_maintenance' => Carbon::now()->addDays(22),
                'operator' => 'Mike Johnson',
                'fuel_level' => 90,
                'hours_used' => 678
            ]
        ]);
        
        return view('field-operations.equipment', compact('equipment'));
    }

    /**
     * Material tracking
     */
    public function materials(Request $request)
    {
        $user = auth()->user();
        $companyId = $user->company_id;
        
        // Mock material data for now
        $materials = collect([
            (object)[
                'id' => 1,
                'name' => 'Concrete Mix C25/30',
                'category' => 'Concrete',
                'quantity' => 150,
                'unit' => 'm³',
                'location' => 'Downtown Construction Site',
                'supplier' => 'BuildMix Ltd',
                'cost_per_unit' => 85.50,
                'total_cost' => 12825.00,
                'delivery_date' => Carbon::now()->subDays(3),
                'status' => 'delivered'
            ],
            (object)[
                'id' => 2,
                'name' => 'Steel Rebar 12mm',
                'category' => 'Steel',
                'quantity' => 2500,
                'unit' => 'kg',
                'location' => 'Material Warehouse',
                'supplier' => 'SteelCorp',
                'cost_per_unit' => 1.25,
                'total_cost' => 3125.00,
                'delivery_date' => Carbon::now()->addDays(2),
                'status' => 'ordered'
            ],
            (object)[
                'id' => 3,
                'name' => 'Brick - Common Red',
                'category' => 'Masonry',
                'quantity' => 10000,
                'unit' => 'pieces',
                'location' => 'Residential Complex Site',
                'supplier' => 'BrickWorks',
                'cost_per_unit' => 0.45,
                'total_cost' => 4500.00,
                'delivery_date' => Carbon::now()->subDays(1),
                'status' => 'delivered'
            ]
        ]);
        
        return view('field-operations.materials', compact('materials'));
    }

    /**
     * Safety management
     */
    public function safety(Request $request)
    {
        $user = auth()->user();
        $companyId = $user->company_id;
        
        // Mock safety data for now
        $safetyReports = collect([
            (object)[
                'id' => 1,
                'type' => 'Near Miss',
                'severity' => 'low',
                'description' => 'Worker almost slipped on wet surface near entrance',
                'location' => 'Downtown Construction Site - Main Entrance',
                'reported_by' => 'Sarah Wilson',
                'date' => Carbon::now()->subHours(6),
                'status' => 'investigating',
                'actions_taken' => 'Added warning signs, scheduled cleaning'
            ],
            (object)[
                'id' => 2,
                'type' => 'Safety Violation',
                'severity' => 'medium',
                'description' => 'Worker not wearing hard hat in designated area',
                'location' => 'Residential Complex - Building A',
                'reported_by' => 'Tom Anderson',
                'date' => Carbon::now()->subDays(1),
                'status' => 'resolved',
                'actions_taken' => 'Worker reminded of safety protocols, additional training scheduled'
            ],
            (object)[
                'id' => 3,
                'type' => 'Equipment Issue',
                'severity' => 'high',
                'description' => 'Crane showing unusual vibrations during operation',
                'location' => 'Downtown Construction Site - Tower Crane',
                'reported_by' => 'Mike Johnson',
                'date' => Carbon::now()->subDays(2),
                'status' => 'resolved',
                'actions_taken' => 'Equipment taken out of service, maintenance completed'
            ]
        ]);
        
        return view('field-operations.safety', compact('safetyReports'));
    }

    /**
     * Work orders management
     */
    public function workOrders(Request $request)
    {
        $user = auth()->user();
        $companyId = $user->company_id;
        
        // Mock work orders data for now
        $workOrders = collect([
            (object)[
                'id' => 1,
                'title' => 'Foundation Pour - Building A',
                'project' => 'Residential Complex',
                'priority' => 'high',
                'status' => 'in_progress',
                'assigned_to' => 'Construction Team Alpha',
                'created_date' => Carbon::now()->subDays(2),
                'due_date' => Carbon::now()->addDays(1),
                'estimated_hours' => 16,
                'actual_hours' => 12,
                'description' => 'Pour foundation concrete for Building A basement level'
            ],
            (object)[
                'id' => 2,
                'title' => 'Electrical Rough-in - Floor 3',
                'project' => 'Downtown Construction Site',
                'priority' => 'medium',
                'status' => 'pending',
                'assigned_to' => 'Electrical Contractors Ltd',
                'created_date' => Carbon::now()->subDays(1),
                'due_date' => Carbon::now()->addDays(5),
                'estimated_hours' => 24,
                'actual_hours' => 0,
                'description' => 'Install electrical wiring and outlets for third floor'
            ],
            (object)[
                'id' => 3,
                'title' => 'Plumbing Installation - Bathrooms',
                'project' => 'Office Renovation',
                'priority' => 'low',
                'status' => 'completed',
                'assigned_to' => 'PlumbPro Services',
                'created_date' => Carbon::now()->subDays(5),
                'due_date' => Carbon::now()->subDays(1),
                'estimated_hours' => 20,
                'actual_hours' => 18,
                'description' => 'Install plumbing fixtures in all bathroom areas'
            ]
        ]);
        
        return view('field-operations.work-orders', compact('workOrders'));
    }

    /**
     * Get field operations statistics
     */
    private function getFieldOperationsStats($companyId, $siteFilter, $projectFilter, $dateRange)
    {
        // Active Projects
        $activeProjects = Project::forCompany($companyId)
            ->whereIn('status', ['planning', 'in_progress'])
            ->when($siteFilter, function($q) use ($siteFilter) {
                return $q->where('site_id', $siteFilter);
            })
            ->count();

        // Tasks Today
        $tasksToday = Task::whereHas('project', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->when($siteFilter, function($q) use ($siteFilter) {
                return $q->whereHas('project', function($subQ) use ($siteFilter) {
                    $subQ->where('site_id', $siteFilter);
                });
            })
            ->when($projectFilter, function($q) use ($projectFilter) {
                return $q->where('project_id', $projectFilter);
            })
            ->whereDate('due_date', today())
            ->count();

        // Overdue Tasks
        $overdueTasks = Task::whereHas('project', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->when($siteFilter, function($q) use ($siteFilter) {
                return $q->whereHas('project', function($subQ) use ($siteFilter) {
                    $subQ->where('site_id', $siteFilter);
                });
            })
            ->when($projectFilter, function($q) use ($projectFilter) {
                return $q->where('project_id', $projectFilter);
            })
            ->where('due_date', '<', now())
            ->where('status', '!=', 'completed')
            ->count();

        // Field Team Members (operatives + site managers)
        $fieldTeamMembers = User::where('company_id', $companyId)
            ->whereIn('role', ['operative', 'site_manager'])
            ->count();

        return [
            'active_projects' => $activeProjects,
            'tasks_today' => $tasksToday,
            'overdue_tasks' => $overdueTasks,
            'field_team_members' => $fieldTeamMembers,
            'equipment_active' => 8, // Mock data
            'safety_incidents' => 2, // Mock data
        ];
    }

    /**
     * Get equipment status (mock data)
     */
    private function getEquipmentStatus($companyId)
    {
        return [
            'active' => 8,
            'maintenance' => 2,
            'offline' => 1,
            'total' => 11
        ];
    }

    /**
     * Get safety incidents (mock data)
     */
    private function getSafetyIncidents($companyId, $dateRange)
    {
        return [
            'total' => 3,
            'resolved' => 2,
            'investigating' => 1,
            'high_priority' => 1
        ];
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
            default:
                return [
                    'start' => now()->startOfDay(),
                    'end' => now()->endOfDay()
                ];
        }
    }
}
