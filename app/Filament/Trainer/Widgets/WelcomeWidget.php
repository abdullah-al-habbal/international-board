<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Widgets;

use App\Models\Trainer;
use Filament\Widgets\Widget;

class WelcomeWidget extends Widget
{
    protected static string $view = 'filament.widgets.welcome';

    protected int|string|array $columnSpan = 'full';

    public function getUserName(): string
    {
        /** @var Trainer|null $trainer */
        $trainer = auth('trainer')->user();

        return $trainer?->name ?? __('app.guest');
    }
}
