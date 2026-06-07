<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AccreditationStatus;
use App\Enums\PanelId;
use Carbon\Carbon;
use Filament\Panel;
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
use App\Models\CertifiedCenterFinancialRequest;
use App\Models\Country;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[UsePolicy(CertifiedCenterPolicy::class)]
class CertifiedCenter extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'address',
        'phone',
        'manager_name',
        'logo',
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

    public function financialRequests(): HasMany
    {
        return $this->hasMany(CertifiedCenterFinancialRequest::class);
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

    public function hasApprovedNonExpiredRequest(): bool
    {
        return $this->accreditationRequests()
            ->where('status', AccreditationStatus::Approved)
            ->where('requested_end_date', '>=', now())
            ->exists();
    }

    public function canPerformActions(): bool
    {
        return $this->is_active && $this->hasApprovedNonExpiredRequest();
    }

    public function accreditationBlockReason(): ?string
    {
        if (!$this->is_active) {
            return __('accreditation.blocked.center_inactive');
        }

        if (!$this->hasApprovedNonExpiredRequest()) {
            return __('accreditation.blocked.no_approved_request');
        }

        if (!$this->hasActiveAccreditationRequest()) {
            return __('accreditation.blocked.period_expired');
        }

        return null;
    }

    public function getLogoUrlAttribute(): ?string
    {
        if ($this->attributes['logo'] ?? null) {
            return Storage::url($this->attributes['logo']);
        }
        return null;
    }
}
