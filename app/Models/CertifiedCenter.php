<?php

declare(strict_types=1);

namespace App\Models;


use App\Enums\AccreditationStatus;
use App\Enums\PanelId;
use Carbon\Carbon;
use Filament\Panel;
use App\Models\Traits\HasEditRequests;
use App\Enums\CenterStatus;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use App\Policies\CertifiedCenterPolicy;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\AccreditationRequest;
use App\Models\CenterDocumentTypeRequest;
use App\Models\CenterTypeRequest;
use App\Models\Certification;
use App\Models\CertifiedCenterDocumentType;
use App\Models\Country;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UsePolicy(CertifiedCenterPolicy::class)]
class CertifiedCenter extends Authenticatable implements FilamentUser
{
    use HasEditRequests;
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'address',
        'phone',
        'manager_name',
        'accreditation_period_start',
        'accreditation_period_end',
        'accreditation_number',
        'status',
        'is_active',
        'country_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'accreditation_period_start' => 'datetime',
            'accreditation_period_end' => 'datetime',
            'status' => CenterStatus::class,
            'is_active' => 'boolean',
        ];
    }

    public function certifications(): HasMany
    {
        return $this->hasMany(Certification::class);
    }

    public function accreditationRequests(): HasMany
    {
        return $this->hasMany(AccreditationRequest::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function centerTypeRequests(): HasMany
    {
        return $this->hasMany(CenterTypeRequest::class);
    }

    public function documentTypeRequests(): HasMany
    {
        return $this->hasMany(CenterDocumentTypeRequest::class);
    }

    public function approvedDocumentTypes(): HasMany
    {
        return $this->hasMany(CertifiedCenterDocumentType::class);
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
    protected function ofStatus(Builder $query, CenterStatus $status): void
    {
        $query->where('status', $status->value);
    }

    #[Scope]
    protected function statusActive(Builder $query): void
    {
        $query->where('status', CenterStatus::Active->value);
    }

    #[Scope]
    protected function statusInactive(Builder $query): void
    {
        $query->where('status', CenterStatus::Inactive->value);
    }

    #[Scope]
    protected function statusPending(Builder $query): void
    {
        $query->where('status', CenterStatus::Pending->value);
    }

    #[Scope]
    protected function statusSuspended(Builder $query): void
    {
        $query->where('status', CenterStatus::Suspended->value);
    }

    #[Scope]
    protected function byEmail(Builder $query, string $email): void
    {
        $query->where('email', $email);
    }

    #[Scope]
    protected function byName(Builder $query, string $name): void
    {
        $query->where('name', 'like', "%{$name}%");
    }

    #[Scope]
    protected function byManagerName(Builder $query, string $managerName): void
    {
        $query->where('manager_name', 'like', "%{$managerName}%");
    }

    #[Scope]
    protected function byAccreditationNumber(Builder $query, string $accreditationNumber): void
    {
        $query->where('accreditation_number', $accreditationNumber);
    }

    #[Scope]
    protected function accreditationExpiring(Builder $query, int $days = 30): void
    {
        $query->where('accreditation_period_end', '<=', now()->addDays($days))
            ->where('accreditation_period_end', '>=', now());
    }

    #[Scope]
    protected function accreditationExpired(Builder $query): void
    {
        $query->where('accreditation_period_end', '<', now());
    }

    #[Scope]
    protected function accreditationActive(Builder $query): void
    {
        $query->where('accreditation_period_start', '<=', now())
            ->where('accreditation_period_end', '>=', now());
    }

    #[Scope]
    protected function orderByName(Builder $query, string $direction = 'asc'): void
    {
        $query->orderBy('name', $direction);
    }

    #[Scope]
    protected function orderByAccreditationEnd(Builder $query, string $direction = 'asc'): void
    {
        $query->orderBy('accreditation_period_end', $direction);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return PanelId::tryFrom($panel->getId()) === PanelId::Center && $this->is_active;
    }

    public function isAccreditationActive(): bool
    {
        if (!$this->accreditation_period_start || !$this->accreditation_period_end) {
            return false;
        }

        return Carbon::now()->between(
            $this->accreditation_period_start,
            $this->accreditation_period_end
        );
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
            ->where('requested_start_date', '<=', $now)
            ->where('requested_end_date', '>=', $now)
            ->exists();
    }

    public function canPerformActions(): bool
    {
        return $this->is_active && $this->hasActiveAccreditationRequest();
    }

    public function accreditationBlockReason(): ?string
    {
        if (!$this->is_active) {
            return __('accreditation.blocked.center_inactive');
        }

        if (!$this->hasApprovedAccreditationRequest()) {
            return __('accreditation.blocked.no_approved_request');
        }

        if (!$this->hasActiveAccreditationRequest()) {
            return __('accreditation.blocked.period_expired');
        }

        return null;
    }
}
