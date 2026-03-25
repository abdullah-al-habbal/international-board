<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PanelId;
use App\Enums\UserType;
use App\Models\Traits\User\UserCheckers;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Scope;
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

    #[Scope]
    protected function ofType(Builder $query, UserType|string $type): void
    {
        $query->where('type', $type instanceof UserType ? $type->value : $type);
    }

    #[Scope]
    protected function admin(Builder $query): void
    {
        $query->where('type', UserType::Admin->value);
    }

    #[Scope]
    protected function client(Builder $query): void
    {
        $query->where('type', UserType::Client->value);
    }

    #[Scope]
    protected function byEmail(Builder $query, string $email): void
    {
        $query->where('email', $email);
    }

    #[Scope]
    protected function verified(Builder $query): void
    {
        $query->whereNotNull('email_verified_at');
    }

    #[Scope]
    protected function unverified(Builder $query): void
    {
        $query->whereNull('email_verified_at');
    }

    #[Scope]
    protected function createdThisMonth(Builder $query): void
    {
        $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }

    #[Scope]
    protected function createdThisYear(Builder $query): void
    {
        $query->whereYear('created_at', now()->year);
    }

    #[Scope]
    protected function orderByName(Builder $query, string $direction = 'asc'): void
    {
        $query->orderBy('name', $direction);
    }

    #[Scope]
    protected function orderByCreated(Builder $query, string $direction = 'desc'): void
    {
        $query->orderBy('created_at', $direction);
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

    public function isClient(): bool
    {
        return $this->type === UserType::Client;
    }

    public function isVerified(): bool
    {
        return $this->email_verified_at !== null;
    }

    public function isUnverified(): bool
    {
        return $this->email_verified_at === null;
    }
}
