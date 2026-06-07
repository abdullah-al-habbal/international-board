<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\AccreditationStatus;
use App\Enums\CenterStatus;
use App\Models\AccreditationRequest;
use App\Models\CertifiedCenter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AccreditationRequestObserver
{
    public function creating(AccreditationRequest $request): void
    {
        $this->assertNoDuplicateActiveRequest($request);
        $this->assertNoTimeOverlap($request);
    }

    public function updating(AccreditationRequest $request): void
    {
        if ($request->isDirty('status')) {
            Log::channel('accreditation')->info('[Center] AccreditationRequest status transition', [
                'request_id' => $request->id,
                'center_id' => $request->certified_center_id,
                'from_status' => $request->getOriginal('status') instanceof AccreditationStatus
                    ? $request->getOriginal('status')->value
                    : $request->getOriginal('status'),
                'to_status' => $request->status instanceof AccreditationStatus
                    ? $request->status->value
                    : $request->status,
                'reviewed_by' => $request->reviewed_by,
            ]);
        }

        if ($request->isDirty(['requested_start_date', 'requested_end_date'])) {
            $this->assertNoTimeOverlap($request, excludeSelf: true);
        }
    }

    public function updated(AccreditationRequest $request): void
    {
        if (!$request->wasChanged('status')) {
            return;
        }

        $status = $request->status instanceof AccreditationStatus
            ? $request->status
            : AccreditationStatus::from($request->status);

        // Safety net: fill reviewer info if the action didn't set them
        if ($status->isReviewed()) {
            $needsSave = false;
            if (empty($request->reviewed_by)) {
                $request->reviewed_by = Auth::id();
                $needsSave = true;
            }
            if (empty($request->reviewed_at)) {
                $request->reviewed_at = now();
                $needsSave = true;
            }
            if ($needsSave) {
                $request->saveQuietly();
            }
        }

        $center = $request->certifiedCenter;

        if (!$center) {
            Log::channel('accreditation')->warning('[Center] AccreditationRequest updated but center not found', [
                'request_id' => $request->id,
            ]);
            return;
        }

        match ($status) {
            AccreditationStatus::Approved => $this->handleApproved($request, $center),
            AccreditationStatus::Rejected => $this->handleRejected($request, $center),
            default => null,
        };
    }

    private function assertNoDuplicateActiveRequest(AccreditationRequest $request): void
    {
        $hasPendingOrUnderReview = AccreditationRequest::query()
            ->where('certified_center_id', $request->certified_center_id)
            ->whereIn('status', [
                AccreditationStatus::Pending->value,
                AccreditationStatus::UnderReview->value,
            ])
            ->exists();

        $hasCurrentlyActiveApproved = AccreditationRequest::query()
            ->where('certified_center_id', $request->certified_center_id)
            ->where('status', AccreditationStatus::Approved->value)
            ->where('requested_start_date', '<=', now())
            ->where('requested_end_date', '>=', now())
            ->exists();

        if ($hasPendingOrUnderReview || $hasCurrentlyActiveApproved) {
            Log::channel('accreditation')->warning('[Center] Blocked duplicate active request creation', [
                'center_id' => $request->certified_center_id,
                'has_pending' => $hasPendingOrUnderReview,
                'has_active_approved' => $hasCurrentlyActiveApproved,
            ]);

            throw new \DomainException(__('accreditation.errors.active_request_exists'));
        }
    }

    private function assertNoTimeOverlap(AccreditationRequest $request, bool $excludeSelf = false): void
    {
        $query = AccreditationRequest::query()
            ->where('certified_center_id', $request->certified_center_id)
            ->where('status', AccreditationStatus::Approved->value)
            ->where('requested_start_date', '<', $request->requested_end_date)
            ->where('requested_end_date', '>', $request->requested_start_date);

        if ($excludeSelf) {
            $query->where('id', '!=', $request->id);
        }

        if ($query->exists()) {
            Log::channel('accreditation')->warning('[Center] Blocked overlapping accreditation period', [
                'center_id' => $request->certified_center_id,
                'start' => $request->requested_start_date,
                'end' => $request->requested_end_date,
            ]);

            throw new \DomainException(__('accreditation.errors.time_overlap'));
        }
    }

    private function handleApproved(AccreditationRequest $request, CertifiedCenter $center): void
    {
        $center->accreditation_period_start = $request->requested_start_date;
        $center->accreditation_period_end = $request->requested_end_date;
        $center->status = CenterStatus::Active;
        $center->is_active = true;
        $center->saveQuietly();

        Log::channel('accreditation')->info('[Center] Accreditation approved and center activated', [
            'center_id' => $center->id,
            'request_id' => $request->id,
            'period' => [
                'start' => $request->requested_start_date,
                'end' => $request->requested_end_date,
            ],
        ]);
    }

    private function handleRejected(AccreditationRequest $request, CertifiedCenter $center): void
    {
        $hasOtherActiveApproved = $center->accreditationRequests()
            ->where('id', '!=', $request->id)
            ->where('status', AccreditationStatus::Approved->value)
            ->where('requested_start_date', '<=', now())
            ->where('requested_end_date', '>=', now())
            ->exists();

        if (!$hasOtherActiveApproved) {
            $center->status = CenterStatus::Suspended;
            $center->is_active = false;
            $center->saveQuietly();

            Log::channel('accreditation')->info('[Center] Center suspended after rejection — no other active approval', [
                'center_id' => $center->id,
                'request_id' => $request->id,
            ]);
        }
    }
}
