<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Membership;

use App\Http\Controllers\Controller;
use App\Services\Membership\MembershipService;
use App\Services\Seo\SeoService;
use Illuminate\Support\Str;
use Illuminate\View\View;

final class MembershipShowController extends Controller
{
    public function __construct(
        private readonly MembershipService $service,
        private readonly SeoService $seoService
    ) {}

    public function __invoke(int $id): View
    {
        $membership = $this->service->findActive($id);
        abort_if($membership === null, 404);

        $this->seoService->setMeta(
            $membership->getTranslation('title', app()->getLocale()),
            Str::limit(strip_tags((string) $membership->getTranslation('description', app()->getLocale())), 160)
        );

        return view('web.memberships.show', compact('membership'));
    }
}
