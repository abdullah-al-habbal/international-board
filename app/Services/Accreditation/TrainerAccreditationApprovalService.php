<?php

declare(strict_types=1);

namespace App\Services\Accreditation;

use App\Enums\AccreditationStatus;
use App\Models\TrainerAccreditationRequest;
use Illuminate\Support\Facades\Auth;

final class TrainerAccreditationApprovalService
{
    public function approve(TrainerAccreditationRequest $request): void
    {
        $request->update([
            'status' => AccreditationStatus::Approved,
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
