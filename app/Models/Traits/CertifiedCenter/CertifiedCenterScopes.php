<?php

declare(strict_types=1);

namespace App\Models\Traits\CertifiedCenter;

use App\Enums\CenterStatus;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait CertifiedCenterScopes
{
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    #[Scope]
    protected function inactive(Builder $query): void
    {
        $query->where('is_active', false);
    }

    #[Scope]
    protected function ofStatus(Builder $query, CenterStatus $status): void
    {
        $query->where('status', $status->value);
    }

    #[Scope]
    protected function statusActive(Builder $query): void
    {
        $query->where('status', CenterStatus::Active->value);
    }

    #[Scope]
    protected function statusInactive(Builder $query): void
    {
        $query->where('status', CenterStatus::Inactive->value);
    }

    #[Scope]
    protected function statusPending(Builder $query): void
    {
        $query->where('status', CenterStatus::Pending->value);
    }

    #[Scope]
    protected function statusSuspended(Builder $query): void
    {
        $query->where('status', CenterStatus::Suspended->value);
    }

    #[Scope]
    protected function byEmail(Builder $query, string $email): void
    {
        $query->where('email', $email);
    }

    #[Scope]
    protected function byName(Builder $query, string $name): void
    {
        $query->where('name', 'like', "%{$name}%");
    }

    #[Scope]
    protected function byManagerName(Builder $query, string $managerName): void
    {
        $query->where('manager_name', 'like', "%{$managerName}%");
    }

    #[Scope]
    protected function byAccreditationNumber(Builder $query, string $accreditationNumber): void
    {
        $query->where('accreditation_number', $accreditationNumber);
    }

    #[Scope]
    protected function accreditationExpiring(Builder $query, int $days = 30): void
    {
        $query->where('accreditation_period_end', '<=', now()->addDays($days))
            ->where('accreditation_period_end', '>=', now());
    }

    #[Scope]
    protected function accreditationExpired(Builder $query): void
    {
        $query->where('accreditation_period_end', '<', now());
    }

    #[Scope]
    protected function accreditationActive(Builder $query): void
    {
        $query->where('accreditation_period_start', '<=', now())
            ->where('accreditation_period_end', '>=', now());
    }

    #[Scope]
    protected function orderByName(Builder $query, string $direction = 'asc'): void
    {
        $query->orderBy('name', $direction);
    }

    #[Scope]
    protected function orderByAccreditationEnd(Builder $query, string $direction = 'asc'): void
    {
        $query->orderBy('accreditation_period_end', $direction);
    }
}
