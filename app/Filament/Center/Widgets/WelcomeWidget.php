<?php

declare(strict_types=1);

namespace App\Filament\Center\Widgets;

use App\Models\CertifiedCenter;
use Filament\Widgets\Widget;

class WelcomeWidget extends Widget
{
    protected static string $view = 'filament.widgets.welcome';

    protected int|string|array $columnSpan = 'full';

    public function getUserName(): string
    {
        /** @var CertifiedCenter|null $center */
        $center = auth('certified_center')->user();

        return $center?->name ?? __('app.guest');
    }
}
