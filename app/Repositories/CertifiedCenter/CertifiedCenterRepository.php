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
            ->publiclyVisible()
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

    /** `certifications` is loaded by the caller, which needs it visibility-filtered. */
    public function findActiveById(int $id): ?CertifiedCenter
    {
        return $this->model
            ->newQuery()
            ->publiclyVisible()
            ->where('id', $id)
            ->with(['approvedDocumentTypes'])
            ->first();
    }

    public function countTotal(): int
    {
        return $this->model->newQuery()->count();
    }

    public function countExpiredAccreditation(): int
    {
        return $this->model->newQuery()->accreditationExpired()->count();
    }

    public function getStatistics(): array
    {
        return [
            'total_centers' => $this->model->newQuery()->publiclyVisible()->count(),
            'active_countries' => $this->model->newQuery()
                ->publiclyVisible()
                ->whereNotNull('country_id')
                ->distinct('country_id')
                ->count('country_id'),
        ];
    }
}
