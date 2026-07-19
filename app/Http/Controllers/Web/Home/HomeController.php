<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Home;

use App\Http\Controllers\Controller;
use App\Services\Seo\SeoService;
use App\Services\Web\HomeService;
use Illuminate\View\View;

final class HomeController extends Controller
{
    public function __construct(
        private readonly HomeService $service,
        private readonly SeoService $seoService
    ) {}

    public function __invoke(): View
    {
        $this->seoService->setMeta(
            __('web.pages.home.title'),
            __('web.pages.home.hero_text')
        );

        return view('home_page', $this->service->getHomeData());
    }
}
