<?php

declare(strict_types=1);

namespace App\Models\Traits\StaticPage;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait StaticPageScopes
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
    protected function localizedSlug(Builder $query, string $slug): void
    {
        $query->where('slug->'.app()->getLocale(), $slug);
    }

    #[Scope]
    protected function bySlug(Builder $query, string $slug, ?string $locale = null): void
    {
        $locale ??= app()->getLocale();
        $query->where("slug->{$locale}", $slug);
    }

    #[Scope]
    protected function orderByTitle(Builder $query, string $direction = 'asc'): void
    {
        $query->orderBy('title->'.app()->getLocale(), $direction);
    }
}
