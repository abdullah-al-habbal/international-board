<?php

declare(strict_types=1);

namespace App\Exports\Stats;

use App\Exports\Contracts\StatExportable;
use App\Models\CertifiedCenter;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

final class CentersExport implements StatExportable, FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    public function query(): Builder
    {
        return CertifiedCenter::query()->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Status', 'Created At'];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->name,
            $row->status?->label(),
            $row->created_at?->format('Y-m-d'),
        ];
    }

    public function label(): string
    {
        return 'Centers';
    }

    public function filename(): string
    {
        return 'centers_' . now()->format('Ymd_His') . '.xlsx';
    }
}
