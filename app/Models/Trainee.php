<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trainee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'country_id',
        'date_of_birth',
        'nationality',
        'gender',
        'occupation',
        'organization',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'medical_info',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

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
            ->whereHas('documentType', function ($query) use ($documentType) {
                $query->where('name', 'like', "%{$documentType}%");
            });
    }

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
