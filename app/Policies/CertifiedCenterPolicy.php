<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CertifiedCenter;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CertifiedCenterPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, CertifiedCenter $certifiedCenter): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, CertifiedCenter $certifiedCenter): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, CertifiedCenter $certifiedCenter): bool
    {
        return $user->isAdmin();
    }
}
