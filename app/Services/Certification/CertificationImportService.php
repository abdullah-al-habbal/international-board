<?php

declare(strict_types=1);

namespace App\Services\Certification;

use App\Models\DocumentType;
use App\Models\User;
use App\Services\Certification\Exceptions\MissingValueException;
use App\Services\Certification\Handlers\ResolveCountryHandler;
use App\Services\Certification\Handlers\ResolveDocumentTypeHandler;
use App\Services\Certification\Handlers\ResolveTraineeHandler;
use App\Services\Certification\Handlers\ResolveTrainerHandler;
use App\Services\Certification\Support\DateParser;
use App\Services\Csv\CsvImportHandler;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;

final class CertificationImportService
{
    public function __construct(
        private readonly CsvImportHandler $csvHandler,
        private readonly CsvHeaderMapper $headerMapper,
        private readonly ResolveTraineeHandler $traineeHandler,
        private readonly ResolveCountryHandler $countryHandler,
        private readonly ResolveTrainerHandler $trainerHandler,
        private readonly ResolveDocumentTypeHandler $documentTypeHandler,
    ) {}

    /**
     * @var array<string, array<string, int>>
     */
    private array $headerMapCache = [];

    public function importCertifications(string $filePath, int $creatorId): array
    {
        $this->warmUpHandlers();

        $batchInserter = static function (array $dataBatch): void {
            self::upsertCertifications($dataBatch);
        };

        $this->logFileStart($filePath, $creatorId);

        $stats = $this->csvHandler->import($filePath, function (array $row, int $index, array $headers) use ($creatorId) {
            return $this->mapRow($row, $headers, $creatorId, $index);
        }, [
            'batch_size' => 500,
            'batch_inserter' => $batchInserter,
            'transaction' => true,
            'has_header' => true,
        ]);

        $this->logStats($stats, 'importCertifications', $filePath);

        return $stats;
    }

    public function importChunk(LazyCollection $rows, int $creatorId, array $headers): array
    {
        $this->warmUpHandlers();

        $stats = [
            'total' => 0,
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        $batchSize = 500;
        $dataBatch = [];
        $batchInserter = static function (array $dataBatch): void {
            self::upsertCertifications($dataBatch);
        };

        foreach ($rows as $index => $row) {
            if (! is_array($row)) {
                continue;
            }

            $stats['total']++;

            try {
                $result = $this->mapRow($row, $headers, $creatorId, $index);

                $dataBatch[] = $result;

                if (count($dataBatch) >= $batchSize) {
                    DB::transaction(function () use ($batchInserter, &$dataBatch): void {
                        $batchInserter($dataBatch);
                    });

                    $dataBatch = [];
                }

                $stats['success']++;
            } catch (\Throwable $exception) {
                $stats['failed']++;

                if (count($stats['errors']) < 100) {
                    $stats['errors'][] = $exception->getMessage();
                }
            }
        }

        if (! empty($dataBatch)) {
            DB::transaction(function () use ($batchInserter, $dataBatch): void {
                $batchInserter($dataBatch);
            });
        }

        $this->logStats($stats, 'importChunk', (string) $creatorId);

        return $stats;
    }

    private function warmUpHandlers(): void
    {
        $this->traineeHandler->warmUp();
        $this->countryHandler->warmUp();
        $this->trainerHandler->warmUp();
        $this->documentTypeHandler->warmUp();
    }

    /**
     * Insert or update certifications keyed on the unique accreditation
     * number so re-imports are idempotent.
     *
     * @param  list<array<string, mixed>>  $dataBatch
     */
    private static function upsertCertifications(array $dataBatch): void
    {
        DB::table('certifications')->upsert(
            $dataBatch,
            ['accreditation_number'],
            [
                'creator_type',
                'creator_id',
                'documentable_type',
                'documentable_id',
                'trainee_id',
                'assigned_trainer_id',
                'country_id',
                'accredited_serial_number',
                'document_code',
                'accreditation_date',
                'notes',
                'updated_at',
            ]
        );
    }

    /**
     * @param  list<string>  $headers
     * @return array<string, mixed>
     */
    private function mapRow(array $row, array $headers, int $creatorId, int $rowIndex): array
    {
        $mapping = $this->resolveHeaderMap($headers);

        if (count($row) > count($headers)) {
            throw new \RuntimeException('Row has '.count($row).' columns, expected '.count($headers)." (row {$rowIndex}).");
        }

        $row = array_pad($row, count($headers), null);

        $value = static function (array $data, ?int $index): string {
            if ($index === null || ! isset($data[$index])) {
                return '';
            }

            return trim((string) $data[$index]);
        };

        $traineeName = $value($row, $mapping['trainee_name'] ?? null);

        if ($traineeName === '') {
            throw new MissingValueException('trainee_name', $rowIndex);
        }

        $now = Carbon::now();
        $dateString = $now->format('Ymd');

        $documentTypeName = $value($row, $mapping['document_type'] ?? null);
        $documentTypeId = $documentTypeName !== ''
            ? $this->documentTypeHandler->handle($documentTypeName)
            : null;

        $countryName = $value($row, $mapping['country_name'] ?? null);
        $countryId = $countryName !== '' ? $this->countryHandler->handle($countryName) : null;

        $traineeId = $this->traineeHandler->handle($traineeName, $countryId);

        $trainerName = $value($row, $mapping['trainer_name'] ?? null);
        $trainerId = $trainerName !== '' ? $this->trainerHandler->handle($trainerName) : null;

        $serialNumber = $value($row, $mapping['accredited_serial_number'] ?? null);
        $documentCode = $value($row, $mapping['document_code'] ?? null);
        $accreditationNumber = $value($row, $mapping['accreditation_number'] ?? null);
        $notes = $value($row, $mapping['notes'] ?? null);

        if ($documentTypeId === null) {
            Log::channel('import')->warning('Row imported without a document type', [
                'row' => $rowIndex,
                'trainee' => $traineeName,
            ]);
        }

        return [
            'creator_type' => User::class,
            'creator_id' => $creatorId,
            'documentable_type' => $documentTypeId !== null ? DocumentType::class : null,
            'documentable_id' => $documentTypeId,
            'trainee_id' => $traineeId,
            'assigned_trainer_id' => $trainerId,
            'country_id' => $countryId,
            'accredited_serial_number' => $serialNumber !== ''
                ? $serialNumber
                : 'SN-'.$dateString.'-'.strtoupper(Str::random(6)),
            'document_code' => $documentCode !== ''
                ? $documentCode
                : 'CERT-'.$dateString.'-'.strtoupper(Str::random(4)),
            'accreditation_number' => $accreditationNumber !== ''
                ? $accreditationNumber
                : null,
            'accreditation_date' => DateParser::parse($value($row, $mapping['accreditation_date'] ?? null)),
            'notes' => $notes !== '' ? $notes : null,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    /**
     * @param  list<string>  $headers
     * @return array<string, int>
     */
    private function resolveHeaderMap(array $headers): array
    {
        $cacheKey = md5(implode("\x1f", $headers));

        if (isset($this->headerMapCache[$cacheKey])) {
            return $this->headerMapCache[$cacheKey];
        }

        $mapping = $this->headerMapper->map($headers);

        Log::channel('import')->debug('Normalized CSV headers', [
            'raw' => $headers,
            'normalized' => $mapping,
        ]);

        return $this->headerMapCache[$cacheKey] = $mapping;
    }

    private function logFileStart(string $filePath, int $creatorId): void
    {
        Log::channel('import')->info('Certification import started', [
            'file' => $filePath,
            'creator_id' => $creatorId,
        ]);
    }

    /**
     * @param  array{total: int, success: int, failed: int, errors: list<string>}  $stats
     */
    private function logStats(array $stats, string $source, string $context): void
    {
        Log::channel('import')->info('Certification import completed', [
            'source' => $source,
            'context' => $context,
            'total' => $stats['total'],
            'success' => $stats['success'],
            'failed' => $stats['failed'],
            'errors' => array_slice($stats['errors'], 0, 20),
        ]);
    }
}
