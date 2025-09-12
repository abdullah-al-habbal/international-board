<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Certification;
use App\Models\Country;
use App\Models\Trainer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MigrateCertificationData extends Command
{
    protected $signature = 'certifications:migrate {--dry-run : Show what would be changed without making changes}';
    protected $description = 'Migrate existing certification data to use new relationships';

    private bool $dryRun = false;
    private int $changesCount = 0;

    public function handle(): int
    {
        $this->dryRun = $this->option('dry-run');

        $this->info('🚀 Starting Certification Data Migration...');
        if ($this->dryRun) {
            $this->warn('🔍 DRY RUN MODE - No actual changes will be made');
        }
        $this->newLine();

        // Check if migration is needed
        if (!$this->isMigrationCompleted()) {
            $this->info('✅ Migration appears to be already completed.');
            $this->info('💡 Use --force to run migration anyway');
            return Command::SUCCESS;
        }

        DB::beginTransaction();

        try {
            $this->seedCountries();
            $this->createTrainersFromCertifications();
            $this->linkCertificationsToRelations();
            $this->updateTrainerStats();

            if ($this->dryRun) {
                DB::rollBack();
                $this->info('🔄 Transaction rolled back (dry run)');
            } else {
                DB::commit();
                $this->info('✅ Transaction committed');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error during migration: ' . $e->getMessage());
            Log::error('Certification data migration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }

        $this->displayStats();
        return Command::SUCCESS;
    }

    private function isMigrationCompleted(): bool
    {
        $countriesExist = Country::count() > 0;
        $trainersExist = Trainer::count() > 0;
        $certificationsLinked = Certification::whereNotNull('country_id')
            ->orWhereNotNull('trainer_id')
            ->count() > 0;

        return $countriesExist && $trainersExist && $certificationsLinked;
    }

    private function seedCountries(): void
    {
        $this->info('🌍 Seeding Country Records...');

        // Run the country seeder
        if (!$this->dryRun) {
            $this->call('db:seed', ['--class' => 'CountrySeeder']);
            $this->info("✅ Countries seeded successfully");
        } else {
            $this->line("  • Would seed countries from CountrySeeder");
        }

        // Create countries from nationalities in certifications
        $additionalNationalities = Certification::whereNotNull('nationality')
            ->where('nationality', '!=', '')
            ->select('nationality')
            ->distinct()
            ->pluck('nationality');

        $nationalityMapping = [
            'Libyan' => 'Libya',
            'Syrian' => 'Syria',
            'Egyptian' => 'Egypt',
            'Yemeni' => 'Yemen',
            'Palestinian' => 'Palestine',
            'Mauritanian' => 'Mauritania',
            'Sultanate of Oman' => 'Oman',
            'Omani' => 'Oman',
            'Saudi' => 'Saudi Arabia',
            'Jordanian' => 'Jordan',
            'Lebanese' => 'Lebanon',
            'Iraqi' => 'Iraq',
            'Kuwaiti' => 'Kuwait',
            'Emirati' => 'UAE',
            'Qatari' => 'Qatar',
            'Bahraini' => 'Bahrain',
            'Moroccan' => 'Morocco',
            'Tunisian' => 'Tunisia',
            'Algerian' => 'Algeria',
            'Sudanese' => 'Sudan',
        ];

        foreach ($additionalNationalities as $nationality) {
            $countryName = $nationalityMapping[trim($nationality)] ?? trim($nationality);

            if (!Country::where('name', $countryName)->exists()) {
                if (!$this->dryRun) {
                    $country = Country::create([
                        'name' => $countryName,
                        'code' => strtoupper(substr($countryName, 0, 2)),
                        'code_2' => strtoupper(substr($countryName, 0, 2)),
                        'nationality' => $countryName,
                        'is_active' => true
                    ]);
                    $this->changesCount++;
                    $this->line("  • Created from nationality: {$countryName}");
                } else {
                    $this->line("  • Would create from nationality: {$countryName}");
                    $this->changesCount++;
                }
            }
        }

        $this->info("✅ Countries processed: {$this->changesCount} created");
        $this->newLine();
    }

    private function createTrainersFromCertifications(): void
    {
        $this->info('👨‍🏫 Creating Trainer Records...');

        $trainerNames = Certification::whereNotNull('trainer_name')
            ->where('trainer_name', '!=', '')
            ->select('trainer_name')
            ->distinct()
            ->pluck('trainer_name');

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
                        'is_active' => true,
                    ]
                );

                if ($trainer->wasRecentlyCreated) {
                    $this->changesCount++;
                    $this->line("  • Created: {$cleanedName}");
                }
            } else {
                $this->line("  • Would create: {$cleanedName}");
                $this->changesCount++;
            }
        }

        $this->info("✅ Trainers processed: {$this->changesCount} created");
        $this->newLine();
    }

    private function linkCertificationsToRelations(): void
    {
        $this->info('🔗 Linking Certifications to Countries and Trainers...');

        $certifications = Certification::whereNull('country_id')
            ->orWhereNull('trainer_id')
            ->get();

        $linked = 0;
        foreach ($certifications as $certification) {
            $updated = false;

            // Link to country
            if (is_null($certification->country_id) && !empty($certification->nationality)) {
                $country = $this->findCountryByNationality($certification->nationality);
                if ($country) {
                    if (!$this->dryRun) {
                        $certification->update(['country_id' => $country->id]);
                    }
                    $updated = true;
                }
            }

            // Link to trainer
            if (is_null($certification->trainer_id) && !empty($certification->trainer_name)) {
                $trainer = Trainer::where('name', $this->cleanPersonName($certification->trainer_name))->first();
                if ($trainer) {
                    if (!$this->dryRun) {
                        $certification->update(['trainer_id' => $trainer->id]);
                    }
                    $updated = true;
                }
            }

            if ($updated) {
                $linked++;
                if ($linked % 100 === 0) {
                    $this->line("  • Processed {$linked} certifications...");
                }
            }
        }

        $this->changesCount = $linked;
        $this->info("✅ Certifications linked: {$linked}");
        $this->newLine();
    }

    private function updateTrainerStats(): void
    {
        if ($this->dryRun) {
            $this->info('📊 Would update trainer statistics...');
            return;
        }

        $this->info('📊 Updating Trainer Statistics...');

        $trainers = Trainer::all();
        foreach ($trainers as $trainer) {
            // Update trainer statistics if method exists
            if (method_exists($trainer, 'updateCertificationsStats')) {
                $trainer->updateCertificationsStats();
            }
        }

        $this->info("✅ Updated statistics for {$trainers->count()} trainers");
        $this->newLine();
    }

    private function findCountryByNationality(string $nationality): ?Country
    {
        $nationality = trim($nationality);

        // Direct match
        $country = Country::where('name', $nationality)->first();
        if ($country) {
            return $country;
        }

        // Mapping match
        $nationalityMapping = [
            'Libyan' => 'Libya',
            'Syrian' => 'Syria',
            'Egyptian' => 'Egypt',
            'Yemeni' => 'Yemen',
            'Palestinian' => 'Palestine',
            'Mauritanian' => 'Mauritania',
            'Sultanate of Oman' => 'Oman',
            'Omani' => 'Oman',
            'Saudi' => 'Saudi Arabia',
            'Jordanian' => 'Jordan',
            'Lebanese' => 'Lebanon',
            'Iraqi' => 'Iraq',
            'Kuwaiti' => 'Kuwait',
            'Emirati' => 'UAE',
            'Qatari' => 'Qatar',
            'Bahraini' => 'Bahrain',
            'Moroccan' => 'Morocco',
            'Tunisian' => 'Tunisia',
            'Algerian' => 'Algeria',
            'Sudanese' => 'Sudan',
        ];

        $mappedCountry = $nationalityMapping[$nationality] ?? null;
        if ($mappedCountry) {
            return Country::where('name', $mappedCountry)->first();
        }

        return null;
    }

    private function cleanPersonName(string $name): string
    {
        $cleaned = preg_replace('/\s+/', ' ', trim($name));
        $cleaned = trim($cleaned, ' .,;-_');

        if (preg_match('/[\x{0600}-\x{06FF}]/u', $name)) {
            return trim($name);
        }

        return ucwords(strtolower($cleaned));
    }

    private function displayStats(): void
    {
        $this->newLine();
        $this->info('📊 MIGRATION STATISTICS');
        $this->line('=======================');

        $this->table(['Metric', 'Count'], [
            ['Changes Made', $this->changesCount],
        ]);

        if ($this->dryRun) {
            $this->warn('⚠️  This was a dry run - no actual changes were made');
            $this->info('💡 Run without --dry-run to apply changes');
        } else {
            $this->info('✅ Data migration completed successfully!');
            $this->newLine();
            $this->info('🎯 Next Steps:');
            $this->line('  1. Run: php artisan certifications:analyze');
            $this->line('  2. Run: php artisan certifications:clean --dry-run');
            $this->line('  3. Run: php artisan certifications:clean');
        }
    }
}
