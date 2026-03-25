<?php
// app/Services/Certification/CertificationService.php

declare(strict_types=1);

namespace App\Services\Certification;

use App\Repositories\Certification\CertificationRepository;
use App\Models\Certification;

final class CertificationService
{
    public function __construct(private readonly CertificationRepository $repo) {}

    public function getByCode(string $code): ?Certification
    {
        return $this->repo->findByDocumentCode($code);
    }

    public function getLatest()
    {
        return $this->repo->latest();
    }

    public function getTotalCount(): int
    {
        return $this->repo->getTotalCount();
    }

    public function getCountThisMonth(): int
    {
        return $this->repo->getCountThisMonth();
    }

    public function getCountByDateRange(\DateTime $startDate, \DateTime $endDate): int
    {
        return $this->repo->getCountByDateRange($startDate, $endDate);
    }

    public function getCountByDocumentType(int|string $type): int
    {
        return $this->repo->getCountByDocumentType($type);
    }

    public function getMonthlyCounts(?int $year = null): array
    {
        return $this->repo->getMonthlyCounts($year);
    }

    public function getMonthlyCountsByCenter(int $centerId, ?int $year = null): array
    {
        return $this->repo->getMonthlyCountsByCenter($centerId, $year);
    }

    public function getTotalCountByCenter(int $centerId): int
    {
        return $this->repo->getTotalCountByCenter($centerId);
    }

    public function getCountThisMonthByCenter(int $centerId): int
    {
        return $this->repo->getCountThisMonthByCenter($centerId);
    }
}
