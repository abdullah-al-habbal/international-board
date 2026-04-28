<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Certification;
use Illuminate\Support\Facades\Cache;

class CertificationObserver
{
    public function saved(Certification $certification): void
    {
        Cache::forget('home_stats_certifications');
    }

    public function deleted(Certification $certification): void
    {
        Cache::forget('home_stats_certifications');
    }
}
