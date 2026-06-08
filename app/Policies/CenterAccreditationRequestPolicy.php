<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CenterAccreditationRequest;
use App\Models\User;

class CenterAccreditationRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, CenterAccreditationRequest $request): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, CenterAccreditationRequest $request): bool
    {
        return true;
    }

    public function delete(User $user, CenterAccreditationRequest $request): bool
    {
        return true;
    }

    public function restore(User $user, CenterAccreditationRequest $request): bool
    {
        return true;
    }

    public function forceDelete(User $user, CenterAccreditationRequest $request): bool
    {
        return true;
    }
}
