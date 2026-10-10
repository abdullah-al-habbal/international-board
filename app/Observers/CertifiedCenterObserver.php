<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\CertifiedCenter;
use Illuminate\Support\Facades\Cache;

class CertifiedCenterObserver
{
    public function creating(CertifiedCenter $center): void
    {
        if (empty($center->accreditation_number)) {
            do {
                $candidate = 'IBVTQ'.now()->format('Ymd').random_int(10000, 99999);
            } while (CertifiedCenter::where('accreditation_number', $candidate)->exists());

            $center->accreditation_number = $candidate;
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
