<?php

declare(strict_types=1);

namespace App\Services\CertifiedCenter;

use App\Enums\CenterStatus;
use App\Repositories\CertifiedCenter\CertifiedCenterRepository;

final class CertifiedCenterService
{
    public function __construct(private readonly CertifiedCenterRepository $repo) {}

    public function getAll()
    {
        return $this->repo->all();
    }

    public function getById(int $id)
    {
        return $this->repo->find($id);
    }

    public function getTotalCount(): int
    {
        return $this->repo->getTotalCount();
    }

    public function getActiveCount(): int
    {
        return $this->repo->getActiveCount();
    }

    public function getInactiveCount(): int
    {
        return $this->repo->getInactiveCount();
    }

    public function getCountByStatus(CenterStatus $status): int
    {
        return $this->repo->getCountByStatus($status);
    }

    public function getExpiredAccreditationCount(): int
    {
        return $this->repo->getExpiredAccreditationCount();
    }

    public function getAccreditationActiveCount(): int
    {
        return $this->repo->getAccreditationActiveCount();
    }

    public function getInactiveOrExpiredCount(): int
    {
        return $this->repo->getInactiveOrExpiredCount();
    }
}
