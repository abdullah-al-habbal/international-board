<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CenterStatus;
use App\Models\Traits\CertifiedCenter\{CertifiedCenterCheckers, CertifiedCenterRelations, CertifiedCenterScopes};
use App\Observers\CertifiedCenterObserver;
use App\Policies\CertifiedCenterPolicy;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Attributes\{ObservedBy, UsePolicy};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[UsePolicy(CertifiedCenterPolicy::class)]
#[ObservedBy([CertifiedCenterObserver::class])]
class CertifiedCenter extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;
    use CertifiedCenterCheckers, CertifiedCenterRelations, CertifiedCenterScopes;

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
}
