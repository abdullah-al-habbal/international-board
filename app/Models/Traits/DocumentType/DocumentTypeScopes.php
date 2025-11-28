<?php

declare(strict_types=1);

namespace App\Models\Traits\DocumentType;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait DocumentTypeScopes
{
    #[Scope]
    protected function byKey(Builder $query, string $key): void
    {
        $query->where('key', $key);
    }

    #[Scope]
    protected function orderByKey(Builder $query, string $direction = 'asc'): void
    {
        $query->orderBy('key', $direction);
    }

    #[Scope]
    protected function withCertifications(Builder $query): void
    {
        $query->has('certifications');
    }
}
