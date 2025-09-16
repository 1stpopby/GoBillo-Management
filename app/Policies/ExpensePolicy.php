<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;

class ExpensePolicy
{
    public function view(User $user, Expense $expense): bool
    {
        if ($user->isSuperAdmin() || $user->isCompanyAdmin()) {
            return true;
        }
        return $expense->company_id === $user->company_id && $expense->user_id === $user->id;
    }

    public function update(User $user, Expense $expense): bool
    {
        if ($user->isSuperAdmin() || $user->isCompanyAdmin()) {
            return true;
        }
        return $expense->company_id === $user->company_id && $expense->user_id === $user->id && $expense->canBeEdited();
    }

    public function delete(User $user, Expense $expense): bool
    {
        if ($user->isSuperAdmin() || $user->isCompanyAdmin()) {
            return true;
        }
        return $expense->company_id === $user->company_id && $expense->user_id === $user->id && $expense->canBeDeleted();
    }

    public function approve(User $user, Expense $expense): bool
    {
        return $user->isSuperAdmin() || $user->isCompanyAdmin();
    }
}







