<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\CenterAccreditationRequest;
use App\Models\Certification;
use App\Models\CertifiedCenter;
use App\Models\Trainee;
use App\Models\Trainer;
use App\Models\TrainerAccreditationRequest;
use App\Observers\CenterAccreditationRequestObserver;
use App\Observers\CertificationObserver;
use App\Observers\CertifiedCenterObserver;
use App\Observers\TraineeObserver;
use App\Observers\TrainerAccreditationRequestObserver;
use App\Observers\TrainerObserver;
use Illuminate\Support\ServiceProvider;

final class ObserverServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        CenterAccreditationRequest::observe(CenterAccreditationRequestObserver::class);
        CertifiedCenter::observe(CertifiedCenterObserver::class);
        Trainer::observe(TrainerObserver::class);
        TrainerAccreditationRequest::observe(TrainerAccreditationRequestObserver::class);
        Certification::observe(CertificationObserver::class);
        Trainee::observe(TraineeObserver::class);
    }
}
