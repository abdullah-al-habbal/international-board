<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Membership;

use App\Http\Controllers\Controller;
use App\Services\ApplicationSetting\ApplicationSettingService;
use App\Services\Membership\MembershipService;
use App\Services\Seo\SeoService;
use Illuminate\View\View;

final class MembershipIndexController extends Controller
{
    public function __construct(
        private readonly MembershipService $service,
        private readonly SeoService $seoService,
        private readonly ApplicationSettingService $appSettingService
    ) {}

    public function __invoke(): View
    {
        $this->seoService->setMeta(
            __('web.pages.memberships.title'),
            __('web.pages.memberships.subtitle')
        );

        $memberships = $this->service->listAll();
        $intro = $this->getMembershipIntro();

        return view('web.memberships.index', compact('memberships', 'intro'));
    }

    private function getMembershipIntro(): string
    {
        $data = $this->appSettingService->getByKey('memberships_intro');

        if (is_array($data)) {
            $locale = app()->getLocale();

            return $data[$locale] ?? $data['ar'] ?? '';
        }

        return '';
    }
}
