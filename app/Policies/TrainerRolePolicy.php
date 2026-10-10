<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\TrainerRole;
use App\Models\User;

class TrainerRolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, TrainerRole $trainerRole): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, TrainerRole $trainerRole): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, TrainerRole $trainerRole): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, TrainerRole $trainerRole): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, TrainerRole $trainerRole): bool
    {
        return $user->isAdmin();
    }
}
