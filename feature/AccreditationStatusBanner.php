<?php

// filePath: app/Filament/Center/Widgets/AccreditationStatusBanner.php
declare(strict_types=1);

namespace App\Filament\Center\Widgets;

use App\Models\CertifiedCenter;
use Filament\Widgets\Widget;

class AccreditationStatusBanner extends Widget
{
    protected static string $view = 'filament.center.widgets.accreditation-status-banner';

    protected static ?int $sort = -10; // Always first on dashboard.

    protected int|string|array $columnSpan = 'full';

    public function getViewData(): array
    {
        /** @var CertifiedCenter|null $center */
        $center = auth('certified_center')->user();

        if (! $center instanceof CertifiedCenter) {
            return ['blocked' => false];
        }

        $blocked = ! $center->canPerformActions();
        $blockReason = $blocked ? $center->accreditationBlockReason() : null;
        $hasActive = $center->hasActiveAccreditationRequest();
        $hasPending = $center->accreditationRequests()
            ->whereIn('status', ['pending', 'under_review'])
            ->exists();

        return compact('blocked', 'blockReason', 'hasActive', 'hasPending');
    }
}
