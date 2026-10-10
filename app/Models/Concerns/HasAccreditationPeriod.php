<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Enums\AccreditationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Accreditation validity, shared by Trainer and CertifiedCenter.
 *
 * An accreditation period is a DATE range, even though it is stored in
 * datetime columns. Every screen renders it as `Y-m-d` and the domain is
 * measured in whole days, so the time component carries no meaning — in
 * practice it is whatever wall-clock time the form happened to be submitted at
 * (rows like `2027-04-30 19:05:52` are typical).
 *
 * Comparing such a value against `now()` expired credentials part-way through
 * their own final day: a period displayed as valid through 2027-04-30 died at
 * 19:05 that afternoon, locking the holder out of their panel.
 *
 * Every comparison here is therefore anchored to day boundaries — a period is
 * valid from the first instant of its start date through the last instant of
 * its end date. Anchors are applied to the comparison value rather than to the
 * column so the queries stay plain SQL and behave identically on SQLite and
 * MySQL.
 *
 * These are all wall-clock comparisons in the application timezone, so
 * APP_TIMEZONE must match the operators' timezone. See CLAUDE.md.
 *
 * @property Carbon|null $accreditation_period_start
 * @property Carbon|null $accreditation_period_end
 */
trait HasAccreditationPeriod
{
    abstract public function accreditationRequests(): HasMany;

    /**
     * Periods whose end date is behind us. A period ending today is NOT expired.
     *
     * Laravel discovers #[Scope] only on methods declared by the model itself,
     * not on inherited trait methods, so each model declares a thin scope that
     * calls this.
     */
    protected function applyAccreditationExpiredScope(Builder $query): void
    {
        $query->whereNotNull('accreditation_period_end')
            ->where('accreditation_period_end', '<', today()->startOfDay());
    }

    /** Periods covering today, inclusive of both the start and the end date. */
    protected function applyAccreditationActiveScope(Builder $query): void
    {
        $query->whereNotNull('accreditation_period_start')
            ->whereNotNull('accreditation_period_end')
            ->where('accreditation_period_start', '<=', today()->endOfDay())
            ->where('accreditation_period_end', '>=', today()->startOfDay());
    }

    public function isAccreditationActive(): bool
    {
        $start = $this->accreditation_period_start;
        $end = $this->accreditation_period_end;

        if (! $start || ! $end) {
            return false;
        }

        // copy() matters: these are mutable Carbon instances held by the model,
        // and mutating them here would corrupt the attribute for the rest of
        // the request.
        return now()->betweenIncluded($start->copy()->startOfDay(), $end->copy()->endOfDay());
    }

    public function hasApprovedAccreditationRequest(): bool
    {
        return $this->accreditationRequests()
            ->where('status', AccreditationStatus::Approved)
            ->exists();
    }

    public function hasActiveAccreditationRequest(): bool
    {
        return $this->activeAccreditationRequestQuery()->exists();
    }

    public function activeAccreditationRequest(): ?Model
    {
        return $this->activeAccreditationRequestQuery()->latest()->first();
    }

    public function accreditationBlockMessage(): ?string
    {
        $request = $this->activeAccreditationRequest();

        if (! $request) {
            return null;
        }

        return in_array($request->status, [
            AccreditationStatus::Pending,
            AccreditationStatus::UnderReview,
        ], true)
            ? __('accreditation.errors.pending_request_exists')
            : __('accreditation.errors.approved_request_exists');
    }

    public function hasApprovedNonExpiredRequest(): bool
    {
        return $this->accreditationRequests()
            ->where('status', AccreditationStatus::Approved)
            ->where('accreditation_end_date', '>=', today()->startOfDay())
            ->exists();
    }

    public function canPerformActions(): bool
    {
        return $this->hasApprovedNonExpiredRequest();
    }

    public function accreditationBlockReason(): ?string
    {
        if (! $this->hasApprovedNonExpiredRequest()) {
            return __('accreditation.blocked.no_approved_request');
        }

        if (! $this->hasActiveAccreditationRequest()) {
            return __('accreditation.blocked.period_expired');
        }

        return null;
    }

    private function activeAccreditationRequestQuery(): HasMany
    {
        $dayStart = today()->startOfDay();
        $dayEnd = today()->endOfDay();

        return $this->accreditationRequests()
            ->where(function (Builder $query) use ($dayStart, $dayEnd): void {
                $query->whereIn('status', [
                    AccreditationStatus::Pending->value,
                    AccreditationStatus::UnderReview->value,
                ])->orWhere(function (Builder $q) use ($dayStart, $dayEnd): void {
                    $q->where('status', AccreditationStatus::Approved->value)
                        ->where('accreditation_start_date', '<=', $dayEnd)
                        ->where('accreditation_end_date', '>=', $dayStart);
                });
            });
    }
}
