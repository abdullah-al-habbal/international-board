<?php

declare(strict_types=1);

namespace App\Exports\Stats;

use App\Eloquent\Resolvers\User\UserAdminUsersExportResolver;
use App\Exports\Contracts\CsvStatExportable;
use App\Models\User;
use App\Services\Csv\CsvExportHandler;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class AdminUsersExport implements CsvStatExportable
{
    public function __construct(
        private readonly CsvExportHandler $csvExportHandler,
        private readonly UserAdminUsersExportResolver $resolver,
    ) {}

    public function export(): StreamedResponse
    {
        $headers = ['ID', 'Name', 'Email', 'Created At'];

        $formatter = fn (User $user): array => [
            $user->id,
            $user->name,
            $user->email,
            $user->created_at->format('Y-m-d'),
        ];

        return $this->csvExportHandler->export(
            $this->resolver->query(),
            $headers,
            $formatter,
            'admin_users_'.now()->format('Ymd_His').'.csv'
        );
    }

    public function label(): string
    {
        return 'Admin Users';
    }
}
