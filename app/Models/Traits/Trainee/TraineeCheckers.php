<?php

declare(strict_types=1);

namespace App\Models\Traits\Trainee;

trait TraineeCheckers
{
    public function hasCertifications(): bool
    {
        return $this->certifications()->exists();
    }

    public function hasRecentCertifications(int $days = 30): bool
    {
        return $this->certifications()
            ->where('created_at', '>=', now()->subDays($days))
            ->exists();
    }

    public function hasCertificationInYear(int $year): bool
    {
        return $this->certifications()
            ->whereYear('accreditation_date', $year)
            ->exists();
    }

    public function hasDocumentType(string $documentType): bool
    {
        return $this->certifications()
            ->whereHas('documentType', function ($query) use ($documentType) {
                $query->where('name', 'like', "%{$documentType}%");
            })
            ->exists();
    }

    public function hasTrainer(int $trainerId): bool
    {
        return $this->certifications()
            ->where('trainer_id', $trainerId)
            ->exists();
    }

    public function hasCenter(int $centerId): bool
    {
        return $this->certifications()
            ->where('certified_center_id', $centerId)
            ->exists();
    }

    public function isActive(): bool
    {
        return $this->hasRecentCertifications(365);
    }

    public function needsDataCleanup(): bool
    {
        return empty($this->name) ||
            trim($this->name) === '' ||
            $this->certifications()->whereNull('accreditation_date')->exists();
    }

    public function hasIncompleteCertifications(): bool
    {
        return $this->certifications()
            ->where(function ($query) {
                $query->whereNull('accreditation_date')
                    ->orWhereNull('accredited_serial_number')
                    ->orWhereNull('document_type_id');
            })
            ->exists();
    }
}
