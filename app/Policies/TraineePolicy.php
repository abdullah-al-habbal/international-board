<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CertifiedCenter;
use App\Models\Trainee;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Builder;

class TraineePolicy
{
    use HandlesAuthorization;

    public function viewAny(User|CertifiedCenter $user): bool
    {
        return $this->isAdminUser($user) || $this->isCenterUser($user);
    }

    public function view(User|CertifiedCenter $user, Trainee $trainee): bool
    {
        if ($this->isAdminUser($user)) {
            return true;
        }

        if ($this->isCenterUser($user)) {
            return $trainee->certifications()->where('certified_center_id', $user->id)->exists();
        }

        return false;
    }

    public function create(User|CertifiedCenter $user): bool
    {
        return $this->isAdminUser($user) || $this->isActiveCenterUser($user);
    }

    public function update(User|CertifiedCenter $user, Trainee $trainee): bool
    {
        if ($this->isAdminUser($user)) {
            return true;
        }

        if ($this->isCenterUser($user)) {
            return $trainee->certifications()->where('certified_center_id', $user->id)->exists();
        }

        return false;
    }

    public function delete(User|CertifiedCenter $user, Trainee $trainee): bool
    {
        // Only admin can delete trainees
        return $this->isAdminUser($user);
    }

    public function restore(User|CertifiedCenter $user, Trainee $trainee): bool
    {
        return $this->isAdminUser($user);
    }

    public function forceDelete(User|CertifiedCenter $user, Trainee $trainee): bool
    {
        return $this->isAdminUser($user);
    }

    private function isAdminUser(User|CertifiedCenter $user): bool
    {
        return $user instanceof User && method_exists($user, 'isAdmin') && $user->isAdmin();
    }

    private function isCenterUser(User|CertifiedCenter $user): bool
    {
        return $user instanceof CertifiedCenter;
    }

    private function isActiveCenterUser(User|CertifiedCenter $user): bool
    {
        return $this->isCenterUser($user) && method_exists($user, 'canPerformActions') && $user->canPerformActions();
    }
}
