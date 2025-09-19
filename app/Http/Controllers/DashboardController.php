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
        // Comprehensive company dashboard with all required stats
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
        
        // Try to get stats if company exists
        try {
            if (auth()->user()->company_id) {
                $companyId = auth()->user()->company_id;
                
                // Project stats
                $stats['total_projects'] = Project::where('company_id', $companyId)->count();
                $stats['active_projects'] = Project::where('company_id', $companyId)->where('status', '!=', 'completed')->count();
                
                // Task stats
                $stats['total_tasks'] = Task::where('company_id', $companyId)->count();
                $stats['completed_tasks'] = Task::where('company_id', $companyId)->where('status', 'completed')->count();
                $stats['pending_tasks'] = Task::where('company_id', $companyId)->where('status', 'pending')->count();
                $stats['in_progress_tasks'] = Task::where('company_id', $companyId)->where('status', 'in_progress')->count();
                $stats['overdue_tasks'] = Task::where('company_id', $companyId)->where('status', '!=', 'completed')->where('due_date', '<', now())->count();
                
                // Calculate completion rate
                if ($stats['total_projects'] > 0) {
                    $completedProjects = Project::where('company_id', $companyId)->where('status', 'completed')->count();
                    $stats['completion_rate'] = round(($completedProjects / $stats['total_projects']) * 100, 1);
                }
                
                // Calculate task efficiency
                if ($stats['total_tasks'] > 0) {
                    $stats['task_efficiency'] = round(($stats['completed_tasks'] / $stats['total_tasks']) * 100, 1);
                }
                
                // Additional stats (using safe model queries with fallbacks)
                try {
                    $stats['total_sites'] = \App\Models\Site::where('company_id', $companyId)->count();
                } catch (\Exception $e) {
                    // Site model might not exist or have issues
                }
                
                try {
                    $stats['team_members'] = User::where('company_id', $companyId)->count();
                } catch (\Exception $e) {
                    // Fallback already set to 0
                }
                
                try {
                    $stats['total_clients'] = Client::where('company_id', $companyId)->count();
                } catch (\Exception $e) {
                    // Client model might not exist or have issues
                }
                
                // Financial stats (with model existence checks)
                try {
                    if (class_exists('\App\Models\Invoice')) {
                        $pendingInvoices = \App\Models\Invoice::where('company_id', $companyId)
                            ->where('status', '!=', 'paid')
                            ->sum('amount');
                        $stats['pending_invoices'] = $pendingInvoices;
                        
                        $monthlyRevenue = \App\Models\Invoice::where('company_id', $companyId)
                            ->where('status', 'paid')
                            ->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->sum('amount');
                        $stats['monthly_revenue'] = $monthlyRevenue;
                    }
                } catch (\Exception $e) {
                    // Invoice model might not exist or have issues
                }
                
                // Budget stats (with model existence checks)
                try {
                    if (class_exists('\App\Models\ProjectExpense')) {
                        $usedBudget = \App\Models\ProjectExpense::whereHas('project', function($query) use ($companyId) {
                            $query->where('company_id', $companyId);
                        })->sum('amount');
                        $stats['used_budget'] = $usedBudget;
                        
                        $totalBudget = Project::where('company_id', $companyId)->sum('budget');
                        $stats['total_budget'] = $totalBudget;
                        
                        if ($totalBudget > 0) {
                            $stats['budget_utilization'] = round(($usedBudget / $totalBudget) * 100, 1);
                        }
                    }
                } catch (\Exception $e) {
                    // ProjectExpense model might not exist or have issues
                }
            }
        } catch (\Exception $e) {
            // If there's any error, just use default stats (already initialized to 0)
        }
        
        return view('dashboard', compact('stats'));
    }
    
    private function superAdminDashboard()
    {
        // Simple SuperAdmin stats
        $stats = [
            'total_companies' => 0,
            'active_companies' => 0,
            'total_users' => 0,
            'total_projects' => 0,
        ];
        
        // Try to get stats safely
        try {
            $stats['total_companies'] = Company::count();
            $stats['active_companies'] = Company::where('status', 'active')->count();
            $stats['total_users'] = User::count();
            $stats['total_projects'] = Project::count();
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
        
        return view('superadmin.dashboard', compact('stats', 'recentCompanies'));
    }
}