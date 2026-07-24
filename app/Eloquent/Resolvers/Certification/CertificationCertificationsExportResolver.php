<?php

declare(strict_types=1);

namespace App\Eloquent\Resolvers\Certification;

use App\Models\Certification;
use Illuminate\Database\Eloquent\Builder;

final class CertificationCertificationsExportResolver
{
    public function __construct(
        private readonly Certification $model,
    ) {}

    public function query(): Builder
    {
        return $this->model->newQuery()
            ->with(['trainee', 'creator', 'assignedTrainer', 'country', 'documentable'])
            ->orderBy('created_at', 'desc');
    }
}
