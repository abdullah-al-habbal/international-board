<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\StaticPage;

use App\Services\StaticPage\StaticPageService;
use Illuminate\View\View;

final class StaticPageController
{
    public function __construct(private readonly StaticPageService $service) {}

    public function show(string $slug): View
    {
        $page = $this->service->getBySlug($slug);
        abort_if(!$page, 404);
        return view('web.page.show', compact('page'));
    }
}
