<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Site;
use App\Models\Invoice;
use App\Models\Expense;
use App\Models\CisPayment;
use App\Models\OperativeInvoice;
use App\Models\ToolHireRequest;
use App\Models\ProjectExpense;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('company.access');
    }

    /**
     * Display the main reports dashboard
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $companyId = $user->company_id;
        
        // Get date range from request or default to current year
        $startDate = $request->get('start_date', now()->startOfYear()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        
        // Get active tab from request
        $activeTab = $request->get('tab', 'overview');
        
        // Generate overview report data
        $reportData = [
            'overview' => $this->getOverviewReport($companyId, $startDate, $endDate),
            'sites' => $this->getSitesReport($companyId, $startDate, $endDate),
            'projects' => $this->getProjectsReport($companyId, $startDate, $endDate),
            'vat' => $this->getVATReport($companyId, $startDate, $endDate),
            'cis' => $this->getCISReport($companyId, $startDate, $endDate),
            'expenses' => $this->getExpensesReport($companyId, $startDate, $endDate),
            'profitability' => $this->getProfitabilityReport($companyId, $startDate, $endDate),
        ];
        
        // Get filter options
        $sites = Site::forCompany($companyId)->orderBy('name')->get();
        $projects = Project::forCompany($companyId)->orderBy('name')->get();
        
        return view('financial-reports.index', compact(
            'reportData', 
            'sites', 
            'projects', 
            'startDate', 
            'endDate', 
            'activeTab'
        ));
    }

    /**
     * Generate overview report
     */
    private function getOverviewReport($companyId, $startDate, $endDate)
    {
        // Total Revenue from paid invoices
        $totalRevenue = Invoice::where('company_id', $companyId)
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->sum('total_amount');

        // Regular Expenses: include items whose reimbursed_at OR approved_at OR expense_date falls in range
        $regularExpenses = Expense::where('company_id', $companyId)
            ->whereBetween(DB::raw('date(COALESCE(reimbursed_at, approved_at, expense_date))'), [$startDate, $endDate])
            ->sum(DB::raw('amount + COALESCE(mileage * mileage_rate, 0)'));
        
        // Operative Wages: include paid and approved operative invoices
        $operativeWages = OperativeInvoice::where('company_id', $companyId)
            ->whereIn('status', ['paid', 'approved'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('paid_at', [$startDate, $endDate])
                      ->orWhereBetween('approved_at', [$startDate, $endDate])
                      ->orWhereBetween('week_ending', [$startDate, $endDate]);
            })
            ->sum('gross_amount');

        // Tool Hire Costs: include completed tool hire requests
        $toolHireCosts = \App\Models\ToolHireRequest::where('company_id', $companyId)
            ->whereIn('status', ['completed', 'returned', 'in_use', 'delivered'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('hire_end_date', [$startDate, $endDate])
                      ->orWhereBetween('actual_return_date', [$startDate, $endDate]);
            })
            ->sum(DB::raw('COALESCE(actual_total_cost, estimated_total_cost, 0)'));

        $totalExpenses = $regularExpenses + $operativeWages + $toolHireCosts;

        // Active Projects
        $activeProjects = Project::where('company_id', $companyId)
            ->whereIn('status', ['planning', 'in_progress'])
            ->count();

        // Recent invoices for overview tab  
        $recentInvoices = Invoice::where('company_id', $companyId)
            ->with(['client', 'project'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Recent expenses for overview tab
        $recentExpenses = Expense::where('company_id', $companyId)
            ->with(['user', 'project'])
            ->orderBy('expense_date', 'desc')
            ->limit(5)
            ->get();

        return [
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'gross_profit' => $totalRevenue - $totalExpenses,
            'profit_margin' => $totalRevenue > 0 ? round((($totalRevenue - $totalExpenses) / $totalRevenue) * 100, 1) : 0,
            'active_projects' => $activeProjects,
            'recent_invoices' => $recentInvoices,
            'recent_expenses' => $recentExpenses,
        ];
    }

    /**
     * Generate sites report
     */
    private function getSitesReport($companyId, $startDate, $endDate)
    {
        return Site::forCompany($companyId)
            ->with(['projects'])
            ->get()
            ->map(function($site) {
                return [
                    'site' => $site,
                    'project_count' => $site->projects->count(),
                    'active_projects' => $site->projects->whereIn('status', ['planning', 'in_progress'])->count()
                ];
            });
    }

    /**
     * Generate projects report
     */
    private function getProjectsReport($companyId, $startDate, $endDate)
    {
        return Project::forCompany($companyId)
            ->with(['site', 'client'])
            ->get()
            ->map(function($project) {
                return [
                    'project' => $project,
                    'status' => $project->status,
                    'budget' => $project->budget ?? 0,
                    'progress' => $project->progress ?? 0
                ];
            });
    }

    /**
     * Generate VAT report from project expenses
     */
    private function getVATReport($companyId, $startDate, $endDate)
    {
        // VAT from project expenses
        $projectVATData = \App\Models\ProjectExpense::whereHas('project', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->where('status', 'approved')
            ->selectRaw("
                SUM(net_amount) as total_net,
                SUM(vat_amount) as total_vat,
                AVG(vat_rate) as average_vat_rate,
                COUNT(*) as expense_count
            ")
            ->first();

        // Monthly VAT breakdown
        $monthlyVATBreakdown = \App\Models\ProjectExpense::whereHas('project', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->where('status', 'approved')
            ->selectRaw("
                to_char(expense_date, 'YYYY-MM') as month,
                SUM(net_amount) as net_amount,
                SUM(vat_amount) as vat_amount,
                COUNT(*) as expense_count
            ")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // VAT by category
        $vatByCategory = \App\Models\ProjectExpense::whereHas('project', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->where('status', 'approved')
            ->selectRaw("
                category,
                SUM(net_amount) as net_amount,
                SUM(vat_amount) as vat_amount,
                COUNT(*) as expense_count
            ")
            ->groupBy('category')
            ->orderBy('vat_amount', 'desc')
            ->get();

        return [
            'summary' => $projectVATData,
            'monthly_breakdown' => $monthlyVATBreakdown,
            'by_category' => $vatByCategory
        ];
    }

    /**
     * Generate CIS report
     */
    private function getCISReport($companyId, $startDate, $endDate)
    {
        // CIS Summary from Operative Invoices
        $operativeInvoicesSummary = OperativeInvoice::forCompany($companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('cis_applicable', true)
            ->whereIn('status', ['approved', 'paid'])
            ->selectRaw('
                SUM(gross_amount) as total_gross_pay,
                SUM(cis_deduction) as total_cis_deductions,
                SUM(net_amount) as total_net_pay,
                COUNT(*) as payment_count,
                COUNT(DISTINCT operative_id) as unique_operatives
            ')
            ->first();

        // Use operative invoices summary as the main data
        $summary = (object) [
            'total_gross_pay' => $operativeInvoicesSummary->total_gross_pay ?? 0,
            'total_cis_deductions' => $operativeInvoicesSummary->total_cis_deductions ?? 0,
            'total_net_pay' => $operativeInvoicesSummary->total_net_pay ?? 0,
            'payment_count' => $operativeInvoicesSummary->payment_count ?? 0,
            'unique_operatives' => $operativeInvoicesSummary->unique_operatives ?? 0
        ];

        // Monthly CIS breakdown from Operative Invoices
        $monthlyBreakdown = OperativeInvoice::forCompany($companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('cis_applicable', true)
            ->whereIn('status', ['approved', 'paid'])
            ->selectRaw("
                to_char(created_at, 'YYYY-MM') as month,
                SUM(gross_amount) as gross_pay,
                SUM(cis_deduction) as cis_deduction,
                SUM(net_amount) as net_pay,
                COUNT(*) as payment_count
            ")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Top operatives by CIS deductions from Operative Invoices
        $topOperatives = OperativeInvoice::where('operative_invoices.company_id', $companyId)
            ->whereBetween('operative_invoices.created_at', [$startDate, $endDate])
            ->where('operative_invoices.cis_applicable', true)
            ->whereIn('operative_invoices.status', ['approved', 'paid'])
            ->join('users', 'operative_invoices.operative_id', '=', 'users.id')
            ->selectRaw("
                users.name as payee_name,
                'operative' as payee_type,
                SUM(operative_invoices.gross_amount) as total_gross_pay,
                SUM(operative_invoices.cis_deduction) as total_cis_deduction,
                SUM(operative_invoices.net_amount) as total_net_pay,
                COUNT(operative_invoices.id) as payment_count
            ")
            ->groupBy('operative_invoices.operative_id', 'users.name')
            ->orderBy('total_cis_deduction', 'desc')
            ->limit(10)
            ->get();

        return [
            'summary' => $summary,
            'monthly_breakdown' => $monthlyBreakdown,
            'top_operatives' => $topOperatives
        ];
    }

    /**
     * Generate expenses report
     */
    private function getExpensesReport($companyId, $startDate, $endDate)
    {
        // Regular expenses summary
        $regularExpenses = \App\Models\Expense::whereHas('project', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->where('status', 'approved')
            ->selectRaw('
                SUM(amount) as total_amount,
                COUNT(*) as expense_count,
                AVG(amount) as average_amount
            ')
            ->first();

        // Project expenses summary (with VAT)
        $projectExpenses = \App\Models\ProjectExpense::whereHas('project', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->where('status', 'approved')
            ->selectRaw('
                SUM(net_amount) as total_net_amount,
                SUM(vat_amount) as total_vat_amount,
                SUM(amount) as total_amount,
                COUNT(*) as expense_count,
                AVG(amount) as average_amount
            ')
            ->first();

        // Expenses by category (combining both regular and project expenses)
        $categoryBreakdown = \App\Models\ProjectExpense::whereHas('project', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->where('status', 'approved')
            ->selectRaw("
                category,
                SUM(net_amount) as net_amount,
                SUM(vat_amount) as vat_amount,
                SUM(amount) as total_amount,
                COUNT(*) as expense_count
            ")
            ->groupBy('category')
            ->orderBy('total_amount', 'desc')
            ->get();

        // Monthly expenses trend
        $monthlyExpenses = \App\Models\ProjectExpense::whereHas('project', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->where('status', 'approved')
            ->selectRaw("
                to_char(expense_date, 'YYYY-MM') as month,
                SUM(net_amount) as net_amount,
                SUM(vat_amount) as vat_amount,
                SUM(amount) as total_amount,
                COUNT(*) as expense_count
            ")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Top projects by expenses
        $topProjectsByExpenses = \App\Models\ProjectExpense::whereHas('project', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->where('project_expenses.status', 'approved')
            ->join('projects', 'project_expenses.project_id', '=', 'projects.id')
            ->selectRaw('
                projects.name as project_name,
                SUM(project_expenses.amount) as total_expenses,
                COUNT(project_expenses.id) as expense_count
            ')
            ->groupBy('projects.id', 'projects.name')
            ->orderBy('total_expenses', 'desc')
            ->limit(10)
            ->get();

        return [
            'regular_expenses' => $regularExpenses,
            'project_expenses' => $projectExpenses,
            'category_breakdown' => $categoryBreakdown,
            'monthly_expenses' => $monthlyExpenses,
            'top_projects' => $topProjectsByExpenses
        ];
    }

    /**
     * Generate profitability report
     */
    private function getProfitabilityReport($companyId, $startDate, $endDate)
    {
        // Overall profitability metrics
        $totalRevenue = \App\Models\Invoice::whereHas('project', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->sum('total_amount');

        $totalExpenses = \App\Models\ProjectExpense::whereHas('project', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->where('status', 'approved')
            ->sum('amount');

        $totalCISDeductions = \App\Models\CisPayment::forCompany($companyId)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->sum('cis_deduction');

        $grossProfit = $totalRevenue - $totalExpenses;
        $netProfit = $grossProfit; // Simplified calculation
        $profitMargin = $totalRevenue > 0 ? round(($grossProfit / $totalRevenue) * 100, 1) : 0;

        // Project profitability analysis
        $projectProfitability = \App\Models\Project::forCompany($companyId)
            ->with(['client'])
            ->get()
            ->map(function($project) use ($startDate, $endDate) {
                $revenue = \App\Models\Invoice::where('project_id', $project->id)
                    ->whereBetween('issue_date', [$startDate, $endDate])
                    ->where('status', 'paid')
                    ->sum('total_amount');
                
                $expenses = \App\Models\ProjectExpense::where('project_id', $project->id)
                    ->whereBetween('expense_date', [$startDate, $endDate])
                    ->where('status', 'approved')
                    ->sum('amount');

                $profit = $revenue - $expenses;
                $margin = $revenue > 0 ? round(($profit / $revenue) * 100, 1) : 0;

                return [
                    'project' => $project,
                    'revenue' => $revenue,
                    'expenses' => $expenses,
                    'profit' => $profit,
                    'margin' => $margin
                ];
            })
            ->filter(function($item) {
                return $item['revenue'] > 0 || $item['expenses'] > 0;
            })
            ->sortByDesc('profit')
            ->take(10);

        // Monthly profitability trend
        $monthlyProfitability = collect();
        $current = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);

        while ($current->lte($end)) {
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();

            $monthRevenue = \App\Models\Invoice::whereHas('project', function($q) use ($companyId) {
                    $q->where('company_id', $companyId);
                })
                ->whereBetween('issue_date', [$monthStart, $monthEnd])
                ->where('status', 'paid')
                ->sum('total_amount');

            $monthExpenses = \App\Models\ProjectExpense::whereHas('project', function($q) use ($companyId) {
                    $q->where('company_id', $companyId);
                })
                ->whereBetween('expense_date', [$monthStart, $monthEnd])
                ->where('status', 'approved')
                ->sum('amount');

            $monthlyProfitability->push([
                'month' => $current->format('Y-m'),
                'revenue' => $monthRevenue,
                'expenses' => $monthExpenses,
                'profit' => $monthRevenue - $monthExpenses,
                'margin' => $monthRevenue > 0 ? round((($monthRevenue - $monthExpenses) / $monthRevenue) * 100, 1) : 0
            ]);

            $current->addMonth();
        }

        return [
            'summary' => [
                'total_revenue' => $totalRevenue,
                'total_expenses' => $totalExpenses,
                'total_cis_deductions' => $totalCISDeductions,
                'gross_profit' => $grossProfit,
                'net_profit' => $netProfit,
                'profit_margin' => $profitMargin
            ],
            'project_profitability' => $projectProfitability,
            'monthly_profitability' => $monthlyProfitability
        ];
    }

    /**
     * Export report as PDF or Excel
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'pdf');
        $reportType = $request->get('report_type', 'overview');
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());
        
        // For now, return JSON response indicating export functionality
        return response()->json([
            'message' => 'Export functionality would integrate with PDF/Excel libraries',
            'format' => $format,
            'report_type' => $reportType,
            'date_range' => $startDate . ' to ' . $endDate
        ]);
    }
}
