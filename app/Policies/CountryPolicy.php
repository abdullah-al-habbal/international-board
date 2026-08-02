<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Country;
use App\Models\User;

class CountryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Country $country): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Country $country): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Country $country): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Country $country): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Country $country): bool
    {
        return $user->isAdmin();
    }
}
