<?php

declare(strict_types=1);

namespace App\Services\StaticPage;

use App\Repositories\StaticPage\StaticPageRepository;

final class StaticPageService
{
    public function __construct(private readonly StaticPageRepository $repo) {}

    public function getBySlug(string $slug)
    {
        return $this->repo->findBySlug($slug);
    }
}
