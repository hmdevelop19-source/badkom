<?php

namespace App\Policies;

use App\Models\Pjutd;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PjutdPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->level, ['admin', 'badkom_pusat', 'badkom_wilayah', 'pjutd']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Pjutd $pjutd): bool
    {
        if (in_array($user->level, ['admin', 'badkom_pusat'])) return true;
        if ($user->level === 'badkom_wilayah') return $user->badkom_id === $pjutd->badkom_id;
        if ($user->level === 'pjutd') return $user->pjutd_id === $pjutd->id;
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->level, ['admin', 'badkom_pusat', 'badkom_wilayah']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Pjutd $pjutd): bool
    {
        if (in_array($user->level, ['admin', 'badkom_pusat'])) return true;
        if ($user->level === 'badkom_wilayah') return $user->badkom_id === $pjutd->badkom_id;
        if ($user->level === 'pjutd') return $user->pjutd_id === $pjutd->id;

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Pjutd $pjutd): bool
    {
        if (in_array($user->level, ['admin', 'badkom_pusat'])) return true;
        if ($user->level === 'badkom_wilayah') return $user->badkom_id === $pjutd->badkom_id;

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Pjutd $pjutd): bool
    {
        return in_array($user->level, ['admin', 'badkom_pusat']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Pjutd $pjutd): bool
    {
        return in_array($user->level, ['admin', 'badkom_pusat']);
    }
}
