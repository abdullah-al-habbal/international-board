<?php

// app\Repositories\Trainer\TrainerRepository.php
declare(strict_types=1);

namespace App\Repositories\Trainer;

use App\Models\Trainer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class TrainerRepository
{
    public function __construct(private readonly Trainer $model) {}

    public function paginateActive(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        return $this->model
            ->newQuery()
            ->when(
                isset($filters['search']) && $filters['search'] !== '',
                fn ($q) => $q->where(function ($inner) use ($filters): void {
                    $inner->where('name', 'like', "%{$filters['search']}%")
                        ->orWhere('email', 'like', "%{$filters['search']}%");
                })
            )
            ->when(
                isset($filters['country_id']) && $filters['country_id'] !== '',
                fn ($q) => $q->where('country_id', $filters['country_id'])
            )
            ->when(
                isset($filters['specialization']) && $filters['specialization'] !== '',
                fn ($q) => $q->whereHas('specializations', fn ($sq) => $sq->where('specializations.id', $filters['specialization']))
            )
            ->with(['country', 'specializations'])
            ->paginate($perPage);
    }

    public function findActiveByKey(int $id): ?Trainer
    {
        return $this->model
            ->newQuery()
            ->where('id', $id)
            ->with(['country', 'certifications', 'specializations'])
            ->first();
    }

    public function countTotal(): int
    {
        return $this->model->newQuery()->count();
    }

    public function getStatistics(): array
    {
        return ['total_active_trainers' => $this->countTotal()];
    }
}
