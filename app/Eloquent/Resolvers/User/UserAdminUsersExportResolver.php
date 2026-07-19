<?php

declare(strict_types=1);

namespace App\Eloquent\Resolvers\User;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class UserAdminUsersExportResolver
{
    public function __construct(
        private readonly User $model,
    ) {}

    public function query(): Builder
    {
        return $this->model->newQuery()
            ->where('type', UserType::Admin)
            ->orderBy('created_at', 'desc');
    }
}
