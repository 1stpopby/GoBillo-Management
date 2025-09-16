<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function view(User $user, Invoice $invoice): bool
    {
        if ($user->isSuperAdmin() || $user->isCompanyAdmin()) {
            return true;
        }
        return $invoice->company_id === $user->company_id;
    }

    public function update(User $user, Invoice $invoice): bool
    {
        if ($user->isSuperAdmin() || $user->isCompanyAdmin()) {
            return true;
        }
        return $invoice->company_id === $user->company_id && $user->canManageProjects();
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        if ($user->isSuperAdmin() || $user->isCompanyAdmin()) {
            return true;
        }
        return $invoice->company_id === $user->company_id && $user->canManageProjects();
    }
}







