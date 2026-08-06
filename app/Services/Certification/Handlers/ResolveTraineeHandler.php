<?php

declare(strict_types=1);

namespace App\Services\Certification\Handlers;

use App\Models\Trainee;
use App\Services\Certification\Exceptions\MissingValueException;

final class ResolveTraineeHandler extends ResolvesEntities
{
    protected function table(): string
    {
        return 'trainees';
    }

    protected function entityType(): string
    {
        return Trainee::class;
    }

    protected function newEntityAttributes(string $rawName, string $normalized, string $key, array $context): array
    {
        return [
            'name' => $rawName,
            'name_normalized' => $normalized,
            'name_key' => $key,
            'country_id' => $context['country_id'] ?? null,
            'review_status' => 'confirmed',
        ];
    }

    public function handle(string $name, ?int $countryId): int
    {
        if (trim($name) === '') {
            throw new MissingValueException('trainee_name');
        }

        $id = $this->resolve($name, ['country_id' => $countryId]);

        if ($id === null) {
            throw new MissingValueException('trainee_name');
        }

        return $id;
    }
}
