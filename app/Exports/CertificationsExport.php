<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Certification;
use App\Models\CertifiedCenter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

final class CertificationsExport implements FromQuery, WithChunkReading, WithHeadings, WithMapping
{
    use Exportable;

    public function query(): Builder
    {
        return $this->filteredQuery();
    }

    public function headings(): array
    {
        return $this->exportHeadings();
    }

    public function map($certification): array
    {
        return $this->mapCertification($certification);
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    private function filteredQuery(): Builder
    {
        $query = Certification::with('certifiedCenter');

        if ($this->isCenterUser()) {
            $query->where('certified_center_id', Auth::guard('web')->id());
        }

        return $query;
    }

    private function isCenterUser(): bool
    {
        $user = Auth::guard('web')->user();

        return $user instanceof CertifiedCenter;
    }

    private function exportHeadings(): array
    {
        return [
            'ID',
            'Center Name',
            'Certificate Type',
            'Trainee Name',
            'Serial Number',
            'Document Code',
            'Document Type',
            'Accreditation Date',
            'Trainer Name',
            'Nationality',
            'Notes',
            'Created At',
        ];
    }

    private function mapCertification(Certification $cert): array
    {
        return [
            $cert->id,
            $cert->certifiedCenter?->name,
            $cert->certificate_type->value,
            $cert->trainee_name,
            $cert->accredited_serial_number,
            $cert->document_code,
            $cert->document_type->value,
            $cert->accreditation_date?->format('Y-m-d'),
            $cert->trainer_name,
            $cert->nationality,
            $cert->notes,
            $cert->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
