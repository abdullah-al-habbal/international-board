<?php

// app/Services/StaticPage/StaticPageService.php
declare(strict_types=1);

namespace App\Services\StaticPage;

use App\Models\StaticPage;
use App\Repositories\StaticPage\StaticPageRepository;
use Illuminate\Database\Eloquent\Collection;

final class StaticPageService
{
    public function __construct(
        private readonly StaticPageRepository $repository
    ) {}

    public function getBySlug(string $slug): ?StaticPage
    {
        return $this->repository->findBySlug($slug);
    }

    public function getActivePages(): Collection
    {
        return $this->repository->getActive();
    }
}
