<?php

declare(strict_types=1);

namespace App\Imports;

use App\Enums\CertificateType;
use App\Models\Certification;
use App\Models\Country;
use App\Models\DocumentType;
use App\Models\Trainer;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

final class CertificationsImport implements ToCollection, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    private const BATCH_SIZE = 500;
    private const CHUNK_SIZE = 500;

    private int $totalRows = 0;
    private int $successfulImports = 0;
    private int $failedImports = 0;
    private int $skippedRows = 0;
    private int $countriesCreated = 0;
    private int $trainersCreated = 0;

    private array $countryCache = [];
    private array $trainerCache = [];

    public function collection(Collection $rows): void
    {
        $this->totalRows += $rows->count();
        Log::info("Processing batch of {$rows->count()} rows");

        $certifications = $rows->map(function (Collection $row) {
            return $this->transformRowToCertification($row);
        })->filter();

        if ($certifications->isNotEmpty()) {
            try {
                $this->insertCertifications($certifications);
                $this->successfulImports += $certifications->count();
            } catch (\Exception $e) {
                $this->failedImports += $certifications->count();
                Log::error('Failed to insert certifications batch', [
                    'error' => $e->getMessage(),
                    'count' => $certifications->count(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
    }

    public function batchSize(): int
    {
        return self::BATCH_SIZE;
    }

    public function chunkSize(): int
    {
        return self::CHUNK_SIZE;
    }

    private function transformRowToCertification(Collection $row): ?array
    {
        if ($this->isRowEmpty($row)) {
            $this->skippedRows++;
            return null;
        }

        // Log available headers for debugging
        if ($this->totalRows === 1) {
            Log::info('Available headers:', $row->keys()->toArray());
        }

        try {
            // Extract data with multiple header variations
            $traineeNameRaw = $this->getValueMultipleKeys($row, [
                'asm_almtdrb',
                'trainee_name',
                'اسم المتدرب'
            ]);
            $serialNumberRaw = $this->getValueMultipleKeys($row, [
                'alrkm_almtslsl_almaatmd',
                'serial_number',
                'الرقم المتسلسل المعتمد'
            ]);
            $documentCodeRaw = $this->getValueMultipleKeys($row, [
                'alrmz',
                'document_code',
                'الرمز'
            ]);
            $accreditationNumberRaw = $this->getValueMultipleKeys($row, [
                'rkm_alaaatmad',
                'accreditation_number',
                'رقم الاعتماد'
            ]);
            $documentTypeRaw = $this->getValueMultipleKeys($row, [
                'noaa_alothyk',
                'document_type',
                'نوع الوثيقة'
            ]);
            $accreditationDateRaw = $this->getValueMultipleKeys($row, [
                'tarykh_alaaatmad',
                'accreditation_date',
                'تاريخ الاعتماد'
            ]);
            $trainerNameRaw = $this->getValueMultipleKeys($row, [
                'asm_almdrb',
                'trainer_name',
                'اسم المدرب'
            ]);
            $nationalityRaw = $this->getValueMultipleKeys($row, [
                'alhnsy',
                'nationality',
                'الجنسية'
            ]);
            $paperReceivedRaw = $this->getValueMultipleKeys($row, [
                'alhsol_aal_alothyk_orkya',
                'paper_received',
                'الحصول على الوثيقة ورقيا'
            ]);
            $notesRaw = $this->getValueMultipleKeys($row, [
                'mlahthat',
                'notes',
                'ملاحظات'
            ]);

            // Skip if essential data is missing
            if (empty($traineeNameRaw) || empty($serialNumberRaw)) {
                $this->skippedRows++;
                return null;
            }

            // Clean and process data
            $traineeName = $this->cleanPersonName($traineeNameRaw);
            $serialNumber = $this->cleanSerialNumber($serialNumberRaw);
            $documentTypeId = $this->getDocumentTypeId($documentTypeRaw);
            $accreditationDate = $this->parseDate($accreditationDateRaw);
            $trainerName = $this->cleanPersonName($trainerNameRaw);
            $nationality = $this->cleanNationality($nationalityRaw);
            $paperReceived = $this->normalizePaperStatus($paperReceivedRaw);

            // Get or create related entities
            $countryId = $this->getOrCreateCountry($nationality);
            $trainerId = $this->getOrCreateTrainer($trainerName);

            $data = [
                'certified_center_id' => null,
                'trainee_name' => $traineeName,
                'accredited_serial_number' => $serialNumber,
                'document_code' => $this->cleanValue($documentCodeRaw),
                'accreditation_number' => $this->cleanValue($accreditationNumberRaw),
                'document_type_id' => $documentTypeId,
                'accreditation_date' => $accreditationDate,
                'trainer_name' => $trainerName,
                'trainer_id' => $trainerId,
                'nationality' => $nationality,
                'country_id' => $countryId,
                'paper_received' => $paperReceived,
                'notes' => $this->cleanValue($notesRaw),
                'certificate_type' => CertificateType::Basic->value,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            return $data;
        } catch (\Exception $e) {
            Log::error('Error transforming row', [
                'error' => $e->getMessage(),
                'row_data' => $row->toArray()
            ]);
            $this->skippedRows++;
            return null;
        }
    }

    private function getValueMultipleKeys(Collection $row, array $keys): ?string
    {
        foreach ($keys as $key) {
            $value = $row->get($key);
            if ($value !== null) {
                return trim((string) $value);
            }

            // Try with normalized key (remove spaces, lowercase)
            $normalizedKey = str_replace([' ', '_'], '', strtolower($key));
            foreach ($row->keys() as $rowKey) {
                $normalizedRowKey = str_replace([' ', '_'], '', strtolower($rowKey));
                if ($normalizedRowKey === $normalizedKey) {
                    $value = $row->get($rowKey);
                    if ($value !== null) {
                        return trim((string) $value);
                    }
                }
            }
        }

        return null;
    }

    private function getValue(Collection $row, string $key): ?string
    {
        $value = $row->get($key);

        if ($value === null) {
            $value = $row->get(trim($key)) ?? $row->get(str_replace(' ', '_', $key));
        }

        Log::debug("Looking for key: '{$key}', Found value: " . ($value ?? 'NULL'));

        if ($value === null || $value === '' || $value === 'null') {
            return null;
        }

        return trim((string) $value);
    }

    private function parseDate(?string $dateString): ?Carbon
    {
        if (empty($dateString)) {
            return null;
        }

        try {
            // Handle MM/DD/YYYY format
            if (str_contains($dateString, '/')) {
                $dateParts = explode('/', $dateString);
                if (count($dateParts) === 3) {
                    [$month, $day, $year] = $dateParts;
                    $date = Carbon::createFromDate($year, $month, $day);

                    // Validate date range
                    if ($date->year < 1900 || $date->isFuture()) {
                        Log::warning("Invalid date detected: {$dateString}");
                        return null;
                    }

                    return $date;
                }
            }

            $date = Carbon::parse($dateString);

            // Validate date range
            if ($date->year < 1900 || $date->isFuture()) {
                Log::warning("Invalid date detected: {$dateString}");
                return null;
            }

            return $date;
        } catch (\Exception $e) {
            Log::warning("Failed to parse date: {$dateString}", ['error' => $e->getMessage()]);
            return null;
        }
    }

    private function isRowEmpty(Collection $row): bool
    {
        return $row->filter(function ($value) {
            return !empty($value) && $value !== '';
        })->isEmpty();
    }

    private function insertCertifications(Collection $certifications): void
    {
        $certifications->chunk(100)->each(function ($chunk) {
            try {
                Certification::insert($chunk->toArray());
            } catch (\Exception $e) {
                // Handle duplicate entries gracefully
                if (str_contains($e->getMessage(), 'Duplicate entry')) {
                    Log::warning('Duplicate entries detected, inserting individually', [
                        'count' => $chunk->count()
                    ]);

                    $chunk->each(function ($certification) {
                        try {
                            Certification::create($certification);
                        } catch (\Exception $e) {
                            if (!str_contains($e->getMessage(), 'Duplicate entry')) {
                                Log::error('Failed to insert individual certification', [
                                    'error' => $e->getMessage(),
                                    'serial' => $certification['accredited_serial_number'] ?? 'unknown'
                                ]);
                            }
                        }
                    });
                } else {
                    throw $e;
                }
            }
        });
    }

    public function getSummaryReport(): array
    {
        $successRate = $this->totalRows > 0 ? ($this->successfulImports / $this->totalRows) * 100 : 0;

        return [
            'total_rows' => $this->totalRows,
            'successful_imports' => $this->successfulImports,
            'failed_imports' => $this->failedImports,
            'skipped_rows' => $this->skippedRows,
            'countries_created' => $this->countriesCreated,
            'trainers_created' => $this->trainersCreated,
            'success_rate' => round($successRate, 1),
        ];
    }

    // Helper methods for data cleaning and relationship management
    private function cleanPersonName(?string $name): ?string
    {
        if (empty($name)) return null;

        $cleaned = preg_replace('/\s+/', ' ', trim($name));
        $cleaned = trim($cleaned, ' .,;-_');

        // Don't change case for Arabic names
        if (preg_match('/[\x{0600}-\x{06FF}]/u', $name)) {
            return $cleaned;
        }

        return ucwords(strtolower($cleaned));
    }

    private function cleanSerialNumber(?string $serial): ?string
    {
        if (empty($serial)) return null;

        $cleaned = trim($serial);

        // Ensure uppercase for letter parts
        if (preg_match('/^([A-Za-z]+)(\d+)$/', $cleaned, $matches)) {
            $cleaned = strtoupper($matches[1]) . $matches[2];
        }

        return $cleaned;
    }

    private function cleanNationality(?string $nationality): ?string
    {
        if (empty($nationality)) return null;

        $cleaned = trim($nationality);

        // Normalize common nationality variations
        $nationalityMap = [
            'Libyan' => 'Libya',
            'Syrian' => 'Syria',
            'Egyptian' => 'Egypt',
            'Yemeni' => 'Yemen',
            'Palestinian' => 'Palestine',
            'Mauritanian' => 'Mauritania',
            'Sultanate of Oman' => 'Oman',
            'Omani' => 'Oman',
        ];

        return $nationalityMap[$cleaned] ?? $cleaned;
    }

    private function normalizePaperStatus(?string $status): ?string
    {
        if (empty($status)) return null;

        $statusMap = [
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

        return $statusMap[trim($status)] ?? 'PENDING';
    }

    private function cleanValue(?string $value): ?string
    {
        if (empty($value) || $value === 'null' || $value === '') {
            return null;
        }

        return trim($value);
    }

    private function getOrCreateCountry(?string $nationality): ?int
    {
        if (empty($nationality)) return null;

        // Check cache first
        if (isset($this->countryCache[$nationality])) {
            return $this->countryCache[$nationality];
        }

        // Find or create country
        $country = Country::firstOrCreate(
            ['name' => $nationality],
            [
                'code' => strtoupper(substr($nationality, 0, 2)),
                'code_2' => strtoupper(substr($nationality, 0, 2)),
                'nationality' => $nationality,
                'is_active' => true
            ]
        );

        if ($country->wasRecentlyCreated) {
            $this->countriesCreated++;
            Log::info("Created country: {$nationality}");
        }

        $this->countryCache[$nationality] = $country->id;
        return $country->id;
    }

    private function getOrCreateTrainer(?string $trainerName): ?int
    {
        if (empty($trainerName)) return null;

        // Check cache first
        if (isset($this->trainerCache[$trainerName])) {
            return $this->trainerCache[$trainerName];
        }

        // Find or create trainer
        $trainer = Trainer::firstOrCreate(
            ['name' => $trainerName],
            [
                'email' => null,
                'phone' => null,
                'country_id' => null,
                'specializations' => ['Training'],
                'is_active' => true
            ]
        );

        if ($trainer->wasRecentlyCreated) {
            $this->trainersCreated++;
            Log::info("Created trainer: {$trainerName}");
        }

        $this->trainerCache[$trainerName] = $trainer->id;
        return $trainer->id;
    }

    private function getDocumentTypeId(?string $documentTypeName): ?int
    {
        if (empty($documentTypeName)) return null;

        $documentType = DocumentType::whereRaw('LOWER(name) = ?', [strtolower($documentTypeName)])->first();
        
        if ($documentType) {
            return $documentType->id;
        }

        $documentType = DocumentType::create([
            'key' => strtolower(str_replace(' ', '_', $documentTypeName)),
            'name' => ['en' => $documentTypeName, 'ar' => $documentTypeName]
        ]);

        return $documentType->id;
    }
}
