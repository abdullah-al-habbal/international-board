<?php

declare(strict_types=1);

namespace App\Exports\Stats;

use App\Exports\Contracts\StatExportable;
use App\Models\Certification;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

final class CertificationsExport implements FromQuery, ShouldAutoSize, StatExportable, WithHeadings, WithMapping
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
        return 'certifications_'.now()->format('Ymd_His').'.xlsx';
    }
}
