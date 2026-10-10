<?php

declare(strict_types=1);

namespace App\Services\Membership;

use App\Models\Membership;
use Illuminate\Database\Eloquent\Collection;

final class MembershipService
{
    public function listAll(): Collection
    {
        return Membership::orderBy('created_at', 'desc')->get();
    }

    public function findById(int $id): ?Membership
    {
        return Membership::find($id);
    }

    public function findBySlug(string $slug): ?Membership
    {
        return Membership::where('slug', $slug)->first();
    }
}
