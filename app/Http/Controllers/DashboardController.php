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

        return view('dashboard', compact('stats'));
    }
    
    private function superAdminDashboard()
    {
        // Complete SuperAdmin stats with all required keys
        $stats = [
            'total_companies' => 0,
            'active_companies' => 0,
            'total_users' => 0,
            'total_projects' => 0,
            'active_projects' => 0,  // Added missing key
            'total_revenue' => 0,    // Added missing key
        ];
        
        // Additional stats that might be used in superadmin dashboard
        $companiesByStatus = [
            'active' => 0,
            'suspended' => 0,
            'inactive' => 0,
        ];
        
        // Companies by subscription plan
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
            
            // Get companies by status
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
}