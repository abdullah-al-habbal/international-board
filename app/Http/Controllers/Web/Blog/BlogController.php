<?php
declare(strict_types=1);

namespace App\Http\Controllers\Web\Blog;

use App\Http\Controllers\Controller;
use App\Services\Blog\BlogPostService;
use Illuminate\Contracts\View\View;

final class BlogController extends Controller
{
    public function __construct(private readonly BlogPostService $service) {}

    public function index(): View
    {
        $posts = $this->service->getPublishedPaginated();
        return view('web.blog.index', compact('posts'));
    }

    public function show(string $slug): View
    {
        $post = $this->service->getBySlug($slug);
        abort_if(! $post, 404);
        return view('web.blog.show', compact('post'));
    }
}
