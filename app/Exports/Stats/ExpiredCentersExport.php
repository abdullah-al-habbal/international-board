<?php

declare(strict_types=1);

namespace App\Exports\Stats;

use App\Exports\Contracts\StatExportable;
use App\Models\CertifiedCenter;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

final class ExpiredCentersExport implements FromQuery, ShouldAutoSize, StatExportable, WithHeadings, WithMapping
{
    public function query(): Builder
    {
        return CertifiedCenter::accreditationExpired()->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Status', 'Accreditation End', 'Created At'];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->name,
            $row->status?->label(),
            $row->accreditation_period_end?->format('Y-m-d'),
            $row->created_at?->format('Y-m-d'),
        ];
    }

    public function label(): string
    {
        return 'Expired Centers';
    }

    public function filename(): string
    {
        return 'expired_centers_'.now()->format('Ymd_His').'.xlsx';
    }
}
