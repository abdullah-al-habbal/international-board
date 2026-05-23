<?php

declare(strict_types=1);

namespace App\Exports\Stats;

use App\Eloquent\Resolvers\Certification\CertificationCertificationsExportResolver;
use App\Exports\Contracts\CsvStatExportable;
use App\Models\Certification;
use App\Services\Csv\CsvExportHandler;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class CertificationsExport implements CsvStatExportable
{
    public function __construct(
        private readonly CsvExportHandler $csvExportHandler,
        private readonly CertificationCertificationsExportResolver $resolver,
    ) {}

    public function export(): StreamedResponse
    {
        $headers = ['ID', 'Serial Number', 'Trainee', 'Center', 'Created At'];

        $formatter = fn (Certification $certification): array => [
            $certification->id,
            $certification->accredited_serial_number,
            $certification->trainee?->name,
            $certification->certifiedCenter?->name,
            $certification->created_at->format('Y-m-d'),
        ];

        return $this->csvExportHandler->export(
            $this->resolver->query(),
            $headers,
            $formatter,
            'certifications_' . now()->format('Ymd_His') . '.csv'
        );
    }

    public function label(): string
    {
        return 'Certifications';
    }
}