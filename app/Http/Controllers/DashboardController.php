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
        // Hardcoded stats to test if the view rendering is the issue
        $stats = [
            'total_projects' => 5,
            'active_projects' => 3,
            'total_tasks' => 15,
            'completed_tasks' => 10,
            'pending_tasks' => 5,
            'in_progress_tasks' => 3,
            'overdue_tasks' => 2,
            'total_sites' => 2,
            'team_members' => 8,
            'total_clients' => 4,
            'monthly_revenue' => 25000,
            'pending_invoices' => 5500,
            'completion_rate' => 60,
            'task_efficiency' => 67,
            'budget_utilization' => 75,
            'used_budget' => 75000,
            'total_budget' => 100000,
        ];
        
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