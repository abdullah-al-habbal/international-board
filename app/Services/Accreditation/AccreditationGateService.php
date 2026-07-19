<?php

declare(strict_types=1);

namespace App\Services\Accreditation;

use App\Models\CertifiedCenter;
use App\Models\Trainer;
use Illuminate\Support\Facades\Auth;

final class AccreditationGateService
{
    public function centerCanPerformActions(CertifiedCenter $center): bool
    {
        return $center->canPerformActions();
    }

    public function currentCenterCanPerformActions(): bool
    {
        /** @var CertifiedCenter|null $center */
        $center = Auth::guard('certified_center')->user();

        if (! $center instanceof CertifiedCenter) {
            return false;
        }

        return $center->canPerformActions();
    }

    public function currentCenterAccreditationBlockReason(): ?string
    {
        /** @var CertifiedCenter|null $center */
        $center = Auth::guard('certified_center')->user();

        return $center?->accreditationBlockReason();
    }

    public function trainerCanPerformActions(Trainer $trainer): bool
    {
        return $trainer->canPerformActions();
    }

    public function currentTrainerCanPerformActions(): bool
    {
        /** @var Trainer|null $trainer */
        $trainer = Auth::guard('trainer')->user();

        if (! $trainer instanceof Trainer) {
            return false;
        }

        return $trainer->canPerformActions();
    }

    public function currentTrainerAccreditationBlockReason(): ?string
    {
        /** @var Trainer|null $trainer */
        $trainer = Auth::guard('trainer')->user();

        return $trainer?->accreditationBlockReason();
    }
}
