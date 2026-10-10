<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\NotifiesAdminOnMutation;
use App\Observers\TraineeObserver;
use App\Policies\TraineePolicy;
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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[ObservedBy([TraineeObserver::class])]
#[UsePolicy(TraineePolicy::class)]
#[Fillable([
    'name',
    'email',
    'phone',
    'country_id',
    'date_of_birth',
    'gender',
    'notes',
    'show_in_public_website',
    'owner_type',
    'owner_id',
])]
class Trainee extends Model
{
    use HasFactory;
    use NotifiesAdminOnMutation;

    protected function casts(): array
    {
        return [
            'show_in_public_website' => 'boolean',
        ];
    }

    public function dateOfBirth(): Attribute
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

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function certifications(): HasMany
    {
        return $this->hasMany(Certification::class);
    }

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    #[Scope]
    protected function ownedBy(Builder $query, string $type, int $id): void
    {
        $query->where('owner_type', $type)
            ->where('owner_id', $id);
    }

    #[Scope]
    protected function publiclyVisible(Builder $query): void
    {
        $query->where('show_in_public_website', true);
    }
}
