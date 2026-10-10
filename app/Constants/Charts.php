<?php

declare(strict_types=1);

namespace App\Constants;

use Illuminate\Support\Carbon;

final class Charts
{
    public static function monthLabels(): array
    {
        return collect(range(1, 12))
            ->map(fn (int $month) => Carbon::createFromDate(null, $month, 1)->translatedFormat('M'))
            ->all();
    }

    public const CHART_TYPES = [
        'LINE' => 'line',
        'DOUGHNUT' => 'doughnut',
        'BAR' => 'bar',
        'PIE' => 'pie',
    ];
}
