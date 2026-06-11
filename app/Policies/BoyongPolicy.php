<?php

namespace App\Policies;

use App\Models\Boyong;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BoyongPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->level, ['admin', 'badkom_pusat']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Boyong $boyong): bool
    {
        return in_array($user->level, ['admin', 'badkom_pusat']);
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
    public function update(User $user, Boyong $boyong): bool
    {
        return in_array($user->level, ['admin', 'badkom_pusat']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Boyong $boyong): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Boyong $boyong): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Boyong $boyong): bool
    {
        return false;
    }
}
