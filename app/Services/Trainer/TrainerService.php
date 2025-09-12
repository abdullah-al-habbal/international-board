<?php

declare(strict_types=1);

namespace App\Services\Trainer;

use App\Repositories\Trainer\TrainerRepository;

final class TrainerService
{
    public function __construct(private readonly TrainerRepository $trainerRepository) {}
}
