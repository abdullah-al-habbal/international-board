<?php

declare(strict_types=1);

namespace App\Services\Trainer;

use App\Models\ApplicationSetting;
use App\Models\Trainer;
use App\Repositories\Trainer\TrainerRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class TrainerService
{
    public function __construct(private readonly TrainerRepository $repo) {}

    public function listActive(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        return $this->repo->paginateActive($filters, $perPage);
    }

    public function findActive(int $id): ?Trainer
    {
        return $this->repo->findActiveByKey($id);
    }

    public function getTotalCount(): int
    {
        return $this->repo->countTotal();
    }

    public function getEvaluationText(): string
    {
        return ApplicationSetting::get(
            'trainer_evaluation_text',
            (string) __('app.trainer_evaluation_default_text')
        );
    }

    public function getStatistics(): array
    {
        return $this->repo->getStatistics();
    }
}
