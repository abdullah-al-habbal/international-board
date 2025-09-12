<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\AccreditationRequest\AccreditationRequestRepository;
use App\Repositories\ApplicationSetting\ApplicationSettingRepository;
use App\Repositories\Certification\CertificationRepository;
use App\Repositories\CertifiedCenter\CertifiedCenterRepository;
use App\Repositories\Membership\MembershipRepository;
use App\Repositories\StaticPage\StaticPageRepository;
use App\Repositories\Trainer\TrainerRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Support\ServiceProvider;

final class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerRepositories();
    }

    public function boot(): void {}

    private function registerRepositories(): void
    {
        $this->app->singleton(AccreditationRequestRepository::class);
        $this->app->singleton(ApplicationSettingRepository::class);
        $this->app->singleton(CertificationRepository::class);
        $this->app->singleton(CertifiedCenterRepository::class);
        $this->app->singleton(MembershipRepository::class);
        $this->app->singleton(StaticPageRepository::class);
        $this->app->singleton(TrainerRepository::class);
        $this->app->singleton(UserRepository::class);
    }
}
