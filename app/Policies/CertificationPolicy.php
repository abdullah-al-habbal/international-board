<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Certification;
use App\Models\CertifiedCenter;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

final class CertificationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User|CertifiedCenter $user): bool
    {
        return true;
    }

    public function view(User|CertifiedCenter $user, Certification $certification): bool
    {
        return $this->isAdminUser($user) || $this->isOwnerCenter($user, $certification);
    }

    public function create(User|CertifiedCenter $user): bool
    {
        return $this->isActiveCenterUser($user);
    }

    public function canCreateForDocumentType(User|CertifiedCenter $user, int $documentTypeId): bool
    {
        if ($this->isAdminUser($user)) {
            return true;
        }

        if (! $this->isCenterUser($user)) {
            return false;
        }

        return $user->allowedDocumentTypes()->where('document_types.id', $documentTypeId)->exists();
    }

    public function update(User|CertifiedCenter $user, Certification $certification): bool
    {
        return $this->isAdminUser($user) || $this->canOwnerModify($user, $certification);
    }

    public function delete(User|CertifiedCenter $user, Certification $certification): bool
    {
        return $this->isAdminUser($user) || $this->canOwnerModify($user, $certification);
    }

    private function isAdminUser(User|CertifiedCenter $user): bool
    {
        return $user instanceof User && $user->isAdmin();
    }

    private function isCenterUser(User|CertifiedCenter $user): bool
    {
        return $user instanceof CertifiedCenter;
    }

    private function isActiveCenterUser(User|CertifiedCenter $user): bool
    {
        return $this->isCenterUser($user) && $user->canPerformActions();
    }

    private function isOwnerCenter(User|CertifiedCenter $user, Certification $certification): bool
    {
        return $this->isCenterUser($user) && $this->isCertificationOwner($user, $certification);
    }

    private function isCertificationOwner(CertifiedCenter $center, Certification $certification): bool
    {
        return $certification->certified_center_id === $center->id;
    }

    private function canOwnerModify(User|CertifiedCenter $user, Certification $certification): bool
    {
        return $this->isOwnerCenter($user, $certification) && $this->isUserActive($user);
    }

    private function isUserActive(User|CertifiedCenter $user): bool
    {
        return $this->isCenterUser($user) && $user->canPerformActions();
    }
}
