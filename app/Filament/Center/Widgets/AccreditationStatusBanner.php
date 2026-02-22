<?php

declare(strict_types=1);

namespace App\Filament\Center\Widgets;

use App\Models\CertifiedCenter;
use Filament\Widgets\Widget;

class AccreditationStatusBanner extends Widget
{
    protected static string $view = 'filament.center.widgets.accreditation-status-banner';

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    public function getCenter(): CertifiedCenter
    {
        /** @var CertifiedCenter $center */
        $center = auth('certified_center')->user();

        return $center;
    }
}
