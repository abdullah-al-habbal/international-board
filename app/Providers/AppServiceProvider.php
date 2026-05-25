<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Certification;
use App\Models\CertifiedCenter;
use App\Models\Trainer;
use App\Observers\CertificationObserver;
use App\Observers\CertifiedCenterObserver;
use App\Observers\TrainerObserver;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['ar', 'en'])
                ->visible(outsidePanels: false);
        });

        Paginator::useBootstrapFive();

        Certification::observe(CertificationObserver::class);
        Trainer::observe(TrainerObserver::class);
        CertifiedCenter::observe(CertifiedCenterObserver::class);
    }
}
