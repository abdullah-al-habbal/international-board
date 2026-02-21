<?php

declare(strict_types=1);

namespace App\Repositories\Trainer;

use App\Models\Trainer;

final class TrainerRepository
{
    public function __construct(private readonly Trainer $trainer) {}

    public function getTotalCount(): int
    {
        return $this->trainer->newQuery()->count();
    }

    public function getActiveCount(): int
    {
        return $this->trainer->newQuery()
            ->where('is_active', true)
            ->count();
    }
}
