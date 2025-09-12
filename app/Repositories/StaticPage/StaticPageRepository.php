<?php

declare(strict_types=1);

namespace App\Repositories\StaticPage;

use App\Models\StaticPage;
use Illuminate\Database\Eloquent\Collection;

final class StaticPageRepository
{
    public function __construct(private readonly StaticPage $model) {}

    public function findBySlug(string $slug): ?StaticPage
    {
        return $this->model->localizedSlug($slug)->active()->first();
    }

    public function findBySlugInLocale(string $slug, string $locale): ?StaticPage
    {
        return $this->model->bySlug($slug, $locale)->active()->first();
    }

    public function getAllActive(): Collection
    {
        return $this->model->active()->orderByTitle()->get();
    }

    public function getTotalCount(): int
    {
        return $this->model->newQuery()->count();
    }

    public function getActiveCount(): int
    {
        return $this->model->active()->count();
    }

    public function getInactiveCount(): int
    {
        return $this->model->inactive()->count();
    }
}
