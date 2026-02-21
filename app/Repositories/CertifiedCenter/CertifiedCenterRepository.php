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

    public function getLastAccreditationNumberForYear(int $year): ?string
    {
        $prefix = "CTR-{$year}-";

        return $this->model->newQuery()
            ->where('accreditation_number', 'like', $prefix . '%')
            ->orderBy('accreditation_number', 'desc')
            ->value('accreditation_number');
    }

    public function accreditationNumberExists(string $accreditationNumber): bool
    {
        return $this->model->newQuery()
            ->where('accreditation_number', $accreditationNumber)
            ->exists();
    }

    public function getExpiredAccreditationCount(): int
    {
        return $this->model->accreditationExpired()->count();
    }

    public function getAccreditationActiveCount(): int
    {
        return $this->model->accreditationActive()->count();
    }

    public function getInactiveOrExpiredCount(): int
    {
        return $this->model->newQuery()
            ->where(function ($query) {
                $query->inactive()
                    ->orWhere(function ($q) {
                        $q->accreditationExpired();
                    });
            })
            ->count();
    }
}
