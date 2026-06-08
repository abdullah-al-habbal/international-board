<?php

declare(strict_types=1);

namespace App\Eloquent\Resolvers\Certification;

use App\Models\Certification;
use Illuminate\Database\Eloquent\Builder;

final class CertificationMonthlyCertificationsExportResolver
{
    public function __construct(
        private readonly Certification $model,
    ) {}

    public function query(): Builder
    {
        return $this->model->newQuery()
            ->with(['trainee', 'creator'])
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->orderBy('created_at', 'desc');
    }
}
