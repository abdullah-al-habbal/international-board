<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AccreditationRequest;
use App\Models\CertifiedCenter;
use App\Models\User;

class AccreditationRequestPolicy
{
    public function viewAny(User|CertifiedCenter $user): bool
    {
        return true;
    }

    public function view(User|CertifiedCenter $user, AccreditationRequest $request): bool
    {
        return true;
    }

    public function create(User|CertifiedCenter $user): bool
    {
        return true;
    }

    public function update(User|CertifiedCenter $user, AccreditationRequest $request): bool
    {
        return true;
    }

    public function delete(User|CertifiedCenter $user, AccreditationRequest $request): bool
    {
        return true;
    }

    public function deleteAny(User|CertifiedCenter $user): bool
    {
        return true;
    }

    // Center-specific rules
    public function viewAnyCertifiedCenter(CertifiedCenter $center): bool
    {
        return true;
    }

    public function viewCertifiedCenter(CertifiedCenter $center, AccreditationRequest $request): bool
    {
        return $request->certified_center_id === $center->id;
    }

    public function createCertifiedCenter(CertifiedCenter $center): bool
    {
        return ! $center->hasActiveAccreditationRequest();
    }

    public function updateCertifiedCenter(CertifiedCenter $center, AccreditationRequest $request): bool
    {
        return false;
    }

    public function deleteCertifiedCenter(CertifiedCenter $center, AccreditationRequest $request): bool
    {
        return false;
    }
}
