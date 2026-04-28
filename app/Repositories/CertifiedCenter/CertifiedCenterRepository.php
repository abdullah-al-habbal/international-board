<?php
// app\Repositories\CertifiedCenter\CertifiedCenterRepository.php
declare(strict_types=1);

namespace App\Repositories\CertifiedCenter;

use App\Models\CertifiedCenter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class CertifiedCenterRepository
{
    public function __construct(private readonly CertifiedCenter $model)
    {
    }

    public function paginateActive(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        return $this->model
            ->newQuery()
            ->where('is_active', true)
            ->when(
                isset($filters['search']) && $filters['search'] !== '',
                fn($q) => $q->where('name', 'like', "%{$filters['search']}%")
            )
            ->when(
                isset($filters['country_id']) && $filters['country_id'] !== '',
                fn($q) => $q->where('country_id', $filters['country_id'])
            )
            ->with(['country', 'approvedDocumentTypes.documentType'])
            ->paginate($perPage);
    }

    public function findActiveById(int $id): ?CertifiedCenter
    {
        return $this->model
            ->newQuery()
            ->where('id', $id)
            ->where('is_active', true)
            ->with(['country', 'approvedDocumentTypes.documentType', 'certifications'])
            ->first();
    }

    public function countActive(): int
    {
        return $this->model->newQuery()->where('is_active', true)->count();
    }
}
