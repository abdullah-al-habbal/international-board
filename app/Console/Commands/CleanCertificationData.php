<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\DocumentType;
use App\Models\Certification;
use App\Models\Country;
use App\Models\Trainer;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanCertificationData extends Command
{
    protected $signature = 'certifications:clean 
        {--dry-run : Show what would be changed without making changes}
        {--step= : Run specific cleaning step (names|serials|dates|nationalities|trainers|document-types|paper-status|all)}';

    protected $description = 'Clean and normalize certification data with database transactions';

    private bool $dryRun = false;
    private array $stats = [
        'processed' => 0,
        'cleaned' => 0,
        'errors' => 0,
    ];

    public function handle(): int
    {
        $this->dryRun = $this->option('dry-run');
        $step = $this->option('step') ?? 'all';

        $this->info('🧹 Starting Certification Data Cleanup...');
        if ($this->dryRun) {
            $this->warn('🔍 DRY RUN MODE - No actual changes will be made');
        }
        $this->newLine();

        DB::beginTransaction();

        try {
            match ($step) {
                'names' => $this->cleanTraineeNames(),
                'serials' => $this->cleanSerialNumbers(),
                'dates' => $this->cleanDates(),
                'nationalities' => $this->cleanNationalities(),
                'trainers' => $this->cleanTrainers(),
                'document-types' => $this->cleanDocumentTypes(),
                'paper-status' => $this->cleanPaperStatus(),
                'all' => $this->cleanAll(),
                default => $this->error("Unknown step: {$step}")
            };

            if ($this->dryRun) {
                DB::rollBack();
                $this->info('🔄 Transaction rolled back (dry run)');
            } else {
                DB::commit();
                $this->info('✅ Transaction committed');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error during cleanup: ' . $e->getMessage());
            Log::error('Certification data cleanup failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }

        $this->displayStats();
        return Command::SUCCESS;
    }

    private function cleanAll(): void
    {
        $this->cleanTraineeNames();
        $this->cleanSerialNumbers();
        $this->cleanDates();
        $this->cleanDocumentTypes();
        $this->cleanTrainers();
        $this->cleanNationalities();
        $this->cleanPaperStatus();
    }

    private function cleanTraineeNames(): void
    {
        $this->info('👤 Cleaning Trainee Names...');

        $certifications = Certification::whereNotNull('trainee_name')
            ->where('trainee_name', '!=', '')
            ->get();

        $cleaned = 0;
        foreach ($certifications as $cert) {
            $originalName = $cert->trainee_name;
            $cleanedName = $this->cleanPersonName($originalName);

            if ($cleanedName !== $originalName) {
                $this->line("  • \"{$originalName}\" → \"{$cleanedName}\"");

                if (!$this->dryRun) {
                    $cert->update(['trainee_name' => $cleanedName]);
                }
                $cleaned++;
            }
            $this->stats['processed']++;
        }

        $this->stats['cleaned'] += $cleaned;
        $this->info("✅ Cleaned {$cleaned} trainee names");
        $this->newLine();
    }

    private function cleanSerialNumbers(): void
    {
        $this->info('🔢 Cleaning Serial Numbers...');

        $certifications = Certification::whereNotNull('accredited_serial_number')
            ->where('accredited_serial_number', '!=', '')
            ->get();

        $cleaned = 0;
        $duplicatesFixed = 0;

        foreach ($certifications as $cert) {
            $originalSerial = $cert->accredited_serial_number;
            $cleanedSerial = $this->cleanSerialNumber($originalSerial);

            if ($cleanedSerial !== $originalSerial) {
                $this->line("  • \"{$originalSerial}\" → \"{$cleanedSerial}\"");

                if (!$this->dryRun) {
                    $cert->update(['accredited_serial_number' => $cleanedSerial]);
                }
                $cleaned++;
            }
            $this->stats['processed']++;
        }

        // Handle duplicates by adding suffix
        $duplicates = Certification::select('accredited_serial_number')
            ->whereNotNull('accredited_serial_number')
            ->groupBy('accredited_serial_number')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('accredited_serial_number');

        foreach ($duplicates as $duplicateSerial) {
            $duplicateCerts = Certification::where('accredited_serial_number', $duplicateSerial)
                ->orderBy('id')
                ->get();

            foreach ($duplicateCerts->skip(1) as $index => $cert) {
                $newSerial = $duplicateSerial . '_' . ($index + 2);
                $this->warn("  • Duplicate: \"{$duplicateSerial}\" → \"{$newSerial}\"");

                if (!$this->dryRun) {
                    $cert->update(['accredited_serial_number' => $newSerial]);
                }
                $duplicatesFixed++;
            }
        }

        $this->stats['cleaned'] += $cleaned + $duplicatesFixed;
        $this->info("✅ Cleaned {$cleaned} serial numbers, fixed {$duplicatesFixed} duplicates");
        $this->newLine();
    }

    private function cleanDates(): void
    {
        $this->info('📅 Cleaning Dates...');

        $certifications = Certification::whereNotNull('accreditation_date')->get();
        $cleaned = 0;
        $nullified = 0;

        foreach ($certifications as $cert) {
            $originalDate = $cert->accreditation_date;

            // Check for future dates or very old dates
            if ($originalDate > now() || $originalDate < Carbon::create(1900)) {
                $this->warn("  • Invalid date removed: {$originalDate} (Serial: {$cert->accredited_serial_number})");

                if (!$this->dryRun) {
                    $cert->update(['accreditation_date' => null]);
                }
                $nullified++;
            }

            $this->stats['processed']++;
        }

        $this->stats['cleaned'] += $nullified;
        $this->info("✅ Removed {$nullified} invalid dates");
        $this->newLine();
    }

    private function cleanDocumentTypes(): void
    {
        $this->info('📄 Cleaning Document Types...');

        $certifications = Certification::whereNotNull('document_type')
            ->where('document_type', '!=', '')
            ->get();

        $cleaned = 0;
        foreach ($certifications as $cert) {
            $originalType = $cert->document_type;
            $normalizedType = DocumentType::normalize($originalType);

            if ($normalizedType !== $originalType) {
                $this->line("  • \"{$originalType}\" → \"{$normalizedType}\"");

                if (!$this->dryRun) {
                    $cert->update(['document_type' => $normalizedType]);
                }
                $cleaned++;
            }
            $this->stats['processed']++;
        }

        $this->stats['cleaned'] += $cleaned;
        $this->info("✅ Normalized {$cleaned} document types");
        $this->newLine();
    }

    private function cleanTrainers(): void
    {
        $this->info('👨‍🏫 Cleaning and Creating Trainer Records...');

        $trainerNames = Certification::whereNotNull('trainer_name')
            ->where('trainer_name', '!=', '')
            ->select('trainer_name')
            ->distinct()
            ->pluck('trainer_name');

        $created = 0;
        $updated = 0;

        foreach ($trainerNames as $trainerName) {
            $cleanedName = $this->cleanPersonName($trainerName);

            if (!$this->dryRun) {
                $trainer = Trainer::firstOrCreate(
                    ['name' => $cleanedName],
                    [
                        'email' => null,
                        'phone' => null,
                        'country_id' => null,
                        'specializations' => ['Training'],
                        'is_active' => true
                    ]
                );

                if ($trainer->wasRecentlyCreated) {
                    $created++;
                    $this->line("  • Created trainer: \"{$cleanedName}\"");
                }

                // Link certifications to trainer
                $linkedCount = Certification::where('trainer_name', $trainerName)
                    ->whereNull('trainer_id')
                    ->update(['trainer_id' => $trainer->id]);

                if ($linkedCount > 0) {
                    $updated += $linkedCount;
                }
            } else {
                $this->line("  • Would create trainer: \"{$cleanedName}\"");
                $created++;
            }
        }

        $this->stats['cleaned'] += $created;
        $this->info("✅ Created {$created} trainer records, linked {$updated} certifications");
        $this->newLine();
    }

    private function cleanNationalities(): void
    {
        $this->info('🌍 Cleaning and Creating Country Records...');

        $nationalities = Certification::whereNotNull('nationality')
            ->where('nationality', '!=', '')
            ->select('nationality')
            ->distinct()
            ->pluck('nationality');

        $countryMapping = [
            'Libya' => 'Libya',
            'Syrian' => 'Syria',
            'Syria' => 'Syria',
            'Egyptian' => 'Egypt',
            'Egypt' => 'Egypt',
            'Yemeni' => 'Yemen',
            'Yemen' => 'Yemen',
            'Palestine' => 'Palestine',
            'Palestinian' => 'Palestine',
            'Mauritania' => 'Mauritania',
            'Mauritanian' => 'Mauritania',
            'Sultanate of Oman' => 'Oman',
            'Oman' => 'Oman',
            'Omani' => 'Oman',
        ];

        $created = 0;
        $updated = 0;

        foreach ($nationalities as $nationality) {
            $cleanedNationality = trim($nationality);
            $standardCountry = $countryMapping[$cleanedNationality] ?? $cleanedNationality;

            if (!$this->dryRun) {
                $country = Country::firstOrCreate(
                    ['name' => $standardCountry],
                    [
                        'code' => strtoupper(substr($standardCountry, 0, 2)),
                        'code_2' => strtoupper(substr($standardCountry, 0, 2)),
                        'nationality' => $standardCountry,
                        'is_active' => true
                    ]
                );

                if ($country->wasRecentlyCreated) {
                    $created++;
                    $this->line("  • Created country: \"{$standardCountry}\"");
                }

                // Link certifications to country
                $linkedCount = Certification::where('nationality', $nationality)
                    ->whereNull('country_id')
                    ->update(['country_id' => $country->id]);

                if ($linkedCount > 0) {
                    $updated += $linkedCount;
                }
            } else {
                $this->line("  • Would create country: \"{$standardCountry}\" for \"{$nationality}\"");
                $created++;
            }
        }

        $this->stats['cleaned'] += $created;
        $this->info("✅ Created {$created} country records, linked {$updated} certifications");
        $this->newLine();
    }

    private function cleanPaperStatus(): void
    {
        $this->info('📋 Cleaning Paper Received Status...');

        $statusMapping = [
            'YAS' => 'YES',
            'Yes' => 'YES',
            'yes' => 'YES',
            'NO' => 'NO',
            'No' => 'NO',
            'no' => 'NO',
            'PENDING' => 'PENDING',
            'Pending' => 'PENDING',
            'pending' => 'PENDING',
        ];

        $cleaned = 0;
        $certifications = Certification::whereNotNull('paper_received')
            ->where('paper_received', '!=', '')
            ->get();

        foreach ($certifications as $cert) {
            $originalStatus = $cert->paper_received;
            $cleanedStatus = $statusMapping[$originalStatus] ?? 'PENDING';

            if ($cleanedStatus !== $originalStatus) {
                $this->line("  • \"{$originalStatus}\" → \"{$cleanedStatus}\"");

                if (!$this->dryRun) {
                    $cert->update(['paper_received' => $cleanedStatus]);
                }
                $cleaned++;
            }
            $this->stats['processed']++;
        }

        $this->stats['cleaned'] += $cleaned;
        $this->info("✅ Standardized {$cleaned} paper received statuses");
        $this->newLine();
    }

    private function cleanPersonName(string $name): string
    {
        // Remove extra whitespace
        $cleaned = preg_replace('/\s+/', ' ', trim($name));

        // Remove leading/trailing special characters
        $cleaned = trim($cleaned, ' .,;-_');

        // Title case for better consistency
        $cleaned = ucwords(strtolower($cleaned));

        // Handle Arabic names properly (don't change case)
        if (preg_match('/[\x{0600}-\x{06FF}]/u', $name)) {
            $cleaned = trim($name);
        }

        return $cleaned;
    }

    private function cleanSerialNumber(string $serial): string
    {
        // Remove whitespace
        $cleaned = trim($serial);

        // Ensure uppercase for letter parts
        if (preg_match('/^([A-Za-z]+)(\d+)$/', $cleaned, $matches)) {
            $cleaned = strtoupper($matches[1]) . $matches[2];
        }

        return $cleaned;
    }

    private function displayStats(): void
    {
        $this->newLine();
        $this->info('📊 CLEANUP STATISTICS');
        $this->line('=====================');

        $this->table(['Metric', 'Count'], [
            ['Records Processed', $this->stats['processed']],
            ['Records Cleaned', $this->stats['cleaned']],
            ['Errors Encountered', $this->stats['errors']],
        ]);

        if ($this->dryRun) {
            $this->warn('⚠️  This was a dry run - no actual changes were made');
            $this->info('💡 Run without --dry-run to apply changes');
        } else {
            $this->info('✅ Data cleanup completed successfully!');
        }
    }
}
