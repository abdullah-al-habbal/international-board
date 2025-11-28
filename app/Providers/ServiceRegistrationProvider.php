<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\AccreditationRequest\AccreditationRequestService;
use App\Services\ApplicationSetting\ApplicationSettingService;
use App\Services\Certification\CertificationService;
use App\Services\CertifiedCenter\CertifiedCenterService;
use App\Services\Membership\MembershipService;
use App\Services\StaticPage\StaticPageService;
use App\Services\Stats\CenterStatsService;
use App\Services\Stats\StatsService;
use App\Services\Trainer\TrainerService;
use App\Services\User\UserService;
use Illuminate\Support\ServiceProvider;

final class ServiceRegistrationProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerStatsServices();
        $this->registerDomainServices();
    }

    public function boot(): void {}

    private function registerStatsServices(): void
    {
        $this->app->singleton(StatsService::class);
        $this->app->singleton(CenterStatsService::class);
    }

    private function registerDomainServices(): void
    {
        $this->app->singleton(AccreditationRequestService::class);
        $this->app->singleton(ApplicationSettingService::class);
        $this->app->singleton(CertificationService::class);
        $this->app->singleton(CertifiedCenterService::class);
        $this->app->singleton(MembershipService::class);
        $this->app->singleton(StaticPageService::class);
        $this->app->singleton(TrainerService::class);
        $this->app->singleton(UserService::class);
    }
}
