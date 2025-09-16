<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    /**
     * Display a listing of expenses
     */
    public function index(Request $request)
    {
        $query = Expense::forCompany()->with(['user', 'project.site', 'approvedBy']);

        // Filter by user role - regular users see only their expenses
        if (!auth()->user()->canManageProjects()) {
            $query->forUser();
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('expense_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('vendor', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('user_id') && auth()->user()->canManageProjects()) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('site_id')) {
            $siteId = $request->site_id;
            // Only show expenses tied to projects under the selected site
            $query->whereNotNull('project_id')
                  ->whereHas('project', function ($p) use ($siteId) {
                      $p->where('site_id', $siteId);
                  });
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('date_from')) {
            $query->where('expense_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('expense_date', '<=', $request->date_to);
        }

        $expenses = $query->latest()->paginate(15);

        // Get filter options
        $categories = ExpenseCategory::forCompany()->active()->orderBy('name')->get();
        $sites = \App\Models\Site::forCompany()->orderBy('name')->get();
        $projects = Project::forCompany()
            ->when($request->filled('site_id'), function ($q) use ($request) {
                $q->where('site_id', $request->site_id);
            })
            ->orderBy('name')
            ->get();
        $users = auth()->user()->canManageProjects() ? 
                 User::forCompany()->orderBy('name')->get() : 
                 collect();

        return view('expenses.index', compact('expenses', 'categories', 'projects', 'users', 'sites'));
    }

    /**
     * Show the form for creating a new expense
     */
    public function create()
    {
        $categories = ExpenseCategory::forCompany()->active()->orderBy('sort_order')->get();
        $projects = Project::forCompany()->orderBy('name')->get();

        return view('expenses.create', compact('categories', 'projects'));
    }

    /**
     * Store a newly created expense
     */
    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string|max:100',
            'description' => 'required|string|max:500',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date|before_or_equal:today',
            'site_id' => 'nullable|exists:sites,id',
            'project_id' => 'nullable|exists:projects,id',
            'vendor' => 'nullable|string|max:255',
            'payment_method' => 'nullable|string|max:100',
            'is_billable' => 'boolean',
            'is_reimbursable' => 'boolean',
            'mileage' => 'nullable|numeric|min:0',
            'mileage_rate' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'receipt' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120', // 5MB max
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }

        $expense = Expense::create([
            'company_id' => auth()->user()->company_id,
            'user_id' => auth()->id(),
            'project_id' => $request->project_id,
            // site allocation is inferred via project when present; keep for search/filter later if you add a column
            'category' => $request->category,
            'description' => $request->description,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'vendor' => $request->vendor,
            'payment_method' => $request->payment_method,
            'is_billable' => $request->boolean('is_billable'),
            'is_reimbursable' => $request->boolean('is_reimbursable'),
            'mileage' => $request->mileage,
            'mileage_rate' => $request->mileage_rate,
            'notes' => $request->notes,
            'receipt_path' => $receiptPath,
            'currency' => 'GBP',
        ]);

        return redirect()->route('expenses.index')
                        ->with('success', 'Expense created successfully.');
    }

    /**
     * Display the specified expense
     */
    public function show(Expense $expense)
    {
        $this->authorize('view', $expense);
        
        $expense->load(['user', 'project', 'approvedBy']);
        
        return view('expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified expense
     */
    public function edit(Expense $expense)
    {
        $this->authorize('update', $expense);
        
        if (!$expense->canBeEdited()) {
            return back()->with('error', 'This expense cannot be edited.');
        }

        $categories = ExpenseCategory::forCompany()->active()->orderBy('sort_order')->get();
        $projects = Project::forCompany()->orderBy('name')->get();

        return view('expenses.edit', compact('expense', 'categories', 'projects'));
    }

    /**
     * Update the specified expense
     */
    public function update(Request $request, Expense $expense)
    {
        $this->authorize('update', $expense);
        
        if (!$expense->canBeEdited()) {
            return back()->with('error', 'This expense cannot be edited.');
        }

        $request->validate([
            'category' => 'required|string|max:100',
            'description' => 'required|string|max:500',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date|before_or_equal:today',
            'project_id' => 'nullable|exists:projects,id',
            'vendor' => 'nullable|string|max:255',
            'payment_method' => 'nullable|string|max:100',
            'is_billable' => 'boolean',
            'is_reimbursable' => 'boolean',
            'mileage' => 'nullable|numeric|min:0',
            'mileage_rate' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'receipt' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $receiptPath = $expense->receipt_path;
        if ($request->hasFile('receipt')) {
            // Delete old receipt
            if ($receiptPath && Storage::disk('public')->exists($receiptPath)) {
                Storage::disk('public')->delete($receiptPath);
            }
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }

        $expense->update([
            'project_id' => $request->project_id,
            'category' => $request->category,
            'description' => $request->description,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'vendor' => $request->vendor,
            'payment_method' => $request->payment_method,
            'is_billable' => $request->boolean('is_billable'),
            'is_reimbursable' => $request->boolean('is_reimbursable'),
            'mileage' => $request->mileage,
            'mileage_rate' => $request->mileage_rate,
            'notes' => $request->notes,
            'receipt_path' => $receiptPath,
        ]);

        return redirect()->route('expenses.show', $expense)
                        ->with('success', 'Expense updated successfully.');
    }

    /**
     * Remove the specified expense
     */
    public function destroy(Expense $expense)
    {
        $this->authorize('delete', $expense);
        
        if (!$expense->canBeDeleted()) {
            return back()->with('error', 'This expense cannot be deleted.');
        }

        $expenseNumber = $expense->expense_number;
        $expense->delete();

        return redirect()->route('expenses.index')
                        ->with('success', "Expense {$expenseNumber} deleted successfully.");
    }

    /**
     * Submit expense for approval
     */
    public function submit(Expense $expense)
    {
        $this->authorize('update', $expense);
        
        if ($expense->status !== Expense::STATUS_DRAFT) {
            return back()->with('error', 'Only draft expenses can be submitted.');
        }

        $expense->submit();

        return back()->with('success', 'Expense submitted for approval.');
    }

    /**
     * Approve expense
     */
    public function approve(Expense $expense)
    {
        $this->authorize('approve', $expense);
        
        if ($expense->status !== Expense::STATUS_SUBMITTED) {
            return back()->with('error', 'Only submitted expenses can be approved.');
        }

        $expense->approve();

        return back()->with('success', 'Expense approved successfully.');
    }

    /**
     * Reject expense
     */
    public function reject(Request $request, Expense $expense)
    {
        $this->authorize('approve', $expense);
        
        if ($expense->status !== Expense::STATUS_SUBMITTED) {
            return back()->with('error', 'Only submitted expenses can be rejected.');
        }

        $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $expense->reject($request->rejection_reason);

        return back()->with('success', 'Expense rejected.');
    }

    /**
     * Mark expense as reimbursed
     */
    public function markReimbursed(Expense $expense)
    {
        $this->authorize('approve', $expense);
        
        if ($expense->status !== Expense::STATUS_APPROVED) {
            return back()->with('error', 'Only approved expenses can be marked as reimbursed.');
        }

        $expense->markAsReimbursed();

        return back()->with('success', 'Expense marked as reimbursed.');
    }

    /**
     * Download receipt
     */
    public function downloadReceipt(Expense $expense)
    {
        $this->authorize('view', $expense);
        
        if (!$expense->hasReceipt()) {
            return back()->with('error', 'No receipt available for this expense.');
        }

        return Storage::disk('public')->download($expense->receipt_path);
    }

    /**
     * Get expense reports
     */
    public function reports(Request $request)
    {
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Access denied.');
        }

        $query = Expense::forCompany()->with(['user', 'project']);

        // Date range filter
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());
        
        $query->whereBetween('expense_date', [$startDate, $endDate]);

        $expenses = $query->get();

        // Calculate totals by category
        $categoryTotals = $expenses->groupBy('category')->map(function ($categoryExpenses) {
            return [
                'count' => $categoryExpenses->count(),
                'total' => $categoryExpenses->sum('total_amount'),
                'approved' => $categoryExpenses->where('status', 'approved')->sum('total_amount'),
                'pending' => $categoryExpenses->where('status', 'submitted')->sum('total_amount'),
            ];
        });

        // Calculate totals by user
        $userTotals = $expenses->groupBy('user.name')->map(function ($userExpenses) {
            return [
                'count' => $userExpenses->count(),
                'total' => $userExpenses->sum('total_amount'),
                'approved' => $userExpenses->where('status', 'approved')->sum('total_amount'),
                'pending' => $userExpenses->where('status', 'submitted')->sum('total_amount'),
            ];
        });

        // Calculate totals by status
        $statusTotals = $expenses->groupBy('status')->map(function ($statusExpenses) {
            return [
                'count' => $statusExpenses->count(),
                'total' => $statusExpenses->sum('total_amount'),
            ];
        });

        return view('expenses.reports', compact(
            'expenses', 
            'categoryTotals', 
            'userTotals', 
            'statusTotals',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Bulk approve expenses
     */
    public function bulkApprove(Request $request)
    {
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Access denied.');
        }

        $request->validate([
            'expense_ids' => 'required|array',
            'expense_ids.*' => 'exists:expenses,id',
        ]);

        $expenses = Expense::forCompany()
                          ->whereIn('id', $request->expense_ids)
                          ->where('status', Expense::STATUS_SUBMITTED)
                          ->get();

        $approvedCount = 0;
        foreach ($expenses as $expense) {
            $expense->approve();
            $approvedCount++;
        }

        return back()->with('success', "{$approvedCount} expenses approved successfully.");
    }
} 