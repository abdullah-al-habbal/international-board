<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Constants\Charts;
use App\Enums\AccreditationStatus;
use App\Enums\ChartColors;
use App\Services\AccreditationRequest\AccreditationRequestService;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;

final class AccreditationChart extends ChartWidget
{
    protected ?string $heading;

    public function mount(): void
    {
        parent::mount();
        $this->heading = __('widgets.charts.accreditation_requests.heading');
    }

    public function getHeading(): string|Htmlable|null
    {
        return $this->heading ?? __('widgets.charts.accreditation_requests.heading');
    }

    protected function getData(): array
    {
        $requestService = app(AccreditationRequestService::class);
        $data = $requestService->getStatusCounts();

        return [
            'datasets' => [
                [
                    'label' => __('widgets.charts.accreditation_requests.label'),
                    'data' => array_values($data),
                    'backgroundColor' => $this->mapColors(array_keys($data)),
                ],
            ],
            'labels' => $this->mapLabels(array_keys($data)),
        ];
    }

    protected function getType(): string
    {
        return Charts::CHART_TYPES['DOUGHNUT'];
    }

    private function mapColors(array $statuses): array
    {
        return array_map(
            fn (string $status) => AccreditationStatus::tryFrom($status)?->rgb() ?? ChartColors::Default->value,
            $statuses
        );
    }

    private function mapLabels(array $statuses): array
    {
        return array_map(
            fn (string $status) => AccreditationStatus::tryFrom($status)?->label() ?? $status,
            $statuses
        );
    }
}
