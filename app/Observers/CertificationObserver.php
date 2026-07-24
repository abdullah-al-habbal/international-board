<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Certification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CertificationObserver
{
    public function creating(Certification $certification): void
    {
        if (empty($certification->document_code)) {
            $certification->document_code = 'CERT-'.now()->format('Ymd').'-'.strtoupper(Str::random(4));
        }

        if (empty($certification->accredited_serial_number)) {
            $certification->accredited_serial_number = 'SN-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));
        }

        if (empty($certification->accreditation_number)) {
            do {
                $candidate = 'IBVTQ'.now()->format('Ymd').random_int(10000, 99999);
            } while (Certification::where('accreditation_number', $candidate)->exists());

            $certification->accreditation_number = $candidate;
        }
    }

    public function saved(Certification $certification): void
    {
        Cache::forget('home_stats_certifications');
    }

    public function deleted(Certification $certification): void
    {
        Cache::forget('home_stats_certifications');
    }
}
