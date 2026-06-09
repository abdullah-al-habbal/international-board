<?php

declare(strict_types=1);

namespace App\Services\Accreditation;

use App\Enums\AccreditationStatus;
use App\Models\TrainerAccreditationRequest;
use Illuminate\Support\Facades\Auth;

final class TrainerAccreditationApprovalService
{
    public function approve(
        TrainerAccreditationRequest $request,
        ?string $accreditationEndDate = null
    ): void {
        $request->update([
            'status' => AccreditationStatus::Approved,
            'accreditation_start_date' => now(),
            'accreditation_end_date' => $accreditationEndDate,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);
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
