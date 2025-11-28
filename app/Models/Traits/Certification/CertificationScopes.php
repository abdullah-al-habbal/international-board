<?php

declare(strict_types=1);

namespace App\Models\Traits\Certification;

use App\Enums\CertificateType;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait CertificationScopes
{
    #[Scope]
    protected function forCenter(Builder $query, int $centerId): void
    {
        $query->where('certified_center_id', $centerId);
    }

    #[Scope]
    protected function ofType(Builder $query, CertificateType|string $type): void
    {
        $query->where('certificate_type', $type instanceof CertificateType ? $type->value : $type);
    }

    #[Scope]
    protected function ofDocumentType(Builder $query, int $documentTypeId): void
    {
        $query->where('document_type_id', $documentTypeId);
    }

    #[Scope]
    protected function byDocumentCode(Builder $query, string $code): void
    {
        $query->where('document_code', $code);
    }

    #[Scope]
    protected function byTraineeName(Builder $query, string $name): void
    {
        $query->where('trainee_name', 'like', "%{$name}%");
    }

    #[Scope]
    protected function byTrainerName(Builder $query, string $name): void
    {
        $query->where('trainer_name', 'like', "%{$name}%");
    }

    #[Scope]
    protected function byNationality(Builder $query, string $nationality): void
    {
        $query->where('nationality', $nationality);
    }

    #[Scope]
    protected function createdThisMonth(Builder $query): void
    {
        $query->whereMonth('created_at', now()->month)
              ->whereYear('created_at', now()->year);
    }

    #[Scope]
    protected function createdThisYear(Builder $query): void
    {
        $query->whereYear('created_at', now()->year);
    }

    #[Scope]
    protected function betweenDates(Builder $query, \DateTime $start, \DateTime $end): void
    {
        $query->whereBetween('created_at', [$start, $end]);
    }

    #[Scope]
    protected function accreditedBetween(Builder $query, \DateTime $start, \DateTime $end): void
    {
        $query->whereBetween('accreditation_date', [$start, $end]);
    }

    #[Scope]
    protected function recentlyCreated(Builder $query): void
    {
        $query->orderBy('created_at', 'desc');
    }

    #[Scope]
    protected function orderByAccreditation(Builder $query, string $direction = 'desc'): void
    {
        $query->orderBy('accreditation_date', $direction);
    }
}
