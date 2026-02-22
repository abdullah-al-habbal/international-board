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

final class ActiveCentersExport implements FromQuery, ShouldAutoSize, StatExportable, WithHeadings, WithMapping
{
    public function query(): Builder
    {
        return CertifiedCenter::query()->where('is_active', true)->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Email', 'Created At'];
    }

    public function map(mixed $row): array
    {
        return [
            $row->id,
            $row->name,
            $row->email,
            $row->created_at->format('Y-m-d'),
        ];
    }

    public function label(): string
    {
        return 'Active Centers';
    }

    public function filename(): string
    {
        return 'active_centers_'.now()->format('Ymd_His').'.xlsx';
    }
}
