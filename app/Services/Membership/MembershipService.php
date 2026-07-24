<?php

declare(strict_types=1);

namespace App\Services\Membership;

use App\Models\Membership;

final class MembershipService
{
    public function findBySlug(string $slug): ?Membership
    {
        return Membership::where('slug', $slug)->first();
    }
}
