<?php

declare(strict_types=1);

namespace App\Repositories\Trainer;

use App\Models\Trainer;

final class TrainerRepository
{
    public function __construct(private readonly Trainer $trainer) {}
}
