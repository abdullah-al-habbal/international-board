<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\StaticPage;

use App\Services\Seo\SeoService;
use App\Services\StaticPage\StaticPageService;
use Illuminate\View\View;

final class StaticPageController
{
    public function __construct(
        private readonly StaticPageService $service,
        private readonly SeoService $seoService
    ) {}

    public function show(string $slug): View
    {
        $page = $this->service->getBySlug($slug);
        abort_if(!$page, 404);

        $this->seoService->setMeta(
            $page->title,
            $page->excerpt ?? strip_tags((string) $page->content)
        );

        return view('web.pages.show', compact('page'));
    }
}
