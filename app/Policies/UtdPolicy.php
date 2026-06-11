<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Utd;
use Illuminate\Auth\Access\Response;

class UtdPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Filtered by index scopes
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Utd $utd): bool
    {
        return true; // Simplified: usually can view if listed
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->level, ['admin', 'badkom_pusat']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Utd $utd): bool
    {
        return in_array($user->level, ['admin', 'badkom_pusat']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Utd $utd): bool
    {
        return in_array($user->level, ['admin', 'badkom_pusat']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Utd $utd): bool
    {
        return in_array($user->level, ['admin', 'badkom_pusat']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Utd $utd): bool
    {
        return in_array($user->level, ['admin', 'badkom_pusat']);
    }
}
