<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\Trainer;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class ActiveCenterTrainer implements ValidationRule
{
    public function __construct(
        private readonly int $centerId,
    ) {}

    public function validate(
        string $attribute,
        mixed $value,
        Closure $fail,
    ): void {
        if (blank($value)) {
            return;
        }

        $trainer = Trainer::query()
            ->whereKey($value)
            ->where('center_id', $this->centerId)
            ->first();

        if (! $trainer) {
            $fail(__('app.trainer_not_available'));

            return;
        }

        if (! $trainer->isAccreditationActive()) {
            $fail(__('app.trainer_accreditation_expired'));
        }
    }
}
