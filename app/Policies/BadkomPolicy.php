<?php

namespace App\Policies;

use App\Models\Badkom;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BadkomPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->level, ['admin', 'badkom_pusat', 'badkom_wilayah']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Badkom $badkom): bool
    {
        if (in_array($user->level, ['admin', 'badkom_pusat'])) {
            return true;
        }

        return $user->level === 'badkom_wilayah' && $user->badkom_id === $badkom->id;
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
    public function update(User $user, Badkom $badkom): bool
    {
        if (in_array($user->level, ['admin', 'badkom_pusat'])) {
            return true;
        }

        return $user->level === 'badkom_wilayah' && $user->badkom_id === $badkom->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Badkom $badkom): bool
    {
        return in_array($user->level, ['admin', 'badkom_pusat']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Badkom $badkom): bool
    {
        return in_array($user->level, ['admin', 'badkom_pusat']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Badkom $badkom): bool
    {
        return in_array($user->level, ['admin', 'badkom_pusat']);
    }
}
