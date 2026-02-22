<?php

// filePath: app/Observers/AccreditationRequestObserver.php
declare(strict_types=1);

namespace App\Observers;

use App\Enums\AccreditationStatus;
use App\Enums\CenterStatus;
use App\Models\AccreditationRequest;
use Illuminate\Support\Facades\Auth;

class AccreditationRequestObserver
{
    public function updating(AccreditationRequest $request): void
    {
        // Auto-stamp reviewed_by / reviewed_at when status changes to a reviewed state.
        if (
            $request->isDirty('status')
            && AccreditationStatus::from($request->status instanceof AccreditationStatus
                ? $request->status->value
                : $request->status)->isReviewed()
        ) {
            if (empty($request->reviewed_by)) {
                $request->reviewed_by = Auth::id();
            }

            if (empty($request->reviewed_at)) {
                $request->reviewed_at = now();
            }
        }
    }

    public function updated(AccreditationRequest $request): void
    {
        if (! $request->wasChanged('status')) {
            return;
        }

        $center = $request->certifiedCenter;

        if (! $center) {
            return;
        }

        $status = $request->status instanceof AccreditationStatus
            ? $request->status
            : AccreditationStatus::from($request->status);

        if ($status === AccreditationStatus::Approved) {
            // Sync accreditation window and activate the center.
            $center->update([
                'accreditation_period_start' => $request->requested_start_date,
                'accreditation_period_end' => $request->requested_end_date,
                'status' => CenterStatus::Active,
                'is_active' => true,
            ]);

            return;
        }

        if ($status === AccreditationStatus::Rejected) {
            // Do not clear dates — only deactivate if no other active approval exists.
            $hasOtherActive = $center->accreditationRequests()
                ->where('id', '!=', $request->id)
                ->where('status', AccreditationStatus::Approved)
                ->exists();

            if (! $hasOtherActive) {
                $center->update([
                    'status' => CenterStatus::Suspended,
                    'is_active' => false,
                ]);
            }
        }
    }
}
