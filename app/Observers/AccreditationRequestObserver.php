<?php

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
        if (!$request->wasChanged('status')) {
            return;
        }

        $center = $request->certifiedCenter;

        if (!$center) {
            return;
        }

        $status = $request->status instanceof AccreditationStatus
            ? $request->status
            : AccreditationStatus::from($request->status);

        if ($status === AccreditationStatus::Approved) {
            $center->accreditation_period_start = $request->requested_start_date;
            $center->accreditation_period_end = $request->requested_end_date;
            $center->status = CenterStatus::Active;
            $center->is_active = true;
            $center->save();

            return;
        }

        if ($status === AccreditationStatus::Rejected) {
            $hasOtherActive = $center->accreditationRequests()
                ->where('id', '!=', $request->id)
                ->where('status', AccreditationStatus::Approved)
                ->exists();

            if (!$hasOtherActive) {
                $center->update([
                    'status' => CenterStatus::Suspended->value,
                    'is_active' => false,
                ]);
            }
        }
    }
}
