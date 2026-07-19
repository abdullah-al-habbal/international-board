<?php

// app/Repositories/StaticPage/StaticPageRepository.php
declare(strict_types=1);

namespace App\Repositories\StaticPage;

use App\Models\StaticPage;
use Illuminate\Database\Eloquent\Collection;

final class StaticPageRepository
{
    public function __construct(private readonly StaticPage $model) {}

    public function findBySlug(string $slug): ?StaticPage
    {
        return $this->model
            ->newQuery()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();
    }

    public function getActive(): Collection
    {
        return $this->model
            ->newQuery()
            ->where('is_active', true)
            ->orderBy('title')
            ->get(['id', 'slug', 'title']);
    }
}
