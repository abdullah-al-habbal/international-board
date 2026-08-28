<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\NotifiesAdminOnMutation;
use App\Observers\CertificationObserver;
use App\Policies\CertificationPolicy;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[ObservedBy([CertificationObserver::class])]
#[UsePolicy(CertificationPolicy::class)]
#[Fillable([
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
])]
class Certification extends Model
{
    use HasFactory;
    use NotifiesAdminOnMutation;

    protected function casts(): array
    {
        return [
            'accreditation_date' => 'date',
            'show_in_public_website' => 'boolean',
        ];
    }

    public function accreditationDate(): Attribute
    {
        return Attribute::make(
            get: function ($value): ?string {
                if (blank($value)) {
                    return null;
                }
                try {
                    return Carbon::parse($value)->toDateString();
                } catch (\Throwable) {
                    return null;
                }
            }
        );
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

    /**
     * Every certification a trainer is credited with, however it got there.
     *
     * A trainer is linked either by having authored the certification (the
     * `creator` morph) or by being assigned to one an admin issued
     * (`assigned_trainer_id`). In practice the second case is the overwhelming
     * majority, so counting only the morph reports near-zero for trainers who
     * hold hundreds of certifications.
     *
     * Written as a single OR predicate rather than two counts so a
     * certification a trainer both created and was assigned is counted once.
     */
    #[Scope]
    protected function forTrainer(Builder $query, int $trainerId): void
    {
        $query->where(function (Builder $related) use ($trainerId): void {
            $related->where(function (Builder $created) use ($trainerId): void {
                $created->where('creator_type', Trainer::class)
                    ->where('creator_id', $trainerId);
            })->orWhere('assigned_trainer_id', $trainerId);
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
