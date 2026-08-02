<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Trainee;
use Illuminate\Support\Facades\Cache;

class TraineeObserver
{
    public function saved(Trainee $trainee): void
    {
        Cache::forget('home_stats_certifications');
    }

    public function deleted(Trainee $trainee): void
    {
        Cache::forget('home_stats_certifications');
    }
}
