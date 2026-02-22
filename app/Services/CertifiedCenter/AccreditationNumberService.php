<?php

declare(strict_types=1);

namespace App\Services\CertifiedCenter;

use App\Repositories\CertifiedCenter\CertifiedCenterRepository;

final class AccreditationNumberService
{
    public function __construct(
        private readonly CertifiedCenterRepository $repository
    ) {}

    public function generate(): string
    {
        $year = now()->year;
        $prefix = "CTR-{$year}-";

        $lastNumber = $this->repository->getLastAccreditationNumberForYear($year);

        $number = $lastNumber
            ? ((int) substr($lastNumber, strlen($prefix)) + 1)
            : 1;

        $accreditationNumber = $prefix.str_pad((string) $number, 5, '0', STR_PAD_LEFT);

        while ($this->repository->accreditationNumberExists($accreditationNumber)) {
            $number++;
            $accreditationNumber = $prefix.str_pad((string) $number, 5, '0', STR_PAD_LEFT);
        }

        return $accreditationNumber;
    }
}
