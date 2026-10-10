<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CertifiedCenter;
use App\Models\Trainee;
use App\Models\Trainer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TraineePolicy
{
    use HandlesAuthorization;

    public function viewAny(User|CertifiedCenter|Trainer $user): bool
    {
        return $this->isAdminUser($user)
            || $this->isCenterUser($user)
            || $this->isTrainerUser($user);
    }

    public function view(User|CertifiedCenter|Trainer $user, Trainee $trainee): bool
    {
        if ($this->isAdminUser($user)) {
            return true;
        }

        return $trainee->owner_type === $user::class
            && (int) $trainee->owner_id === (int) $user->getKey();
    }

    public function create(User|CertifiedCenter|Trainer $user): bool
    {
        return $this->isAdminUser($user)
            || $this->isActiveCenterUser($user)
            || $this->isActiveTrainerUser($user);
    }

    public function update(User|CertifiedCenter|Trainer $user, Trainee $trainee): bool
    {
        if ($this->isAdminUser($user)) {
            return true;
        }

        return $trainee->owner_type === $user::class
            && (int) $trainee->owner_id === (int) $user->getKey();
    }

    public function delete(User|CertifiedCenter|Trainer $user, Trainee $trainee): bool
    {
        // Only admin can delete trainees
        return $this->isAdminUser($user);
    }

    public function restore(User|CertifiedCenter|Trainer $user, Trainee $trainee): bool
    {
        return $this->isAdminUser($user);
    }

    public function forceDelete(User|CertifiedCenter|Trainer $user, Trainee $trainee): bool
    {
        return $this->isAdminUser($user);
    }

    private function isAdminUser(User|CertifiedCenter|Trainer $user): bool
    {
        return $user instanceof User && method_exists($user, 'isAdmin') && $user->isAdmin();
    }

    private function isCenterUser(User|CertifiedCenter|Trainer $user): bool
    {
        return $user instanceof CertifiedCenter;
    }

    private function isTrainerUser(User|CertifiedCenter|Trainer $user): bool
    {
        return $user instanceof Trainer;
    }

    private function isActiveCenterUser(User|CertifiedCenter|Trainer $user): bool
    {
        return $this->isCenterUser($user) && method_exists($user, 'canPerformActions') && $user->canPerformActions();
    }

    private function isActiveTrainerUser(User|CertifiedCenter|Trainer $user): bool
    {
        return $this->isTrainerUser($user) && method_exists($user, 'canPerformActions') && $user->canPerformActions();
    }
}
