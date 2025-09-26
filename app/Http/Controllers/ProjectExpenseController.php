<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Project $project)
    {
        $expenses = $project->projectExpenses()
            ->with(['creator', 'approver'])
            ->latest()
            ->get();

        return response()->json($expenses);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Project $project)
    {
        return response()->json([
            'categories' => [
                'materials' => 'Materials',
                'travel' => 'Travel',
                'equipment' => 'Equipment',
                'subcontractor' => 'Subcontractor',
                'labor' => 'Labor',
                'permits' => 'Permits',
                'utilities' => 'Utilities',
                'other' => 'Other'
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'category' => 'required|in:materials,travel,equipment,subcontractor,labor,permits,utilities,other',
            'net_amount' => 'required|numeric|min:0',
            'vat_rate' => 'required|numeric|min:0|max:100',
            'currency' => 'string|max:3|default:GBP',
            'expense_date' => 'required|date',
            'vendor_name' => 'nullable|string|max:255',
            'invoice_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240' // 10MB max
        ]);

        // Calculate VAT amount and total amount
        $netAmount = $validated['net_amount'];
        $vatRate = $validated['vat_rate'];
        $vatAmount = ($netAmount * $vatRate) / 100;
        $totalAmount = $netAmount + $vatAmount;

        // Remove fields that don't exist in database
        unset($validated['net_amount'], $validated['vat_rate']);
        
        $validated['project_id'] = $project->id;
        $validated['company_id'] = auth()->user()->company_id;
        $validated['created_by'] = auth()->id();
        $validated['currency'] = $validated['currency'] ?? 'GBP';
        $validated['amount'] = $totalAmount;

        // Handle receipt upload
        if ($request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('project-receipts/' . $project->id, $filename, 'public');
            $validated['receipt_path'] = $path;
        }

        $expense = ProjectExpense::create($validated);
        $expense->load(['creator', 'approver']);

        return response()->json([
            'success' => true,
            'message' => 'Expense added successfully',
            'expense' => $expense
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project, ProjectExpense $expense)
    {
        $expense->load(['creator', 'approver']);
        return view('project-expenses.show', compact('project', 'expense'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project, ProjectExpense $expense)
    {
        $expense->load(['creator', 'approver']);
        return response()->json([
            'expense' => $expense,
            'categories' => [
                'materials' => 'Materials',
                'travel' => 'Travel',
                'equipment' => 'Equipment',
                'subcontractor' => 'Subcontractor',
                'labor' => 'Labor',
                'permits' => 'Permits',
                'utilities' => 'Utilities',
                'other' => 'Other'
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project, ProjectExpense $expense)
    {
        $validated = $request->validate([
            'category' => 'required|in:materials,travel,equipment,subcontractor,labor,permits,utilities,other',
            'net_amount' => 'required|numeric|min:0',
            'vat_rate' => 'required|numeric|min:0|max:100',
            'currency' => 'string|max:3',
            'expense_date' => 'required|date',
            'vendor_name' => 'nullable|string|max:255',
            'invoice_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240'
        ]);

        // Calculate VAT amount and total amount
        $netAmount = $validated['net_amount'];
        $vatRate = $validated['vat_rate'];
        $vatAmount = ($netAmount * $vatRate) / 100;
        $totalAmount = $netAmount + $vatAmount;

        // Remove fields that don't exist in database
        unset($validated['net_amount'], $validated['vat_rate']);
        $validated['amount'] = $totalAmount;

        // Handle receipt upload
        if ($request->hasFile('receipt')) {
            // Delete old receipt
            if ($expense->receipt_path) {
                Storage::disk('public')->delete($expense->receipt_path);
            }

            $file = $request->file('receipt');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('project-receipts/' . $project->id, $filename, 'public');
            $validated['receipt_path'] = $path;
        }

        $expense->update($validated);
        $expense->load(['creator', 'approver']);

        return response()->json([
            'success' => true,
            'message' => 'Expense updated successfully',
            'expense' => $expense
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, ProjectExpense $expense)
    {
        // Delete receipt file
        if ($expense->receipt_path) {
            Storage::disk('public')->delete($expense->receipt_path);
        }

        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => 'Expense deleted successfully'
        ]);
    }

    /**
     * Approve an expense
     */
    public function approve(Project $project, ProjectExpense $expense)
    {
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Unauthorized to approve expenses');
        }

        $expense->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now()
        ]);

        $expense->load(['creator', 'approver']);

        return response()->json([
            'success' => true,
            'message' => 'Expense approved successfully',
            'expense' => $expense
        ]);
    }

    /**
     * Reject an expense
     */
    public function reject(Project $project, ProjectExpense $expense)
    {
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Unauthorized to reject expenses');
        }

        $expense->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now()
        ]);

        $expense->load(['creator', 'approver']);

        return response()->json([
            'success' => true,
            'message' => 'Expense rejected',
            'expense' => $expense
        ]);
    }
}
