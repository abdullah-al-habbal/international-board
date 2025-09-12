<?php

declare(strict_types=1);

namespace App\Models\Traits\AccreditationRequest;

use App\Enums\AccreditationStatus;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait AccreditationRequestScopes
{
    #[Scope]
    protected function ofStatus(Builder $query, AccreditationStatus|string $status): void
    {
        $query->where('status', $status instanceof AccreditationStatus ? $status->value : $status);
    }

    #[Scope]
    protected function pending(Builder $query): void
    {
        $query->where('status', AccreditationStatus::Pending->value);
    }

    #[Scope]
    protected function approved(Builder $query): void
    {
        $query->where('status', AccreditationStatus::Approved->value);
    }

    #[Scope]
    protected function rejected(Builder $query): void
    {
        $query->where('status', AccreditationStatus::Rejected->value);
    }

    #[Scope]
    protected function underReview(Builder $query): void
    {
        $query->where('status', AccreditationStatus::UnderReview->value);
    }

    #[Scope]
    protected function forCenter(Builder $query, int $centerId): void
    {
        $query->where('certified_center_id', $centerId);
    }

    #[Scope]
    protected function reviewedBy(Builder $query, int $reviewerId): void
    {
        $query->where('reviewed_by', $reviewerId);
    }

    #[Scope]
    protected function requestedBetween(Builder $query, string $startDate, string $endDate): void
    {
        $query->whereBetween('requested_start_date', [$startDate, $endDate]);
    }

    #[Scope]
    protected function recentlyCreated(Builder $query): void
    {
        $query->orderBy('created_at', 'desc');
    }

    #[Scope]
    protected function recentlyReviewed(Builder $query): void
    {
        $query->orderBy('reviewed_at', 'desc');
    }
}
