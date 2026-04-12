<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Certification;
use App\Models\CertifiedCenter;
use App\Models\Trainer;
use App\Observers\CertificationObserver;
use App\Observers\CertifiedCenterObserver;
use App\Observers\TrainerObserver;
use Illuminate\Support\ServiceProvider;

final class ObserverServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        CertifiedCenter::observe(CertifiedCenterObserver::class);
        Trainer::observe(TrainerObserver::class);
        Certification::observe(CertificationObserver::class);
    }
}
