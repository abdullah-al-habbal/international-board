<?php

declare(strict_types=1);

namespace App\Services\Membership;

use App\Models\Membership;
use Illuminate\Database\Eloquent\Collection;

final class MembershipService
{
    public function listActive(): Collection
    {
        return Membership::where('is_active', true)->orderBy('id')->get();
    }

    public function findActive(int $id): ?Membership
    {
        return Membership::where('id', $id)->where('is_active', true)->first();
    }

    public function findBySlug(string $slug): ?Membership
    {
        return Membership::where('slug', $slug)->where('is_active', true)->first();
    }
}
