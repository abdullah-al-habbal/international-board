<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Trainer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Trainer\TrainerIndexRequest;
use App\Services\ApplicationSetting\ApplicationSettingService;
use App\Services\Seo\SeoService;
use App\Services\Trainer\TrainerService;
use Illuminate\View\View;

final class TrainerController extends Controller
{
    public function __construct(
        private readonly TrainerService $service,
        private readonly SeoService $seoService,
        private readonly ApplicationSettingService $appSettingService
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
            'specializations',
            'certifications' => fn ($q) => $q->publiclyVisible(),
            'documentTypes',
            'accreditationRequests',
        ]);

        $this->seoService->setMeta(
            $trainerModel->name,
            $trainerModel->bio ?? __('web.pages.trainers.subtitle')
        );

        return view('web.trainers.show', ['trainer' => $trainerModel]);
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
