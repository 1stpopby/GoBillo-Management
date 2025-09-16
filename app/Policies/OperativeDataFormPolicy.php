<?php

namespace App\Policies;

use App\Models\OperativeDataForm;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OperativeDataFormPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Allow company admins and managers to view forms from their company
        return $user->isCompanyAdmin() || $user->canManageProjects();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OperativeDataForm $operativeDataForm): bool
    {
        // Allow users to view forms from their own company
        return $user->company_id === $operativeDataForm->company_id && 
               ($user->isCompanyAdmin() || $user->canManageProjects());
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Allow company admins and managers to create forms
        return $user->isCompanyAdmin() || $user->canManageProjects();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OperativeDataForm $operativeDataForm): bool
    {
        // Allow users to update forms from their own company
        return $user->company_id === $operativeDataForm->company_id && 
               ($user->isCompanyAdmin() || $user->canManageProjects());
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OperativeDataForm $operativeDataForm): bool
    {
        // Allow company admins to delete forms from their own company
        return $user->company_id === $operativeDataForm->company_id && $user->isCompanyAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, OperativeDataForm $operativeDataForm): bool
    {
        // Allow company admins to restore forms from their own company
        return $user->company_id === $operativeDataForm->company_id && $user->isCompanyAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, OperativeDataForm $operativeDataForm): bool
    {
        // Allow company admins to permanently delete forms from their own company
        return $user->company_id === $operativeDataForm->company_id && $user->isCompanyAdmin();
    }
}
