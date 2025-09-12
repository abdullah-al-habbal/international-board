<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Services\Stats\StatsService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

final class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $statsService = app(StatsService::class);
        $stats = $statsService->getDashboardStats();

        return [
            $this->createTotalCentersStat($stats['centers']['total']),
            $this->createActiveCentersStat($stats['centers']['active']),
            $this->createTotalCertificationsStat($stats['certifications']['total']),
            $this->createPendingRequestsStat($stats['requests']['pending']),
            $this->createAdminUsersStat($stats['users']['admins']),
            $this->createMonthlyContificationsStat($stats['certifications']['this_month']),
        ];
    }

    private function createTotalCentersStat(int $count): Stat
    {
        return Stat::make(__('widgets.stats.total_centers.label'), $count)
            ->description(__('widgets.stats.total_centers.description'))
            ->descriptionIcon('heroicon-o-building-office')
            ->color('success');
    }

    private function createActiveCentersStat(int $count): Stat
    {
        return Stat::make(__('widgets.stats.active_centers.label'), $count)
            ->description(__('widgets.stats.active_centers.description'))
            ->descriptionIcon('heroicon-o-check-circle')
            ->color('success');
    }

    private function createTotalCertificationsStat(int $count): Stat
    {
        return Stat::make(__('widgets.stats.total_certifications.label'), $count)
            ->description(__('widgets.stats.total_certifications.description'))
            ->descriptionIcon('heroicon-o-academic-cap')
            ->color('info');
    }

    private function createPendingRequestsStat(int $count): Stat
    {
        return Stat::make(__('widgets.stats.pending_requests.label'), $count)
            ->description(__('widgets.stats.pending_requests.description'))
            ->descriptionIcon('heroicon-o-clock')
            ->color('warning');
    }

    private function createAdminUsersStat(int $count): Stat
    {
        return Stat::make(__('widgets.stats.admin_users.label'), $count)
            ->description(__('widgets.stats.admin_users.description'))
            ->descriptionIcon('heroicon-o-user-group')
            ->color('primary');
    }

    private function createMonthlyContificationsStat(int $count): Stat
    {
        return Stat::make(__('widgets.stats.monthly_certifications.label'), $count)
            ->description(__('widgets.stats.monthly_certifications.description'))
            ->descriptionIcon('heroicon-o-calendar')
            ->color('success');
    }
}
