<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\EditRequest;
use App\Models\User;

class EditRequestPolicy
{
    /**
     * Determine if the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->type === 'admin';
    }

    /**
     * Determine if the user can view the model.
     */
    public function view(User $user, EditRequest $editRequest): bool
    {
        return $user->type === 'admin';
    }

    /**
     * Determine if the user can create models.
     */
    public function create(User $user): bool
    {
        return false; // Edit requests are created programmatically
    }

    /**
     * Determine if the user can update the model.
     */
    public function update(User $user, EditRequest $editRequest): bool
    {
        return $user->type === 'admin';
    }

    /**
     * Determine if the user can delete the model.
     */
    public function delete(User $user, EditRequest $editRequest): bool
    {
        return $user->type === 'admin';
    }
}
