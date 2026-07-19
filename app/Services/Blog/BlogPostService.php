<?php

declare(strict_types=1);

namespace App\Services\Blog;

use App\Models\BlogPost;
use App\Repositories\Blog\BlogPostRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final class BlogPostService
{
    public function __construct(private readonly BlogPostRepository $repository) {}

    public function getPublishedPaginated(int $perPage = 12): LengthAwarePaginator
    {
        return $this->repository->paginatePublished($perPage);
    }

    public function getBySlug(string $slug): ?BlogPost
    {
        return $this->repository->findBySlug($slug);
    }

    public function getLatest(int $limit = 3): Collection
    {
        return $this->repository->getLatest($limit);
    }
}
