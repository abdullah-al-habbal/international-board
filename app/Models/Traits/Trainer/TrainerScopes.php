<?php

declare(strict_types=1);

namespace App\Models\Traits\Trainer;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait TrainerScopes
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
    protected function byName(Builder $query, string $name): void
    {
        $query->where('name', 'like', "%{$name}%");
    }

    #[Scope]
    protected function byEmail(Builder $query, string $email): void
    {
        $query->where('email', 'like', "%{$email}%");
    }

    #[Scope]
    protected function byCountry(Builder $query, int $countryId): void
    {
        $query->where('country_id', $countryId);
    }

    #[Scope]
    protected function byCountryName(Builder $query, string $countryName): void
    {
        $query->whereHas('country', function (Builder $query) use ($countryName) {
            $query->where('name', 'like', "%{$countryName}%");
        });
    }

    #[Scope]
    protected function withSpecialization(Builder $query, string $specialization): void
    {
        $query->whereJsonContains('specializations', $specialization);
    }

    #[Scope]
    protected function withAnySpecialization(Builder $query, array $specializations): void
    {
        $query->where(function (Builder $query) use ($specializations) {
            foreach ($specializations as $specialization) {
                $query->orWhereJsonContains('specializations', $specialization);
            }
        });
    }

    #[Scope]
    protected function withAllSpecializations(Builder $query, array $specializations): void
    {
        foreach ($specializations as $specialization) {
            $query->whereJsonContains('specializations', $specialization);
        }
    }

    #[Scope]
    protected function withContactInfo(Builder $query): void
    {
        $query->where(function (Builder $query) {
            $query->whereNotNull('email')
                ->orWhereNotNull('phone');
        });
    }

    #[Scope]
    protected function withoutContactInfo(Builder $query): void
    {
        $query->where(function (Builder $query) {
            $query->whereNull('email')
                ->whereNull('phone');
        });
    }

    #[Scope]
    protected function withCertifications(Builder $query): void
    {
        $query->whereHas('certifications');
    }

    #[Scope]
    protected function withoutCertifications(Builder $query): void
    {
        $query->whereDoesntHave('certifications');
    }

    #[Scope]
    protected function withRecentCertifications(Builder $query, int $days = 30): void
    {
        $query->whereHas('certifications', function (Builder $query) use ($days) {
            $query->where('accreditation_date', '>=', now()->subDays($days));
        });
    }

    #[Scope]
    protected function withCertificationsInYear(Builder $query, int $year): void
    {
        $query->whereHas('certifications', function (Builder $query) use ($year) {
            $query->whereYear('accreditation_date', $year);
        });
    }

    #[Scope]
    protected function orderByName(Builder $query, string $direction = 'asc'): void
    {
        $query->orderBy('name', $direction);
    }

    #[Scope]
    protected function orderByCertificationsCount(Builder $query, string $direction = 'desc'): void
    {
        $query->withCount('certifications')
            ->orderBy('certifications_count', $direction);
    }

    #[Scope]
    protected function orderByRecentActivity(Builder $query, string $direction = 'desc'): void
    {
        $query->withMax('certifications', 'accreditation_date')
            ->orderBy('certifications_max_accreditation_date', $direction);
    }

    #[Scope]
    protected function recentlyCreated(Builder $query, int $days = 30): void
    {
        $query->where('created_at', '>=', now()->subDays($days));
    }

    #[Scope]
    protected function recentlyUpdated(Builder $query, int $days = 30): void
    {
        $query->where('updated_at', '>=', now()->subDays($days));
    }
}
