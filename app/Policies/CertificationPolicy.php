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
        return $this->isAdminUser($user) || $this->isOwnerCenter($user, $certification);
    }

    public function create(User|CertifiedCenter|Trainer $user): bool
    {
        return $this->isAdminUser($user) || $this->isActiveCenterUser($user);
    }

    public function update(User|CertifiedCenter|Trainer $user, Certification $certification): bool
    {
        return $this->isAdminUser($user) || $this->canOwnerModify($user, $certification);
    }

    public function delete(User|CertifiedCenter|Trainer $user, Certification $certification): bool
    {
        return $this->isAdminUser($user) || $this->canOwnerModify($user, $certification);
    }

    private function isAdminUser(User|CertifiedCenter|Trainer $user): bool
    {
        return $user instanceof User && $user->isAdmin();
    }

    private function isCenterUser(User|CertifiedCenter|Trainer $user): bool
    {
        return $user instanceof CertifiedCenter;
    }

    private function isActiveCenterUser(User|CertifiedCenter|Trainer $user): bool
    {
        return $this->isCenterUser($user) && $user->canPerformActions();
    }

    private function isOwnerCenter(User|CertifiedCenter|Trainer $user, Certification $certification): bool
    {
        return $this->isCenterUser($user) && $this->isCertificationOwner($user, $certification);
    }

    private function isCertificationOwner(CertifiedCenter $center, Certification $certification): bool
    {
        return $certification->creator_type === CertifiedCenter::class
            && $certification->creator_id === $center->id;
    }

    private function canOwnerModify(User|CertifiedCenter|Trainer $user, Certification $certification): bool
    {
        return $this->isOwnerCenter($user, $certification) && $this->isUserActive($user);
    }

    private function isUserActive(User|CertifiedCenter|Trainer $user): bool
    {
        return $this->isCenterUser($user) && $user->canPerformActions();
    }
}
