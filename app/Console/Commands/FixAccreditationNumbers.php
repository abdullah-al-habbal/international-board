<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\CertifiedCenter;
use Illuminate\Console\Command;

class FixAccreditationNumbers extends Command
{
    protected $signature = 'accreditation:fix-numbers';

    protected $description = 'Generate IBVTQXXXXX accreditation numbers for centers missing the pattern.';

    public function handle(): int
    {
        $pattern = '/^IBVTQ\d{5}$/';

        CertifiedCenter::where(function ($query) use ($pattern): void {
            $query->whereNull('accreditation_number')
                ->orWhere('accreditation_number', 'NOT REGEXP', $pattern);
        })
        ->chunk(100, function ($centers) use ($pattern): void {
            foreach ($centers as $center) {
                if (! $center->accreditation_number || ! preg_match($pattern, $center->accreditation_number)) {
                    do {
                        $number = random_int(10000, 99999);
                        $candidate = 'IBVTQ' . $number;
                    } while (CertifiedCenter::where('accreditation_number', $candidate)->exists());

                    $center->update(['accreditation_number' => $candidate]);
                    $this->line("Updated center #{$center->id} \u{2192} {$candidate}");
                }
            }
        });

        $this->info('Done.');

        return self::SUCCESS;
    }
}
