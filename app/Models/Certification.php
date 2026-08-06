<?php

declare(strict_types=1);

namespace App\Models;

use App\Policies\CertificationPolicy;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[UsePolicy(CertificationPolicy::class)]
class Certification extends Model
{
    use HasFactory;

    protected $fillable = [
        'creator_type',
        'creator_id',
        'documentable_type',
        'documentable_id',
        'trainee_id',
        'assigned_trainer_id',
        'accredited_serial_number',
        'document_code',
        'accreditation_number',
        'accreditation_date',
        'country_id',
        'notes',
        'show_in_public_website',
    ];

    protected function casts(): array
    {
        return [
            'accreditation_date' => 'date',
            'show_in_public_website' => 'boolean',
        ];
    }

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

    public function creator(): MorphTo
    {
        return $this->morphTo();
    }

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function trainee(): BelongsTo
    {
        return $this->belongsTo(Trainee::class);
    }

    public function assignedTrainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class, 'assigned_trainer_id');
    }

    #[Scope]
    protected function createdBy(Builder $query, string $type, int $id): void
    {
        $query->where('creator_type', $type)->where('creator_id', $id);
    }

    #[Scope]
    protected function publiclyVisible(Builder $query): void
    {
        $query->where('show_in_public_website', true)
            ->where(function (Builder $q) {
                $q->whereNull('trainee_id')
                    ->orWhereHas('trainee', fn (Builder $t) => $t->where('trainees.show_in_public_website', true));
            });
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
