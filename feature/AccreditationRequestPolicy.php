<?php

// filePath: app/Policies/AccreditationRequestPolicy.php
declare(strict_types=1);

namespace App\Policies;

use App\Models\AccreditationRequest;
use App\Models\CertifiedCenter;
use App\Models\User;

class AccreditationRequestPolicy
{
    // ──────────────────────────────────────────────
    // Admin (User) gates
    // ──────────────────────────────────────────────

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, AccreditationRequest $request): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, AccreditationRequest $request): bool
    {
        return true;
    }

    public function delete(User $user, AccreditationRequest $request): bool
    {
        return true;
    }

    public function deleteAny(User $user): bool
    {
        return true;
    }

    // ──────────────────────────────────────────────
    // Center gates  (guard: certified_center)
    // ──────────────────────────────────────────────

    public function viewAnyCertifiedCenter(CertifiedCenter $center): bool
    {
        return true;
    }

    public function viewCertifiedCenter(CertifiedCenter $center, AccreditationRequest $request): bool
    {
        return $request->certified_center_id === $center->id;
    }

    /**
     * A center may only submit a new request when it has no currently active one.
     */
    public function createCertifiedCenter(CertifiedCenter $center): bool
    {
        return ! $center->hasActiveAccreditationRequest();
    }

    /** Centers cannot edit submitted requests. */
    public function updateCertifiedCenter(CertifiedCenter $center, AccreditationRequest $request): bool
    {
        return false;
    }

    /** Centers cannot delete requests. */
    public function deleteCertifiedCenter(CertifiedCenter $center, AccreditationRequest $request): bool
    {
        return false;
    }
}
