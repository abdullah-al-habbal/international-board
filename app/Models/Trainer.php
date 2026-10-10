<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasAccreditationPeriod;
use App\Models\Concerns\NotifiesAdminOnMutation;
use App\Observers\TrainerObserver;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

#[ObservedBy([TrainerObserver::class])]
#[Fillable([
    'name',
    'avatar',
    'bio',
    'email',
    'phone',
    'address',
    'country_id',
    'trainer_role_id',
    'center_id',
    'accreditation_number',
    'accreditation_period_start',
    'accreditation_period_end',
    'password',
    'show_in_public_website',
])]
#[Hidden([
    'password',
    'remember_token',
])]
class Trainer extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasAccreditationPeriod;
    use HasFactory;
    use Notifiable;
    use NotifiesAdminOnMutation;

    protected function casts(): array
    {
        return [
            'address' => 'array',
            'accreditation_period_start' => 'datetime',
            'accreditation_period_end' => 'datetime',
            'password' => 'hashed',
            'show_in_public_website' => 'boolean',
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function trainerRole(): BelongsTo
    {
        return $this->belongsTo(TrainerRole::class);
    }

    public function center(): BelongsTo
    {
        return $this->belongsTo(CertifiedCenter::class, 'center_id');
    }

    public function specializations(): BelongsToMany
    {
        return $this->belongsToMany(Specialization::class, 'specialization_trainer')
            ->withTimestamps();
    }

    /** Certifications this trainer authored. Most are issued by admins instead — see assignedCertifications(). */
    public function certifications(): MorphMany
    {
        return $this->morphMany(Certification::class, 'creator');
    }

    /** Certifications an admin issued and attributed to this trainer. */
    public function assignedCertifications(): HasMany
    {
        return $this->hasMany(Certification::class, 'assigned_trainer_id');
    }

    public function trainees(): MorphMany
    {
        return $this->morphMany(Trainee::class, 'owner');
    }

    public function financialRequests(): MorphMany
    {
        return $this->morphMany(FinancialRequest::class, 'requestable');
    }

    public function documentTypes(): HasMany
    {
        return $this->hasMany(TrainerDocumentType::class);
    }

    public function accreditationRequests(): HasMany
    {
        return $this->hasMany(TrainerAccreditationRequest::class);
    }

    #[Scope]
    protected function publiclyVisible(Builder $query): void
    {
        $query->where('show_in_public_website', true);
    }

    #[Scope]
    protected function accreditationExpired(Builder $query): void
    {
        $this->applyAccreditationExpiredScope($query);
    }

    #[Scope]
    protected function accreditationActive(Builder $query): void
    {
        $this->applyAccreditationActiveScope($query);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'trainer' && is_null($this->center_id);
    }

    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! empty($this->attributes['avatar']) && Storage::disk('public')->exists($this->attributes['avatar'])) {
                    return Storage::disk('public')->url($this->attributes['avatar']);
                }

                return null;
            }
        );
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }
}
