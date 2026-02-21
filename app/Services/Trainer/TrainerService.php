<?php

declare(strict_types=1);

namespace App\Services\Trainer;

use App\Repositories\Trainer\TrainerRepository;

final class TrainerService
{
    public function __construct(private readonly TrainerRepository $trainerRepository) {}

    public function getTotalCount(): int
    {
        return $this->trainerRepository->getTotalCount();
    }

    public function getActiveCount(): int
    {
        return $this->trainerRepository->getActiveCount();
    }
}
