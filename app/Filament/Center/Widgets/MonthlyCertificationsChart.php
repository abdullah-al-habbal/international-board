<?php

declare(strict_types=1);

namespace App\Filament\Center\Widgets;

use App\Constants\Charts;
use App\Enums\ChartColors;
use App\Services\Certification\CertificationService;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;

class MonthlyCertificationsChart extends ChartWidget
{
    protected ?string $heading;

    public function mount(): void
    {
        parent::mount();
        $this->heading = __('widgets.charts.monthly_certifications.heading');
    }

    public function getHeading(): string|Htmlable|null
    {
        return $this->heading ?? __('widgets.charts.monthly_certifications.heading');
    }

    protected function getData(): array
    {
        $certificationService = app(CertificationService::class);
        $centerId = Auth::guard('certified_center')->id();
        $chartData = $certificationService->getMonthlyCountsByCenter($centerId);
        $colors = ChartColors::getCenterChartColors();

        return [
            'datasets' => [
                [
                    'label' => __('widgets.charts.monthly_certifications.label'),
                    'data' => $chartData,
                    'borderColor' => $colors['border'],
                    'backgroundColor' => $colors['background'],
                    'fill' => true,
                ],
            ],
            'labels' => Charts::monthLabels(),
        ];
    }

    protected function getType(): string
    {
        return Charts::CHART_TYPES['LINE'];
    }
}
