<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\CertifiedCenter;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CertifiedCentersExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function query()
    {
        return CertifiedCenter::query();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Address',
            'Phone',
            'Manager Name',
            'Accreditation Start',
            'Accreditation End',
            'Accreditation Number',
            'Status',
            'Is Active',
            'Created At',
        ];
    }

    public function map($center): array
    {
        return [
            $center->id,
            $center->name,
            $center->email,
            $center->address,
            $center->phone,
            $center->manager_name,
            $center->accreditation_period_start?->format('Y-m-d H:i:s'),
            $center->accreditation_period_end?->format('Y-m-d H:i:s'),
            $center->accreditation_number,
            $center->status->value,
            $center->is_active ? 'Yes' : 'No',
            $center->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
