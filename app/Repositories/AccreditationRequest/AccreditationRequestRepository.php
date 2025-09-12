<?php

declare(strict_types=1);

namespace App\Repositories\AccreditationRequest;

use App\Enums\AccreditationStatus;
use App\Models\AccreditationRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final class AccreditationRequestRepository
{
    public function __construct(private readonly AccreditationRequest $model) {}

    public function findByCenter(int $centerId): ?AccreditationRequest
    {
        return $this->model->forCenter($centerId)->recentlyCreated()->first();
    }

    public function getTotalCount(): int
    {
        return $this->model->newQuery()->count();
    }

    public function getCountByStatus(AccreditationStatus|string $status): int
    {
        return $this->model->ofStatus($status)->count();
    }

    public function getPendingCount(): int
    {
        return $this->model->pending()->count();
    }

    public function getApprovedCount(): int
    {
        return $this->model->approved()->count();
    }

    public function getRejectedCount(): int
    {
        return $this->model->rejected()->count();
    }

    public function getUnderReviewCount(): int
    {
        return $this->model->underReview()->count();
    }

    public function getPendingCountByCenter(int $centerId): int
    {
        return $this->model->forCenter($centerId)->pending()->count();
    }

    public function getRecentRequestsByCenter(int $centerId, int $limit = 10): Collection
    {
        return $this->model
            ->forCenter($centerId)
            ->recentlyCreated()
            ->limit($limit)
            ->get();
    }

    public function getRequestsByStatus(AccreditationStatus $status): Collection
    {
        return $this->model
            ->ofStatus($status)
            ->recentlyCreated()
            ->get();
    }

    public function getRequestsReviewedBy(int $reviewerId): Collection
    {
        return $this->model
            ->reviewedBy($reviewerId)
            ->recentlyReviewed()
            ->get();
    }

    public function getRequestsBetweenDates(string $startDate, string $endDate): Collection
    {
        return $this->model
            ->requestedBetween($startDate, $endDate)
            ->recentlyCreated()
            ->get();
    }

    public function getStatusCounts(): array
    {
        return $this->model->newQuery()
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    public function findPendingForCenter(int $centerId): ?AccreditationRequest
    {
        return $this->model
            ->forCenter($centerId)
            ->pending()
            ->recentlyCreated()
            ->first();
    }
}
