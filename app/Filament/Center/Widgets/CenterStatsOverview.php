<?php

declare(strict_types=1);

namespace App\Filament\Center\Widgets;

use App\Models\CertifiedCenter;
use App\Services\Stats\CenterStatsService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

final class CenterStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $centerStatsService = app(CenterStatsService::class);
        $center = $this->getCenter();
        $stats = $centerStatsService->getCenterDashboardStats($center);
        $accreditationStatus = $stats['accreditation_status'];

        return [
            $this->createTotalCertificationsStat($stats['total_certifications']),
            $this->createThisMonthStat($stats['this_month_certifications']),
            $this->createPendingRequestsStat($stats['pending_requests']),
            $this->createAccreditationStatusStat($accreditationStatus),
        ];
    }

    private function getCenter(): CertifiedCenter
    {
        /** @var CertifiedCenter $center */
        $center = Auth::guard('certified_center')->user();

        return $center;
    }

    private function createTotalCertificationsStat(int $count): Stat
    {
        return Stat::make(__('widgets.stats.total_certifications.label'), $count)
            ->description(__('widgets.stats.total_certifications.description'))
            ->descriptionIcon('heroicon-o-academic-cap')
            ->color('success');
    }

    private function createThisMonthStat(int $count): Stat
    {
        return Stat::make(__('widgets.stats.this_month.label'), $count)
            ->description(__('widgets.stats.this_month.description'))
            ->descriptionIcon('heroicon-o-calendar')
            ->color('info');
    }

    private function createPendingRequestsStat(int $count): Stat
    {
        return Stat::make(__('widgets.stats.pending_requests.label'), $count)
            ->description(__('widgets.stats.pending_requests.description'))
            ->descriptionIcon('heroicon-o-clock')
            ->color('warning');
    }

    private function createAccreditationStatusStat(array $statusData): Stat
    {
        return Stat::make(__('widgets.stats.accreditation_status.label'), $statusData['status_label'])
            ->description($statusData['description'])
            ->descriptionIcon('heroicon-o-shield-check')
            ->color($statusData['color']);
    }
}
