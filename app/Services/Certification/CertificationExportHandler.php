<?php

declare(strict_types=1);

namespace App\Services\Certification;

use App\Models\Certification;
use App\Models\CertifiedCenter;
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
                'creator',
                'assignedTrainer',
                'country',
                'trainee',
            ])
            ->where('creator_type', CertifiedCenter::class)
            ->where('creator_id', $centerId)
            ->orderByDesc('created_at');

        return $this->export($query, 'certifications.csv');
    }

    public function exportForAdmin(): StreamedResponse
    {
        $query = Certification::with([
                'creator',
                'assignedTrainer',
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
            'Issued By',
            'Assigned Trainer',
            'Document Code',
            'Accreditation Number',
            'Accreditation Date',
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
            $certification->creator?->name,
            $certification->assignedTrainer?->name,
            $certification->document_code,
            $certification->accreditation_number,
            $certification->accreditation_date?->format('Y-m-d'),
            $certification->country?->nationality,
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
