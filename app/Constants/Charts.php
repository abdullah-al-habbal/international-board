<?php

declare(strict_types=1);

namespace App\Constants;

final class Charts
{
    public const MONTH_LABELS = [
        'Jan',
        'Feb',
        'Mar',
        'Apr',
        'May',
        'Jun',
        'Jul',
        'Aug',
        'Sep',
        'Oct',
        'Nov',
        'Dec'
    ];

    public const CHART_TYPES = [
        'LINE' => 'line',
        'DOUGHNUT' => 'doughnut',
        'BAR' => 'bar',
        'PIE' => 'pie',
    ];
}
