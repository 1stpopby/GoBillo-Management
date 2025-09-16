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
        // Simple company dashboard with basic stats
        $stats = [
            'total_projects' => 0,
            'active_projects' => 0,
            'total_tasks' => 0,
            'completed_tasks' => 0,
        ];
        
        // Try to get stats if company exists
        try {
            if (auth()->user()->company_id) {
                $stats['total_projects'] = Project::where('company_id', auth()->user()->company_id)->count();
                $stats['active_projects'] = Project::where('company_id', auth()->user()->company_id)->where('status', '!=', 'completed')->count();
                $stats['total_tasks'] = Task::where('company_id', auth()->user()->company_id)->count();
                $stats['completed_tasks'] = Task::where('company_id', auth()->user()->company_id)->where('status', 'completed')->count();
            }
        } catch (\Exception $e) {
            // If there's any error, just use default stats
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