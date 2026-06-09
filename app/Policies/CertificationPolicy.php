<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Certification;
use App\Models\CertifiedCenter;
use App\Models\Trainer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

final class CertificationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User|CertifiedCenter|Trainer $user): bool
    {
        return true;
    }

    public function view(User|CertifiedCenter|Trainer $user, Certification $certification): bool
    {
        return $this->isAdminUser($user)
            || $this->isOwnerCenter($user, $certification)
            || $this->isOwnerTrainer($user, $certification);
    }

    public function create(User|CertifiedCenter|Trainer $user): bool
    {
        return $this->isAdminUser($user)
            || $this->isActiveCenterUser($user)
            || $this->isActiveTrainerUser($user);
    }

    public function update(User|CertifiedCenter|Trainer $user, Certification $certification): bool
    {
        return $this->isAdminUser($user)
            || $this->canOwnerCenterModify($user, $certification)
            || $this->canOwnerTrainerModify($user, $certification);
    }

    public function delete(User|CertifiedCenter|Trainer $user, Certification $certification): bool
    {
        return $this->isAdminUser($user)
            || $this->canOwnerCenterModify($user, $certification)
            || $this->canOwnerTrainerModify($user, $certification);
    }

    // ------ Helpers ------

    private function isAdminUser(User|CertifiedCenter|Trainer $user): bool
    {
        return $user instanceof User && $user->isAdmin();
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
        return $this->isCenterUser($user) && $user->canPerformActions();
    }

    private function isActiveTrainerUser(User|CertifiedCenter|Trainer $user): bool
    {
        return $this->isTrainerUser($user) && $user->canPerformActions();
    }

    private function isOwnerCenter(User|CertifiedCenter|Trainer $user, Certification $certification): bool
    {
        return $this->isCenterUser($user)
            && $certification->creator_type === CertifiedCenter::class
            && $certification->creator_id === $user->id;
    }

    private function isOwnerTrainer(User|CertifiedCenter|Trainer $user, Certification $certification): bool
    {
        return $this->isTrainerUser($user)
            && $certification->creator_type === Trainer::class
            && $certification->creator_id === $user->id;
    }

    private function canOwnerCenterModify(User|CertifiedCenter|Trainer $user, Certification $certification): bool
    {
        return $this->isOwnerCenter($user, $certification) && $user->canPerformActions();
    }

    private function canOwnerTrainerModify(User|CertifiedCenter|Trainer $user, Certification $certification): bool
    {
        return $this->isOwnerTrainer($user, $certification) && $user->canPerformActions();
    }
}
