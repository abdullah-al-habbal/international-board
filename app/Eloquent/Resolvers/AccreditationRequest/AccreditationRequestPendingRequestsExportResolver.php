<?php

declare(strict_types=1);

namespace App\Eloquent\Resolvers\AccreditationRequest;

use App\Enums\AccreditationStatus;
use App\Models\CenterAccreditationRequest;
use Illuminate\Database\Eloquent\Builder;

final class AccreditationRequestPendingRequestsExportResolver
{
    public function __construct(
        private readonly CenterAccreditationRequest $model,
    ) {}

    public function query(): Builder
    {
        return $this->model->newQuery()
            ->with('certifiedCenter')
            ->where('status', AccreditationStatus::Pending)
            ->orderBy('created_at', 'desc');
    }
}
