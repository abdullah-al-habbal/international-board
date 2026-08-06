<?php

declare(strict_types=1);

namespace App\Services\Certification\Handlers;

use App\Models\Trainer;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * Trainers are open like trainees, but the population is far smaller and the same
 * few people recur across thousands of rows — so the batch cache earns its keep here.
 *
 * The accreditation number is generated per row, which means it must be produced
 * inside newEntityAttributes() and cannot be part of the identity key.
 */
final class ResolveTrainerHandler extends ResolvesEntities
{
    protected function table(): string
    {
        return 'trainers';
    }

    protected function entityType(): string
    {
        return Trainer::class;
    }

    protected function newEntityAttributes(string $rawName, string $normalized, string $key, array $context): array
    {
        return [
            'name' => $rawName,
            'name_normalized' => $normalized,
            'name_key' => $key,
            'accreditation_number' => 'IBVTQ'.Carbon::now()->format('Ymd').'-'.Str::uuid()->toString(),
            'review_status' => 'confirmed',
        ];
    }

    public function handle(string $name): ?int
    {
        return $this->resolve($name);
    }
}
