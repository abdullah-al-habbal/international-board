<?php

declare(strict_types=1);

namespace App\Services\Certification;

use App\Models\Certification;
use App\Services\Csv\CsvExportHandler;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class CertificationExportHandler
{
    public function __construct(
        private readonly CsvExportHandler $csvExportHandler,
    ) {}

    public function exportForCenter(int $centerId): StreamedResponse
    {
        $query = Certification::with([
                'certifiedCenter',
                'documentType',
                'trainer',
                'country',
                'trainee',
            ])
            ->where('certified_center_id', $centerId)
            ->orderByDesc('created_at');

        return $this->export($query, 'certifications.csv');
    }

    public function exportForAdmin(): StreamedResponse
    {
        $query = Certification::with([
                'certifiedCenter',
                'documentType',
                'trainer',
                'country',
                'trainee',
            ])
            ->orderByDesc('created_at');

        return $this->export($query, 'certifications.csv');
    }

    private function export($query, string $fileName): StreamedResponse
    {
        $headers = [
            'ID',
            'Serial Number',
            'Trainee Name',
            'Center',
            'Document Code',
            'Document Type',
            'Accreditation Number',
            'Accreditation Date',
            'Trainer Name',
            'Nationality',
            'Paper Received',
            'Country',
            'Notes',
            'Created At',
        ];

        $formatter = fn (Certification $certification): array => [
            $certification->id,
            $certification->accredited_serial_number,
            $certification->trainee?->name,
            $certification->certifiedCenter?->name,
            $certification->document_code,
            $certification->documentType?->name,
            $certification->accreditation_number,
            $certification->accreditation_date?->format('Y-m-d'),
            $certification->trainer?->name,
            $certification->nationality,
            $certification->paper_received ? 'YES' : 'NO',
            $certification->country?->name,
            $certification->notes,
            $certification->created_at?->format('Y-m-d H:i:s'),
        ];

        return $this->csvExportHandler->export(
            $query,
            $headers,
            $formatter,
            $fileName
        );
    }
}
