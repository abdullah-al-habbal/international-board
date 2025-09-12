<?php

declare(strict_types=1);

namespace App\Services\AccreditationRequest;

use App\Repositories\AccreditationRequest\AccreditationRequestRepository;

final class AccreditationRequestService
{
    public function __construct(private readonly AccreditationRequestRepository $repo) {}

    public function getLatestForCenter(int $centerId)
    {
        return $this->repo->findByCenter($centerId);
    }

    public function getTotalCount(): int
    {
        return $this->repo->getTotalCount();
    }

    public function getCountByStatus(string $status): int
    {
        return $this->repo->getCountByStatus($status);
    }

    public function getPendingCount(): int
    {
        return $this->repo->getPendingCount();
    }

    public function getApprovedCount(): int
    {
        return $this->repo->getApprovedCount();
    }

    public function getRejectedCount(): int
    {
        return $this->repo->getRejectedCount();
    }

    public function getStatusCounts(): array
    {
        return $this->repo->getStatusCounts();
    }

    public function getPendingCountByCenter(int $centerId): int
    {
        return $this->repo->getPendingCountByCenter($centerId);
    }
}
