<?php

declare(strict_types=1);

namespace App\Models\Traits\Trainee;

trait TraineeHelpers
{
    public function getFullName(): string
    {
        return $this->name;
    }

    public function getDisplayName(): string
    {
        return $this->name;
    }

    public function getCertificationsCount(): int
    {
        return $this->certifications()->count();
    }

    public function getLatestCertificationDate(): ?string
    {
        $latest = $this->certifications()
            ->orderBy('accreditation_date', 'desc')
            ->first();

        return $latest?->accreditation_date?->format('Y-m-d');
    }

    public function getCertificationsByYear(): array
    {
        return $this->certifications()
            ->selectRaw('YEAR(accreditation_date) as year, COUNT(*) as count')
            ->whereNotNull('accreditation_date')
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->pluck('count', 'year')
            ->toArray();
    }

    public function getUniqueDocumentTypes(): array
    {
        return $this->certifications()
            ->with('documentType')
            ->get()
            ->pluck('documentType.name')
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }
}
