<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PanelId;
use App\Enums\UserType;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
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
            'type' => UserType::class,
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return PanelId::tryFrom($panel->getId()) === PanelId::Admin
            && $this->type === UserType::Admin;
    }

    public function isAdmin(): bool
    {
        return $this->type === UserType::Admin;
    }
    public function scopeOfType(Builder $query, UserType|string $type): Builder
    {
        return $query->where('type', $type instanceof UserType ? $type->value : $type);
    }
    public function scopeAdmin(Builder $query): Builder
    {
        return $query->where('type', UserType::Admin->value);
    }
    public function scopeClient(Builder $query): Builder
    {
        return $query->where('type', UserType::Client->value);
    }
    public function scopeByEmail(Builder $query, string $email): Builder
    {
        return $query->where('email', $email);
    }
    public function scopeVerified(Builder $query): Builder
    {
        return $query->whereNotNull('email_verified_at');
    }
    public function scopeUnverified(Builder $query): Builder
    {
        return $query->whereNull('email_verified_at');
    }
    public function scopeCreatedThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }
    public function scopeCreatedThisYear(Builder $query): Builder
    {
        return $query->whereYear('created_at', now()->year);
    }
    public function scopeOrderByName(Builder $query, string $direction = 'asc'): Builder
    {
        return $query->orderBy('name', $direction);
    }
    public function scopeOrderByCreated(Builder $query, string $direction = 'desc'): Builder
    {
        return $query->orderBy('created_at', $direction);
    }
}
