<?php

declare(strict_types=1);

namespace App\Services\Certification;

use App\Models\DocumentType;
use App\Models\User;
use App\Services\Certification\Exceptions\MissingValueException;
use App\Services\Certification\Exceptions\RowLengthException;
use App\Services\Certification\Handlers\ResolveCountryHandler;
use App\Services\Certification\Handlers\ResolveDocumentTypeHandler;
use App\Services\Certification\Handlers\ResolveTraineeHandler;
use App\Services\Certification\Handlers\ResolveTrainerHandler;
use App\Services\Certification\Support\DateParser;
use App\Services\Csv\CsvImportHandler;
use Carbon\Carbon;
use Generator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;
use RuntimeException;
use SplFileObject;
use Throwable;

final class CertificationImportService
{
    private const BATCH_SIZE = 500;

    /** @var array<string, array<string, int>> */
    private array $headerMapCache = [];

    public function __construct(
        private readonly CsvImportHandler $csvHandler,
        private readonly CsvHeaderMapper $headerMapper,
        private readonly ResolveTraineeHandler $traineeHandler,
        private readonly ResolveCountryHandler $countryHandler,
        private readonly ResolveTrainerHandler $trainerHandler,
        private readonly ResolveDocumentTypeHandler $documentTypeHandler,
    ) {}

    /**
     * Single-file (non-chunked) import.
     *
     * This reads the CSV directly rather than going through CsvImportHandler::import().
     * That callback-per-row API cannot express batched resolution: it hands rows over
     * one at a time, which is precisely the shape that forced the old per-row lookups.
     * $csvHandler is left injected for the rest of the class / other callers.
     *
     * @return array{total: int, success: int, failed: int, errors: list<string>}
     */
    public function importCertifications(string $filePath, int $creatorId): array
    {
        $this->logFileStart($filePath, $creatorId);

        if (! is_file($filePath) || ! is_readable($filePath)) {
            throw new RuntimeException("Import file is not readable: {$filePath}");
        }

        $headers = $this->readHeaders($filePath);

        if ($headers === []) {
            throw new RuntimeException("Import file has no header row: {$filePath}");
        }

        $stats = $this->processRows($this->readRows($filePath), $creatorId, $headers);

        $this->logStats($stats, 'importCertifications', $filePath);

        return $stats;
    }

    /**
     * @return list<string>
     */
    private function readHeaders(string $filePath): array
    {
        foreach ($this->csvLines($filePath) as $row) {
            return $row;
        }

        return [];
    }

    private function readRows(string $filePath): LazyCollection
    {
        return LazyCollection::make(function () use ($filePath): Generator {
            $isHeader = true;

            foreach ($this->csvLines($filePath) as $row) {
                if ($isHeader) {
                    $isHeader = false;

                    continue;
                }

                yield $row;
            }
        });
    }

    /**
     * @return Generator<int, list<string|null>>
     */
    private function csvLines(string $filePath): Generator
    {
        $file = new SplFileObject($filePath);
        $file->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY | SplFileObject::READ_AHEAD);
        $file->setCsvControl(',');

        foreach ($file as $row) {
            if ($row === false || ! is_array($row)) {
                continue;
            }

            $isEmpty = true;

            foreach ($row as $cell) {
                if ($cell !== null && $cell !== '') {
                    $isEmpty = false;

                    break;
                }
            }

            if (! $isEmpty) {
                yield $row;
            }
        }
    }

    /**
     * @param  list<string>  $headers
     * @return array{total: int, success: int, failed: int, errors: list<string>}
     */
    public function importChunk(LazyCollection $rows, int $creatorId, array $headers): array
    {
        $stats = $this->processRows($rows, $creatorId, $headers);

        $this->logStats($stats, 'importChunk', (string) $creatorId);

        return $stats;
    }

    /**
     * @param  iterable<int, mixed>  $rows
     * @param  list<string>  $headers
     * @return array{total: int, success: int, failed: int, errors: list<string>}
     */
    private function processRows(iterable $rows, int $creatorId, array $headers): array
    {
        $stats = ['total' => 0, 'success' => 0, 'failed' => 0, 'errors' => []];
        $buffer = [];

        foreach ($rows as $index => $row) {
            if (! is_array($row)) {
                continue;
            }

            $stats['total']++;
            $buffer[$index] = $row;

            if (count($buffer) >= self::BATCH_SIZE) {
                $this->flushBatch($buffer, $headers, $creatorId, $stats);
                $buffer = [];
            }
        }

        if ($buffer !== []) {
            $this->flushBatch($buffer, $headers, $creatorId, $stats);
        }

        return $stats;
    }

    /**
     * Resolve every entity the batch mentions, then map and write the batch.
     *
     * @param  array<int, array<int, string|null>>  $buffer
     * @param  list<string>  $headers
     * @param  array{total: int, success: int, failed: int, errors: list<string>}  $stats
     */
    private function flushBatch(array $buffer, array $headers, int $creatorId, array &$stats): void
    {
        $mapping = $this->resolveHeaderMap($headers);
        $extracted = [];

        $countryNames = [];
        $documentTypeNames = [];
        $trainerNames = [];

        foreach ($buffer as $index => $row) {
            try {
                $values = $this->extractValues($row, $headers, $mapping);
            } catch (RowLengthException $exception) {
                $stats['failed']++;

                if (count($stats['errors']) < 100) {
                    $stats['errors'][] = $exception->getMessage();
                }

                continue;
            }

            $extracted[$index] = $values;

            if ($values['country_name'] !== '') {
                $countryNames[$values['country_name']] = true;
            }

            if ($values['document_type'] !== '') {
                $documentTypeNames[$values['document_type']] = true;
            }

            if ($values['trainer_name'] !== '') {
                $trainerNames[$values['trainer_name']] = true;
            }
        }

        $countries = $this->countryHandler->resolveMany(array_keys($countryNames));
        $documentTypes = $this->documentTypeHandler->resolveMany(array_keys($documentTypeNames));
        $trainers = $this->trainerHandler->resolveMany(array_keys($trainerNames));

        $traineeNames = [];
        $traineeContext = [];

        foreach ($extracted as $values) {
            if ($values['trainee_name'] === '') {
                continue;
            }

            $traineeNames[$values['trainee_name']] = true;

            $traineeContext[$values['trainee_name']] ??= [
                'country_id' => $countries[$values['country_name']] ?? null,
            ];
        }

        $trainees = $this->traineeHandler->resolveMany(array_keys($traineeNames), $traineeContext);

        $payload = [];

        foreach ($extracted as $index => $values) {
            try {
                $payload[] = $this->buildRow($values, $creatorId, $index, $countries, $documentTypes, $trainers, $trainees);
                $stats['success']++;
            } catch (Throwable $exception) {
                $stats['failed']++;

                if (count($stats['errors']) < 100) {
                    $stats['errors'][] = $exception->getMessage();
                }
            }
        }

        if ($payload === []) {
            return;
        }

        DB::transaction(static function () use ($payload): void {
            self::upsertCertifications($payload);
        });
    }

    /**
     * @param  array<int, string|null>  $row
     * @param  list<string>  $headers
     * @param  array<string, int>  $mapping
     * @return array<string, string>
     */
    private function extractValues(array $row, array $headers, array $mapping): array
    {
        if (count($row) > count($headers)) {
            throw new RowLengthException(count($headers), count($row));
        }

        $row = array_pad($row, count($headers), null);

        $value = static function (?int $index) use ($row): string {
            if ($index === null || ! isset($row[$index])) {
                return '';
            }

            return trim((string) $row[$index]);
        };

        return [
            'trainee_name' => $value($mapping['trainee_name'] ?? null),
            'country_name' => $value($mapping['country_name'] ?? null),
            'trainer_name' => $value($mapping['trainer_name'] ?? null),
            'document_type' => $value($mapping['document_type'] ?? null),
            'accredited_serial_number' => $value($mapping['accredited_serial_number'] ?? null),
            'document_code' => $value($mapping['document_code'] ?? null),
            'accreditation_number' => $value($mapping['accreditation_number'] ?? null),
            'accreditation_date' => $value($mapping['accreditation_date'] ?? null),
            'notes' => $value($mapping['notes'] ?? null),
            'column_count' => (string) count($row),
        ];
    }

    /**
     * @param  array<string, string>  $values
     * @param  array<string, int>  $countries
     * @param  array<string, int>  $documentTypes
     * @param  array<string, int>  $trainers
     * @param  array<string, int>  $trainees
     * @return array<string, mixed>
     */
    private function buildRow(
        array $values,
        int $creatorId,
        int $rowIndex,
        array $countries,
        array $documentTypes,
        array $trainers,
        array $trainees,
    ): array {
        if ($values['trainee_name'] === '') {
            throw new MissingValueException('trainee_name', $rowIndex);
        }

        $traineeId = $trainees[$values['trainee_name']] ?? null;

        if ($traineeId === null) {
            throw new MissingValueException('trainee_name', $rowIndex);
        }

        $documentTypeId = $values['document_type'] === ''
            ? null
            : ($documentTypes[$values['document_type']] ?? null);

        if ($documentTypeId === null) {
            // Log::channel('import')->warning('Row imported without a document type', [
            //     'row' => $rowIndex,
            //     'trainee' => $values['trainee_name'],
            // ]);
        }

        $now = Carbon::now();
        $dateString = $now->format('Ymd');

        return [
            'creator_type' => User::class,
            'creator_id' => $creatorId,
            'documentable_type' => $documentTypeId !== null ? DocumentType::class : null,
            'documentable_id' => $documentTypeId,
            'trainee_id' => $traineeId,
            'assigned_trainer_id' => $values['trainer_name'] === ''
                ? null
                : ($trainers[$values['trainer_name']] ?? null),
            'country_id' => $values['country_name'] === ''
                ? null
                : ($countries[$values['country_name']] ?? null),
            'accredited_serial_number' => $values['accredited_serial_number'] !== ''
                ? $values['accredited_serial_number']
                : 'SN-'.$dateString.'-'.strtoupper(Str::random(6)),
            'document_code' => $values['document_code'] !== ''
                ? $values['document_code']
                : 'CERT-'.$dateString.'-'.strtoupper(Str::random(4)),
            'accreditation_number' => $values['accreditation_number'] !== ''
                ? $values['accreditation_number']
                : null,
            'accreditation_date' => DateParser::parse($values['accreditation_date']),
            'notes' => $values['notes'] !== '' ? $values['notes'] : null,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    /**
     * Keyed on accreditation_number so re-imports are idempotent.
     *
     * @param  list<array<string, mixed>>  $dataBatch
     */
    private static function upsertCertifications(array $dataBatch): void
    {
        DB::table('certifications')->upsert(
            $dataBatch,
            ['accreditation_number'],
            [
                'creator_type', 'creator_id', 'documentable_type', 'documentable_id',
                'trainee_id', 'assigned_trainer_id', 'country_id',
                'accredited_serial_number', 'document_code', 'accreditation_date',
                'notes', 'updated_at',
            ]
        );
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
