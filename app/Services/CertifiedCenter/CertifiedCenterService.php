<?php
declare(strict_types=1);

namespace App\Services\CertifiedCenter;

use App\Models\CertifiedCenter;
use App\Repositories\CertifiedCenter\CertifiedCenterRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class CertifiedCenterService
{
    public function __construct(private readonly CertifiedCenterRepository $repo)
    {
    }

    public function listActive(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        return $this->repo->paginateActive($filters, $perPage);
    }

    public function findActive(int $id): ?CertifiedCenter
    {
        return $this->repo->findActiveById($id);
    }

    public function getTotalCount(): int
    {
        return $this->repo->countTotal();
    }

    public function getActiveCount(): int
    {
        return $this->repo->countActive();
    }

    public function getInactiveCount(): int
    {
        return $this->repo->countInactive();
    }

    public function getExpiredAccreditationCount(): int
    {
        return $this->repo->countExpiredAccreditation();
    }
}
