<?php

// app\Services\Web\HomeService.php
declare(strict_types=1);

namespace App\Services\Web;

use App\Models\ApplicationSetting;
use App\Repositories\Certification\CertificationRepository;
use App\Repositories\CertifiedCenter\CertifiedCenterRepository;
use App\Repositories\Trainer\TrainerRepository;
use App\Services\Blog\BlogPostService;
use App\Services\StaticPage\StaticPageService;
use Illuminate\Support\Facades\Cache;

final class HomeService
{
    public function __construct(
        private readonly CertificationRepository $certificationRepository,
        private readonly TrainerRepository $trainerRepository,
        private readonly CertifiedCenterRepository $centerRepository,
        private readonly StaticPageService $staticPageService,
        private readonly BlogPostService $blogPostService
    ) {}

    public function getHomeData(): array
    {
        return [
            'statistics' => $this->buildStatistics(),
            'testimonials' => $this->getTestimonials(),
            'aboutPage' => $this->staticPageService->getBySlug('about-us'),
            'servicesPage' => $this->staticPageService->getBySlug('our-services'),
            'blogPosts' => $this->blogPostService->getLatest(3),
        ];
    }

    private function buildStatistics(): array
    {
        return [
            'certifications' => Cache::rememberForever('home_stats_certifications', fn () => $this->certificationRepository->getTotalCount()),
            'trainers' => Cache::rememberForever('home_stats_trainers', fn () => $this->trainerRepository->countActive()),
            'centers' => Cache::rememberForever('home_stats_centers', fn () => $this->centerRepository->countActive()),
        ];
    }

    private function getTestimonials(): array
    {
        $value = ApplicationSetting::get('home_testimonials', []);

        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);

            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }
}
