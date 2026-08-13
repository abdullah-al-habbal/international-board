<?php

declare(strict_types=1);

namespace App\Services\Stats;

use App\Models\Trainer;

final class TrainerStatsService
{
    public function getTrainerDashboardStats(Trainer $trainer): array
    {
        return [
            'total_certifications' => $trainer->certifications()->count(),
            'this_month_certifications' => $trainer->certifications()->createdThisMonth()->count(),
            'total_financial_requests' => $trainer->financialRequests()->count(),
            'accreditation_status' => $this->getAccreditationStatusData($trainer),
        ];
    }

    public function getAccreditationStatusData(Trainer $trainer): array
    {
        return [
            'status_label' => $trainer->isAccreditationActive() ? __('widgets.status.active') : __('widgets.status.expired'),
            'description' => $this->getStatusDescription($trainer),
            'color' => $this->getStatusColor($trainer),
            'days_until_expiry' => $this->getDaysUntilExpiry($trainer),
        ];
    }

    private function getStatusDescription(Trainer $trainer): string
    {
        if (! $trainer->accreditation_period_end) {
            return __('widgets.status.no_accreditation_period');
        }

        $daysLeft = $this->getDaysUntilExpiry($trainer);

        return match (true) {
            $daysLeft < 0 => __('widgets.status.accreditation_expired'),
            $daysLeft <= 30 => __('widgets.status.expires_in_days', ['days' => $daysLeft]),
            default => __('widgets.status.valid_until', ['date' => $trainer->accreditation_period_end->translatedFormat('M d, Y')]),
        };
    }

    private function getStatusColor(Trainer $trainer): string
    {
        if (! $trainer->isAccreditationActive()) {
            return 'danger';
        }

        $daysLeft = $this->getDaysUntilExpiry($trainer);

        return match (true) {
            $daysLeft <= 30 => 'warning',
            default => 'success',
        };
    }

    private function getDaysUntilExpiry(Trainer $trainer): int
    {
        if (! $trainer->accreditation_period_end) {
            return 0;
        }

        return (int) now()->diffInDays($trainer->accreditation_period_end, false);
    }
}
