<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AccreditationRequest;
use App\Models\CertifiedCenter;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

final class AccreditationRequestPolicy
{
    use HandlesAuthorization;

    private const PENDING_STATUS = 'pending';

    public function viewAny(User|CertifiedCenter $user): bool
    {
        return true;
    }

    public function view(User|CertifiedCenter $user, AccreditationRequest $accreditationRequest): bool
    {
        return $this->isAdminUser($user) || $this->isOwnerCenter($user, $accreditationRequest);
    }

    public function create(User|CertifiedCenter $user): bool
    {
        return $this->isActiveCenterUser($user);
    }

    public function update(User|CertifiedCenter $user, AccreditationRequest $accreditationRequest): bool
    {
        return $this->isAdminUser($user) || $this->canOwnerModify($user, $accreditationRequest);
    }

    public function delete(User|CertifiedCenter $user, AccreditationRequest $accreditationRequest): bool
    {
        return $this->isAdminUser($user) || $this->canOwnerModify($user, $accreditationRequest);
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

    private function isOwnerCenter(User|CertifiedCenter $user, AccreditationRequest $accreditationRequest): bool
    {
        return $this->isCenterUser($user) && $this->isRequestOwner($user, $accreditationRequest);
    }

    private function isRequestOwner(CertifiedCenter $center, AccreditationRequest $accreditationRequest): bool
    {
        return $accreditationRequest->certified_center_id === $center->id;
    }

    private function canOwnerModify(User|CertifiedCenter $user, AccreditationRequest $accreditationRequest): bool
    {
        return $this->isOwnerCenter($user, $accreditationRequest)
            && $this->isRequestPending($accreditationRequest);
    }

    private function isRequestPending(AccreditationRequest $accreditationRequest): bool
    {
        return $accreditationRequest->status->value === self::PENDING_STATUS;
    }
}
