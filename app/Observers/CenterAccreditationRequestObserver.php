<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\AccreditationStatus;
use App\Models\CenterAccreditationRequest;
use App\Models\CertifiedCenter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CenterAccreditationRequestObserver
{
    public function creating(CenterAccreditationRequest $request): void
    {
        $this->assertNoDuplicateActiveRequest($request);
        $this->assertNoTimeOverlap($request);
    }

    public function updating(CenterAccreditationRequest $request): void
    {
        if ($request->isDirty('status')) {
            Log::channel('accreditation')->info('[Center] CenterAccreditationRequest status transition', [
                'request_id' => $request->id,
                'certified_center_id' => $request->certified_center_id,
                'from' => $request->getOriginal('status') instanceof AccreditationStatus
                    ? $request->getOriginal('status')->value
                    : $request->getOriginal('status'),
                'to' => $request->status instanceof AccreditationStatus
                    ? $request->status->value
                    : $request->status,
            ]);
        }

        if ($request->isDirty(['accreditation_start_date', 'accreditation_end_date'])) {
            $this->assertNoTimeOverlap($request, excludeSelf: true);
        }
    }

    public function updated(CenterAccreditationRequest $request): void
    {
        if (!$request->wasChanged('status')) {
            return;
        }

        $status = $request->status instanceof AccreditationStatus
            ? $request->status
            : AccreditationStatus::from($request->status);

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
            Log::channel('accreditation')->warning('[Center] Request updated but center not found', [
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

    private function assertNoDuplicateActiveRequest(CenterAccreditationRequest $request): void
    {
        $hasActive = CenterAccreditationRequest::query()
            ->where('certified_center_id', $request->certified_center_id)
            ->whereIn('status', [
                AccreditationStatus::Pending->value,
                AccreditationStatus::UnderReview->value,
            ])
            ->exists();

        $hasCurrentlyActiveApproved = CenterAccreditationRequest::query()
            ->where('certified_center_id', $request->certified_center_id)
            ->where('status', AccreditationStatus::Approved->value)
            ->where('accreditation_start_date', '<=', now())
            ->where('accreditation_end_date', '>=', now())
            ->exists();

        if ($hasActive || $hasCurrentlyActiveApproved) {
            Log::channel('accreditation')->warning('[Center] Blocked duplicate active request', [
                'certified_center_id' => $request->certified_center_id,
            ]);

            throw new \DomainException(__('accreditation.errors.active_request_exists'));
        }
    }

    private function assertNoTimeOverlap(CenterAccreditationRequest $request, bool $excludeSelf = false): void
    {
        if (is_null($request->accreditation_start_date) || is_null($request->accreditation_end_date)) {
            return;
        }

        $query = CenterAccreditationRequest::query()
            ->where('certified_center_id', $request->certified_center_id)
            ->where('status', AccreditationStatus::Approved->value)
            ->where('accreditation_start_date', '<', $request->accreditation_end_date)
            ->where('accreditation_end_date', '>', $request->accreditation_start_date);

        if ($excludeSelf) {
            $query->where('id', '!=', $request->id);
        }

        if ($query->exists()) {
            Log::channel('accreditation')->warning('[Center] Blocked overlapping accreditation period', [
                'certified_center_id' => $request->certified_center_id,
                'start' => $request->accreditation_start_date,
                'end' => $request->accreditation_end_date,
            ]);

            throw new \DomainException(__('accreditation.errors.time_overlap'));
        }
    }

    private function handleApproved(CenterAccreditationRequest $request, CertifiedCenter $center): void
    {
        $center->is_active = true;
        $center->saveQuietly();

        Log::channel('accreditation')->info('[Center] Accreditation approved', [
            'certified_center_id' => $center->id,
            'period' => [
                'start' => $request->accreditation_start_date,
                'end' => $request->accreditation_end_date,
            ],
        ]);
    }

    private function handleRejected(CenterAccreditationRequest $request, CertifiedCenter $center): void
    {
        $hasOtherActive = $center->accreditationRequests()
            ->where('id', '!=', $request->id)
            ->where('status', AccreditationStatus::Approved->value)
            ->where('accreditation_start_date', '<=', now())
            ->where('accreditation_end_date', '>=', now())
            ->exists();

        if (!$hasOtherActive) {
            $center->is_active = false;
            $center->saveQuietly();

            Log::channel('accreditation')->info('[Center] Center deactivated after rejection', [
                'certified_center_id' => $center->id,
            ]);
        }
    }
}
