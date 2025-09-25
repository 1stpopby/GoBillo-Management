<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Expense;
use App\Models\Estimate;
use App\Models\Project;
use App\Models\Client;
use App\Models\Site;
use App\Models\User;
use App\Models\OperativeInvoice;
use App\Models\ToolHireRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialReportController extends Controller
{
    /**
     * Display financial dashboard
     */
    public function index(Request $request)
    {
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Access denied.');
        }

        $dateRange = $this->getDateRange($request);
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];

        // Get financial summary
        $summary = $this->getFinancialSummary($startDate, $endDate);
        
        // Get monthly trends (last 12 months)
        $monthlyTrends = $this->getMonthlyTrends();
        
        // Get top clients by revenue
        $topClients = $this->getTopClients($startDate, $endDate);
        
        // Get profit margins by project
        $projectProfits = $this->getProjectProfitMargins($startDate, $endDate);

        // Company overview add-ons
        [$siteOverview, $siteTotals] = $this->getSiteOverview($startDate, $endDate);
        $paymentsFromClients = $this->getPaymentsFromClients($startDate, $endDate);
        [$paymentsByRole, $topPaidUsers] = $this->getPaymentsToTeam($startDate, $endDate);

        // Calculate additional overview metrics
        $grossProfit = $summary['total_revenue'] - $summary['total_expenses'];
        $profitMargin = $summary['total_revenue'] > 0 
            ? round(($grossProfit / $summary['total_revenue']) * 100, 1)
            : 0;
        
        $companyId = auth()->user()->company_id ?? 1;
        $activeProjects = Project::where('company_id', $companyId)
            ->whereIn('status', ['in_progress', 'planning', 'on_hold'])
            ->count();
        
        // Get recent financial records for overview tab
        $recentInvoices = Invoice::where('company_id', $companyId)
            ->with(['client', 'project'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        $recentExpenses = Expense::where('company_id', $companyId)
            ->with(['user', 'project'])
            ->orderBy('expense_date', 'desc')
            ->limit(5)
            ->get();
        
        // Structure the data for the view
        $reportData = [
            'overview' => [
                'total_revenue' => $summary['total_revenue'],
                'total_expenses' => $summary['total_expenses'],
                'gross_profit' => $grossProfit,
                'profit_margin' => $profitMargin,
                'active_projects' => $activeProjects,
                'outstanding_invoices' => $summary['outstanding_invoices'],
                'pending_estimates' => $summary['pending_estimates'],
                'recent_invoices' => $recentInvoices,
                'recent_expenses' => $recentExpenses,
            ],
            'sites' => $siteOverview,
            'siteTotals' => $siteTotals,
            'projects' => $projectProfits,
            'monthlyTrends' => $monthlyTrends,
            'topClients' => $topClients,
            'paymentsFromClients' => $paymentsFromClients,
            'paymentsByRole' => $paymentsByRole,
            'topPaidUsers' => $topPaidUsers,
            // VAT and CIS data will be calculated in their respective methods
            'vat' => [],
            'cis' => [],
        ];

        return view('financial-reports.index', compact(
            'reportData',
            'summary',
            'monthlyTrends',
            'topClients',
            'projectProfits',
            'siteOverview',
            'siteTotals',
            'paymentsFromClients',
            'paymentsByRole',
            'topPaidUsers',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Revenue report
     */
    public function revenue(Request $request)
    {
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Access denied.');
        }

        $dateRange = $this->getDateRange($request);
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];

        // Revenue by source
        $revenueBySource = [
            'invoices' => Invoice::forCompany()
                ->where('status', 'paid')
                ->whereBetween('paid_at', [$startDate, $endDate])
                ->sum('total_amount'),
            'estimates_converted' => Estimate::forCompany()
                ->where('status', 'converted')
                ->whereBetween('approved_at', [$startDate, $endDate])
                ->sum('total_amount'),
        ];

        // Revenue by client
        $revenueByClient = Invoice::forCompany()
            ->with('client')
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->select('client_id', DB::raw('SUM(total_amount) as total_revenue'))
            ->groupBy('client_id')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        // Revenue by project
        $revenueByProject = Invoice::forCompany()
            ->with('project')
            ->where('status', 'paid')
            ->whereNotNull('project_id')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->select('project_id', DB::raw('SUM(total_amount) as total_revenue'))
            ->groupBy('project_id')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        // Monthly revenue trend (SQLite compatible)
        $driver = DB::connection()->getDriverName();
        $monthExprRevenue = $driver === 'sqlite'
            ? 'strftime("%Y-%m", paid_at)'
            : 'DATE_FORMAT(paid_at, "%Y-%m")';

        $monthlyRevenue = Invoice::forCompany()
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->select(
                DB::raw($monthExprRevenue . ' as month'),
                DB::raw('SUM(total_amount) as total_revenue')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('financial-reports.revenue', compact(
            'revenueBySource',
            'revenueByClient',
            'revenueByProject',
            'monthlyRevenue',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Expense report
     */
    public function expenses(Request $request)
    {
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Access denied.');
        }

        $dateRange = $this->getDateRange($request);
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];
        $companyId = auth()->user()->company_id ?? 1;

        // Expenses by category (explicit DB query, SQLite-safe date filter)
        $expensesByCategory = DB::table('expenses')
            ->where('company_id', $companyId)
            ->whereBetween(DB::raw('date(COALESCE(reimbursed_at, approved_at, expense_date))'), [$startDate, $endDate])
            ->select('category', DB::raw('SUM(amount + COALESCE(mileage * mileage_rate, 0)) as total_amount'))
            ->groupBy('category')
            ->orderByDesc('total_amount')
            ->get();

        // Expenses by project
        $expensesByProject = DB::table('expenses')
            ->join('projects', 'expenses.project_id', '=', 'projects.id')
            ->where('expenses.company_id', $companyId)
            ->whereNotNull('expenses.project_id')
            ->whereBetween(DB::raw('date(COALESCE(expenses.reimbursed_at, expenses.approved_at, expenses.expense_date))'), [$startDate, $endDate])
            ->select('expenses.project_id', 'projects.name as project_name', DB::raw('SUM(expenses.amount + COALESCE(expenses.mileage * expenses.mileage_rate, 0)) as total_amount'))
            ->groupBy('expenses.project_id', 'projects.name')
            ->orderByDesc('total_amount')
            ->limit(10)
            ->get();

        // Expenses by user
        $expensesByUser = DB::table('expenses')
            ->join('users', 'expenses.user_id', '=', 'users.id')
            ->where('expenses.company_id', $companyId)
            ->whereBetween(DB::raw('date(COALESCE(expenses.reimbursed_at, expenses.approved_at, expenses.expense_date))'), [$startDate, $endDate])
            ->select('expenses.user_id', 'users.name as user_name', DB::raw('SUM(expenses.amount + COALESCE(expenses.mileage * expenses.mileage_rate, 0)) as total_amount'))
            ->groupBy('expenses.user_id', 'users.name')
            ->orderByDesc('total_amount')
            ->limit(10)
            ->get();

        // Monthly expense trend (SQLite compatible)
        $driver = DB::connection()->getDriverName();
        $monthExprExpense = $driver === 'sqlite'
            ? 'strftime("%Y-%m", COALESCE(reimbursed_at, approved_at, expense_date))'
            : 'DATE_FORMAT(COALESCE(reimbursed_at, approved_at, expense_date), "%Y-%m")';

        $monthlyExpenses = DB::table('expenses')
            ->where('company_id', $companyId)
            ->whereBetween(DB::raw('date(COALESCE(reimbursed_at, approved_at, expense_date))'), [$startDate, $endDate])
            ->select(
                DB::raw($monthExprExpense . ' as month'),
                DB::raw('SUM(amount + COALESCE(mileage * mileage_rate, 0)) as total_amount')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('financial-reports.expenses', compact(
            'expensesByCategory',
            'expensesByProject',
            'expensesByUser',
            'monthlyExpenses',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Profit & Loss report
     */
    public function profitLoss(Request $request)
    {
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Access denied.');
        }

        $dateRange = $this->getDateRange($request);
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];

        // Revenue
        $revenue = Invoice::forCompany()
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->sum('total_amount');

        // Direct costs (project-related expenses)
        $directCosts = Expense::forCompany()
            ->whereIn('status', ['approved', 'reimbursed'])
            ->whereNotNull('project_id')
            ->where('is_billable', false)
            ->whereBetween(DB::raw('date(COALESCE(reimbursed_at, approved_at, expense_date))'), [$startDate, $endDate])
            ->sum(DB::raw('amount + COALESCE(mileage * mileage_rate, 0)'));

        // Operating expenses (non-project expenses)
        $operatingExpenses = Expense::forCompany()
            ->whereIn('status', ['approved', 'reimbursed'])
            ->whereNull('project_id')
            ->whereBetween(DB::raw('date(COALESCE(reimbursed_at, approved_at, expense_date))'), [$startDate, $endDate])
            ->sum(DB::raw('amount + COALESCE(mileage * mileage_rate, 0)'));

        // Calculate margins
        $grossProfit = $revenue - $directCosts;
        $netProfit = $grossProfit - $operatingExpenses;
        $grossMargin = $revenue > 0 ? ($grossProfit / $revenue) * 100 : 0;
        $netMargin = $revenue > 0 ? ($netProfit / $revenue) * 100 : 0;

        // Monthly P&L trend
        $monthlyPL = [];
        $months = collect();
        $current = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        while ($current->lte($end)) {
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();

            $monthRevenue = Invoice::forCompany()
                ->where('status', 'paid')
                ->whereBetween('paid_at', [$monthStart, $monthEnd])
                ->sum('total_amount');

            $monthExpenses = Expense::forCompany()
                ->whereIn('status', ['approved', 'reimbursed'])
                ->whereBetween(DB::raw('date(COALESCE(reimbursed_at, approved_at, expense_date))'), [$monthStart, $monthEnd])
                ->sum(DB::raw('amount + COALESCE(mileage * mileage_rate, 0)'));

            $monthlyPL[] = [
                'month' => $current->format('Y-m'),
                'month_name' => $current->format('M Y'),
                'revenue' => $monthRevenue,
                'expenses' => $monthExpenses,
                'profit' => $monthRevenue - $monthExpenses,
                'margin' => $monthRevenue > 0 ? (($monthRevenue - $monthExpenses) / $monthRevenue) * 100 : 0,
            ];

            $current->addMonth();
        }

        return view('financial-reports.profit-loss', compact(
            'revenue',
            'directCosts',
            'operatingExpenses',
            'grossProfit',
            'netProfit',
            'grossMargin',
            'netMargin',
            'monthlyPL',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Cash flow report
     */
    public function cashFlow(Request $request)
    {
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Access denied.');
        }

        $dateRange = $this->getDateRange($request);
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];

        // Outstanding invoices (money owed to us)
        $outstandingInvoices = Invoice::forCompany()
            ->whereIn('status', ['sent', 'overdue'])
            ->sum('total_amount');

        // Pending expenses (money we owe)
        $pendingExpenses = Expense::forCompany()
            ->whereIn('status', ['approved'])
            ->where('is_reimbursable', true)
            ->sum(DB::raw('amount + COALESCE(mileage * mileage_rate, 0)'));

        // Monthly cash flow (SQLite compatible)
        $driver = DB::connection()->getDriverName();
        $monthExprPaid = $driver === 'sqlite' ? 'strftime("%Y-%m", paid_at)' : 'DATE_FORMAT(paid_at, "%Y-%m")';
        $monthExprReimb = $driver === 'sqlite' ? 'strftime("%Y-%m", reimbursed_at)' : 'DATE_FORMAT(reimbursed_at, "%Y-%m")';

        $cashInRows = Invoice::forCompany()
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->select(DB::raw($monthExprPaid . ' as month'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('month')
            ->get()->keyBy('month');

        $cashOutRows = Expense::forCompany()
            ->where('status', 'reimbursed')
            ->whereBetween('reimbursed_at', [$startDate, $endDate])
            ->select(DB::raw($monthExprReimb . ' as month'), DB::raw('SUM(amount + COALESCE(mileage * mileage_rate, 0)) as total'))
            ->groupBy('month')
            ->get()->keyBy('month');

        $monthlyCashFlow = [];
        $cursor = Carbon::parse($startDate)->startOfMonth();
        $endCursor = Carbon::parse($endDate)->startOfMonth();
        while ($cursor->lte($endCursor)) {
            $key = $cursor->format('Y-m');
            $in = (float) ($cashInRows[$key]->total ?? 0);
            $out = (float) ($cashOutRows[$key]->total ?? 0);
            $monthlyCashFlow[] = [
                'month' => $key,
                'month_name' => $cursor->format('M Y'),
                'cash_in' => $in,
                'cash_out' => $out,
                'net_cash_flow' => $in - $out,
            ];
            $cursor->addMonth();
        }

        return view('financial-reports.cash-flow', compact(
            'outstandingInvoices',
            'pendingExpenses',
            'monthlyCashFlow',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Get date range from request or default
     */
    private function getDateRange(Request $request): array
    {
        $startDate = $request->get('start_date', now()->startOfYear()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        return [
            'start' => Carbon::parse($startDate),
            'end' => Carbon::parse($endDate),
        ];
    }

    /**
     * Get financial summary
     */
    private function getFinancialSummary($startDate, $endDate): array
    {
        // For SuperAdmin (company_id = null), default to company 1 for now
        // In a real implementation, SuperAdmin should be able to select which company to view
        $companyId = auth()->user()->company_id ?? 1;
        
        // Revenue: paid invoices
        $paidRevenue = Invoice::where('company_id', $companyId)
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
        $toolHireCosts = ToolHireRequest::where('company_id', $companyId)
            ->whereIn('status', ['completed', 'returned', 'in_use', 'delivered'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('hire_end_date', [$startDate, $endDate])
                      ->orWhereBetween('actual_return_date', [$startDate, $endDate]);
            })
            ->sum(DB::raw('COALESCE(actual_total_cost, estimated_total_cost, 0)'));
        
        // Total Expenses = Regular Expenses + Operative Wages + Tool Hire
        $totalExpenses = $regularExpenses + $operativeWages + $toolHireCosts;

        return [
            'total_revenue' => $paidRevenue,
            'total_expenses' => $totalExpenses,
            'regular_expenses' => $regularExpenses,
            'operative_wages' => $operativeWages,
            'tool_hire_costs' => $toolHireCosts,
            'outstanding_invoices' => Invoice::where('company_id', $companyId)
                ->whereIn('status', ['sent', 'overdue'])
                ->sum('total_amount'),
            'pending_estimates' => Estimate::where('company_id', $companyId)
                ->where('status', 'sent')
                ->sum('total_amount'),
        ];
    }

    /**
     * Get monthly trends for the last 12 months
     */
    private function getMonthlyTrends(): array
    {
        $trends = [];
        $startDate = now()->subMonths(11)->startOfMonth();
        
        for ($i = 0; $i < 12; $i++) {
            $monthStart = $startDate->copy()->addMonths($i)->startOfMonth();
            $monthEnd = $monthStart->copy()->endOfMonth();
            
            $revenue = Invoice::forCompany()
                ->where('status', 'paid')
                ->whereBetween('paid_at', [$monthStart, $monthEnd])
                ->sum('total_amount');
                
            $expenses = Expense::forCompany()
                ->whereIn('status', ['approved', 'reimbursed'])
                ->whereBetween('expense_date', [$monthStart, $monthEnd])
                ->sum(DB::raw('amount + COALESCE(mileage * mileage_rate, 0)'));
            
            $trends[] = [
                'month' => $monthStart->format('M Y'),
                'revenue' => $revenue,
                'expenses' => $expenses,
                'profit' => $revenue - $expenses,
            ];
        }
        
        return $trends;
    }

    /**
     * Get top clients by revenue
     */
    private function getTopClients($startDate, $endDate)
    {
        return Invoice::forCompany()
            ->with('client')
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->select('client_id', DB::raw('SUM(total_amount) as total_revenue'))
            ->groupBy('client_id')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();
    }

    /**
     * Get project profit margins
     */
    private function getProjectProfitMargins($startDate, $endDate)
    {
        $projects = Project::forCompany()
            ->with(['client'])
            ->get();

        $projectProfits = [];
        
        foreach ($projects as $project) {
            $revenue = Invoice::forCompany()
                ->where('project_id', $project->id)
                ->where('status', 'paid')
                ->whereBetween('paid_at', [$startDate, $endDate])
                ->sum('total_amount');
                
            $expenses = Expense::forCompany()
                ->where('project_id', $project->id)
                ->whereIn('status', ['approved', 'reimbursed'])
                ->whereBetween(DB::raw('date(COALESCE(reimbursed_at, approved_at, expense_date))'), [$startDate, $endDate])
                ->sum(DB::raw('amount + COALESCE(mileage * mileage_rate, 0)'));
            
            if ($revenue > 0 || $expenses > 0) {
                $profit = $revenue - $expenses;
                $margin = $revenue > 0 ? ($profit / $revenue) * 100 : -100;
                
                $projectProfits[] = [
                    'project' => $project,
                    'revenue' => $revenue,
                    'expenses' => $expenses,
                    'profit' => $profit,
                    'margin' => $margin,
                ];
            }
        }
        
        // Sort by profit descending
        usort($projectProfits, function($a, $b) {
            return $b['profit'] <=> $a['profit'];
        });
        
        return array_slice($projectProfits, 0, 10);
    }

    /**
     * Build per-site revenue/expense/profit overview
     */
    private function getSiteOverview($startDate, $endDate): array
    {
        $companyId = auth()->user()->company_id ?? 1;

        // Revenue by site
        $revenueBySite = Invoice::query()
            ->where('invoices.company_id', $companyId)
            ->where('invoices.status', 'paid')
            ->whereBetween('invoices.paid_at', [$startDate, $endDate])
            ->join('projects', 'invoices.project_id', '=', 'projects.id')
            ->join('sites', 'projects.site_id', '=', 'sites.id')
            ->select('sites.id as site_id', 'sites.name as site_name', DB::raw('SUM(invoices.total_amount) as revenue'))
            ->groupBy('sites.id', 'sites.name')
            ->get()
            ->keyBy('site_id');

        // Expenses by site
        $expensesBySite = Expense::query()
            ->where('expenses.company_id', $companyId)
            ->whereBetween(DB::raw('COALESCE(expenses.reimbursed_at, expenses.approved_at, expenses.expense_date)'), [$startDate, $endDate])
            ->join('projects', 'expenses.project_id', '=', 'projects.id')
            ->join('sites', 'projects.site_id', '=', 'sites.id')
            ->select('sites.id as site_id', DB::raw('SUM(expenses.amount + COALESCE(expenses.mileage * expenses.mileage_rate, 0)) as expenses'))
            ->groupBy('sites.id')
            ->get()
            ->keyBy('site_id');

        // Project counts and budgets by site
        $projectsBySite = Project::query()
            ->where('projects.company_id', $companyId)
            ->whereNotNull('site_id')
            ->select('site_id', DB::raw('COUNT(*) as projects_count'))
            ->groupBy('site_id')
            ->get()
            ->keyBy('site_id');

        $budgetsBySite = Project::query()
            ->where('projects.company_id', $companyId)
            ->whereNotNull('site_id')
            ->select('site_id', DB::raw('SUM(COALESCE(budget,0)) as total_budget'))
            ->groupBy('site_id')
            ->get()
            ->keyBy('site_id');

        // Collect sites in company
        $sites = Site::query()
            ->where('sites.company_id', $companyId)
            ->select('id', 'name')
            ->get();

        $overview = [];
        $totals = [
            'revenue' => 0,
            'expenses' => 0,
            'profit' => 0,
        ];

        foreach ($sites as $site) {
            $rev = (float) ($revenueBySite[$site->id]->revenue ?? 0);
            $estimatedBudget = (float) ($budgetsBySite[$site->id]->total_budget ?? 0);
            $effectiveRevenue = $rev > 0 ? $rev : $estimatedBudget; // fallback to budgets when no paid invoices
            $exp = (float) ($expensesBySite[$site->id]->expenses ?? 0);
            $profit = $effectiveRevenue - $exp;
            $margin = $effectiveRevenue > 0 ? ($profit / $effectiveRevenue) * 100 : 0;
            $projectsCount = (int) ($projectsBySite[$site->id]->projects_count ?? 0);

            $overview[] = [
                'site_id' => $site->id,
                'site_name' => $site->name,
                'projects_count' => $projectsCount,
                'revenue' => $effectiveRevenue,
                'expenses' => $exp,
                'profit' => $profit,
                'margin' => $margin,
            ];

            $totals['revenue'] += $effectiveRevenue;
            $totals['expenses'] += $exp;
            $totals['profit'] += $profit;
        }

        // Sort by profit desc
        usort($overview, function ($a, $b) {
            return $b['profit'] <=> $a['profit'];
        });

        return [$overview, $totals];
    }

    /**
     * Payments received from clients (paid invoices)
     */
    private function getPaymentsFromClients($startDate, $endDate)
    {
        return Invoice::forCompany()
            ->with('client')
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->select(
                'client_id',
                DB::raw('COUNT(id) as paid_invoices'),
                DB::raw('SUM(total_amount) as total_received'),
                DB::raw('MAX(paid_at) as last_payment_at')
            )
            ->groupBy('client_id')
            ->orderByDesc('total_received')
            ->get();
    }

    /**
     * Payments made to team (employees/operatives/subcontractors) via reimbursed expenses
     */
    private function getPaymentsToTeam($startDate, $endDate): array
    {
        // Totals by user role
        $byRole = Expense::query()
            ->where('expenses.company_id', auth()->user()->company_id)
            ->where('status', 'reimbursed')
            ->whereBetween('reimbursed_at', [$startDate, $endDate])
            ->join('users', 'expenses.user_id', '=', 'users.id')
            ->select(
                'users.role',
                DB::raw('SUM(amount + COALESCE(mileage * mileage_rate, 0)) as total_paid')
            )
            ->groupBy('users.role')
            ->get();

        // Top reimbursed users
        $topUsers = Expense::query()
            ->where('expenses.company_id', auth()->user()->company_id)
            ->where('status', 'reimbursed')
            ->whereBetween('reimbursed_at', [$startDate, $endDate])
            ->join('users', 'expenses.user_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.name',
                'users.role',
                DB::raw('SUM(amount + COALESCE(mileage * mileage_rate, 0)) as total_paid')
            )
            ->groupBy('users.id', 'users.name', 'users.role')
            ->orderByDesc('total_paid')
            ->limit(10)
            ->get();

        return [$byRole, $topUsers];
    }
} 