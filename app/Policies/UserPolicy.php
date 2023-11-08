<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Http\Request;


class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, $id): Response
    {
        return $user->type == 'admin' || $user->id == $id
        ? Response::allow()
        : throw new \Exception("Não autorizado", 403);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, $id): Response
    {
        return $user->type == 'admin' || $user->id == $id
        ? Response::allow()
        : throw new \Exception("Não autorizado", 403);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, $id): Response
    {
        return $user->type == 'admin' || $user->id == $id
        ? Response::allow()
        : throw new \Exception("Não autorizado", 403);

    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        //
    }

    public function verifyUserAuthorization(User $user, $id): Response
    {
        return $user->type == 'admin' || $user->id == $id
        ? Response::allow()
        : throw new \Exception("Não autorizado", 403);
    }


}
