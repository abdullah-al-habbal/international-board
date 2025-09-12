<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\CertifiedCenter;
use App\Notifications\AccreditationExpiring;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

final class CheckAccreditationExpiry extends Command
{
    protected $signature = 'accreditation:check-expiry';
    protected $description = 'Check for expiring accreditations and send notifications';

    public function handle(): int
    {
        $centers = $this->activeCentersWithAccreditation();

        $notificationsSent = 0;
        $centersDeactivated = 0;

        foreach ($centers as $center) {
            $days = $this->daysUntilExpiry($center);

            if ($days < 0) {
                $this->deactivateCenter($center);
                $centersDeactivated++;
                continue;
            }

            if ($this->shouldNotify($days)) {
                $this->sendExpiryNotification($center, $days);
                $notificationsSent++;
            }
        }

        $this->summarize($notificationsSent, $centersDeactivated);

        return self::SUCCESS;
    }


    private function activeCentersWithAccreditation(): Collection
    {
        return CertifiedCenter::query()
            ->where('is_active', true)
            ->whereNotNull('accreditation_period_end')
            ->get();
    }

    private function daysUntilExpiry(CertifiedCenter $center): int
    {
        return (int) Carbon::now()->diffInDays($center->accreditation_period_end, false);
    }

    private function shouldNotify(int $days): bool
    {
        return in_array($days, [30, 15, 7, 1], true);
    }

    private function deactivateCenter(CertifiedCenter $center): void
    {
        $center->update(['is_active' => false]);
        $this->info("Deactivated expired center: {$center->name}");
    }

    private function sendExpiryNotification(CertifiedCenter $center, int $days): void
    {
        $center->notify(new AccreditationExpiring($center, $days));
        $this->info("Sent expiry notification to {$center->name} ({$days} days remaining)");
    }

    private function summarize(int $notificationsSent, int $centersDeactivated): void
    {
        $this->info("Task completed:");
        $this->info("- Notifications sent: {$notificationsSent}");
        $this->info("- Centers deactivated: {$centersDeactivated}");
    }
}
