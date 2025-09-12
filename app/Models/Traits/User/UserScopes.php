<?php

declare(strict_types=1);

namespace App\Models\Traits\User;

use App\Enums\UserType;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait UserScopes
{
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
}
