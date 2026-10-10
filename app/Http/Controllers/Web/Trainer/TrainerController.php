<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Trainer;

use App\Enums\DocumentTypeRequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Trainer\TrainerIndexRequest;
use App\Services\ApplicationSetting\ApplicationSettingService;
use App\Services\Certification\CertificationService;
use App\Services\Seo\SeoService;
use App\Services\Trainer\TrainerService;
use Illuminate\View\View;

final class TrainerController extends Controller
{
    public function __construct(
        private readonly TrainerService $service,
        private readonly SeoService $seoService,
        private readonly ApplicationSettingService $appSettingService,
        private readonly CertificationService $certificationService
    ) {}

    public function index(TrainerIndexRequest $request): View
    {
        $this->seoService->setMeta(
            __('web.pages.trainers.title'),
            __('web.pages.trainers.subtitle')
        );

        $trainers = $this->service->listActive(
            filters: $request->filters(),
            perPage: 12
        );

        $whatsappNumber = $this->appSettingService->getByKey('whatsapp_number');
        $stats = $this->service->getStatistics();

        return view('web.trainers.index', compact('trainers', 'whatsappNumber', 'stats'));
    }

    public function show(int $trainer): View
    {
        $trainerModel = $this->service->findActive($trainer);
        abort_if($trainerModel === null, 404);

        $trainerModel->load([
            'country',
            'center',
            'trainerRole',
            'specializations',
        ]);

        // Counted, never listed — so load counts rather than the rows themselves.
        $trainerModel->loadCount([
            'documentTypes' => fn ($q) => $q->where('status', DocumentTypeRequestStatus::Approved->value),
            'accreditationRequests',
            'specializations',
        ]);

        $this->seoService->setMeta(
            $trainerModel->name,
            $trainerModel->bio ?? __('web.pages.trainers.subtitle')
        );

        return view('web.trainers.show', [
            'trainer' => $trainerModel,
            'certificationsCount' => $this->certificationService
                ->countPubliclyVisibleForTrainer($trainerModel->id),
        ]);
    }

    public function evaluation(): View
    {
        $this->seoService->setMeta(
            __('web.pages.trainers.evaluation_title'),
            __('web.default_title')
        );

        $evaluationText = $this->service->getEvaluationText();

        return view('web.trainers.evaluation', compact('evaluationText'));
    }
}
