<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AccreditationStatus;
use App\Models\Concerns\NotifiesAdminOnMutation;
use App\Observers\TrainerObserver;
use Carbon\Carbon;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
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
class Trainer extends Authenticatable implements FilamentUser
{
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

    public function center(): BelongsTo
    {
        return $this->belongsTo(CertifiedCenter::class, 'center_id');
    }

    public function specializations(): BelongsToMany
    {
        return $this->belongsToMany(Specialization::class, 'specialization_trainer')
            ->withTimestamps();
    }

    public function certifications(): MorphMany
    {
        return $this->morphMany(Certification::class, 'creator');
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
    protected function accreditationExpired(Builder $query): void
    {
        $query->where('accreditation_period_end', '<', now());
    }

    #[Scope]
    protected function publiclyVisible(Builder $query): void
    {
        $query->where('show_in_public_website', true);
    }

    #[Scope]
    protected function accreditationActive(Builder $query): void
    {
        $query->where('accreditation_period_start', '<=', now())
            ->where('accreditation_period_end', '>=', now());
    }

    public function isAccreditationActive(): bool
    {
        if (! $this->accreditation_period_start || ! $this->accreditation_period_end) {
            return false;
        }

        return Carbon::now()->between(
            $this->accreditation_period_start,
            $this->accreditation_period_end
        );
    }

    public function isAccredited(): bool
    {
        return $this->hasApprovedNonExpiredRequest();
    }

    public function hasApprovedAccreditationRequest(): bool
    {
        return $this->accreditationRequests()
            ->where('status', AccreditationStatus::Approved)
            ->exists();
    }

    public function hasActiveAccreditationRequest(): bool
    {
        $now = Carbon::now();

        return $this->accreditationRequests()
            ->where('status', AccreditationStatus::Approved)
            ->where('accreditation_start_date', '<=', $now)
            ->where('accreditation_end_date', '>=', $now)
            ->exists();
    }

    public function hasApprovedNonExpiredRequest(): bool
    {
        return $this->accreditationRequests()
            ->where('status', AccreditationStatus::Approved)
            ->where('accreditation_end_date', '>=', now())
            ->exists();
    }

    public function canPerformActions(): bool
    {
        return $this->hasApprovedNonExpiredRequest();
    }

    public function accreditationBlockReason(): ?string
    {

        if (! $this->hasApprovedNonExpiredRequest()) {
            return __('accreditation.blocked.no_approved_request');
        }

        if (! $this->hasActiveAccreditationRequest()) {
            return __('accreditation.blocked.period_expired');
        }

        return null;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'trainer' && is_null($this->center_id);
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if (! empty($this->attributes['avatar']) && Storage::disk('public')->exists($this->attributes['avatar'])) {
            return Storage::disk('public')->url($this->attributes['avatar']);
        }

        return null;
    }
}
