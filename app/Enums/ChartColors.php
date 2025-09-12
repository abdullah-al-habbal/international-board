<?php

declare(strict_types=1);

namespace App\Enums;

enum ChartColors: string
{
    case Primary = 'rgb(75, 192, 192)';
    case PrimaryTransparent = 'rgba(75, 192, 192, 0.2)';
    case Success = 'rgb(34, 197, 94)';
    case SuccessTransparent = 'rgba(34, 197, 94, 0.1)';
    case Warning = 'rgb(255, 205, 86)';
    case Danger = 'rgb(255, 99, 132)';
    case Info = 'rgb(54, 162, 235)';
    case Default = 'rgb(201, 203, 207)';

    public static function getMonthlyChartColors(): array
    {
        return [
            'border' => self::Primary->value,
            'background' => self::PrimaryTransparent->value,
        ];
    }

    public static function getCenterChartColors(): array
    {
        return [
            'border' => self::Success->value,
            'background' => self::SuccessTransparent->value,
        ];
    }
}
