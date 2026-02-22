<?php

// filePath: app/Models/Traits/AccreditationRequest/AccreditationRequestScopes.php
declare(strict_types=1);

namespace App\Models\Traits\AccreditationRequest;

use App\Enums\AccreditationStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait AccreditationRequestScopes
{
    /**
     * Requests that are Approved and whose date window covers now.
     * This is the canonical "active subscription" definition.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('status', AccreditationStatus::Approved)
            ->where('requested_start_date', '<=', Carbon::now())
            ->where('requested_end_date', '>=', Carbon::now());
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', AccreditationStatus::Pending);
    }

    public function scopeUnderReview(Builder $query): Builder
    {
        return $query->where('status', AccreditationStatus::UnderReview);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', AccreditationStatus::Approved);
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', AccreditationStatus::Rejected);
    }

    public function scopeForCenter(Builder $query, int $centerId): Builder
    {
        return $query->where('certified_center_id', $centerId);
    }
}
