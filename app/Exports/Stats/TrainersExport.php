<?php

declare(strict_types=1);

namespace App\Exports\Stats;

use App\Exports\Contracts\StatExportable;
use App\Models\Trainer;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

final class TrainersExport implements StatExportable, FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    public function query(): Builder
    {
        return Trainer::query()->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Email', 'Phone', 'Country', 'Specializations', 'Active', 'Created At'];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->name,
            $row->email,
            $row->phone,
            $row->country?->name,
            is_array($row->specializations) ? implode(', ', $row->specializations) : $row->specializations,
            $row->is_active ? 'Yes' : 'No',
            $row->created_at?->format('Y-m-d'),
        ];
    }

    public function label(): string
    {
        return 'Trainers';
    }

    public function filename(): string
    {
        return 'trainers_' . now()->format('Ymd_His') . '.xlsx';
    }
}
