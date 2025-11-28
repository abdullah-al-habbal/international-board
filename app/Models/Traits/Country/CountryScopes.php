<?php

declare(strict_types=1);

namespace App\Models\Traits\Country;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait CountryScopes
{
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
    protected function byCode(Builder $query, string $code): void
    {
        $query->where('code', strtoupper($code));
    }

    #[Scope]
    protected function byCode2(Builder $query, string $code2): void
    {
        $query->where('code_2', strtoupper($code2));
    }

    #[Scope]
    protected function byName(Builder $query, string $name): void
    {
        $query->where('name', 'like', '%'.$name.'%');
    }

    #[Scope]
    protected function orderByName(Builder $query, string $direction = 'asc'): void
    {
        $query->orderBy('name', $direction);
    }

    #[Scope]
    protected function orderByCode(Builder $query, string $direction = 'asc'): void
    {
        $query->orderBy('code', $direction);
    }
}
