<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Services\Stats\StatsService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
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
            $this->createExpiredCentersStat($stats['centers']['expired']),
            $this->createTotalCertificationsStat($stats['certifications']['total']),
            $this->createPendingRequestsStat($stats['requests']['pending']),
            $this->createAdminUsersStat($stats['users']['admins']),
            $this->createNumberOfTrainersStat($stats['trainers']['total']),
            $this->createMonthlyContificationsStat($stats['certifications']['this_month']),
        ];
    }

    public function triggerExport(string $type): void
    {
        $label = __('widgets.stats.'.$type.'.label');
        $url = route('admin.exports.download', ['type' => $type]);

        Notification::make()
            ->title(__('widgets.export.title', ['label' => $label]))
            ->body(__('widgets.export.body', ['label' => $label]))
            ->warning()
            ->persistent()
            ->actions([
                Action::make('download')
                    ->label(__('widgets.export.download'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url($url)
                    ->openUrlInNewTab()
                    ->button()
                    ->color('success')
                    ->close(),
                Action::make('cancel')
                    ->label(__('widgets.export.cancel'))
                    ->color('gray')
                    ->close(),
            ])
            ->send();
    }

    private function statAttributes(string $type): array
    {
        $wireTarget = 'triggerExport';

        return [
            'wire:click' => "triggerExport('{$type}')",
            'wire:target' => $wireTarget,
            'wire:loading.attr' => 'disabled',
            'wire:loading.class' => 'opacity-60 pointer-events-none',
            'role' => 'button',
            'tabindex' => '0',
            'class' => 'cursor-pointer hover:opacity-80 transition',
            'style' => 'cursor: pointer;',
            'onkeydown' => "if(event.key === 'Enter' || event.key === ' ') { event.preventDefault(); this.dispatchEvent(new MouseEvent('click', {bubbles:true})); }",
        ];
    }

    private function createTotalCentersStat(int $count): Stat
    {
        return Stat::make(__('widgets.stats.total_centers.label'), $count)
            ->description(__('widgets.stats.total_centers.description'))
            ->descriptionIcon('heroicon-o-building-office')
            ->color('success')
            ->extraAttributes($this->statAttributes('total_centers'));
    }

    private function createExpiredCentersStat(int $count): Stat
    {
        return Stat::make(__('widgets.stats.expired_centers.label'), $count)
            ->description(__('widgets.stats.expired_centers.description'))
            ->descriptionIcon('heroicon-o-exclamation-triangle')
            ->color('danger')
            ->extraAttributes($this->statAttributes('expired_centers'));
    }

    private function createTotalCertificationsStat(int $count): Stat
    {
        return Stat::make(__('widgets.stats.total_certifications.label'), $count)
            ->description(__('widgets.stats.total_certifications.description'))
            ->descriptionIcon('heroicon-o-academic-cap')
            ->color('info')
            ->extraAttributes($this->statAttributes('total_certifications'));
    }

    private function createPendingRequestsStat(int $count): Stat
    {
        return Stat::make(__('widgets.stats.pending_requests.label'), $count)
            ->description(__('widgets.stats.pending_requests.description'))
            ->descriptionIcon('heroicon-o-clock')
            ->color('warning')
            ->extraAttributes($this->statAttributes('pending_requests'));
    }

    private function createAdminUsersStat(int $count): Stat
    {
        return Stat::make(__('widgets.stats.admin_users.label'), $count)
            ->description(__('widgets.stats.admin_users.description'))
            ->descriptionIcon('heroicon-o-user-group')
            ->color('primary')
            ->extraAttributes($this->statAttributes('admin_users'));
    }

    private function createNumberOfTrainersStat(int $count): Stat
    {
        return Stat::make(__('widgets.stats.trainers.label'), $count)
            ->description(__('widgets.stats.trainers.description'))
            ->descriptionIcon('heroicon-o-user')
            ->color('primary')
            ->extraAttributes($this->statAttributes('trainers'));
    }

    private function createMonthlyContificationsStat(int $count): Stat
    {
        return Stat::make(__('widgets.stats.monthly_certifications.label'), $count)
            ->description(__('widgets.stats.monthly_certifications.description'))
            ->descriptionIcon('heroicon-o-calendar')
            ->color('success')
            ->extraAttributes($this->statAttributes('monthly_certifications'));
    }
}
