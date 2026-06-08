<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Country;
use App\Models\Certification;
use App\Models\TrainerDocumentType;
use App\Models\TrainerFinancialRequest;
use App\Models\TrainerAccreditationRequest;
use App\Enums\AccreditationStatus;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class Trainer extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'avatar',
        'bio',
        'email',
        'phone',
        'address',
        'country_id',
        'specializations',
        'is_active',
        'unique_trainer_code',
        'accreditation_number',
        'membership_start_date',
        'membership_end_date',
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
            'membership_start_date' => 'date',
            'membership_end_date' => 'date',
            'password' => 'hashed',
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function certifications(): HasMany
    {
        return $this->hasMany(Certification::class);
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

    public function isAccredited(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        return $this->accreditationRequests()
            ->where('status', AccreditationStatus::Approved)
            ->where('accreditation_end_date', '>=', now())
            ->exists();
    }

    public function hasActiveAccreditationRequest(): bool
    {
        return $this->accreditationRequests()
            ->where('status', AccreditationStatus::Approved)
            ->where('accreditation_start_date', '<=', now())
            ->where('accreditation_end_date', '>=', now())
            ->exists();
    }

    public function accreditationBlockReason(): ?string
    {
        if (!$this->is_active) {
            return __('accreditation.blocked.trainer_inactive');
        }

        if (!$this->isAccredited()) {
            return __('accreditation.blocked.no_approved_request');
        }

        return null;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'trainer' && $this->is_active;
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->attributes['avatar'] ?? null) {
            return Storage::url($this->attributes['avatar']);
        }
        return null;
    }
}
