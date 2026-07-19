<?php

declare(strict_types=1);

namespace App\Exports\Stats;

use App\Eloquent\Resolvers\CertifiedCenter\CertifiedCenterExpiredCentersExportResolver;
use App\Exports\Contracts\CsvStatExportable;
use App\Models\CertifiedCenter;
use App\Services\Csv\CsvExportHandler;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ExpiredCentersExport implements CsvStatExportable
{
    public function __construct(
        private readonly CsvExportHandler $csvExportHandler,
        private readonly CertifiedCenterExpiredCentersExportResolver $resolver,
    ) {}

    public function export(): StreamedResponse
    {
        $headers = ['ID', 'Name', 'Status', 'Accreditation End', 'Created At'];

        $formatter = fn (CertifiedCenter $center): array => [
            $center->id,
            $center->name,
            $center->status?->label(),
            $center->accreditation_period_end?->format('Y-m-d'),
            $center->created_at?->format('Y-m-d'),
        ];

        return $this->csvExportHandler->export(
            $this->resolver->query(),
            $headers,
            $formatter,
            'expired_centers_'.now()->format('Ymd_His').'.csv'
        );
    }

    public function label(): string
    {
        return 'Expired Centers';
    }
}
