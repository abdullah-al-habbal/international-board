<?php
declare(strict_types=1);
namespace App\Exports\Stats;

use App\Enums\AccreditationStatus;
use App\Exports\Contracts\StatExportable;
use App\Models\AccreditationRequest;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

final class PendingRequestsExport implements StatExportable, FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    public function query()
    {
        return AccreditationRequest::query()
            ->with('certifiedCenter')
            ->where('status', AccreditationStatus::Pending)
            ->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return ['ID', 'Center', 'Requested Start', 'Requested End', 'Created At'];
    }

    public function map(mixed $row): array
    {
        return [
            $row->id,
            $row->certifiedCenter?->name,
            $row->requested_start_date?->format('Y-m-d'),
            $row->requested_end_date?->format('Y-m-d'),
            $row->created_at->format('Y-m-d'),
        ];
    }

    public function label(): string
    {
        return 'Pending Requests';
    }

    public function filename(): string
    {
        return 'pending_requests_' . now()->format('Ymd_His') . '.xlsx';
    }
}
