<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Trainer;

class TrainerObserver
{
    public function creating(Trainer $trainer): void
    {
        if (empty($trainer->unique_trainer_code)) {
            $trainer->unique_trainer_code = 'TRN-' . strtoupper(uniqid());
        }
    }
}
