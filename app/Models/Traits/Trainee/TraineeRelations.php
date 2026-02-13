<?php

declare(strict_types=1);

namespace App\Models\Traits\Trainee;

use App\Models\Certification;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait TraineeRelations
{
    public function certifications(): HasMany
    {
        return $this->hasMany(Certification::class);
    }

    public function recentCertifications(): HasMany
    {
        return $this->hasMany(Certification::class)
            ->orderBy('accreditation_date', 'desc')
            ->limit(10);
    }

    public function certificationsThisYear(): HasMany
    {
        return $this->hasMany(Certification::class)
            ->whereYear('accreditation_date', now()->year);
    }

    public function certificationsByYear(int $year): HasMany
    {
        return $this->hasMany(Certification::class)
            ->whereYear('accreditation_date', $year);
    }

    public function certificationsByDocumentType(string $documentType): HasMany
    {
        return $this->hasMany(Certification::class)
            ->whereHas('documentType', function ($query) use ($documentType) {
                $query->where('name', 'like', "%{$documentType}%");
            });
    }
}
