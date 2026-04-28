<?php
// app\Services\Web\HomeService.php
declare(strict_types=1);

namespace App\Services\Web;

use App\Models\ApplicationSetting;
use App\Repositories\Certification\CertificationRepository;
use App\Repositories\CertifiedCenter\CertifiedCenterRepository;
use App\Repositories\Trainer\TrainerRepository;
use App\Services\StaticPage\StaticPageService;

final class HomeService
{
    public function __construct(
        private readonly CertificationRepository   $certificationRepository,
        private readonly TrainerRepository         $trainerRepository,
        private readonly CertifiedCenterRepository $centerRepository,
        private readonly StaticPageService         $staticPageService,
    ) {}

    public function getHomeData(): array
    {
        return [
            'statistics'   => $this->buildStatistics(),
            'testimonials' => $this->getTestimonials(),
            'aboutPage'    => $this->staticPageService->getBySlug('about-us'),
        ];
    }

    private function buildStatistics(): array
    {
        return [
            'certifications' => $this->certificationRepository->getTotalCount(),
            'trainers'       => $this->trainerRepository->countActive(),
            'centers'        => $this->centerRepository->countActive(),
        ];
    }

    private function getTestimonials(): array
    {
        $raw = ApplicationSetting::get('home_testimonials', '[]');

        return json_decode((string) $raw, true) ?? [];
    }
}
