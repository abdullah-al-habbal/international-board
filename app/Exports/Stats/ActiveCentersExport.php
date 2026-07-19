<?php

declare(strict_types=1);

namespace App\Exports\Stats;

use App\Eloquent\Resolvers\CertifiedCenter\CertifiedCenterActiveCentersExportResolver;
use App\Exports\Contracts\CsvStatExportable;
use App\Models\CertifiedCenter;
use App\Services\Csv\CsvExportHandler;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ActiveCentersExport implements CsvStatExportable
{
    public function __construct(
        private readonly CsvExportHandler $csvExportHandler,
        private readonly CertifiedCenterActiveCentersExportResolver $resolver,
    ) {}

    public function export(): StreamedResponse
    {
        $headers = ['ID', 'Name', 'Email', 'Created At'];

        $formatter = fn (CertifiedCenter $center): array => [
            $center->id,
            $center->name,
            $center->email,
            $center->created_at->format('Y-m-d'),
        ];

        return $this->csvExportHandler->export(
            $this->resolver->query(),
            $headers,
            $formatter,
            'active_centers_'.now()->format('Ymd_His').'.csv'
        );
    }

    public function label(): string
    {
        return 'Active Centers';
    }
}
