<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Trainer;
use Illuminate\Support\Facades\Cache;

class TrainerObserver
{
    public function creating(Trainer $trainer): void
    {
        if (empty($trainer->accreditation_number)) {
            do {
                $candidate = 'IBVTQ'.now()->format('Ymd').random_int(10000, 99999);
            } while (Trainer::where('accreditation_number', $candidate)->exists());

            $trainer->accreditation_number = $candidate;
        }
    }

    public function saved(Trainer $trainer): void
    {
        Cache::forget('home_stats_trainers');
    }

    public function deleted(Trainer $trainer): void
    {
        Cache::forget('home_stats_trainers');
    }
}
