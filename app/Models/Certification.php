<?php
// app/Models/Certification.php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Traits\Certification\CertificationScopes;
use App\Policies\CertificationPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CertifiedCenter;
use App\Models\Country;
use App\Models\DocumentType;
use App\Models\Trainee;
use App\Models\Trainer;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UsePolicy(CertificationPolicy::class)]
class Certification extends Model
{
    use CertificationScopes;
    use HasFactory;

    protected $fillable = [
        'certified_center_id',
        'trainee_id',
        'nationality',
        'accredited_serial_number',
        'document_code',
        'accreditation_number',
        'document_type_id',
        'accreditation_date',
        'trainer_id',
        'country_id',
        'paper_received',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'accreditation_date' => 'date',
            'paper_received' => 'boolean',
        ];
    }

    public function certifiedCenter(): BelongsTo
    {
        return $this->belongsTo(CertifiedCenter::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function trainee(): BelongsTo
    {
        return $this->belongsTo(Trainee::class);
    }

    #[Scope]
    protected function forCenter(Builder $query, int $centerId): void
    {
        $query->where('certified_center_id', $centerId);
    }

    #[Scope]
    protected function ofType(Builder $query, int|string $type): void
    {
        if (is_int($type)) {
            $query->where('document_type_id', $type);
        } else {
            $query->whereHas('documentType', function (Builder $q) use ($type) {
                $q->where('key', $type);
            });
        }
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
        $query->whereHas('trainee', function (Builder $q) use ($name) {
            $q->where('name', 'like', "%{$name}%");
        });
    }

    #[Scope]
    protected function byTrainerName(Builder $query, string $name): void
    {
        $query->whereHas('trainer', function (Builder $q) use ($name) {
            $q->where('name', 'like', "%{$name}%");
        });
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

    public function hasPaperReceived(): bool
    {
        if (is_string($this->paper_received)) {
            return in_array(strtoupper($this->paper_received), ['YES', 'YAS', '1', 'TRUE']);
        }

        return (bool) $this->paper_received;
    }

    public function isTrainingCertificate(): bool
    {
        $key = strtolower($this->documentType?->key ?? '');

        return str_contains($key, 'training') ||
            str_contains($key, 'tot');
    }

    public function isAccreditationCenterCertificate(): bool
    {
        $key = strtolower($this->documentType?->key ?? '');

        return str_contains($key, 'accreditation_center');
    }

    public function isExperienceCertificate(): bool
    {
        $key = strtolower($this->documentType?->key ?? '');

        return str_contains($key, 'experience');
    }

    public function isConsultantCertificate(): bool
    {
        $key = strtolower($this->documentType?->key ?? '');

        return str_contains($key, 'adviser') ||
            str_contains($key, 'consultant');
    }

    public function hasValidData(): bool
    {
        return ! empty($this->trainee_id) &&
            ! empty($this->accredited_serial_number) &&
            ! empty($this->document_type_id) &&
            ! empty($this->accreditation_date);
    }

    public function isRecent(): bool
    {
        return $this->created_at && $this->created_at->isAfter(now()->subDays(30));
    }

    public function isAccreditedInYear(int $year): bool
    {
        return $this->accreditation_date &&
            $this->accreditation_date->year === $year;
    }

    public function needsDataCleanup(): bool
    {
        return empty($this->document_type_id) ||
            empty($this->certified_center_id) ||
            $this->hasInconsistentNationality() ||
            $this->hasInconsistentPaperStatus();
    }

    private function hasInconsistentNationality(): bool
    {
        if (empty($this->nationality)) {
            return true;
        }

        $nationality = strtolower(trim($this->nationality));
        $standardNationalities = ['libyan', 'egyptian', 'syrian', 'yemeni', 'mauritanian'];

        return ! in_array($nationality, $standardNationalities) &&
            ! in_array($nationality, ['libya', 'egypt', 'syria', 'yemen', 'mauritania']);
    }

    private function hasInconsistentPaperStatus(): bool
    {
        if (empty($this->paper_received)) {
            return false;
        }

        $status = strtoupper(trim($this->paper_received));

        return ! in_array($status, ['YES', 'NO', 'TRUE', 'FALSE', '1', '0']);
    }

    public function getDocumentTypeName(): ?string
    {
        return $this->documentType?->name;
    }

    public function isComplete(): bool
    {
        return ! empty($this->trainee_id) &&
            ! empty($this->accredited_serial_number) &&
            ! empty($this->accreditation_date) &&
            ! empty($this->document_type_id);
    }

    public function hasValidDate(): bool
    {
        if (empty($this->accreditation_date)) {
            return false;
        }

        return $this->accreditation_date >= '1900-01-01' &&
            $this->accreditation_date <= now();
    }

    public function getDataQualityScore(): int
    {
        $score = 0;
        $maxScore = 10;

        if (! empty($this->trainee_id)) {
            $score += 1;
        }
        if (! empty($this->accredited_serial_number)) {
            $score += 1;
        }
        if (! empty($this->document_type_id)) {
            $score += 1;
        }
        if (! empty($this->accreditation_date)) {
            $score += 1;
        }
        if ($this->hasValidDate()) {
            $score += 1;
        }

        if (! empty($this->country_id)) {
            $score += 1;
        }
        if (! empty($this->trainer_id)) {
            $score += 1;
        }
        if (! empty($this->certified_center_id)) {
            $score += 1;
        }

        if (! empty($this->paper_received)) {
            $score += 1;
        }
        if (! empty($this->notes)) {
            $score += 1;
        }

        return (int) round(($score / $maxScore) * 100);
    }

    public function getDataQualityStatus(): string
    {
        $score = $this->getDataQualityScore();

        return match (true) {
            $score >= 90 => 'excellent',
            $score >= 75 => 'good',
            $score >= 60 => 'fair',
            $score >= 40 => 'poor',
            default => 'critical'
        };
    }
}
