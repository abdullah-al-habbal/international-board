<?php

declare(strict_types=1);

namespace App\Eloquent\Resolvers\Trainee;

use App\Models\Trainee;
use Illuminate\Database\Eloquent\Builder;

final class TraineeTraineesExportResolver
{
    public function __construct(
        private readonly Trainee $model,
    ) {}

    public function query(): Builder
    {
        return $this->model->newQuery()
            ->with('country')
            ->withCount('certifications')
            ->orderBy('created_at', 'desc');
    }
}
