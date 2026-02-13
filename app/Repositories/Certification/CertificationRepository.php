<?php

declare(strict_types=1);

namespace App\Repositories\Certification;

use App\Models\Certification;
use Illuminate\Database\Eloquent\Collection;

final class CertificationRepository
{
    public function __construct(private readonly Certification $model)
    {
    }

    public function findByDocumentCode(string $code): ?Certification
    {
        return $this->model->byDocumentCode($code)->first();
    }

    public function latest(int $limit = 10): Collection
    {
        return $this->model->recentlyCreated()->take($limit)->get();
    }

    public function getTotalCount(): int
    {
        return $this->model->newQuery()->count();
    }

    public function getCountThisMonth(): int
    {
        return $this->model->createdThisMonth()->count();
    }

    public function getCountByDateRange(\DateTime $startDate, \DateTime $endDate): int
    {
        return $this->model->betweenDates($startDate, $endDate)->count();
    }

    public function getCountByDocumentType(int|string $type): int
    {
        return $this->model->ofType($type)->count();
    }

    public function getTotalCountByCenter(int $centerId): int
    {
        return $this->model->forCenter($centerId)->count();
    }

    public function getCountThisMonthByCenter(int $centerId): int
    {
        return $this->model->forCenter($centerId)->createdThisMonth()->count();
    }

    public function getCertificationsByCenter(int $centerId, int $limit = 50): Collection
    {
        return $this->model
            ->forCenter($centerId)
            ->recentlyCreated()
            ->limit($limit)
            ->get();
    }

    public function getCertificationsByDocumentType(int|string $type): Collection
    {
        return $this->model
            ->ofType($type)
            ->recentlyCreated()
            ->get();
    }

    public function searchByTraineeName(string $name): Collection
    {
        return $this->model
            ->byTraineeName($name)
            ->recentlyCreated()
            ->get();
    }

    public function searchByTrainerName(string $name): Collection
    {
        return $this->model
            ->byTrainerName($name)
            ->recentlyCreated()
            ->get();
    }

    public function getCertificationsByNationality(string $nationality): Collection
    {
        return $this->model
            ->byNationality($nationality)
            ->recentlyCreated()
            ->get();
    }

    public function getMonthlyCounts(?int $year = null): array
    {
        $year ??= now()->year;

        $monthlyCounts = $this->model->newQuery()
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        return collect(range(1, 12))
            ->map(fn(int $month) => $monthlyCounts->get($month, 0))
            ->toArray();
    }

    public function getMonthlyCountsByCenter(int $centerId, ?int $year = null): array
    {
        $year ??= now()->year;

        $monthlyCounts = $this->model->forCenter($centerId)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        return collect(range(1, 12))
            ->map(fn(int $month) => $monthlyCounts->get($month, 0))
            ->toArray();
    }
}
