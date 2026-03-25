<?php
// filePath: app/Console/Commands/SyncCenterAccreditationPeriods.php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\AccreditationStatus;
use App\Enums\CenterStatus;
use App\Models\AccreditationRequest;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * One-time back-fill command.
 *
 * Run once after deploying the observer-removal refactor to ensure any
 * existing Approved AccreditationRequest records are reflected on their
 * CertifiedCenter rows (accreditation_period_start/end, status, is_active).
 *
 * Usage:
 *   php artisan accreditation:sync-centers
 *   php artisan accreditation:sync-centers --dry-run
 */
class SyncCenterAccreditationPeriods extends Command
{
    protected $signature = 'accreditation:sync-centers {--dry-run : Print changes without writing to DB}';

    protected $description = 'Back-fill certified_centers columns from their latest Approved accreditation_requests.';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $now = Carbon::now();

        $requests = AccreditationRequest::query()
            ->where('status', AccreditationStatus::Approved)
            ->with('certifiedCenter')
            ->orderByDesc('requested_end_date') // latest approval wins
            ->get()
            ->unique('certified_center_id');    // one per center

        if ($requests->isEmpty()) {
            $this->info('No approved requests found. Nothing to sync.');
            return self::SUCCESS;
        }

        foreach ($requests as $request) {
            $center = $request->certifiedCenter;

            if (!$center) {
                $this->warn("Request #{$request->id}: center #{$request->certified_center_id} not found — skipped.");
                continue;
            }

            $isActive = $request->requested_start_date <= $now
                && $request->requested_end_date >= $now;

            $payload = [
                'accreditation_period_start' => $request->requested_start_date,
                'accreditation_period_end' => $request->requested_end_date,
                'status' => $isActive ? CenterStatus::Active : $center->status,
                'is_active' => $isActive,
            ];

            $this->line(sprintf(
                '%s Center #%d (%s): start=%s end=%s is_active=%s',
                $dryRun ? '[DRY-RUN]' : '[SYNC]',
                $center->id,
                $center->name,
                $request->requested_start_date->toDateTimeString(),
                $request->requested_end_date->toDateTimeString(),
                $isActive ? 'true' : 'false'
            ));

            if (!$dryRun) {
                $center->update($payload);
            }
        }

        $this->info($dryRun ? 'Dry run complete — no changes written.' : 'Sync complete.');

        return self::SUCCESS;
    }
}
