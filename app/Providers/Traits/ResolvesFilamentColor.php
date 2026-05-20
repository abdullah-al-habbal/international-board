<?php

declare(strict_types=1);

namespace App\Providers\Traits;

use Filament\Support\Colors\Color;

trait ResolvesFilamentColor
{
    protected function resolveColor(string $name): array
    {
        return match (strtolower($name)) {
            'amber' => Color::Amber,
            'blue' => Color::Blue,
            'gray' => Color::Gray,
            'green' => Color::Green,
            'indigo' => Color::Indigo,
            'lime' => Color::Lime,
            'pink' => Color::Pink,
            'purple' => Color::Purple,
            'red' => Color::Red,
            'teal' => Color::Teal,
            'yellow' => Color::Yellow,
            'emerald' => Color::Emerald,
            default => Color::Amber,
        };
    }
}
