<?php

declare(strict_types=1);

namespace App\Models\Traits\Trainee;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait TraineeScopes
{
    #[Scope]
    protected function byName(Builder $query, string $name): void
    {
        $query->where('name', 'like', "%{$name}%");
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
    protected function withCertificationsInYear(Builder $query, int $year): void
    {
        $query->whereHas('certifications', function (Builder $query) use ($year) {
            $query->whereYear('accreditation_date', $year);
        });
    }

    #[Scope]
    protected function withDocumentType(Builder $query, string $documentType): void
    {
        $query->whereHas('certifications.documentType', function (Builder $query) use ($documentType) {
            $query->where('name', 'like', "%{$documentType}%");
        });
    }

    #[Scope]
    protected function withCertificateType(Builder $query, string $certificateType): void
    {
        $query->whereHas('certifications', function (Builder $query) use ($certificateType) {
            $query->where('certificate_type', $certificateType);
        });
    }

    #[Scope]
    protected function withTrainer(Builder $query, int $trainerId): void
    {
        $query->whereHas('certifications', function (Builder $query) use ($trainerId) {
            $query->where('trainer_id', $trainerId);
        });
    }

    #[Scope]
    protected function withCenter(Builder $query, int $centerId): void
    {
        $query->whereHas('certifications', function (Builder $query) use ($centerId) {
            $query->where('certified_center_id', $centerId);
        });
    }

    #[Scope]
    protected function orderByCertificationsCount(Builder $query, string $direction = 'desc'): void
    {
        $query->withCount('certifications')
            ->orderBy('certifications_count', $direction);
    }

    #[Scope]
    protected function orderByLatestCertification(Builder $query, string $direction = 'desc'): void
    {
        $query->leftJoin('certifications', 'trainees.id', '=', 'certifications.trainee_id')
            ->select('trainees.*')
            ->selectRaw('MAX(certifications.accreditation_date) as latest_certification_date')
            ->groupBy('trainees.id')
            ->orderBy('latest_certification_date', $direction);
    }
}
