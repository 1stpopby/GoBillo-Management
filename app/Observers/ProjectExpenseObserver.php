<?php

namespace App\Observers;

use App\Models\ProjectExpense;
use App\Models\Expense;

class ProjectExpenseObserver
{
    /**
     * Mirror to unified expenses table after create.
     */
    public function created(ProjectExpense $pe): void
    {
        // If already mirrored from Expense->ProjectExpense, skip duplicate
        if (Expense::where('project_expense_id', $pe->id)->exists()) {
            return;
        }

        Expense::create([
            'company_id' => $pe->company_id,
            'project_id' => $pe->project_id,
            'user_id' => $pe->created_by,
            'expense_number' => Expense::generateExpenseNumber($pe->company_id),
            'status' => $pe->status === 'approved' ? Expense::STATUS_APPROVED : Expense::STATUS_SUBMITTED,
            'category' => $pe->category,
            'vendor' => $pe->vendor_name,
            'description' => $pe->title,
            'amount' => $pe->amount,
            'currency' => $pe->currency,
            'expense_date' => $pe->expense_date,
            'payment_method' => null,
            'is_billable' => false,
            'is_reimbursable' => true,
            'mileage' => null,
            'mileage_rate' => null,
            'notes' => $pe->notes,
            'receipt_path' => $pe->receipt_path,
            'submitted_at' => now(),
            'approved_at' => $pe->approved_at,
            'approved_by' => $pe->approved_by,
            'project_expense_id' => $pe->id,
        ]);
    }

    /**
     * Mirror updates to unified expenses table.
     */
    public function updated(ProjectExpense $pe): void
    {
        $expense = Expense::where('project_expense_id', $pe->id)->first();
        if (!$expense) {
            $this->created($pe);
            return;
        }
        $expense->update([
            'company_id' => $pe->company_id,
            'project_id' => $pe->project_id,
            'user_id' => $pe->created_by,
            'status' => $pe->status === 'approved' ? Expense::STATUS_APPROVED : ($pe->status === 'rejected' ? Expense::STATUS_REJECTED : Expense::STATUS_SUBMITTED),
            'category' => $pe->category,
            'vendor' => $pe->vendor_name,
            'description' => $pe->title,
            'amount' => $pe->amount,
            'currency' => $pe->currency,
            'expense_date' => $pe->expense_date,
            'notes' => $pe->notes,
            'receipt_path' => $pe->receipt_path,
            'approved_at' => $pe->approved_at,
            'approved_by' => $pe->approved_by,
        ]);
    }

    /**
     * Cleanup mirrored record.
     */
    public function deleted(ProjectExpense $pe): void
    {
        Expense::where('project_expense_id', $pe->id)->delete();
    }
}


