<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AccreditationStatus;
use Carbon\Carbon;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class Trainer extends Authenticatable implements FilamentUser
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name',
        'avatar',
        'bio',
        'email',
        'phone',
        'address',
        'country_id',
        'center_id',
        'specializations',
        'is_active',
        'unique_trainer_code',
        'accreditation_number',
        'accreditation_period_start',
        'accreditation_period_end',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'address' => 'array',
            'specializations' => 'array',
            'is_active' => 'boolean',
            'accreditation_period_start' => 'datetime',
            'accreditation_period_end' => 'datetime',
            'password' => 'hashed',
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

    public function certifications(): MorphMany
    {
        return $this->morphMany(Certification::class, 'creator');
    }

    public function financialRequests(): HasMany
    {
        return $this->hasMany(TrainerFinancialRequest::class);
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
        if (! $this->is_active) {
            return false;
        }

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
        return $this->is_active && $this->hasApprovedNonExpiredRequest();
    }

    public function accreditationBlockReason(): ?string
    {
        if (! $this->is_active) {
            return __('accreditation.blocked.trainer_inactive');
        }

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
        return $panel->getId() === 'trainer' && $this->is_active && is_null($this->center_id);
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->attributes['avatar'] ?? null) {
            return Storage::url($this->attributes['avatar']);
        }

        return null;
    }
}
