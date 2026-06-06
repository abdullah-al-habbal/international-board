<?php
// app/Models/Certification.php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
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
use Illuminate\Support\Str;

#[UsePolicy(CertificationPolicy::class)]
class Certification extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Certification $certification) {
            if (empty($certification->document_code)) {
                $certification->document_code = self::generateDocumentCode();
            }
        });
    }

    protected static function generateDocumentCode(): string
    {
        return 'CERT-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
    }

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
    ];    }

    public function getAccreditationDateAttribute($value): ?string
    {
        if (blank($value)) {
            return null;
        }
        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
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
    protected function betweenDates(Builder $query, \DateTime $start, \DateTime $end): void
    {
        $query->whereBetween('created_at', [$start, $end]);
    }

    #[Scope]
    protected function recentlyCreated(Builder $query): void
    {
        $query->orderBy('created_at', 'desc');
    }
}
