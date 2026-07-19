<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Blog;

use App\Http\Controllers\Controller;
use App\Services\Blog\BlogPostService;
use App\Services\Seo\SeoService;
use Illuminate\Contracts\View\View;

final class BlogController extends Controller
{
    public function __construct(
        private readonly BlogPostService $service,
        private readonly SeoService $seoService
    ) {}

    public function index(): View
    {
        $this->seoService->setMeta(
            __('web.pages.blog.title'),
            __('web.pages.blog.subtitle')
        );

        $posts = $this->service->getPublishedPaginated();

        return view('web.blog.index', compact('posts'));
    }

    public function show(string $slug): View
    {
        $post = $this->service->getBySlug($slug);
        abort_if(! $post, 404);

        $this->seoService->setMeta(
            $post->title,
            $post->excerpt ?? strip_tags((string) $post->content)
        );

        return view('web.blog.show', compact('post'));
    }
}
