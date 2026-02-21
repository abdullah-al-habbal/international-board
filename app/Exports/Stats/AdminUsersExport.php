<?php

declare(strict_types=1);

namespace App\Exports\Stats;

use App\Enums\UserType;
use App\Exports\Contracts\StatExportable;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

final class AdminUsersExport implements StatExportable, FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    public function query(): Builder
    {
        return User::query()->where('type', UserType::Admin)->orderBy('created_at', 'desc');
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
        return 'Admin Users';
    }

    public function filename(): string
    {
        return 'admin_users_' . now()->format('Ymd_His') . '.xlsx';
    }
}
