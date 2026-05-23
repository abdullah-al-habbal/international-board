<?php

declare(strict_types=1);

namespace App\Eloquent\Resolvers\Trainer;

use App\Models\Trainer;
use Illuminate\Database\Eloquent\Builder;

final class TrainerTrainersExportResolver
{
    public function __construct(
        private readonly Trainer $model,
    ) {}

    public function query(): Builder
    {
        return $this->model->newQuery()
            ->orderBy('created_at', 'desc');
    }
}