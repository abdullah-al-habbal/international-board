<?php

declare(strict_types=1);

namespace App\Repositories\Membership;

use App\Models\Membership;

final class MembershipRepository
{
    public function __construct(private readonly Membership $membership) {}
}
