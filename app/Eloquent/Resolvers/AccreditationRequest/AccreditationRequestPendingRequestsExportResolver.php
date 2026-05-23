<?php

declare(strict_types=1);

namespace App\Eloquent\Resolvers\AccreditationRequest;

use App\Enums\AccreditationStatus;
use App\Models\AccreditationRequest;
use Illuminate\Database\Eloquent\Builder;

final class AccreditationRequestPendingRequestsExportResolver
{
    public function __construct(
        private readonly AccreditationRequest $model,
    ) {}

    public function query(): Builder
    {
        return $this->model->newQuery()
            ->with('certifiedCenter')
            ->where('status', AccreditationStatus::Pending)
            ->orderBy('created_at', 'desc');
    }
}