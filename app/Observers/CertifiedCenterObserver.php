<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\CertifiedCenter;
use Illuminate\Support\Facades\Cache;

class CertifiedCenterObserver
{
    public function saved(CertifiedCenter $center): void
    {
        Cache::forget('home_stats_centers');
    }

    public function deleted(CertifiedCenter $center): void
    {
        Cache::forget('home_stats_centers');
    }
}
