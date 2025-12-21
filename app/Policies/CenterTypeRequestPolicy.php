<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CenterTypeRequest;
use App\Models\CertifiedCenter;
use App\Models\User;

class CenterTypeRequestPolicy
{
    /**
     * Determine if the user can view any models.
     */
    public function viewAny(User|CertifiedCenter $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the model.
     */
    public function view(User|CertifiedCenter $user, CenterTypeRequest $centerTypeRequest): bool
    {
        if ($user instanceof User) {
            return $user->type === 'admin';
        }

        // Center can only view own requests
        return $user->id === $centerTypeRequest->certified_center_id;
    }

    /**
     * Determine if the user can create models.
     */
    public function create(User|CertifiedCenter $user): bool
    {
        return $user instanceof CertifiedCenter;
    }

    /**
     * Determine if the user can update the model.
     */
    public function update(User|CertifiedCenter $user, CenterTypeRequest $centerTypeRequest): bool
    {
        if ($user instanceof User) {
            return $user->type === 'admin';
        }

        // Center can only update own pending requests
        return $user->id === $centerTypeRequest->certified_center_id
            && $centerTypeRequest->status->value === 'pending';
    }

    /**
     * Determine if the user can delete the model.
     */
    public function delete(User|CertifiedCenter $user, CenterTypeRequest $centerTypeRequest): bool
    {
        if ($user instanceof User) {
            return $user->type === 'admin';
        }

        // Center can only delete own pending requests
        return $user->id === $centerTypeRequest->certified_center_id
            && $centerTypeRequest->status->value === 'pending';
    }
}
