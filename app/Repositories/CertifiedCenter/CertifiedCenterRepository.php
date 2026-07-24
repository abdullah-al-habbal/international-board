<?php

// app\Repositories\CertifiedCenter\CertifiedCenterRepository.php
declare(strict_types=1);

namespace App\Repositories\CertifiedCenter;

use App\Models\CertifiedCenter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class CertifiedCenterRepository
{
    public function __construct(private readonly CertifiedCenter $model) {}

    public function paginateActive(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        return $this->model
            ->newQuery()
            ->when(
                isset($filters['search']) && $filters['search'] !== '',
                fn ($q) => $q->where('name', 'like', "%{$filters['search']}%")
            )
            ->when(
                isset($filters['country_id']) && $filters['country_id'] !== '',
                fn ($q) => $q->where('country_id', $filters['country_id'])
            )
            ->with(['country', 'approvedDocumentTypes'])
            ->paginate($perPage);
    }

    public function findActiveById(int $id): ?CertifiedCenter
    {
        return $this->model
            ->newQuery()
            ->where('id', $id)
            ->with(['country', 'approvedDocumentTypes', 'certifications'])
            ->first();
    }

    public function countTotal(): int
    {
        return $this->model->newQuery()->count();
    }

    public function countActive(): int
    {
        return $this->model->newQuery()->active()->count();
    }

    public function countInactive(): int
    {
        return $this->model->newQuery()->inactive()->count();
    }

    public function countExpiredAccreditation(): int
    {
        return $this->model->newQuery()->accreditationExpired()->count();
    }

    public function getStatistics(): array
    {
        return [
            'total_active_centers' => $this->countActive(),
            'active_countries' => $this->model->newQuery()
                ->whereNotNull('country_id')
                ->distinct('country_id')
                ->count('country_id'),
        ];
    }
}
