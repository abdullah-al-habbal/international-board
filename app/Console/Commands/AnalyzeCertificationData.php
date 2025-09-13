<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Certification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AnalyzeCertificationData extends Command
{
    protected $signature = 'certifications:analyze {--column=all : Specific column to analyze} {--export= : Export results to file}';
    protected $description = 'Analyze certification data to identify issues and patterns with comprehensive reporting';

    private const KNOWN_DOCUMENT_TYPES = [
        'training_of_trainers',
        'accreditation_center',
        'experience_certificate',
        'adviser_certificate',
        'consultant_training',
        'specialization_certificate',
        'icdl_certificate',
        'basic_certificate',
    ];

    public function handle(): int
    {
        $this->info('🔍 Starting Comprehensive Certification Data Analysis...');
        $this->newLine();

        $this->runAnalysis($this->option('column') ?? 'all');

        $this->newLine();
        $this->info('✅ Analysis completed!');

        if ($exportFile = $this->option('export')) {
            $this->exportResults($exportFile);
        }

        return Command::SUCCESS;
    }

    private function runAnalysis(string $column): void
    {
        match ($column) {
            'trainee_name' => $this->analyzeTraineeNames(),
            'serial' => $this->analyzeSerialNumbers(),
            'document_type' => $this->analyzeDocumentTypes(),
            'trainer_name' => $this->analyzeTrainerNames(),
            'nationality' => $this->analyzeNationalities(),
            'dates' => $this->analyzeDates(),
            'paper_received' => $this->analyzePaperReceived(),
            'notes' => $this->analyzeNotes(),
            'all' => $this->analyzeAllColumns(),
            default => $this->error("Unknown column: {$column}"),
        };
    }

    private function buildTable(array $headers, array $data): void
    {
        $this->table($headers, $data);
    }

    private function buildPercentageTable(array $metrics): void
    {
        $this->table(['Metric', 'Count', 'Percentage'], $metrics);
    }

    private function getDataQualitySummary(): array
    {
        return [
            'complete_records' => Certification::whereNotNull('trainee_name')
                ->whereNotNull('accredited_serial_number')
                ->whereNotNull('accreditation_date')
                ->count(),
            'with_countries' => Certification::whereNotNull('country_id')->count(),
            'with_trainers' => Certification::whereNotNull('trainer_id')->count(),
            'duplicate_serials' => Certification::select('accredited_serial_number')
                ->whereNotNull('accredited_serial_number')
                ->groupBy('accredited_serial_number')
                ->havingRaw('COUNT(*) > 1')
                ->count(),
        ];
    }

    private function analyzeAllColumns(): void
    {
        $this->analyzeTraineeNames();
        $this->analyzeSerialNumbers();
        $this->analyzeDocumentTypes();
        $this->analyzeTrainerNames();
        $this->analyzeNationalities();
        $this->analyzeDates();
        $this->analyzePaperReceived();
        $this->analyzeNotes();
        $this->analyzeDataQuality();
        $this->analyzeMissingRelationships();
    }


    private function analyzeTraineeNames(): void
    {
        $this->info('👤 TRAINEE NAMES ANALYSIS');
        $this->line('================================');

        $total = Certification::count();
        $withNames = Certification::whereNotNull('trainee_name')->where('trainee_name', '!=', '')->count();
        $nullNames = $total - $withNames;

        $this->buildPercentageTable([
            ['Total Records', $total, '100%'],
            ['With Names', $withNames, round(($withNames / $total) * 100, 2) . '%'],
            ['Without Names', $nullNames, round(($nullNames / $total) * 100, 2) . '%'],
        ]);

        $issues = [
            'Leading/Trailing Spaces' => Certification::whereRaw("trainee_name != TRIM(trainee_name)")->count(),
            'Multiple Spaces' => Certification::whereRaw("trainee_name LIKE '%  %'")->count(),
            'Contains Numbers' => Certification::whereRaw("trainee_name REGEXP '[0-9]'")->count(),
            'Contains Special Chars' => Certification::whereRaw("trainee_name REGEXP '[^a-zA-Z\u0600-\u06FF ]'")->count(),
            'Very Short (< 3 chars)' => Certification::whereRaw("LENGTH(trainee_name) < 3")->count(),
            'Very Long (> 100 chars)' => Certification::whereRaw("LENGTH(trainee_name) > 100")->count(),
        ];

        $this->info('🔍 Name Quality Issues:');
        foreach ($issues as $issue => $count) {
            if ($count > 0) {
                $this->warn("  • {$issue}: {$count} records");
            }
        }

        $problematic = Certification::whereRaw("trainee_name REGEXP '[0-9]' OR trainee_name LIKE '%  %' OR LENGTH(trainee_name) < 3")
            ->limit(5)
            ->pluck('trainee_name');

        if ($problematic->count() > 0) {
            $this->info('📋 Sample Problematic Names:');
            foreach ($problematic as $name) {
                $this->line("  • \"{$name}\"");
            }
        }

        $this->newLine();
    }

    private function analyzeSerialNumbers(): void
    {
        $this->info('🔢 SERIAL NUMBERS ANALYSIS');
        $this->line('=================================');

        $total = Certification::count();
        $withSerial = Certification::whereNotNull('accredited_serial_number')->where('accredited_serial_number', '!=', '')->count();
        $duplicates = Certification::select('accredited_serial_number')
            ->whereNotNull('accredited_serial_number')
            ->groupBy('accredited_serial_number')
            ->havingRaw('COUNT(*) > 1')
            ->count();

        $this->buildTable(['Metric', 'Count'], [
            ['Total Records', $total],
            ['With Serial Numbers', $withSerial],
            ['Missing Serial Numbers', $total - $withSerial],
            ['Duplicate Serial Numbers', $duplicates],
        ]);

        $patterns = DB::table('certifications')
            ->selectRaw("
                SUM(CASE WHEN accredited_serial_number REGEXP '^IB[0-9]+$' THEN 1 ELSE 0 END) as ib_pattern,
                SUM(CASE WHEN accredited_serial_number REGEXP '^[A-Z]{2}[0-9]+$' THEN 1 ELSE 0 END) as letter_number_pattern,
                SUM(CASE WHEN accredited_serial_number REGEXP '^[0-9]+$' THEN 1 ELSE 0 END) as only_numbers,
                SUM(CASE WHEN accredited_serial_number IS NOT NULL AND accredited_serial_number NOT REGEXP '^(IB|[A-Z]{2})[0-9]+$' THEN 1 ELSE 0 END) as irregular_pattern
            ")
            ->first();

        $this->info('📊 Serial Number Patterns:');
        $this->buildTable(['Pattern', 'Count'], [
            ['IB + Numbers (IB123)', $patterns->ib_pattern],
            ['2 Letters + Numbers', $patterns->letter_number_pattern],
            ['Only Numbers', $patterns->only_numbers],
            ['Irregular Patterns', $patterns->irregular_pattern],
        ]);

        if ($duplicates > 0) {
            $duplicatesList = Certification::select('accredited_serial_number', DB::raw('COUNT(*) as count'))
                ->whereNotNull('accredited_serial_number')
                ->groupBy('accredited_serial_number')
                ->havingRaw('COUNT(*) > 1')
                ->limit(5)
                ->get();

            $this->warn('⚠️ Sample Duplicate Serial Numbers:');
            foreach ($duplicatesList as $dup) {
                $this->line("  • {$dup->accredited_serial_number}: {$dup->count} times");
            }
        }

        $this->newLine();
    }

    private function analyzeDocumentTypes(): void
    {
        $this->info('📄 DOCUMENT TYPES ANALYSIS');
        $this->line('==========================');

        $types = Certification::select('document_type', DB::raw('COUNT(*) as count'))
            ->whereNotNull('document_type')
            ->groupBy('document_type')
            ->orderBy('count', 'desc')
            ->get();

        $this->info('📊 Current Document Types:');
        $tableData = [];
        foreach ($types as $type) {
            $tableData[] = [$type->document_type, $type->count];
        }
        $this->buildTable(['Document Type', 'Count'], $tableData);

        $unmapped = $types->pluck('document_type')->filter(fn($type) => !in_array($type, self::KNOWN_DOCUMENT_TYPES));

        if (!empty($unmapped)) {
            $this->warn('⚠️ Unmapped Document Types (need enum mapping):');
            foreach ($unmapped as $type) {
                $this->line("  • \"{$type}\"");
            }
        }

        $this->newLine();
    }

    private function analyzeTrainerNames(): void
    {
        $this->info('👨‍🏫 TRAINER NAMES ANALYSIS');
        $this->line('==========================');

        $total = Certification::count();
        $withTrainer = Certification::whereNotNull('trainer_name')->where('trainer_name', '!=', '')->count();

        $trainers = Certification::select('trainer_name', DB::raw('COUNT(*) as count'))
            ->whereNotNull('trainer_name')
            ->where('trainer_name', '!=', '')
            ->groupBy('trainer_name')
            ->orderBy('count', 'desc')
            ->get();

        $this->buildTable(['Metric', 'Count'], [
            ['Total Records', $total],
            ['With Trainer Names', $withTrainer],
            ['Unique Trainers', $trainers->count()],
        ]);

        $this->info('📊 Top Trainers:');
        $topTrainers = $trainers->take(10);
        $trainerTable = [];
        foreach ($topTrainers as $trainer) {
            $trainerTable[] = [$trainer->trainer_name, $trainer->count];
        }
        $this->buildTable(['Trainer Name', 'Certifications'], $trainerTable);

        $this->newLine();
    }

    private function analyzeNationalities(): void
    {
        $this->info('🌍 NATIONALITIES ANALYSIS');
        $this->line('=========================');

        $nationalities = Certification::select('nationality', DB::raw('COUNT(*) as count'))
            ->whereNotNull('nationality')
            ->where('nationality', '!=', '')
            ->groupBy('nationality')
            ->orderBy('count', 'desc')
            ->get();

        $this->info('📊 Current Nationalities:');
        $nationalityTable = [];
        foreach ($nationalities as $nat) {
            $nationalityTable[] = [trim($nat->nationality), $nat->count];
        }
        $this->buildTable(['Nationality', 'Count'], $nationalityTable);

        $this->newLine();
    }

    private function analyzeDates(): void
    {
        $this->info('📅 DATES ANALYSIS');
        $this->line('==================');

        $total = Certification::count();
        $withDates = Certification::whereNotNull('accreditation_date')->count();
        $futureDates = Certification::where('accreditation_date', '>', now())->count();
        $oldDates = Certification::where('accreditation_date', '<', '1900-01-01')->count();

        $dateRange = DB::table('certifications')
            ->selectRaw('MIN(accreditation_date) as min_date, MAX(accreditation_date) as max_date')
            ->whereNotNull('accreditation_date')
            ->first();

        $this->buildTable(['Metric', 'Count'], [
            ['Total Records', $total],
            ['With Valid Dates', $withDates],
            ['Missing Dates', $total - $withDates],
            ['Future Dates (Invalid)', $futureDates],
            ['Very Old Dates (< 1900)', $oldDates],
            ['Date Range', $dateRange->min_date . ' to ' . $dateRange->max_date],
        ]);

        $this->newLine();
    }

    private function analyzePaperReceived(): void
    {
        $this->info('📋 PAPER RECEIVED ANALYSIS');
        $this->line('==========================');

        $paperStats = Certification::select('paper_received', DB::raw('COUNT(*) as count'))
            ->groupBy('paper_received')
            ->get();

        $this->info('📊 Paper Received Status:');
        $paperTable = [];
        foreach ($paperStats as $stat) {
            $status = $stat->paper_received ?: 'NULL/Empty';
            $paperTable[] = [$status, $stat->count];
        }
        $this->buildTable(['Status', 'Count'], $paperTable);

        $this->newLine();
    }

    private function analyzeNotes(): void
    {
        $this->info('📝 NOTES ANALYSIS');
        $this->line('==================');

        $total = Certification::count();
        $withNotes = Certification::whereNotNull('notes')->where('notes', '!=', '')->count();
        $avgLength = Certification::whereNotNull('notes')->where('notes', '!=', '')->avg(DB::raw('LENGTH(notes)'));

        $this->buildTable(['Metric', 'Value'], [
            ['Total Records', $total],
            ['With Notes', $withNotes],
            ['Without Notes', $total - $withNotes],
            ['Average Note Length', round($avgLength, 2) . ' characters'],
        ]);

        $this->newLine();
    }

    private function analyzeDataQuality(): void
    {
        $this->info('🔍 Data Quality Analysis:');

        $duplicates = Certification::select('accredited_serial_number', DB::raw('COUNT(*) as count'))
            ->whereNotNull('accredited_serial_number')
            ->where('accredited_serial_number', '!=', '')
            ->groupBy('accredited_serial_number')
            ->having('count', '>', 1)
            ->count();

        $this->line("Duplicate serial numbers: {$duplicates}");

        $invalidDates = Certification::whereNotNull('accreditation_date')
            ->where('accreditation_date', '<', '1900-01-01')
            ->orWhere('accreditation_date', '>', now()->addYear())
            ->count();

        $this->line("Invalid dates: {$invalidDates}");

        $missingTrainee = Certification::whereNull('trainee_name')->orWhere('trainee_name', '')->count();
        $this->line("Missing trainee names: {$missingTrainee}");

        $this->newLine();
    }

    private function analyzeMissingRelationships(): void
    {
        $this->info('🔗 Relationship Analysis:');

        $orphaned = Certification::whereNull('certified_center_id')->count();
        $this->line("Certifications without center: {$orphaned}");

        $centersExist = DB::table('certified_centers')->exists();
        $this->line("Certified centers table exists: " . ($centersExist ? 'Yes' : 'No'));

        if ($centersExist) {
            $centerCount = DB::table('certified_centers')->count();
            $this->line("Total certified centers: {$centerCount}");
        }

        $withoutCountry = Certification::whereNull('country_id')->count();
        $this->line("Certifications without country: {$withoutCountry}");

        $withoutTrainer = Certification::whereNull('trainer_id')->count();
        $this->line("Certifications without trainer: {$withoutTrainer}");

        $this->newLine();
    }

    private function exportResults(string $filename): void
    {
        $this->info("📄 Exporting results to: {$filename}");

        $data = [
            'analysis_date' => now()->toDateTimeString(),
            'total_certifications' => Certification::count(),
            'data_quality_summary' => $this->getDataQualitySummary(),
            'document_types' => Certification::select('document_type', DB::raw('COUNT(*) as count'))
                ->whereNotNull('document_type')
                ->groupBy('document_type')
                ->get()
                ->toArray(),
            'nationalities' => Certification::select('nationality', DB::raw('COUNT(*) as count'))
                ->whereNotNull('nationality')
                ->groupBy('nationality')
                ->orderByDesc('count')
                ->get()
                ->toArray(),
        ];

        file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));
        $this->info("✅ Results exported successfully!");
    }
}
