<?php

namespace App\Console\Commands;

use App\Models\Certification;
use App\Models\CertifiedCenter;
use Illuminate\Console\Command;

class CleanupCertificationData extends Command
{
    protected $signature = 'certifications:cleanup 
                            {--dry-run : Show what would be changed without making changes}
                            {--batch-size=100 : Number of records to process at once}';

    protected $description = 'Clean up and normalize imported certification data';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $batchSize = (int) $this->option('batch-size');

        $this->info('Starting certification data cleanup...');

        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        $this->cleanupNationalities($isDryRun, $batchSize);
        $this->assignCertificateTypes($isDryRun, $batchSize);
        $this->normalizePaperReceived($isDryRun, $batchSize);
        $this->assignDefaultCenters($isDryRun, $batchSize);
        $this->fixAccreditationNumbers($isDryRun, $batchSize);

        $this->info('Cleanup completed!');
    }

    private function cleanupNationalities(bool $isDryRun, int $batchSize): void
    {
        $this->info('Cleaning up nationalities...');

        $nationalityMappings = [
            'Libya' => 'Libyan',
            'Syria' => 'Syrian',
            'Egypt' => 'Egyptian',
            'Egyptian' => 'Egyptian',
            'Yemen' => 'Yemeni',
            'Yemeni' => 'Yemeni',
            'Mauritania' => 'Mauritanian',
        ];

        foreach ($nationalityMappings as $from => $to) {
            $count = Certification::where('nationality', 'like', "%{$from}%")->count();

            if ($count > 0) {
                $this->line("  - Found {$count} records with nationality '{$from}' -> '{$to}'");

                if (!$isDryRun) {
                    Certification::where('nationality', 'like', "%{$from}%")
                        ->chunk($batchSize, function ($certifications) use ($to) {
                            foreach ($certifications as $cert) {
                                $cert->update(['nationality' => $to]);
                            }
                        });
                }
            }
        }
    }

    private function assignCertificateTypes(bool $isDryRun, int $batchSize): void
    {
        $this->info('Assigning certificate types based on document types...');

        $typeRules = [
            ['contains' => ['training of trainers', 'tot'], 'type' => 'professional'],
            ['contains' => ['accreditation center'], 'type' => 'advanced'],
            ['contains' => ['experience', 'adviser', 'consultant'], 'type' => 'specialist'],
            ['default' => 'basic'],
        ];

        Certification::whereNull('certificate_type')
            ->orWhere('certificate_type', '')
            ->chunk($batchSize, function ($certifications) use ($isDryRun, $typeRules) {
                foreach ($certifications as $cert) {
                    $documentType = strtolower($cert->document_type ?? '');
                    $assignedType = 'basic';

                    foreach ($typeRules as $rule) {
                        if (isset($rule['contains'])) {
                            foreach ($rule['contains'] as $keyword) {
                                if (str_contains($documentType, $keyword)) {
                                    $assignedType = $rule['type'];
                                    break 2;
                                }
                            }
                        }
                    }

                    if ($cert->certificate_type !== $assignedType) {
                        $this->line("  - {$cert->trainee_name}: {$cert->document_type} -> {$assignedType}");

                        if (!$isDryRun) {
                            $cert->update(['certificate_type' => $assignedType]);
                        }
                    }
                }
            });
    }

    private function normalizePaperReceived(bool $isDryRun, int $batchSize): void
    {
        $this->info('Normalizing paper received status...');

        $yesValues = ['YES', 'YAS', 'yes', 'yas', '1', 'true'];
        $noValues = ['NO', 'no', '0', 'false', 'N'];

        Certification::whereNotNull('paper_received')
            ->chunk($batchSize, function ($certifications) use ($isDryRun, $yesValues, $noValues) {
                foreach ($certifications as $cert) {
                    $current = trim($cert->paper_received);
                    $normalized = null;

                    if (in_array($current, $yesValues)) {
                        $normalized = 'YES';
                    } elseif (in_array($current, $noValues)) {
                        $normalized = 'NO';
                    }

                    if ($normalized && $cert->paper_received !== $normalized) {
                        $this->line("  - {$cert->trainee_name}: '{$current}' -> '{$normalized}'");

                        if (!$isDryRun) {
                            $cert->update(['paper_received' => $normalized]);
                        }
                    }
                }
            });
    }

    private function assignDefaultCenters(bool $isDryRun, int $batchSize): void
    {
        $this->info('Assigning default centers for orphaned certifications...');

        $defaultCenter = CertifiedCenter::first();

        if (!$defaultCenter) {
            $this->warn('No certified centers found. Skipping center assignment.');
            return;
        }

        $orphanedCount = Certification::whereNull('certified_center_id')->count();

        if ($orphanedCount > 0) {
            $this->line("  - Found {$orphanedCount} certifications without assigned centers");
            $this->line("  - Will assign to: {$defaultCenter->name}");

            if (!$isDryRun) {
                if ($this->confirm("Assign all orphaned certifications to '{$defaultCenter->name}'?")) {
                    Certification::whereNull('certified_center_id')
                        ->chunk($batchSize, function ($certifications) use ($defaultCenter) {
                            Certification::whereIn('id', $certifications->pluck('id'))
                                ->update(['certified_center_id' => $defaultCenter->id]);
                        });

                    $this->info("  - Assigned {$orphanedCount} certifications to {$defaultCenter->name}");
                }
            }
        }
    }

    private function fixAccreditationNumbers(bool $isDryRun, int $batchSize): void
    {
        $this->info('Fixing accreditation numbers...');

        Certification::where('trainee_name', 'like', '%Mohammed Amou Hawbe%')
            ->where('accreditation_number', 'IB14423')
            ->chunk($batchSize, function ($certifications) use ($isDryRun) {
                foreach ($certifications as $cert) {
                    $this->line("  - Fixing accreditation number for {$cert->trainee_name}: IB14423 -> IB14323");

                    if (!$isDryRun) {
                        $cert->update(['accreditation_number' => 'IB14323']);
                    }
                }
            });

        Certification::whereNotNull('accredited_serial_number')
            ->whereNotNull('document_code')
            ->chunk($batchSize, function ($certifications) use ($isDryRun) {
                foreach ($certifications as $cert) {
                    $expected = $cert->accredited_serial_number . $cert->document_code;

                    if ($cert->accreditation_number !== $expected) {
                        $this->line("  - Reconstructing accreditation number for {$cert->trainee_name}: {$cert->accreditation_number} -> {$expected}");

                        if (!$isDryRun) {
                            $cert->update(['accreditation_number' => $expected]);
                        }
                    }
                }
            });
    }
}
