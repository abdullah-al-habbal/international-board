<?php

declare(strict_types=1);

namespace App\Exports\Stats;

use App\Exports\Contracts\StatExportable;
use App\Models\Certification;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

final class MonthlyCertificationsExport implements FromQuery, ShouldAutoSize, StatExportable, WithHeadings, WithMapping
{
    public function query()
    {
        return Certification::query()
            ->with(['trainee', 'certifiedCenter'])
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->orderBy('created_at', 'desc');
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
        return 'Monthly Certifications';
    }

    public function filename(): string
    {
        return 'monthly_certifications_'.now()->format('Ymd_His').'.xlsx';
    }
}
