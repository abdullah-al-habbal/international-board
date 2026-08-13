<?php

declare(strict_types=1);

namespace App\Exports\Stats;

use App\Eloquent\Resolvers\Certification\CertificationMonthlyCertificationsExportResolver;
use App\Exports\Contracts\CsvStatExportable;
use App\Models\Certification;
use App\Services\Csv\CsvExportHandler;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class MonthlyCertificationsExport implements CsvStatExportable
{
    public function __construct(
        private readonly CsvExportHandler $csvExportHandler,
        private readonly CertificationMonthlyCertificationsExportResolver $resolver,
    ) {}

    public function export(): StreamedResponse
    {
        $headers = [
            __('exports.headers.id'), __('exports.headers.serial_number'), __('exports.headers.trainee'),
            __('exports.headers.issued_by'), __('exports.headers.created_at'),
        ];

        $formatter = fn (Certification $certification): array => [
            $certification->id,
            $certification->accredited_serial_number,
            $certification->trainee?->name,
            $certification->creator?->name,
            $certification->created_at->format('Y-m-d'),
        ];

        return $this->csvExportHandler->export(
            $this->resolver->query(),
            $headers,
            $formatter,
            'monthly_certifications_'.now()->format('Ymd_His').'.csv'
        );
    }

    public function label(): string
    {
        return __('exports.titles.monthly_certifications');
    }
}
