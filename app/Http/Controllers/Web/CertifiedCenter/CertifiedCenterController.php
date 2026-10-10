<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\CertifiedCenter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\CertifiedCenter\CenterIndexRequest;
use App\Services\CertifiedCenter\CertifiedCenterService;
use App\Services\Seo\SeoService;
use Illuminate\View\View;

final class CertifiedCenterController extends Controller
{
    public function __construct(
        private readonly CertifiedCenterService $service,
        private readonly SeoService $seoService
    ) {}

    public function index(CenterIndexRequest $request): View
    {
        $this->seoService->setMeta(
            __('web.pages.centers.title'),
            __('web.pages.centers.subtitle')
        );

        $centers = $this->service->listActive(
            filters: $request->filters(),
            perPage: 12
        );

        $countries = $this->service->getFilterCountries();
        $stats = $this->service->getStatistics();

        return view('web.centers.index', compact('centers', 'countries', 'stats'));
    }

    public function show(int $id): View
    {
        $center = $this->service->findActive($id);
        abort_if($center === null, 404);

        $center->load([
            'country',
            // Kept as rows: the view lists the latest few.
            'certifications' => fn ($q) => $q->publiclyVisible(),
        ]);

        // Counted, never listed. Counts are unscoped totals so the public page
        // agrees with the admin panel; the listing above stays visibility-filtered.
        $center->loadCount([
            'certifications',
            'trainers',
            'approvedDocumentTypes',
            'accreditationRequests',
        ]);

        $this->seoService->setMeta(
            $center->name,
            $center->address ?? __('web.pages.centers.subtitle')
        );

        return view('web.centers.show', compact('center'));
    }
}
