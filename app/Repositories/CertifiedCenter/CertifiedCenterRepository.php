<?php

declare(strict_types=1);

namespace App\Repositories\CertifiedCenter;

use App\Enums\CenterStatus;
use App\Models\CertifiedCenter;

final class CertifiedCenterRepository
{
    public function __construct(private readonly CertifiedCenter $model) {}

    public function all()
    {
        return $this->model->ofStatus(CenterStatus::Active)->get();
    }

    public function find(int $id): ?CertifiedCenter
    {
        return $this->model->find($id);
    }

    public function getTotalCount(): int
    {
        return $this->model->newQuery()->count();
    }

    public function getActiveCount(): int
    {
        return $this->model->active()->count();
    }

    public function getInactiveCount(): int
    {
        return $this->model->inactive()->count();
    }

    public function getCountByStatus(CenterStatus $status): int
    {
        return $this->model->ofStatus($status)->count();
    }
}
