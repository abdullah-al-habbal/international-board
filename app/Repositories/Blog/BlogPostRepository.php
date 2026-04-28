<?php
declare(strict_types=1);

namespace App\Repositories\Blog;

use App\Models\BlogPost;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final class BlogPostRepository
{
    public function __construct(private readonly BlogPost $model) {}

    public function paginatePublished(int $perPage = 12): LengthAwarePaginator
    {
        return $this->model
            ->where('is_published', true)
            ->orderByDesc('published_at')
            ->paginate($perPage);
    }

    public function findBySlug(string $slug): ?BlogPost
    {
        return $this->model
            ->where('slug', $slug)
            ->where('is_published', true)
            ->first();
    }

    public function getLatest(int $limit = 3): Collection
    {
        return $this->model
            ->where('is_published', true)
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }
}
