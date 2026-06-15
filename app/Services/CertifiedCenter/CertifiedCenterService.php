<?php
declare(strict_types=1);

namespace App\Services\CertifiedCenter;

use App\Models\CertifiedCenter;
use App\Models\Country;
use App\Repositories\CertifiedCenter\CertifiedCenterRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

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

    /**
     * Active countries that have at least one active certified center (for index filters).
     *
     * @return Collection<int, Country>
     */
    public function getFilterCountries(): Collection
    {
        $countryIds = CertifiedCenter::query()
            ->where('is_active', true)
            ->whereNotNull('country_id')
            ->distinct()
            ->pluck('country_id');

        return Country::query()
            ->where('is_active', true)
            ->whereIn('id', $countryIds)
            ->get()
            ->sortBy(fn (Country $country): string => (string) $country->name)
            ->values();
    }

    public function getStatistics(): array
    {
        return $this->repo->getStatistics();
    }
}
