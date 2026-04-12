<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Certification;

class CertificationObserver
{
    public function creating(Certification $certification): void
    {
        if (empty($certification->accredited_serial_number)) {
            $certification->accredited_serial_number = 'CERT-' . strtoupper(uniqid());
        }
    }
}
