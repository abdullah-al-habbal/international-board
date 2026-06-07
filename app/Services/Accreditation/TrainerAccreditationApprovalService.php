<?php

declare(strict_types=1);

namespace App\Services\Accreditation;

use App\Enums\AccreditationStatus;
use App\Models\TrainerAccreditationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class TrainerAccreditationApprovalService
{
    public function approve(TrainerAccreditationRequest $request): void
    {
        DB::transaction(function () use ($request) {
            $trainer = $request->trainer;

            $trainer->update([
                'membership_start_date' => $request->requested_start_date,
                'membership_end_date' => $request->requested_end_date,
            ]);

            $request->update([
                'status' => AccreditationStatus::Approved,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);
        });
    }

    public function reject(TrainerAccreditationRequest $request, ?string $adminNotes = null): void
    {
        $request->update([
            'status' => AccreditationStatus::Rejected,
            'admin_notes' => $adminNotes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);
    }
}
