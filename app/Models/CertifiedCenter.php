<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CenterStatus;
use App\Enums\PanelId;
use App\Models\Concerns\HasAccreditationPeriod;
use App\Observers\CertifiedCenterObserver;
use App\Policies\CertifiedCenterPolicy;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

#[ObservedBy([CertifiedCenterObserver::class])]
#[UsePolicy(CertifiedCenterPolicy::class)]
#[Fillable([
    'name',
    'email',
    'password',
    'address',
    'phone',
    'manager_name',
    'logo',
    'notes',
    'accreditation_period_start',
    'accreditation_period_end',
    'accreditation_number',
    'status',
    'country_id',
    'show_in_public_website',
])]
#[Hidden([
    'password',
    'remember_token',
])]
class CertifiedCenter extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasAccreditationPeriod;
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'accreditation_period_start' => 'datetime',
            'accreditation_period_end' => 'datetime',
            'status' => CenterStatus::class,
            'show_in_public_website' => 'boolean',
        ];
    }

    public function certifications(): MorphMany
    {
        return $this->morphMany(Certification::class, 'creator');
    }

    public function trainees(): MorphMany
    {
        return $this->morphMany(Trainee::class, 'owner');
    }

    public function trainers(): HasMany
    {
        return $this->hasMany(Trainer::class, 'center_id');
    }

    public function accreditationRequests(): HasMany
    {
        return $this->hasMany(CenterAccreditationRequest::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function documentTypes(): HasMany
    {
        return $this->hasMany(CertifiedCenterDocumentType::class);
    }

    public function approvedDocumentTypes(): HasMany
    {
        return $this->documentTypes()->where('status', 'approved');
    }

    public function financialRequests(): MorphMany
    {
        return $this->morphMany(FinancialRequest::class, 'requestable');
    }

    #[Scope]
    protected function ofStatus(Builder $query, CenterStatus $status): void
    {
        $query->where('status', $status->value);
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
        return PanelId::tryFrom($panel->getId()) === PanelId::Center;
    }

    public function getLogoUrlAttribute(): ?string
    {
        if (! empty($this->attributes['logo']) && Storage::disk('public')->exists($this->attributes['logo'])) {
            return Storage::disk('public')->url($this->attributes['logo']);
        }

        return null;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->logo_url;
    }
}
