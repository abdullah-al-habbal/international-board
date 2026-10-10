<?php

declare(strict_types=1);

namespace App\Exports\Stats;

use App\Eloquent\Resolvers\CertifiedCenter\CertifiedCenterCentersExportResolver;
use App\Exports\Contracts\CsvStatExportable;
use App\Models\CertifiedCenter;
use App\Services\Csv\CsvExportHandler;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class CentersExport implements CsvStatExportable
{
    public function __construct(
        private readonly CsvExportHandler $csvExportHandler,
        private readonly CertifiedCenterCentersExportResolver $resolver,
    ) {}

    public function export(): StreamedResponse
    {
        $headers = [
            __('exports.headers.id'), __('exports.headers.name'), __('exports.headers.email'), __('exports.headers.phone'),
            __('exports.headers.country'), __('exports.headers.manager'),
            __('exports.headers.accreditation_number'), __('exports.headers.status'), __('exports.headers.accreditation_start'),
            __('exports.headers.accreditation_end'), __('exports.headers.trainers'), __('exports.headers.certifications'),
            __('exports.headers.document_types'), __('exports.headers.created_at'),
        ];

        $formatter = fn (CertifiedCenter $center): array => [
            $center->id,
            $center->name,
            $center->email,
            $center->phone,
            $center->country?->name,
            $center->manager_name,
            $center->accreditation_number,
            $center->status?->label(),
            $center->accreditation_period_start?->format('Y-m-d'),
            $center->accreditation_period_end?->format('Y-m-d'),
            $center->trainers_count ?? 0,
            $center->certifications_count ?? 0,
            $center->approved_document_types_count ?? 0,
            $center->created_at?->format('Y-m-d'),
        ];

        return $this->csvExportHandler->export(
            $this->resolver->query(),
            $headers,
            $formatter,
            'centers_'.now()->format('Ymd_His').'.csv'
        );
    }

    public function label(): string
    {
        return __('exports.titles.centers');
    }
}
