<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\CertifiedCenter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CertifiedCenterObserver
{
    public function creating(CertifiedCenter $center): void
    {
        if (empty($center->accreditation_number)) {
            $center->accreditation_number = (string) Str::orderedUuid();
        }
    }

    public function saved(CertifiedCenter $center): void
    {
        Cache::forget('home_stats_centers');
    }

    public function deleted(CertifiedCenter $center): void
    {
        Cache::forget('home_stats_centers');
    }
}
