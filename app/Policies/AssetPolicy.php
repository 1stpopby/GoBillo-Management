<?php

namespace App\Policies;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AssetPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->is_active;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Asset $asset): bool
    {
        // SuperAdmin and Company Admin can view all assets
        if ($user->isSuperAdmin() || $user->isCompanyAdmin()) {
            return true;
        }

        // Project managers can view assets in their company
        if ($user->isProjectManager()) {
            return $asset->company_id === $user->company_id;
        }

        // Other users can view assets assigned to them or in their company
        return $asset->company_id === $user->company_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isCompanyAdmin() || $user->isProjectManager();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Asset $asset): bool
    {
        // SuperAdmin and Company Admin can update all assets
        if ($user->isSuperAdmin() || $user->isCompanyAdmin()) {
            return true;
        }

        // Project managers can update assets in their company
        if ($user->isProjectManager()) {
            return $asset->company_id === $user->company_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Asset $asset): bool
    {
        // Only SuperAdmin and Company Admin can delete assets
        return ($user->isSuperAdmin() || $user->isCompanyAdmin()) && 
               $asset->company_id === $user->company_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Asset $asset): bool
    {
        return $user->isSuperAdmin() || $user->isCompanyAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Asset $asset): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can import assets.
     */
    public function import(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isCompanyAdmin() || $user->isProjectManager();
    }

    /**
     * Determine whether the user can export assets.
     */
    public function export(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isCompanyAdmin() || $user->isProjectManager();
    }

    /**
     * Determine whether the user can manage attachments.
     */
    public function attach(User $user, Asset $asset): bool
    {
        return $this->update($user, $asset);
    }

    /**
     * Determine whether the user can assign assets.
     */
    public function assign(User $user, Asset $asset): bool
    {
        return $this->update($user, $asset);
    }
}