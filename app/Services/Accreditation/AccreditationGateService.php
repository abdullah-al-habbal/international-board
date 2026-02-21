<?php
declare(strict_types=1);
namespace App\Services\Accreditation;

use App\Models\CertifiedCenter;

final class AccreditationGateService
{
    public function centerCanPerformActions(CertifiedCenter $center): bool
    {
        return $center->canPerformActions();
    }

    public function currentCenterCanPerformActions(): bool
    {
        /** @var CertifiedCenter|null $center */
        $center = auth('certified_center')->user();

        if (!$center instanceof CertifiedCenter) {
            return false;
        }

        return $center->canPerformActions();
    }
}
