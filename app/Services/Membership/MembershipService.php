<?php

declare(strict_types=1);

namespace App\Services\Membership;

use App\Repositories\Membership\MembershipRepository;

final class MembershipService
{
    public function __construct(private readonly MembershipRepository $membershipRepository) {}
}
