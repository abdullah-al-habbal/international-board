<?php

declare(strict_types=1);

namespace App\Services\Certification;

use App\Services\Certification\Exceptions\MissingHeaderException;

final class CsvHeaderMapper
{
    private const ALIASES = [

        'اسم المتدرب' => 'trainee_name',
        'اسم المدرب' => 'trainer_name',
        'نوع الوثيقة' => 'document_type',
        'تاريخ الاعتماد' => 'accreditation_date',
        'الحنسية' => 'country_name',
        'الجنسية' => 'country_name',
        'رقم الاعتماد' => 'accreditation_number',
        'الرقم المتسلسل المعتمد' => 'accredited_serial_number',
        'الرمز' => 'document_code',
        'ملاحظات' => 'notes',
        'الحصول على الوثيقة ورقيا' => 'paper_delivery',

        'trainee name' => 'trainee_name',
        'trainee_name' => 'trainee_name',
        'traineename' => 'trainee_name',
        'trainer name' => 'trainer_name',
        'trainer_name' => 'trainer_name',
        'trainername' => 'trainer_name',
        'document type' => 'document_type',
        'document_type' => 'document_type',
        'documenttype' => 'document_type',
        'accreditation date' => 'accreditation_date',
        'accreditation_date' => 'accreditation_date',
        'accreditationdate' => 'accreditation_date',
        'country name' => 'country_name',
        'country_name' => 'country_name',
        'countryname' => 'country_name',
        'accreditation number' => 'accreditation_number',
        'accreditation_number' => 'accreditation_number',
        'accreditationnumber' => 'accreditation_number',
        'accredited serial number' => 'accredited_serial_number',
        'accredited_serial_number' => 'accredited_serial_number',
        'accreditedserialnumber' => 'accredited_serial_number',
        'document code' => 'document_code',
        'document_code' => 'document_code',
        'documentcode' => 'document_code',
        'notes' => 'notes',
        'paper delivery' => 'paper_delivery',
        'paper_delivery' => 'paper_delivery',
    ];

    private const REQUIRED = ['trainee_name'];

    public function map(array $rawHeaders): array
    {
        $mapping = [];

        foreach ($rawHeaders as $index => $raw) {
            $key = $this->normalize((string) $raw);

            if ($key === '' || isset($mapping[$key])) {
                continue;
            }

            $mapping[$key] = $index;
        }

        $missing = array_values(array_diff(self::REQUIRED, array_keys($mapping)));

        if ($missing !== []) {
            throw new MissingHeaderException($missing);
        }

        return $mapping;
    }

    public function normalize(string $header): string
    {
        $cleaned = (string) preg_replace('/^\x{FEFF}/u', '', $header);
        $cleaned = trim($cleaned, " \t\n\r\0\x0B\"");
        $cleaned = trim((string) preg_replace('/\s+/', ' ', $cleaned));

        return self::ALIASES[mb_strtolower($cleaned)] ?? '';
    }
}
