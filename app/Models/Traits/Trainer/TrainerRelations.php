<?php

declare(strict_types=1);

namespace App\Models\Traits\Trainer;

use App\Models\Certification;
use App\Models\Country;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait TrainerRelations
{
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

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
            ->where('document_type', $documentType);
    }

    public function certificationsByCertificateType(string $certificateType): HasMany
    {
        return $this->hasMany(Certification::class)
            ->where('certificate_type', $certificateType);
    }
}
