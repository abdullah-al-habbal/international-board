<?php

declare(strict_types=1);

namespace App\Eloquent\Resolvers\CertifiedCenter;

use App\Models\CertifiedCenter;
use Illuminate\Database\Eloquent\Builder;

final class CertifiedCenterActiveCentersExportResolver
{
    public function __construct(
        private readonly CertifiedCenter $model,
    ) {}

    public function query(): Builder
    {
        return $this->model->newQuery()
            ->where('is_active', true)
            ->orderBy('created_at', 'desc');
    }
}