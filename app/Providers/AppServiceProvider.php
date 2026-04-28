<?php

declare(strict_types=1);

namespace App\Providers;

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

        \App\Models\Certification::observe(\App\Observers\CertificationObserver::class);
        \App\Models\Trainer::observe(\App\Observers\TrainerObserver::class);
        \App\Models\CertifiedCenter::observe(\App\Observers\CertifiedCenterObserver::class);
    }
}
