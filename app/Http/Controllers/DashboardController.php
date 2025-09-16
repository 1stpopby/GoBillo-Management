<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Client;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()->isSuperAdmin()) {
            return $this->superAdminDashboard();
        } else {
            $stats = $this->getCompanyStats();
            
            // Get recent sites
            $recentSites = \App\Models\Site::forCompany()
                ->with(['client', 'projects'])
                ->latest()
                ->limit(5)
                ->get();
            
            // Get recent projects
            $recentProjects = Project::forCompany()
                ->with(['site', 'client', 'manager'])
                ->latest()
                ->limit(5)
                ->get();
            
            // Get user's recent tasks with custom sorting
            if (auth()->user()->canViewAllTasks()) {
                $recentTasks = Task::forCompany()
                    ->with(['project.site', 'assignedUser', 'taskCategory'])
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
                    ->limit(10)
                    ->get();
            } else {
                $recentTasks = Task::forCompany()
                    ->forUser()
                    ->with(['project.site', 'assignedUser', 'taskCategory'])
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
                    ->limit(10)
                    ->get();
            }
        }

        return view('dashboard', compact('stats', 'recentSites', 'recentProjects', 'recentTasks'));
    }

    private function superAdminDashboard()
    {
        $stats = $this->getSuperAdminStats();
        
        // Get recent companies
        $recentCompanies = Company::with(['users', 'activeSubscription.membershipPlan'])
            ->withCount(['users', 'projects'])
            ->latest()
            ->limit(10)
            ->get();
        
        // Get companies by status
        $companiesByStatus = [
            'active' => Company::where('status', Company::STATUS_ACTIVE)->count(),
            'suspended' => Company::where('status', Company::STATUS_SUSPENDED)->count(),
            'inactive' => Company::where('status', Company::STATUS_INACTIVE)->count(),
        ];
        
        // Get companies by subscription plan (using new subscription system)
        $companiesByPlan = [
            'starter' => Company::whereHas('activeSubscription.membershipPlan', function($q) {
                $q->where('slug', 'starter');
            })->count(),
            'professional' => Company::whereHas('activeSubscription.membershipPlan', function($q) {
                $q->where('slug', 'professional');
            })->count(),
            'enterprise' => Company::whereHas('activeSubscription.membershipPlan', function($q) {
                $q->where('slug', 'enterprise');
            })->count(),
            'trial' => Company::whereHas('activeSubscription', function($q) {
                $q->where('status', 'trial');
            })->count(),
        ];
        
        // Get recent activity across all companies
        $recentActivity = collect();
        
        // Recent users
        $recentUsers = User::whereNotNull('company_id')
            ->with('company')
            ->latest()
            ->limit(10)
            ->get()
            ->map(function($user) {
                return [
                    'type' => 'user',
                    'title' => 'New User Registered',
                    'description' => $user->name . ' joined ' . $user->company->name,
                    'company' => $user->company->name,
                    'date' => $user->created_at,
                    'icon' => 'bi-person-plus',
                    'color' => 'success'
                ];
            });
        
        // Recent projects
        $recentProjects = Project::with(['company', 'site'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function($project) {
                return [
                    'type' => 'project',
                    'title' => 'New Project Created',
                    'description' => $project->name,
                    'company' => $project->company->name,
                    'date' => $project->created_at,
                    'icon' => 'bi-building',
                    'color' => 'primary'
                ];
            });
        
        $recentActivity = $recentUsers->merge($recentProjects)
            ->sortByDesc('date')
            ->take(15);
        
        // Get top companies by activity
        $topCompanies = Company::withCount(['users', 'projects'])
            ->orderBy('users_count', 'desc')
            ->orderBy('projects_count', 'desc')
            ->limit(10)
            ->get();
        
        return view('superadmin.dashboard', compact(
            'stats',
            'recentCompanies',
            'companiesByStatus',
            'companiesByPlan',
            'recentActivity',
            'topCompanies'
        ));
    }

    private function getSuperAdminStats()
    {
        $stats = [
            'total_companies' => Company::count(),
            'active_companies' => Company::where('status', Company::STATUS_ACTIVE)->count(),
            'trial_companies' => Company::where('subscription_plan', Company::PLAN_TRIAL)->count(),
            'total_users' => User::count(),
            'total_projects' => Project::count(),
            'active_projects' => Project::whereIn('status', ['planning', 'in_progress'])->count(),
            'total_tasks' => Task::count(),
            'total_clients' => Client::count(),
            'total_revenue' => $this->calculateTotalRevenue(), // Real revenue from subscriptions
        ];

        return $stats;
    }

    private function calculateTotalRevenue()
    {
        // Calculate total monthly revenue from active subscriptions
        return \App\Models\Subscription::whereIn('status', ['active', 'trial'])
            ->sum('amount');
    }

    private function getCompanyStats(): array
    {
        $companyId = auth()->user()->company_id;
        
        // Basic counts
        $stats = [
            'total_sites' => \App\Models\Site::forCompany()->count(),
            'active_sites' => \App\Models\Site::forCompany()->where('status', 'active')->count(),
            'total_projects' => Project::forCompany()->count(),
            'active_projects' => Project::forCompany()->where('status', 'in_progress')->count(),
            'pending_tasks' => Task::forCompany()->where('status', 'pending')->count(),
            'in_progress_tasks' => Task::forCompany()->where('status', 'in_progress')->count(),
            'completed_tasks' => Task::forCompany()->where('status', 'completed')->count(),
            'overdue_tasks' => Task::forCompany()
                                  ->where('due_date', '<', now())
                                  ->where('status', '!=', 'completed')
                                  ->count(),
            'team_members' => User::forCompany()->count(),
            'total_clients' => Client::forCompany()->count()
        ];

        // Financial metrics
        $stats['monthly_revenue'] = \App\Models\Invoice::forCompany()
            ->where('status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        $stats['pending_invoices'] = \App\Models\Invoice::forCompany()
            ->where('status', 'sent')
            ->sum('total');

        // Project completion rate
        $totalProjects = $stats['total_projects'];
        $completedProjects = Project::forCompany()->where('status', 'completed')->count();
        $stats['completion_rate'] = $totalProjects > 0 ? round(($completedProjects / $totalProjects) * 100, 1) : 0;

        // Task efficiency
        $totalTasks = Task::forCompany()->count();
        $completedTasks = $stats['completed_tasks'];
        $stats['task_efficiency'] = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;

        // Recent activity counts
        $stats['recent_projects'] = Project::forCompany()
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        $stats['recent_tasks'] = Task::forCompany()
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        // Budget utilization
        $totalBudget = Project::forCompany()->sum('budget');
        $usedBudget = \App\Models\ProjectExpense::whereHas('project', function($q) {
            $q->forCompany();
        })->where('status', 'approved')->sum('amount');
        
        $stats['budget_utilization'] = $totalBudget > 0 ? round(($usedBudget / $totalBudget) * 100, 1) : 0;
        $stats['total_budget'] = $totalBudget;
        $stats['used_budget'] = $usedBudget;

        return $stats;
    }

    private function getAdminStats($user)
    {
        return [
            'total_projects' => Project::forCompany($user->company_id)->count(),
            'active_projects' => Project::forCompany($user->company_id)
                                      ->whereIn('status', ['planning', 'in_progress'])
                                      ->count(),
            'completed_projects' => Project::forCompany($user->company_id)
                                          ->where('status', 'completed')
                                          ->count(),
            'total_tasks' => Task::forCompany($user->company_id)->count(),
            'pending_tasks' => Task::forCompany($user->company_id)
                                 ->where('status', 'pending')
                                 ->count(),
            'in_progress_tasks' => Task::forCompany($user->company_id)
                                     ->where('status', 'in_progress')
                                     ->count(),
            'completed_tasks' => Task::forCompany($user->company_id)
                                   ->where('status', 'completed')
                                   ->count(),
            'total_clients' => Client::forCompany($user->company_id)->count(),
            'active_clients' => Client::forCompany($user->company_id)
                                    ->where('is_active', true)
                                    ->count(),
            'total_users' => User::forCompany($user->company_id)->count(),
            'active_users' => User::forCompany($user->company_id)
                                ->where('is_active', true)
                                ->count(),
        ];
    }

    private function getUserStats($user)
    {
        return [
            'my_projects' => Project::forCompany($user->company_id)
                                  ->whereHas('users', function ($q) use ($user) {
                                      $q->where('user_id', $user->id);
                                  })
                                  ->count(),
            'my_tasks' => Task::forCompany($user->company_id)
                            ->where('assigned_to', $user->id)
                            ->count(),
            'pending_tasks' => Task::forCompany($user->company_id)
                                 ->where('assigned_to', $user->id)
                                 ->where('status', 'pending')
                                 ->count(),
            'in_progress_tasks' => Task::forCompany($user->company_id)
                                     ->where('assigned_to', $user->id)
                                     ->where('status', 'in_progress')
                                     ->count(),
            'completed_tasks' => Task::forCompany($user->company_id)
                                   ->where('assigned_to', $user->id)
                                   ->where('status', 'completed')
                                   ->count(),
            'overdue_tasks' => Task::forCompany($user->company_id)
                                 ->where('assigned_to', $user->id)
                                 ->where('due_date', '<', now())
                                 ->whereNotIn('status', ['completed', 'cancelled'])
                                 ->count(),
        ];
    }

    private function getProjectStatusData($companyId)
    {
        return Project::forCompany($companyId)
                     ->selectRaw('status, COUNT(*) as count')
                     ->groupBy('status')
                     ->get()
                     ->pluck('count', 'status');
    }

    private function getTaskPriorityData($companyId)
    {
        return Task::forCompany($companyId)
                  ->selectRaw('priority, COUNT(*) as count')
                  ->groupBy('priority')
                  ->get()
                  ->pluck('count', 'priority');
    }
}