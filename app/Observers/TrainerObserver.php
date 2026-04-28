<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Trainer;
use Illuminate\Support\Facades\Cache;

class TrainerObserver
{
    public function saved(Trainer $trainer): void
    {
        Cache::forget('home_stats_trainers');
    }

    public function deleted(Trainer $trainer): void
    {
        Cache::forget('home_stats_trainers');
    }
}
