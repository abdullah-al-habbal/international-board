<?php
// app\Http\Controllers\Web\Certification\CertificationController.php
declare(strict_types=1);

namespace App\Http\Controllers\Web\Certification;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Certification\CertificationSearchRequest;
use App\Services\Certification\CertificationService;
use App\Services\Seo\SeoService;
use Illuminate\View\View;

final class CertificationController extends Controller
{
    public function __construct(
        private readonly CertificationService $service,
        private readonly SeoService $seoService
    ) {}

    public function index(): View
    {
        $this->seoService->setMeta(
            __('web.pages.certifications.title'),
            __('web.pages.certifications.subtitle')
        );
        $stats = $this->service->getStatistics();
        return view('web.certifications.index', compact('stats'));
    }

    public function search(CertificationSearchRequest $request): View
    {
        $serial = $request->validated('serial');
        $certification = $serial ? $this->service->getBySerial($serial) : null;
        $notFound = $serial !== null && $serial !== '' && $certification === null;

        $this->seoService->setMeta(
            __('web.pages.certifications.title') . ($serial ? ": {$serial}" : ''),
            __('web.pages.certifications.subtitle')
        );

        return view('web.certifications.search', compact('certification', 'serial', 'notFound'));
    }

    public function show(string $serial): View
    {
        $certification = $this->service->getBySerial($serial);
        abort_if($certification === null, 404);

        $this->seoService->setMeta(
            "{$certification->trainee?->name} | " . __('web.labels.serial_number') . ": {$serial}",
            __('web.pages.certifications.subtitle')
        );

        return view('web.certifications.show', compact('certification'));
    }
}
