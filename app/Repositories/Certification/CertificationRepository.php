<?php

declare(strict_types=1);

namespace App\Repositories\Certification;

use App\Models\Certification;
use App\Models\CertifiedCenter;
use Illuminate\Database\Eloquent\Collection;

final class CertificationRepository
{
    public function __construct(private readonly Certification $model) {}

    public function findByDocumentCode(string $code): ?Certification
    {
        return $this->model->with([
            'creator',
            'documentable',
            'assignedTrainer',
            'trainee:id,name',
            'country:id,name',
        ])
            ->byDocumentCode($code)
            ->first();
    }

    public function findBySerial(string $serial): ?Certification
    {
        return $this->model->with([
            'creator',
            'documentable',
            'assignedTrainer',
            'trainee:id,name',
            'country:id,name',
        ])
            ->where(function ($query) use ($serial): void {
                $query->where('accredited_serial_number', $serial)
                    ->orWhere('document_code', $serial);
            })
            ->first();
    }

    public function latest(int $limit = 10): Collection
    {
        return $this->model->with([
            'creator',
            'assignedTrainer',
            'trainee:id,name',
        ])
            ->recentlyCreated()
            ->take($limit)
            ->get();
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

    public function getTotalCountByCenter(int $centerId): int
    {
        return $this->model
            ->where('creator_type', CertifiedCenter::class)
            ->where('creator_id', $centerId)
            ->count();
    }

    public function getCountThisMonthByCenter(int $centerId): int
    {
        return $this->model
            ->where('creator_type', CertifiedCenter::class)
            ->where('creator_id', $centerId)
            ->createdThisMonth()
            ->count();
    }

    public function getCertificationsByCenter(int $centerId, int $limit = 50): Collection
    {
        return $this->model
            ->where('creator_type', CertifiedCenter::class)
            ->where('creator_id', $centerId)
            ->recentlyCreated()
            ->limit($limit)
            ->get();
    }

    public function searchByTraineeName(string $name): Collection
    {
        return $this->model
            ->byTraineeName($name)
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
            ->map(fn (int $month) => $monthlyCounts->get($month, 0))
            ->toArray();
    }

    public function getMonthlyCountsByCenter(int $centerId, ?int $year = null): array
    {
        $year ??= now()->year;

        $monthlyCounts = $this->model
            ->where('creator_type', CertifiedCenter::class)
            ->where('creator_id', $centerId)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        return collect(range(1, 12))
            ->map(fn (int $month) => $monthlyCounts->get($month, 0))
            ->toArray();
    }

    public function getStatistics(): array
    {
        $total = $this->model->newQuery()->count();
        $distinctCountries = $this->model->newQuery()
            ->whereNotNull('country_id')
            ->distinct('country_id')
            ->count('country_id');
        $distinctTrainees = $this->model->newQuery()
            ->whereNotNull('trainee_id')
            ->distinct('trainee_id')
            ->count('trainee_id');
        $distinctTrainers = $this->model->newQuery()
            ->whereNotNull('assigned_trainer_id')
            ->distinct('assigned_trainer_id')
            ->count('assigned_trainer_id');
        $byCreatorRaw = $this->model->newQuery()
            ->selectRaw('creator_type, count(*) as count')
            ->groupBy('creator_type')
            ->pluck('count', 'creator_type')
            ->toArray();

        return [
            'total' => $total,
            'countries' => $distinctCountries,
            'trainees' => $distinctTrainees,
            'trainers' => $distinctTrainers,
            'by_creator' => $byCreatorRaw,
        ];
    }
}
