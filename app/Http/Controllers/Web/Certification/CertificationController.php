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
        $accreditationNumber = $request->validated('accreditation_number');
        $certification = $accreditationNumber ? $this->service->getByAccreditationNumber($accreditationNumber) : null;
        $notFound = $accreditationNumber !== null && $accreditationNumber !== '' && $certification === null;
        $qrSvg = $certification ? $this->service->getVerificationQrSvg($certification) : null;

        $this->seoService->setMeta(
            __('web.pages.certifications.title').($accreditationNumber ? ": {$accreditationNumber}" : ''),
            __('web.pages.certifications.subtitle')
        );

        return view('web.certifications.search', compact('certification', 'accreditationNumber', 'notFound', 'qrSvg'));
    }

    public function show(string $accreditationNumber): View
    {
        $certification = $this->service->getByAccreditationNumber($accreditationNumber);
        abort_if($certification === null, 404);

        $this->seoService->setMeta(
            "{$certification->trainee?->name} | ".__('web.labels.accreditation_number').": {$accreditationNumber}",
            __('web.pages.certifications.subtitle')
        );

        $qrSvg = $this->service->getVerificationQrSvg($certification);

        return view('web.certifications.show', compact('certification', 'qrSvg'));
    }
}
