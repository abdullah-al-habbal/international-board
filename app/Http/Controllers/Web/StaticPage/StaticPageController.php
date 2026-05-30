<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\StaticPage;

use App\Http\Controllers\Controller;
use App\Services\Seo\SeoService;
use App\Services\StaticPage\StaticPageService;
use Illuminate\Support\Str;
use Illuminate\View\View;

final class StaticPageController extends Controller
{
    public function __construct(
        private readonly StaticPageService $service,
        private readonly SeoService $seoService
    ) {}

    public function show(string $slug): View
    {
        $page = $this->service->getBySlug($slug);
        abort_if($page === null, 404);

        $this->seoService->setMeta(
            $page->getTranslation('title', app()->getLocale()),
            Str::limit(strip_tags((string) $page->getTranslation('content', app()->getLocale())), 160)
        );

        return view('web.pages.show', compact('page'));
    }
}
