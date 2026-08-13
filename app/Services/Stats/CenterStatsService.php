<?php

declare(strict_types=1);

namespace App\Services\Stats;

use App\Models\CertifiedCenter;
use App\Services\AccreditationRequest\AccreditationRequestService;
use App\Services\Certification\CertificationService;

final class CenterStatsService
{
    public function __construct(
        private readonly CertificationService $certificationService,
        private readonly AccreditationRequestService $requestService
    ) {}

    public function getCenterDashboardStats(CertifiedCenter $center): array
    {
        return [
            'total_certifications' => $this->certificationService->getTotalCountByCenter($center->id),
            'this_month_certifications' => $this->certificationService->getCountThisMonthByCenter($center->id),
            'pending_requests' => $this->requestService->getPendingCountByCenter($center->id),
            'accreditation_status' => $this->getAccreditationStatusData($center),
        ];
    }

    public function getAccreditationStatusData(CertifiedCenter $center): array
    {
        return [
            'status_label' => $center->isAccreditationActive() ? __('widgets.status.active') : __('widgets.status.expired'),
            'description' => $this->getStatusDescription($center),
            'color' => $this->getStatusColor($center),
            'days_until_expiry' => $this->getDaysUntilExpiry($center),
        ];
    }

    private function getStatusDescription(CertifiedCenter $center): string
    {
        if (! $center->accreditation_period_end) {
            return __('widgets.status.no_accreditation_period');
        }

        $daysLeft = $this->getDaysUntilExpiry($center);

        return match (true) {
            $daysLeft < 0 => __('widgets.status.accreditation_expired'),
            $daysLeft <= 30 => __('widgets.status.expires_in_days', ['days' => $daysLeft]),
            default => __('widgets.status.valid_until', ['date' => $center->accreditation_period_end->translatedFormat('M d, Y')]),
        };
    }

    private function getStatusColor(CertifiedCenter $center): string
    {
        if (! $center->isAccreditationActive()) {
            return 'danger';
        }

        $daysLeft = $this->getDaysUntilExpiry($center);

        return match (true) {
            $daysLeft <= 30 => 'warning',
            default => 'success',
        };
    }

    private function getDaysUntilExpiry(CertifiedCenter $center): int
    {
        if (! $center->accreditation_period_end) {
            return 0;
        }

        return (int) now()->diffInDays($center->accreditation_period_end, false);
    }
}
