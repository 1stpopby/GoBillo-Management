<?php

namespace App\Http\Controllers;

use App\Models\CisPayment;
use App\Models\CisReturn;
use App\Models\Employee;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class CisController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('company.access');
    }

    /**
     * Display CIS dashboard
     */
    public function index(Request $request)
    {
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Access denied.');
        }

        $companyId = auth()->user()->company_id;
        
        // Get current period
        $currentPeriod = CisReturn::getCurrentPeriod();
        $currentYear = $currentPeriod['tax_year'];
        
        // Handle CSV export
        if ($request->get('export') === 'csv') {
            return $this->exportFilteredData($request);
        }
        
        // Summary statistics (always show full stats in header)
        $stats = [
            'total_payments_ytd' => CisPayment::forCompany()
                ->whereYear('payment_date', $currentYear)
                ->sum('gross_amount'),
            'total_deductions_ytd' => CisPayment::forCompany()
                ->whereYear('payment_date', $currentYear)
                ->sum('cis_deduction'),
            'registered_subcontractors' => Employee::forCompany()
                ->where('cis_status', 'verified')
                ->count(),
            'pending_payments' => CisPayment::forCompany()
                ->where('status', CisPayment::STATUS_DRAFT)
                ->count(),
            'overdue_returns' => CisReturn::forCompany()
                ->overdue()
                ->count(),
        ];

        // Build filtered payments query
        $paymentsQuery = CisPayment::forCompany()
            ->with(['employee', 'user', 'project']);

        // Apply filters
        if ($request->filled('date_from')) {
            $paymentsQuery->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $paymentsQuery->whereDate('payment_date', '<=', $request->date_to);
        }

        if ($request->filled('operative_id')) {
            $paymentsQuery->where('employee_id', $request->operative_id);
        }

        if ($request->filled('project_id')) {
            $paymentsQuery->where('project_id', $request->project_id);
        }

        if ($request->filled('cis_rate')) {
            $paymentsQuery->where('cis_rate', $request->cis_rate);
        }

        if ($request->filled('status')) {
            $paymentsQuery->where('status', $request->status);
        }

        if ($request->filled('amount_min')) {
            $paymentsQuery->where('gross_amount', '>=', $request->amount_min);
        }

        if ($request->filled('amount_max')) {
            $paymentsQuery->where('gross_amount', '<=', $request->amount_max);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $paymentsQuery->whereHas('employee', function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('cis_number', 'LIKE', "%{$search}%");
            });
        }

        // Get filtered payments
        $recentPayments = $paymentsQuery->latest('payment_date')->get();

        // Apply sorting to the operative summary
        $sortBy = $request->get('sort_by', 'total_deductions_desc');
        
        // Get filter dropdown data
        $allOperatives = Employee::forCompany()
            ->whereHas('cisPayments')
            ->orderBy('first_name')
            ->get();
            
        $allProjects = Project::forCompany()
            ->whereHas('cisPayments')
            ->orderBy('name')
            ->get();

        // Upcoming returns (not filtered)
        $upcomingReturns = CisReturn::forCompany()
            ->where('status', CisReturn::STATUS_DRAFT)
            ->where('due_date', '>=', now())
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        return view('cis.index', compact(
            'stats', 
            'recentPayments', 
            'upcomingReturns', 
            'currentYear',
            'allOperatives',
            'allProjects'
        ));
    }

    /**
     * Export filtered CIS data as CSV
     */
    private function exportFilteredData(Request $request)
    {
        $query = CisPayment::forCompany()
            ->with(['employee', 'project']);

        // Apply same filters as index method
        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }
        if ($request->filled('operative_id')) {
            $query->where('employee_id', $request->operative_id);
        }
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }
        if ($request->filled('cis_rate')) {
            $query->where('cis_rate', $request->cis_rate);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('amount_min')) {
            $query->where('gross_amount', '>=', $request->amount_min);
        }
        if ($request->filled('amount_max')) {
            $query->where('gross_amount', '<=', $request->amount_max);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee', function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('cis_number', 'LIKE', "%{$search}%");
            });
        }

        $payments = $query->orderBy('payment_date', 'desc')->get();

        // Generate CSV content
        $csvContent = "Date,Operative,Project,Gross Amount,CIS Rate,CIS Deduction,Materials,Net Payment,Status\n";
        
        foreach ($payments as $payment) {
            $csvContent .= sprintf(
                "%s,\"%s\",\"%s\",%s,%s%%,%s,%s,%s,\"%s\"\n",
                $payment->payment_date->format('Y-m-d'),
                $payment->employee->full_name ?? 'Unknown',
                $payment->project->name ?? 'No Project',
                number_format($payment->gross_amount, 2),
                number_format($payment->cis_rate, 1),
                number_format($payment->cis_deduction, 2),
                number_format($payment->materials_cost, 2),
                number_format($payment->net_payment, 2),
                ucfirst($payment->status)
            );
        }

        $filename = 'cis-payments-' . now()->format('Y-m-d') . '.csv';
        
        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    /**
     * Display CIS payments
     */
    public function payments(Request $request)
    {
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Access denied.');
        }

        $query = CisPayment::forCompany()
            ->with(['employee', 'project', 'verifiedBy']);

        // Apply filters
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        $payments = $query->latest('payment_date')->paginate(15);

        // Filter options
        $employees = Employee::forCompany()
            ->whereNotNull('cis_number')
            ->orderBy('first_name')
            ->get();
        
        $projects = Project::forCompany()
            ->orderBy('name')
            ->get();

        return view('cis.payments.index', compact('payments', 'employees', 'projects'));
    }

    /**
     * Show form for creating new CIS payment
     */
    public function createPayment()
    {
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Access denied.');
        }

        $employees = Employee::forCompany()
            ->whereNotNull('cis_number')
            ->orderBy('first_name')
            ->get();
        
        $projects = Project::forCompany()
            ->orderBy('name')
            ->get();

        return view('cis.payments.create', compact('employees', 'projects'));
    }

    /**
     * Store new CIS payment
     */
    public function storePayment(Request $request)
    {
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Access denied.');
        }

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'project_id' => 'nullable|exists:projects,id',
            'payment_reference' => 'nullable|string|max:255',
            'payment_date' => 'required|date',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after:period_start',
            'gross_amount' => 'required|numeric|min:0',
            'materials_cost' => 'nullable|numeric|min:0',
            'other_deductions' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'deduction_notes' => 'nullable|string',
        ]);

        $employee = Employee::forCompany()->findOrFail($request->employee_id);
        $cisRate = CisPayment::getCisRateForEmployee($employee);

        $payment = new CisPayment($request->validated());
        $payment->company_id = auth()->user()->company_id;
        $payment->materials_cost = $request->materials_cost ?? 0;
        $payment->other_deductions = $request->other_deductions ?? 0;
        $payment->cis_rate = $cisRate;
        
        // Calculate deductions
        $payment->calculateDeduction();
        $payment->save();

        return redirect()
            ->route('cis.payments')
            ->with('success', 'CIS payment recorded successfully.');
    }

    /**
     * Show CIS payment details
     */
    public function showPayment(CisPayment $payment)
    {
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Access denied.');
        }

        $payment->load(['employee', 'project', 'verifiedBy', 'cisReturn']);
        
        return view('cis.payments.show', compact('payment'));
    }

    /**
     * Verify CIS payment
     */
    public function verifyPayment(CisPayment $payment)
    {
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Access denied.');
        }

        $payment->update([
            'status' => CisPayment::STATUS_VERIFIED,
            'verification_status' => CisPayment::VERIFICATION_VERIFIED,
            'verified_at' => now(),
            'verified_by' => auth()->id(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Payment verified successfully.');
    }

    /**
     * Display CIS returns
     */
    public function returns(Request $request)
    {
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Access denied.');
        }

        $query = CisReturn::forCompany()
            ->with(['preparedBy', 'submittedBy']);

        // Apply filters
        if ($request->filled('tax_year')) {
            $query->where('tax_year', $request->tax_year);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $returns = $query->latest('period_start')->paginate(15);

        // Available tax years
        $taxYears = CisReturn::forCompany()
            ->distinct()
            ->pluck('tax_year')
            ->sort()
            ->values();

        return view('cis.returns.index', compact('returns', 'taxYears'));
    }

    /**
     * Create new CIS return
     */
    public function createReturn(Request $request)
    {
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Access denied.');
        }

        $request->validate([
            'tax_year' => 'required|integer|min:2020|max:2030',
            'tax_month' => 'required|integer|min:1|max:12',
        ]);

        $companyId = auth()->user()->company_id;

        // Check if return already exists
        $existing = CisReturn::forCompany()
            ->where('tax_year', $request->tax_year)
            ->where('tax_month', $request->tax_month)
            ->first();

        if ($existing) {
            return redirect()
                ->route('cis.returns')
                ->with('error', 'A return for this period already exists.');
        }

        $cisReturn = CisReturn::createForPeriod(
            $request->tax_year,
            $request->tax_month,
            $companyId
        );

        // Include pending payments
        $included = $cisReturn->includePendingPayments();

        return redirect()
            ->route('cis.returns.show', $cisReturn)
            ->with('success', "Return created successfully. {$included} payments included.");
    }

    /**
     * Show CIS return details
     */
    public function showReturn(CisReturn $cisReturn)
    {
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Access denied.');
        }

        $cisReturn->load(['preparedBy', 'submittedBy', 'cisPayments.employee']);
        
        return view('cis.returns.show', compact('cisReturn'));
    }

    /**
     * Generate CIS return report
     */
    public function generateReturnReport(CisReturn $cisReturn)
    {
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Access denied.');
        }

        $cisReturn->calculateTotals();
        $submissionData = $cisReturn->generateHmrcSubmission();

        return response()->json([
            'success' => true,
            'data' => $submissionData,
            'totals' => [
                'subcontractors' => $cisReturn->total_subcontractors,
                'payments' => $cisReturn->total_payments,
                'deductions' => $cisReturn->total_deductions,
                'materials' => $cisReturn->total_materials,
            ]
        ]);
    }

    /**
     * Submit CIS return
     */
    public function submitReturn(CisReturn $cisReturn)
    {
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Access denied.');
        }

        if (!$cisReturn->canBeSubmitted()) {
            return redirect()
                ->back()
                ->with('error', 'Return cannot be submitted in its current state.');
        }

        // In a real implementation, this would submit to HMRC API
        $cisReturn->update([
            'status' => CisReturn::STATUS_SUBMITTED,
            'submitted_at' => now(),
            'submitted_by' => auth()->id(),
            'hmrc_reference' => 'DEMO-' . now()->format('YmdHis'),
            'is_late' => $cisReturn->due_date->isPast(),
        ]);

        return redirect()
            ->route('cis.returns.show', $cisReturn)
            ->with('success', 'Return submitted successfully. (Demo mode - not actually submitted to HMRC)');
    }

    /**
     * Get CIS summary for employee
     */
    public function getEmployeeSummary(Employee $employee, Request $request)
    {
        $year = $request->get('year', now()->year);
        
        $payments = $employee->cisPayments()
            ->whereYear('payment_date', $year)
            ->get();

        $summary = [
            'total_payments' => $payments->sum('gross_amount'),
            'total_deductions' => $payments->sum('cis_deduction'),
            'net_payments' => $payments->sum('net_payment'),
            'payment_count' => $payments->count(),
            'average_rate' => $payments->avg('cis_rate'),
        ];

        return response()->json($summary);
    }

    /**
     * Show operative-specific CIS payments and financial data
     */
    public function operativePayments(Employee $employee, Request $request)
    {
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Access denied.');
        }

        // Ensure employee belongs to the company
        if ($employee->company_id !== auth()->user()->company_id) {
            abort(404, 'Employee not found.');
        }

        $year = $request->get('year', now()->year);
        $period = $request->get('period', 'ytd'); // ytd, quarterly, monthly

        // Get all CIS payments for this employee
        $query = $employee->cisPayments()
            ->with(['project', 'verifiedBy', 'cisReturn']);

        // Apply period filter
        switch ($period) {
            case 'monthly':
                $query->whereMonth('payment_date', now()->month)
                      ->whereYear('payment_date', now()->year);
                break;
            case 'quarterly':
                $currentQuarter = ceil(now()->month / 3);
                $quarterStart = (($currentQuarter - 1) * 3) + 1;
                $quarterEnd = $currentQuarter * 3;
                $query->whereMonth('payment_date', '>=', $quarterStart)
                      ->whereMonth('payment_date', '<=', $quarterEnd)
                      ->whereYear('payment_date', now()->year);
                break;
            case 'ytd':
            default:
                $query->whereYear('payment_date', $year);
                break;
        }

        $payments = $query->latest('payment_date')->get();

        // Calculate comprehensive statistics
        $stats = [
            'total_gross' => $payments->sum('gross_amount'),
            'total_deductions' => $payments->sum('cis_deduction'),
            'total_net' => $payments->sum('net_payment'),
            'total_materials' => $payments->sum('materials_cost'),
            'payment_count' => $payments->count(),
            'average_rate' => $payments->avg('cis_rate') ?? 0,
            'highest_payment' => $payments->max('gross_amount') ?? 0,
            'average_payment' => $payments->avg('gross_amount') ?? 0,
            'last_payment_date' => $payments->first()?->payment_date,
            'first_payment_date' => $payments->sortBy('payment_date')->first()?->payment_date,
        ];

        // Monthly breakdown for charts
        $monthlyBreakdown = $payments->groupBy(function($payment) {
            return $payment->payment_date->format('Y-m');
        })->map(function($monthPayments) {
            return [
                'gross' => $monthPayments->sum('gross_amount'),
                'deductions' => $monthPayments->sum('cis_deduction'),
                'net' => $monthPayments->sum('net_payment'),
                'count' => $monthPayments->count(),
            ];
        })->sortKeys();

        // Project breakdown
        $projectBreakdown = $payments->groupBy('project_id')->map(function($projectPayments) {
            $project = $projectPayments->first()->project;
            return [
                'project' => $project,
                'gross' => $projectPayments->sum('gross_amount'),
                'deductions' => $projectPayments->sum('cis_deduction'),
                'net' => $projectPayments->sum('net_payment'),
                'count' => $projectPayments->count(),
            ];
        })->sortByDesc('gross');

        // CIS rate analysis
        $rateAnalysis = $payments->groupBy('cis_rate')->map(function($ratePayments, $rate) {
            return [
                'rate' => $rate,
                'count' => $ratePayments->count(),
                'total_gross' => $ratePayments->sum('gross_amount'),
                'total_deductions' => $ratePayments->sum('cis_deduction'),
            ];
        })->sortBy('rate');

        return view('cis.operative-payments', compact(
            'employee', 
            'payments', 
            'stats', 
            'monthlyBreakdown', 
            'projectBreakdown', 
            'rateAnalysis', 
            'year', 
            'period'
        ));
    }

    /**
     * Show employee-specific CIS payments and financial data (for managers/employees)
     */
    public function employeePayments(User $user, Request $request)
    {
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Access denied.');
        }

        // Ensure user belongs to the company
        if ($user->company_id !== auth()->user()->company_id) {
            abort(404, 'Employee not found.');
        }

        $year = $request->get('year', now()->year);
        $period = $request->get('period', 'ytd'); // ytd, quarterly, monthly

        // Get all CIS payments for this user
        $query = $user->cisPayments()
            ->with(['project', 'verifiedBy', 'cisReturn']);

        // Apply period filter
        switch ($period) {
            case 'monthly':
                $query->whereMonth('payment_date', now()->month)
                      ->whereYear('payment_date', now()->year);
                break;
            case 'quarterly':
                $currentQuarter = ceil(now()->month / 3);
                $quarterStart = (($currentQuarter - 1) * 3) + 1;
                $quarterEnd = $currentQuarter * 3;
                $query->whereMonth('payment_date', '>=', $quarterStart)
                      ->whereMonth('payment_date', '<=', $quarterEnd)
                      ->whereYear('payment_date', now()->year);
                break;
            case 'ytd':
            default:
                $query->whereYear('payment_date', $year);
                break;
        }

        $payments = $query->latest('payment_date')->get();

        // Calculate comprehensive statistics
        $stats = [
            'total_gross' => $payments->sum('gross_amount'),
            'total_deductions' => $payments->sum('cis_deduction'),
            'total_net' => $payments->sum('net_payment'),
            'total_materials' => $payments->sum('materials_cost'),
            'payment_count' => $payments->count(),
            'average_rate' => $payments->avg('cis_rate') ?? 0,
            'highest_payment' => $payments->max('gross_amount') ?? 0,
            'average_payment' => $payments->avg('gross_amount') ?? 0,
            'last_payment_date' => $payments->first()?->payment_date,
            'first_payment_date' => $payments->sortBy('payment_date')->first()?->payment_date,
            'self_employed_count' => $payments->where('employment_status', CisPayment::EMPLOYMENT_SELF_EMPLOYED)->count(),
            'employed_count' => $payments->where('employment_status', CisPayment::EMPLOYMENT_EMPLOYED)->count(),
        ];

        // Monthly breakdown for charts
        $monthlyBreakdown = $payments->groupBy(function($payment) {
            return $payment->payment_date->format('Y-m');
        })->map(function($monthPayments) {
            return [
                'gross' => $monthPayments->sum('gross_amount'),
                'deductions' => $monthPayments->sum('cis_deduction'),
                'net' => $monthPayments->sum('net_payment'),
                'count' => $monthPayments->count(),
            ];
        })->sortKeys();

        // Project breakdown
        $projectBreakdown = $payments->groupBy('project_id')->map(function($projectPayments) {
            $project = $projectPayments->first()->project;
            return [
                'project' => $project,
                'gross' => $projectPayments->sum('gross_amount'),
                'deductions' => $projectPayments->sum('cis_deduction'),
                'net' => $projectPayments->sum('net_payment'),
                'count' => $projectPayments->count(),
            ];
        })->sortByDesc('gross');

        // CIS rate analysis
        $rateAnalysis = $payments->groupBy('cis_rate')->map(function($ratePayments, $rate) {
            return [
                'rate' => $rate,
                'count' => $ratePayments->count(),
                'total_gross' => $ratePayments->sum('gross_amount'),
                'total_deductions' => $ratePayments->sum('cis_deduction'),
            ];
        })->sortBy('rate');

        // Employment status breakdown
        $employmentBreakdown = $payments->groupBy('employment_status')->map(function($statusPayments, $status) {
            return [
                'status' => $status,
                'count' => $statusPayments->count(),
                'total_gross' => $statusPayments->sum('gross_amount'),
                'total_deductions' => $statusPayments->sum('cis_deduction'),
            ];
        });

        return view('cis.employee-payments', compact(
            'user', 
            'payments', 
            'stats', 
            'monthlyBreakdown', 
            'projectBreakdown', 
            'rateAnalysis', 
            'employmentBreakdown',
            'year', 
            'period'
        ));
    }

    /**
     * Generate CIS Statement for operative
     */
    public function generateCisStatement(Employee $employee, Request $request)
    {
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Access denied.');
        }

        $period = $request->get('period', 'current_month');
        $startDate = null;
        $endDate = null;

        // Determine date range based on period
        switch ($period) {
            case 'current_month':
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                $periodLabel = now()->format('F Y');
                break;
            case 'last_month':
                $startDate = now()->subMonth()->startOfMonth();
                $endDate = now()->subMonth()->endOfMonth();
                $periodLabel = now()->subMonth()->format('F Y');
                break;
            case 'ytd':
                // UK tax year starts April 6th
                $currentYear = now()->month >= 4 ? now()->year : now()->year - 1;
                $startDate = Carbon::create($currentYear, 4, 6);
                $endDate = now();
                $periodLabel = "Year to Date ({$currentYear}/" . ($currentYear + 1) . ")";
                break;
            case 'custom':
                $startDate = Carbon::parse($request->get('start_date'));
                $endDate = Carbon::parse($request->get('end_date'));
                $periodLabel = $startDate->format('M j, Y') . ' - ' . $endDate->format('M j, Y');
                break;
        }

        // Get payments for the period
        $payments = CisPayment::forCompany()
            ->where('employee_id', $employee->id)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->with(['project'])
            ->orderBy('payment_date')
            ->get();

        if ($payments->isEmpty()) {
            return response()->json(['error' => 'No payments found for the selected period.'], 404);
        }

        // Calculate summary statistics
        $summary = [
            'total_gross' => $payments->sum('gross_amount'),
            'total_deductions' => $payments->sum('cis_deduction'),
            'total_materials' => $payments->sum('materials_cost'),
            'total_net' => $payments->sum('net_payment'),
            'payment_count' => $payments->count(),
            'period_start' => $startDate,
            'period_end' => $endDate,
            'period_label' => $periodLabel,
            'average_rate' => $payments->avg('cis_rate'),
        ];

        // Get company information
        $company = auth()->user()->company;

        // Generate PDF using HTML template
        return $this->generateCisStatementPdf($employee, $payments, $summary, $company);
    }

    /**
     * Generate CIS Statement for employee/manager
     */
    public function generateEmployeeCisStatement(User $user, Request $request)
    {
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Access denied.');
        }

        // Ensure user belongs to the company
        if ($user->company_id !== auth()->user()->company_id) {
            abort(404, 'Employee not found.');
        }

        $period = $request->get('period', 'current_month');
        
        // Determine date range based on period
        switch ($period) {
            case 'current_month':
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                break;
            case 'last_month':
                $startDate = now()->subMonth()->startOfMonth();
                $endDate = now()->subMonth()->endOfMonth();
                break;
            case 'ytd':
                $startDate = now()->startOfYear();
                $endDate = now();
                break;
            case 'custom':
                $startDate = Carbon::parse($request->get('start_date'));
                $endDate = Carbon::parse($request->get('end_date'));
                break;
            default:
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
        }

        // Get CIS payments for the period
        $payments = $user->cisPayments()
            ->with(['project', 'verifiedBy'])
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->orderBy('payment_date')
            ->get();

        // Calculate summary
        $summary = [
            'period_start' => $startDate,
            'period_end' => $endDate,
            'total_gross' => $payments->sum('gross_amount'),
            'total_deductions' => $payments->sum('cis_deduction'),
            'total_net' => $payments->sum('net_payment'),
            'total_materials' => $payments->sum('materials_cost'),
            'payment_count' => $payments->count(),
            'average_rate' => $payments->avg('cis_rate') ?? 0,
            'self_employed_payments' => $payments->where('employment_status', CisPayment::EMPLOYMENT_SELF_EMPLOYED)->count(),
            'employed_payments' => $payments->where('employment_status', CisPayment::EMPLOYMENT_EMPLOYED)->count(),
        ];

        $company = auth()->user()->company;

        return $this->generateEmployeeCisStatementPdf($user, $payments, $summary, $company);
    }

    /**
     * Generate CIS Statement PDF
     */
    private function generateCisStatementPdf($employee, $payments, $summary, $company)
    {
        // Create filename
        $filename = 'CIS-Statement-' . str_replace(' ', '-', $employee->full_name) . '-' . $summary['period_start']->format('Y-m') . '.pdf';

        // Generate PDF using DomPDF
        $pdf = Pdf::loadView('cis.statement-template', compact('employee', 'payments', 'summary', 'company'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'dpi' => 150,
                'defaultFont' => 'Arial',
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        // Return PDF download
        return $pdf->download($filename);
    }

    /**
     * Generate Employee CIS Statement PDF
     */
    private function generateEmployeeCisStatementPdf($user, $payments, $summary, $company)
    {
        $filename = 'CIS-Statement-' . str_replace(' ', '-', $user->name) . '-' . $summary['period_start']->format('Y-m') . '.pdf';
        
        $pdf = Pdf::loadView('cis.employee-statement-template', compact('user', 'payments', 'summary', 'company'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'dpi' => 150,
                'defaultFont' => 'Arial',
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'isRemoteEnabled' => true,
            ]);
            
        return $pdf->download($filename);
    }
}