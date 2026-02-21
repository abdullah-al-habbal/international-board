<?php

declare(strict_types=1);

namespace App\Exports\Stats;

use App\Exports\Contracts\StatExportable;
use App\Models\Certification;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

final class CertificationsExport implements StatExportable, FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    public function query(): Builder
    {
        return Certification::query()->with(['trainee', 'certifiedCenter'])->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return ['ID', 'Serial Number', 'Trainee', 'Center', 'Created At'];
    }

    public function map(mixed $row): array
    {
        return [
            $row->id,
            $row->accredited_serial_number,
            $row->trainee?->name,
            $row->certifiedCenter?->name,
            $row->created_at->format('Y-m-d'),
        ];
    }

    public function label(): string
    {
        return 'Certifications';
    }

    public function filename(): string
    {
        return 'certifications_' . now()->format('Ymd_His') . '.xlsx';
    }
}
