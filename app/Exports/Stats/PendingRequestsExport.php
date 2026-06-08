<?php

declare(strict_types=1);

namespace App\Exports\Stats;

use App\Eloquent\Resolvers\AccreditationRequest\AccreditationRequestPendingRequestsExportResolver;
use App\Exports\Contracts\CsvStatExportable;
use App\Models\CenterAccreditationRequest;
use App\Services\Csv\CsvExportHandler;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class PendingRequestsExport implements CsvStatExportable
{
    public function __construct(
        private readonly CsvExportHandler $csvExportHandler,
        private readonly AccreditationRequestPendingRequestsExportResolver $resolver,
    ) {}

    public function export(): StreamedResponse
    {
        $headers = ['ID', 'Center', 'Requested Start', 'Requested End', 'Created At'];

        $formatter = fn (CenterAccreditationRequest $request): array => [
            $request->id,
            $request->certifiedCenter?->name,
            $request->accreditation_start_date?->format('Y-m-d'),
            $request->accreditation_end_date?->format('Y-m-d'),
            $request->created_at->format('Y-m-d'),
        ];

        return $this->csvExportHandler->export(
            $this->resolver->query(),
            $headers,
            $formatter,
            'pending_requests_' . now()->format('Ymd_His') . '.csv'
        );
    }

    public function label(): string
    {
        return 'Pending Requests';
    }
}