<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Widgets;

use App\Models\Trainer;
use App\Services\Stats\TrainerStatsService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

final class TrainerStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $trainerStatsService = app(TrainerStatsService::class);
        $trainer = $this->getTrainer();
        $stats = $trainerStatsService->getTrainerDashboardStats($trainer);
        $accreditationStatus = $stats['accreditation_status'];

        return [
            $this->createTotalCertificationsStat($stats['total_certifications']),
            $this->createThisMonthStat($stats['this_month_certifications']),
            $this->createFinancialRequestsStat($stats['total_financial_requests']),
            $this->createAccreditationStatusStat($accreditationStatus),
        ];
    }

    private function getTrainer(): Trainer
    {
        /** @var Trainer $trainer */
        $trainer = Auth::guard('trainer')->user();

        return $trainer;
    }

    private function createTotalCertificationsStat(int $count): Stat
    {
        return Stat::make(__('widgets.stats.trainer.total_certifications.label'), $count)
            ->description(__('widgets.stats.trainer.total_certifications.description'))
            ->descriptionIcon('heroicon-o-academic-cap')
            ->color('success');
    }

    private function createThisMonthStat(int $count): Stat
    {
        return Stat::make(__('widgets.stats.trainer.this_month.label'), $count)
            ->description(__('widgets.stats.trainer.this_month.description'))
            ->descriptionIcon('heroicon-o-calendar')
            ->color('info');
    }

    private function createFinancialRequestsStat(int $count): Stat
    {
        return Stat::make(__('widgets.stats.trainer.financial_requests.label'), $count)
            ->description(__('widgets.stats.trainer.financial_requests.description'))
            ->descriptionIcon('heroicon-o-banknotes')
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
