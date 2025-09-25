<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\OperativeInvoice;
use App\Models\Expense;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TestFinancialController extends Controller
{
    public function showFinancialSummary()
    {
        // Force login as Alex for testing
        Auth::loginUsingId(2);
        
        $companyId = 1;
        $startDate = Carbon::parse('2025-01-01');
        $endDate = Carbon::now();
        
        // Get revenue
        $revenue = Invoice::where('company_id', $companyId)
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->sum('total_amount');
        
        // Get operative wages  
        $operativeWages = OperativeInvoice::where('company_id', $companyId)
            ->whereIn('status', ['paid', 'approved'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('paid_at', [$startDate, $endDate])
                      ->orWhereBetween('approved_at', [$startDate, $endDate])
                      ->orWhereBetween('week_ending', [$startDate, $endDate]);
            })
            ->sum('gross_amount');
        
        // Get regular expenses
        $regularExpenses = Expense::where('company_id', $companyId)
            ->whereBetween(\DB::raw('date(COALESCE(reimbursed_at, approved_at, expense_date))'), [$startDate, $endDate])
            ->sum(\DB::raw('amount + COALESCE(mileage * mileage_rate, 0)'));
        
        $totalExpenses = $operativeWages + $regularExpenses;
        $grossProfit = $revenue - $totalExpenses;
        $profitMargin = $revenue > 0 ? round(($grossProfit / $revenue) * 100, 1) : 0;
        
        // Get active projects
        $activeProjects = Project::where('company_id', $companyId)
            ->whereIn('status', ['in_progress', 'planning', 'on_hold'])
            ->count();
        
        // Get recent invoices
        $recentInvoices = Invoice::where('company_id', $companyId)
            ->with(['client', 'project'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('test-financial', compact(
            'revenue', 
            'operativeWages', 
            'regularExpenses',
            'totalExpenses',
            'grossProfit',
            'profitMargin',
            'activeProjects',
            'recentInvoices'
        ));
    }
}