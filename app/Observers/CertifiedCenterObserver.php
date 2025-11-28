<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\CertifiedCenter;
use App\Notifications\AccreditationExpiring;
use Carbon\Carbon;

final class CertifiedCenterObserver
{
    private const NOTIFICATION_THRESHOLDS = [30, 15, 7, 1];

    private const MIN_VALID_DAYS = 0;

    public function creating(CertifiedCenter $center): void
    {
        if (is_null($center->email_verified_at)) {
            $center->email_verified_at = now();
        }
    }

    public function updated(CertifiedCenter $center): void
    {
        $this->handleExpiredAccreditation($center);
    }

    public function saved(CertifiedCenter $center): void
    {
        $this->handleExpirationNotification($center);
    }

    private function shouldDeactivate(CertifiedCenter $center): bool
    {
        return $center->is_active
            && $center->accreditation_period_end !== null
            && $center->accreditation_period_end->isPast();
    }

    private function shouldNotify(CertifiedCenter $center): bool
    {
        return $center->is_active && $center->accreditation_period_end !== null;
    }

    private function daysUntilExpiry(CertifiedCenter $center): int
    {
        return (int) Carbon::now()->diffInDays($center->accreditation_period_end, false);
    }

    private function isNotificationThreshold(int $days): bool
    {
        return in_array($days, self::NOTIFICATION_THRESHOLDS, true);
    }

    private function handleExpiredAccreditation(CertifiedCenter $center): void
    {
        if ($this->shouldDeactivate($center)) {
            $this->deactivateCenter($center);
        }
    }

    private function handleExpirationNotification(CertifiedCenter $center): void
    {
        if (! $this->canSendNotification($center)) {
            return;
        }

        if (! $center->wasChanged(['accreditation_period_end', 'is_active'])) {
            return;
        }

        $daysUntilExpiry = $this->daysUntilExpiry($center);

        if ($this->shouldSendExpirationNotification($daysUntilExpiry)) {
            $this->sendExpirationNotification($center, $daysUntilExpiry);
        }
    }

    private function deactivateCenter(CertifiedCenter $center): void
    {
        $center->newQuery()
            ->where('id', $center->id)
            ->update(['is_active' => false]);
    }

    private function canSendNotification(CertifiedCenter $center): bool
    {
        return $center->exists && $this->shouldNotify($center);
    }

    private function shouldSendExpirationNotification(int $daysUntilExpiry): bool
    {
        return $this->isNotificationThreshold($daysUntilExpiry)
            && $daysUntilExpiry > self::MIN_VALID_DAYS;
    }

    private function sendExpirationNotification(CertifiedCenter $center, int $daysUntilExpiry): void
    {
        $center->notify(new AccreditationExpiring($center, $daysUntilExpiry));
    }
}
