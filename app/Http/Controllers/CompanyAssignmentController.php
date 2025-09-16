<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyAssignmentController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        
        // If user already has a company, redirect to dashboard
        if ($user->company_id) {
            return redirect()->route('dashboard');
        }
        
        // Get available companies (active companies that allow new users)
        $companies = Company::where('status', Company::STATUS_ACTIVE)
            ->whereRaw('(SELECT COUNT(*) FROM users WHERE company_id = companies.id) < companies.max_users')
            ->withCount(['users', 'projects'])
            ->get();
        
        return view('company-assignment', compact('companies', 'user'));
    }
    
    public function assign(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'message' => 'nullable|string|max:500',
        ]);
        
        $user = auth()->user();
        $company = Company::findOrFail($request->company_id);
        
        // Check if company can accept more users
        if ($company->users()->count() >= $company->max_users) {
            return back()->with('error', 'This company has reached its user limit.');
        }
        
        // For now, we'll auto-assign users as contractors
        // In a real application, this might require approval
        $user->update([
            'company_id' => $company->id,
            'role' => $user::ROLE_CONTRACTOR, // Default role for new users
        ]);
        
        return redirect()->route('dashboard')->with('success', 'You have been assigned to ' . $company->name . '!');
    }
}
