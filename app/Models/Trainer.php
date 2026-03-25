<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\HasEditRequests;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trainer extends Model
{
    use HasEditRequests;
    use HasFactory;

    protected $fillable = [
        'name',
        'avatar',
        'bio',
        'email',
        'phone',
        'address',
        'country_id',
        'specializations',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'address' => 'array',
            'specializations' => 'array',
            'is_active' => 'boolean',
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

    public function hasValidEmail(): bool
    {
        return ! empty($this->email) && filter_var($this->email, FILTER_VALIDATE_EMAIL);
    }

    public function hasValidPhone(): bool
    {
        if (empty($this->phone)) {
            return false;
        }

        $phone = preg_replace('/[^0-9+]/', '', $this->phone);

        return strlen($phone) >= 7 && strlen($phone) <= 15;
    }

    public function hasCompleteProfile(): bool
    {
        return ! empty($this->name) &&
            ! empty($this->email) &&
            ! empty($this->phone) &&
            ! empty($this->country_id);
    }

    public function hasSpecializations(): bool
    {
        $specializations = $this->getSpecializationsList();

        return ! empty($specializations);
    }

    public function hasAddress(): bool
    {
        return ! empty($this->address);
    }

    public function hasBio(): bool
    {
        return ! empty($this->bio);
    }

    public function hasAvatar(): bool
    {
        return ! empty($this->avatar);
    }

    public function isRecentlyActive(): bool
    {
        if (! $this->updated_at) {
            return false;
        }

        return $this->updated_at->isAfter(now()->subDays(30));
    }

    public function hasRecentCertifications(): bool
    {
        return $this->certifications()
            ->where('accreditation_date', '>=', now()->subDays(30))
            ->exists();
    }

    public function hasCertificationsThisYear(): bool
    {
        return $this->certifications()
            ->whereYear('accreditation_date', now()->year)
            ->exists();
    }

    public function isHighVolumeTrainer(): bool
    {
        $thisYearCount = $this->certifications()
            ->whereYear('accreditation_date', now()->year)
            ->count();

        return $thisYearCount >= 10;
    }

    public function needsProfileUpdate(): bool
    {
        return empty($this->bio) ||
            empty($this->avatar) ||
            empty($this->specializations) ||
            ! $this->hasValidEmail() ||
            ! $this->hasValidPhone();
    }

    public function canBeDeactivated(): bool
    {
        $hasRecentCertifications = $this->certifications()
            ->where('accreditation_date', '>=', now()->subDays(90))
            ->exists();

        return ! $hasRecentCertifications;
    }

    public function hasIncompleteData(): bool
    {
        return empty($this->name) ||
            empty($this->email) ||
            empty($this->phone) ||
            empty($this->country_id) ||
            empty($this->specializations);
    }

    public function getFullName(): string
    {
        return $this->name;
    }

    public function getDisplayName(): string
    {
        return $this->name;
    }

    public function hasContactInfo(): bool
    {
        return ! empty($this->email) || ! empty($this->phone);
    }

    public function getContactInfo(): array
    {
        return [
            'email' => $this->email,
            'phone' => $this->phone,
        ];
    }

    public function getAddressString(): ?string
    {
        if (empty($this->address)) {
            return null;
        }

        $address = is_array($this->address) ? $this->address : json_decode($this->address, true);

        if (! is_array($address)) {
            return null;
        }

        return implode(', ', array_filter([
            $address['street'] ?? null,
            $address['city'] ?? null,
            $address['state'] ?? null,
            $address['country'] ?? null,
            $address['postal_code'] ?? null,
        ]));
    }

    public function getSpecializationsList(): array
    {
        if (empty($this->specializations)) {
            return [];
        }

        return is_array($this->specializations) ? $this->specializations : json_decode($this->specializations, true) ?? [];
    }

    public function hasSpecialization(string $specialization): bool
    {
        $specializations = $this->getSpecializationsList();

        return in_array($specialization, $specializations);
    }

    public function addSpecialization(string $specialization): void
    {
        $specializations = $this->getSpecializationsList();

        if (! in_array($specialization, $specializations)) {
            $specializations[] = $specialization;
            $this->update(['specializations' => $specializations]);
        }
    }

    public function removeSpecialization(string $specialization): void
    {
        $specializations = $this->getSpecializationsList();
        $specializations = array_filter($specializations, fn($spec) => $spec !== $specialization);
        $this->update(['specializations' => array_values($specializations)]);
    }

    public function getCertificationsCount(): int
    {
        return $this->certifications()->count();
    }

    public function getRecentCertifications(int $limit = 5)
    {
        return $this->certifications()
            ->orderBy('accreditation_date', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getCertificationsByYear(int $year)
    {
        return $this->certifications()
            ->whereYear('accreditation_date', $year)
            ->get();
    }

    public function getCertificationsStats(): array
    {
        $total = $this->getCertificationsCount();
        $thisYear = $this->certifications()
            ->whereYear('accreditation_date', now()->year)
            ->count();
        $lastMonth = $this->certifications()
            ->whereMonth('accreditation_date', now()->subMonth()->month)
            ->whereYear('accreditation_date', now()->subMonth()->year)
            ->count();

        return [
            'total' => $total,
            'this_year' => $thisYear,
            'last_month' => $lastMonth,
        ];
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }
}
