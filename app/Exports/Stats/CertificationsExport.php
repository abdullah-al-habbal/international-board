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
        $headers = [
            'ID', 'Serial Number', 'Document Code', 'Accreditation Number',
            'Trainee', 'Document Type', 'Issued By', 'Assigned Trainer',
            'Country', 'Accreditation Date', 'Created At',
        ];

        $formatter = fn (Certification $c): array => [
            $c->id,
            $c->accredited_serial_number,
            $c->document_code,
            $c->accreditation_number,
            $c->trainee?->name,
            $this->documentTypeName($c),
            $c->creator?->name,
            $c->assignedTrainer?->name,
            $c->country?->name,
            $c->accreditation_date,
            $c->created_at?->format('Y-m-d'),
        ];

        return $this->csvExportHandler->export(
            $this->resolver->query(),
            $headers,
            $formatter,
            'certifications_'.now()->format('Ymd_His').'.csv'
        );
    }

    public function label(): string
    {
        return 'Certifications';
    }

    private function documentTypeName(Certification $certification): string
    {
        $name = $certification->documentable?->name;

        if (is_array($name)) {
            return $name[app()->getLocale()] ?? $name['en'] ?? '';
        }

        return (string) ($name ?? '');
    }
}
