<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PanelId;
use App\Enums\UserType;
use App\Policies\UserPolicy;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'name',
    'email',
    'password',
    'type',
    'avatar',
])]
#[Hidden([
    'password',
    'remember_token',
])]
#[UsePolicy(UserPolicy::class)]
class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasFactory, Notifiable;

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

    public function certifications(): MorphMany
    {
        return $this->morphMany(Certification::class, 'creator');
    }

    public function trainees(): MorphMany
    {
        return $this->morphMany(Trainee::class, 'owner');
    }

    #[Scope]
    protected function ofType(Builder $query, UserType|string $type): Builder
    {
        return $query->where('type', $type instanceof UserType ? $type->value : $type);
    }

    #[Scope]
    protected function admin(Builder $query): Builder
    {
        return $query->where('type', UserType::Admin->value);
    }

    #[Scope]
    protected function client(Builder $query): Builder
    {
        return $query->where('type', UserType::Client->value);
    }

    #[Scope]
    protected function byEmail(Builder $query, string $email): Builder
    {
        return $query->where('email', $email);
    }

    #[Scope]
    protected function verified(Builder $query): Builder
    {
        return $query->whereNotNull('email_verified_at');
    }

    #[Scope]
    protected function unverified(Builder $query): Builder
    {
        return $query->whereNull('email_verified_at');
    }

    #[Scope]
    protected function createdThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }

    #[Scope]
    protected function createdThisYear(Builder $query): Builder
    {
        return $query->whereYear('created_at', now()->year);
    }

    #[Scope]
    protected function orderByName(Builder $query, string $direction = 'asc'): Builder
    {
        return $query->orderBy('name', $direction);
    }

    #[Scope]
    protected function orderByCreated(Builder $query, string $direction = 'desc'): Builder
    {
        return $query->orderBy('created_at', $direction);
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url;
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
}
