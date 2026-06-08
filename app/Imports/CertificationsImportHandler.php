<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\Certification;
use App\Models\Country;
use App\Models\DocumentType;
use App\Models\Trainee;
use App\Models\Trainer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

final class CertificationsImportHandler
{
    private array $countryCache = [];
    private array $trainerCache = [];
    private array $traineeCache = [];

    public function processRow(array $row, int $rowNumber, array $headers): array|false
    {
        $headerMap = $this->buildHeaderMap($headers);

        $traineeNameRaw = $this->getValue($row, $headerMap, ['trainee_name', 'اسم_المتدرب', 'اسم المتدرب']);
        $serialNumberRaw = $this->getValue($row, $headerMap, ['accredited_serial_number', 'serial_number', 'الرقم_المتسلسل_المعتمد']);
        $documentCodeRaw = $this->getValue($row, $headerMap, ['document_code', 'document_code', 'الرمز']);
        $accreditationNumberRaw = $this->getValue($row, $headerMap, ['accreditation_number', 'رقم_الاعتماد']);
        $documentTypeRaw = $this->getValue($row, $headerMap, ['document_type', 'نوع_الوثيقة', 'document_type_name']);
        $accreditationDateRaw = $this->getValue($row, $headerMap, ['accreditation_date', 'تاريخ_الاعتماد', 'date']);
        $trainerNameRaw = $this->getValue($row, $headerMap, ['trainer_name', 'اسم_المدرب']);
        $nationalityRaw = $this->getValue($row, $headerMap, ['nationality', 'الجنسية']);
        $paperReceivedRaw = $this->getValue($row, $headerMap, ['paper_received', 'الحصول_على_الوثيقة_ورقيا', 'paper_received_status']);
        $notesRaw = $this->getValue($row, $headerMap, ['notes', 'ملاحظات']);

        if (empty($serialNumberRaw)) {
            Log::warning("Row {$rowNumber}: missing required serial number.");
            return false;
        }

        $serialNumber = $this->cleanSerialNumber($serialNumberRaw);

        return [
            'trainee_id' => $this->getOrCreateTrainee($traineeNameRaw, $nationalityRaw),
            'source' => 'board',
            'accredited_serial_number' => $serialNumber,
            'document_code' => $this->cleanValue($documentCodeRaw),
            'accreditation_number' => $this->cleanValue($accreditationNumberRaw),
            'document_type_id' => $this->getDocumentTypeId($documentTypeRaw),
            'accreditation_date' => $this->parseDate($accreditationDateRaw),
            'trainer_id' => $this->getOrCreateTrainer($trainerNameRaw),
            'country_id' => $this->getOrCreateCountry($nationalityRaw),
            'paper_received' => $this->normalizePaperStatus($paperReceivedRaw),
            'notes' => $this->cleanValue($notesRaw),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function batchInsert(array $dataBatch): void
    {
        Certification::insert($dataBatch);
    }

    private function buildHeaderMap(array $headers): array
    {
        $map = [];

        foreach ($headers as $index => $header) {
            if (! is_string($header)) {
                continue;
            }

            $map[$this->normalizeHeader($header)] = $index;
        }

        return $map;
    }

    private function getValue(array $row, array $headerMap, array $keys): ?string
    {
        foreach ($keys as $key) {
            $normalized = $this->normalizeHeader($key);

            if (isset($headerMap[$normalized]) && isset($row[$headerMap[$normalized]])) {
                return trim((string) $row[$headerMap[$normalized]]);
            }
        }

        return null;
    }

    private function normalizeHeader(string $header): string
    {
        $header = trim($header);
        $header = mb_strtolower($header);
        $header = preg_replace('/[\s_]+/u', '_', $header);

        return $header;
    }

    private function cleanPersonName(?string $name): ?string
    {
        if (empty($name)) {
            return null;
        }

        $cleaned = preg_replace('/\s+/u', ' ', trim($name));
        $cleaned = trim($cleaned, ' .,;\-_');

        if (preg_match('/[\x{0600}-\x{06FF}]/u', $cleaned)) {
            return $cleaned;
        }

        return mb_convert_case(mb_strtolower($cleaned), MB_CASE_TITLE, 'UTF-8');
    }

    private function cleanSerialNumber(?string $serial): ?string
    {
        if (empty($serial)) {
            return null;
        }

        $cleaned = trim($serial);

        if (preg_match('/^([A-Za-z]+)(\d+)$/', $cleaned, $matches)) {
            return strtoupper($matches[1]) . $matches[2];
        }

        return $cleaned;
    }

    private function cleanNationality(?string $nationality): ?string
    {
        if (empty($nationality)) {
            return null;
        }

        $cleaned = trim($nationality);
        $nationalityMap = [
            'libyan' => 'Libya',
            'syrian' => 'Syria',
            'egyptian' => 'Egypt',
            'yemeni' => 'Yemen',
            'palestinian' => 'Palestine',
            'mauritanian' => 'Mauritania',
            'sultanate_of_oman' => 'Oman',
            'omani' => 'Oman',
        ];

        $lookup = str_replace(' ', '_', mb_strtolower($cleaned));

        return $nationalityMap[$lookup] ?? $cleaned;
    }

    private function normalizePaperStatus(?string $status): ?string
    {
        if (empty($status)) {
            return null;
        }

        $value = mb_strtolower(trim($status));

        return match ($value) {
            'y', 'yes', 'yas', '1', 'true' => 'YES',
            'n', 'no', '0', 'false' => 'NO',
            default => mb_strtoupper(trim($status)),
        };
    }

    private function cleanValue(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        if ($value === '' || mb_strtolower($value) === 'null') {
            return null;
        }

        return $value;
    }

    private function parseDate(?string $dateString): ?string
    {
        if (empty($dateString)) {
            return null;
        }

        try {
            if (str_contains($dateString, '/')) {
                $parts = explode('/', $dateString);

                if (count($parts) === 3) {
                    [$month, $day, $year] = $parts;
                    $date = Carbon::createFromDate((int) $year, (int) $month, (int) $day);
                } else {
                    $date = Carbon::parse($dateString);
                }
            } else {
                $date = Carbon::parse($dateString);
            }

            if ($date->year < 1900 || $date->isFuture()) {
                Log::warning("Invalid date detected: {$dateString}");
                return null;
            }

            return $date->format('Y-m-d');
        } catch (\Throwable $exception) {
            Log::warning('Failed to parse date: ' . $dateString, ['error' => $exception->getMessage()]);

            return null;
        }
    }

    private function getOrCreateCountry(?string $nationality): ?int
    {
        if (empty($nationality)) {
            return null;
        }

        if (isset($this->countryCache[$nationality])) {
            return $this->countryCache[$nationality];
        }

        $country = Country::firstOrCreate(
            ['name' => $nationality],
            [
                'code' => strtoupper(substr($nationality, 0, 2)),
                'code_2' => strtoupper(substr($nationality, 0, 2)),
                'nationality' => $nationality,
                'is_active' => true,
            ]
        );

        $this->countryCache[$nationality] = $country->id;

        return $country->id;
    }

    private function getOrCreateTrainer(?string $trainerName): ?int
    {
        if (empty($trainerName)) {
            return null;
        }

        $trainerName = $this->cleanPersonName($trainerName);

        if ($trainerName === null) {
            return null;
        }

        if (isset($this->trainerCache[$trainerName])) {
            return $this->trainerCache[$trainerName];
        }

        $trainer = Trainer::firstOrCreate(
            ['name' => $trainerName],
            [
                'email' => null,
                'phone' => null,
                'country_id' => null,
                'specializations' => ['Training'],
                'is_active' => true,
            ]
        );

        $this->trainerCache[$trainerName] = $trainer->id;

        return $trainer->id;
    }

    private function getOrCreateTrainee(?string $traineeName, ?string $nationality): ?int
    {
        if (empty($traineeName)) {
            return null;
        }

        $traineeName = $this->cleanPersonName($traineeName);

        if ($traineeName === null) {
            return null;
        }

        if (isset($this->traineeCache[$traineeName])) {
            return $this->traineeCache[$traineeName];
        }

        $trainee = Trainee::firstOrCreate(
            ['name' => $traineeName],
            [
                'email' => null,
                'phone' => null,
                'nationality' => $this->cleanNationality($nationality),
            ]
        );

        $this->traineeCache[$traineeName] = $trainee->id;

        return $trainee->id;
    }

    private function getDocumentTypeId(?string $documentTypeName): ?int
    {
        if (empty($documentTypeName)) {
            return null;
        }

        $name = trim($documentTypeName);

        $documentType = DocumentType::whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->first();

        if ($documentType !== null) {
            return $documentType->id;
        }

        $documentType = DocumentType::create([
            'key' => str_replace(' ', '_', mb_strtolower($name)),
            'name' => ['en' => $name, 'ar' => $name],
        ]);

        return $documentType->id;
    }
}
