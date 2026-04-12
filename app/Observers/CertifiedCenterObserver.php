<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\CertifiedCenter;

class CertifiedCenterObserver
{
    public function creating(CertifiedCenter $center): void
    {
        if (empty($center->accreditation_number)) {
            $center->accreditation_number = 'CTR-' . strtoupper(uniqid());
        }
    }
}
