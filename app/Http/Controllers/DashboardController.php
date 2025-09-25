<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Client;
use App\Models\User;
use App\Models\Company;
use App\Models\Site;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Http\Controllers\OnboardingController;

class DashboardController extends Controller
{
    public function index()
    {
        // Simple role-based dashboard routing
        $user = auth()->user();
        
        if ($user->role === 'superadmin') {
            return $this->superAdminDashboard();
        } else {
            return $this->companyDashboard();
        }
    }
    
    private function companyDashboard()
    {
        // Initialize ALL stats the view expects - this ensures no "undefined array key" errors
        $stats = [
            'total_projects' => 0,
            'active_projects' => 0,
            'total_tasks' => 0,
            'completed_tasks' => 0,
            'pending_tasks' => 0,
            'in_progress_tasks' => 0,
            'overdue_tasks' => 0,
            'total_sites' => 0,
            'team_members' => 0,
            'total_clients' => 0,
            'monthly_revenue' => 0,
            'pending_invoices' => 0,
            'completion_rate' => 0,
            'task_efficiency' => 0,
            'budget_utilization' => 0,
            'used_budget' => 0,
            'total_budget' => 0,
        ];

        // Get authenticated user
        $user = auth()->user();
        
        // Only proceed if user has a company_id
        if (!$user || !$user->company_id) {
            return view('dashboard', compact('stats'));
        }
        
        $companyId = $user->company_id;
        
        // Get real data from database with proper error handling
        try {
            // Project statistics
            $stats['total_projects'] = Project::where('company_id', $companyId)->count();
            $stats['active_projects'] = Project::where('company_id', $companyId)
                ->whereNotIn('status', ['completed', 'cancelled'])->count();
            
            // Basic completion rate
            if ($stats['total_projects'] > 0) {
                $completedProjects = Project::where('company_id', $companyId)->where('status', 'completed')->count();
                $stats['completion_rate'] = round(($completedProjects / $stats['total_projects']) * 100, 1);
            }
            
        } catch (\Exception $e) {
            \Log::error('Project stats error: ' . $e->getMessage());
        }
        
        try {
            // Team statistics
            $stats['team_members'] = User::where('company_id', $companyId)->count();
        } catch (\Exception $e) {
            \Log::error('User stats error: ' . $e->getMessage());
        }
        
        try {
            // Client statistics
            $stats['total_clients'] = Client::where('company_id', $companyId)->count();
        } catch (\Exception $e) {
            \Log::error('Client stats error: ' . $e->getMessage());
        }
        
        try {
            // Task statistics (only if Task model exists and has the required columns)
            if (class_exists('App\Models\Task')) {
                $stats['total_tasks'] = Task::where('company_id', $companyId)->count();
                $stats['completed_tasks'] = Task::where('company_id', $companyId)->where('status', 'completed')->count();
                $stats['pending_tasks'] = Task::where('company_id', $companyId)->where('status', 'pending')->count();
                $stats['in_progress_tasks'] = Task::where('company_id', $companyId)->where('status', 'in_progress')->count();
                
                // Calculate task efficiency
                if ($stats['total_tasks'] > 0) {
                    $stats['task_efficiency'] = round(($stats['completed_tasks'] / $stats['total_tasks']) * 100, 1);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Task stats error: ' . $e->getMessage());
        }
        
        try {
            // Site statistics (only if Site model exists)
            if (class_exists('App\Models\Site')) {
                $stats['total_sites'] = \App\Models\Site::where('company_id', $companyId)->count();
            }
        } catch (\Exception $e) {
            \Log::error('Site stats error: ' . $e->getMessage());
        }
        
        try {
            // Financial statistics (only if Invoice model exists)
            if (class_exists('App\Models\Invoice')) {
                // Monthly revenue - invoices paid this month
                $stats['monthly_revenue'] = \App\Models\Invoice::where('company_id', $companyId)
                    ->where('status', 'paid')
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->sum('amount');
                
                // Pending invoices amount
                $stats['pending_invoices'] = \App\Models\Invoice::where('company_id', $companyId)
                    ->whereIn('status', ['pending', 'sent'])
                    ->sum('amount');
            }
        } catch (\Exception $e) {
            \Log::error('Invoice stats error: ' . $e->getMessage());
        }
        
        try {
            // Budget statistics (only if ProjectExpense model exists)
            if (class_exists('App\Models\ProjectExpense')) {
                // Calculate used budget from project expenses
                $stats['used_budget'] = \App\Models\ProjectExpense::whereHas('project', function($query) use ($companyId) {
                    $query->where('company_id', $companyId);
                })->sum('amount');
                
                // Get total budget from projects
                $stats['total_budget'] = Project::where('company_id', $companyId)->sum('budget');
                
                // Calculate budget utilization percentage
                if ($stats['total_budget'] > 0) {
                    $stats['budget_utilization'] = round(($stats['used_budget'] / $stats['total_budget']) * 100, 1);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Budget stats error: ' . $e->getMessage());
        }
        
        // Get recent projects for the company
        $recentProjects = collect([]);
        try {
            $recentProjects = Project::where('company_id', $companyId)
                ->latest()
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            \Log::error('Recent projects error: ' . $e->getMessage());
        }
        
        // Get recent tasks for the company
        $recentTasks = collect([]);
        try {
            $recentTasks = Task::whereHas('project', function($query) use ($companyId) {
                    $query->where('company_id', $companyId);
                })
                ->latest()
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            \Log::error('Recent tasks error: ' . $e->getMessage());
        }

        // Get recent sites for the company
        $recentSites = collect([]);
        try {
            $recentSites = Site::where('company_id', $companyId)
                ->where('is_active', true)
                ->with(['client', 'projects'])
                ->withCount([
                    'projects',
                    'projects as active_projects_count' => function($q) {
                        $q->where('status', 'in_progress');
                    },
                    'projects as completed_projects_count' => function($q) {
                        $q->where('status', 'completed');
                    }
                ])
                ->latest()
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            \Log::error('Recent sites error: ' . $e->getMessage());
        }

        // Get recent activities for the company
        $recentActivities = collect([]);
        try {
            $recentActivities = Activity::with(['subject', 'causer'])
                ->where(function($query) use ($companyId) {
                    // Get activities where the subject belongs to this company
                    $query->whereHasMorph('subject', [Project::class, Task::class, Client::class, User::class], function($q, $type) use ($companyId) {
                        if ($type === Project::class) {
                            $q->where('company_id', $companyId);
                        } elseif ($type === Task::class) {
                            $q->whereHas('project', function($projectQuery) use ($companyId) {
                                $projectQuery->where('company_id', $companyId);
                            });
                        } elseif ($type === Client::class) {
                            $q->where('company_id', $companyId);
                        } elseif ($type === User::class) {
                            $q->where('company_id', $companyId);
                        }
                    })
                    // OR get activities with no subject but caused by users from this company
                    ->orWhere(function($subQuery) use ($companyId) {
                        $subQuery->whereNull('subject_type')
                                 ->whereHasMorph('causer', [User::class], function($q) use ($companyId) {
                                     $q->where('company_id', $companyId);
                                 });
                    });
                })
                ->latest()
                ->limit(5)
                ->get()
                ->map(function($activity) {
                    return [
                        'title' => $this->formatActivityTitle($activity),
                        'description' => $this->formatActivityDescription($activity),
                        'time' => $activity->created_at,
                        'icon' => $this->getActivityIcon($activity),
                        'color' => $this->getActivityColor($activity)
                    ];
                });
        } catch (\Exception $e) {
            \Log::error('Recent activities error: ' . $e->getMessage());
        }

        // Get onboarding data for new users
        $onboardingData = null;
        try {
            $onboardingController = new OnboardingController();
            $onboardingData = $onboardingController->getDashboardData();
        } catch (\Exception $e) {
            \Log::error('Onboarding data error: ' . $e->getMessage());
        }
        
        return view('dashboard', compact('stats', 'recentProjects', 'recentTasks', 'recentSites', 'recentActivities', 'onboardingData'));
    }
    
    private function superAdminDashboard()
    {
        // Complete SuperAdmin stats with all required keys - Production Cache Refresh v1.1
        $stats = [
            'total_companies' => 0,
            'active_companies' => 0,
            'total_users' => 0,
            'total_projects' => 0,
            'active_projects' => 0,  // Added missing key
            'total_revenue' => 0,    // Added missing key
        ];
        
        // Initialize all variables with default values
        $companiesByStatus = [
            'active' => 0,
            'suspended' => 0,
            'inactive' => 0,
        ];
        
        $companiesByPlan = [
            'trial' => 0,
            'starter' => 0,
            'professional' => 0,
            'enterprise' => 0,
        ];
        
        // Try to get stats safely
        try {
            $stats['total_companies'] = Company::count();
            $stats['active_companies'] = Company::where('status', 'active')->count();
            $stats['total_users'] = User::count();
            $stats['total_projects'] = Project::count();
            $stats['active_projects'] = Project::whereIn('status', ['in_progress', 'pending'])->count();
            
            // Get companies by status - ensure these are always set
            $companiesByStatus['active'] = Company::where('status', 'active')->count();
            $companiesByStatus['suspended'] = Company::where('status', 'suspended')->count();
            $companiesByStatus['inactive'] = Company::where('status', 'inactive')->count();
            
            // Get companies by plan (if subscription_plan column exists)
            try {
                $companiesByPlan['trial'] = Company::where('subscription_plan', 'trial')->count();
                $companiesByPlan['starter'] = Company::where('subscription_plan', 'starter')->count();
                $companiesByPlan['professional'] = Company::where('subscription_plan', 'professional')->count();
                $companiesByPlan['enterprise'] = Company::where('subscription_plan', 'enterprise')->count();
            } catch (\Exception $e) {
                // Column might not exist, use defaults
            }
        } catch (\Exception $e) {
            // If there's any error, just use default stats
        }
        
        // Get recent companies safely
        $recentCompanies = [];
        try {
            $recentCompanies = Company::latest()->limit(5)->get();
        } catch (\Exception $e) {
            // If there's any error, just use empty array
        }
        
        // Get recent activity - create empty collection for now
        $recentActivity = collect([]);
        
        // Get top companies - create empty array for now
        $topCompanies = [];
        try {
            // You could implement actual top companies logic here based on metrics
            $topCompanies = Company::orderBy('created_at', 'desc')->limit(10)->get();
        } catch (\Exception $e) {
            // If there's any error, just use empty array
        }
        
        return view('superadmin.dashboard', compact('stats', 'recentCompanies', 'companiesByStatus', 'companiesByPlan', 'recentActivity', 'topCompanies'));
    }

    /**
     * Format activity title based on the activity description and subject
     */
    private function formatActivityTitle($activity)
    {
        $subjectType = class_basename($activity->subject_type ?? '');
        $description = $activity->description ?? 'updated';
        
        if ($activity->subject) {
            $subjectName = $activity->subject->name ?? $activity->subject->title ?? "#{$activity->subject->id}";
            
            switch ($description) {
                case 'created':
                    return "{$subjectType} \"{$subjectName}\" was created";
                case 'updated':
                    return "{$subjectType} \"{$subjectName}\" was updated";
                case 'deleted':
                    return "{$subjectType} \"{$subjectName}\" was deleted";
                default:
                    return "{$subjectType} \"{$subjectName}\" {$description}";
            }
        }
        
        return ucfirst($description) . ' activity';
    }

    /**
     * Format activity description based on the changes made
     */
    private function formatActivityDescription($activity)
    {
        $causerName = $activity->causer->name ?? 'System';
        
        if (!empty($activity->properties['attributes']) && !empty($activity->properties['old'])) {
            $changes = array_keys(array_diff_assoc($activity->properties['attributes'], $activity->properties['old']));
            if (!empty($changes)) {
                $changeList = implode(', ', array_slice($changes, 0, 3));
                if (count($changes) > 3) {
                    $changeList .= ' and ' . (count($changes) - 3) . ' more';
                }
                return "Changes: {$changeList} by {$causerName}";
            }
        }
        
        return "Action performed by {$causerName}";
    }

    /**
     * Get Bootstrap icon class for activity type
     */
    private function getActivityIcon($activity)
    {
        $description = $activity->description ?? '';
        $subjectType = class_basename($activity->subject_type ?? '');
        
        switch ($description) {
            case 'created':
                return 'bi-plus-circle';
            case 'updated':
                return 'bi-pencil-square';
            case 'deleted':
                return 'bi-trash';
            default:
                switch (strtolower($subjectType)) {
                    case 'project':
                        return 'bi-folder';
                    case 'task':
                        return 'bi-check-square';
                    case 'user':
                        return 'bi-person';
                    case 'client':
                        return 'bi-building';
                    default:
                        return 'bi-activity';
                }
        }
    }

    /**
     * Get Bootstrap color class for activity type
     */
    private function getActivityColor($activity)
    {
        $description = $activity->description ?? '';
        
        switch ($description) {
            case 'created':
                return 'success';
            case 'updated':
                return 'info';
            case 'deleted':
                return 'danger';
            default:
                return 'primary';
        }
    }
}