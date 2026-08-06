<?php

declare(strict_types=1);

namespace App\Services\Certification\Handlers;

use App\Models\Trainee;
use App\Services\Certification\Exceptions\MissingValueException;
use App\Support\Text\NameNormalizer;

/**
 * Trainees are an OPEN set: a name the system has never seen is the normal case,
 * not an anomaly. Two consequences follow.
 *
 * - Never preload the table. isClosedSet() stays false, so resolution costs two
 *   indexed SELECTs per batch instead of a full scan per chunk job.
 *
 * - Never fuzzy match. With no passport, national id, email or date of birth in
 *   the source data, a name is the only identifier available, and edit distance
 *   cannot tell "Ali Hassan" from "Ali Hussain". Merging two real people is not a
 *   tidy-up, it is a certificate appearing under the wrong person's record, and it
 *   is very hard to unpick afterwards. Suspected duplicates go to
 *   entity_merge_candidates for a human instead.
 *
 * Identity is name_key alone, deliberately not (name_key, country_id): country is
 * frequently blank in the source, and in MariaDB NULLs do not collide in a unique
 * index, so scoping by it would let the same person land twice — once with a
 * country and once without. See PLAN.md if you want the scoped variant instead.
 */
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
            // Keep the original spelling for display; the keys carry identity.
            'name' => $rawName,
            'name_normalized' => $normalized,
            'name_key' => $key,
            'country_id' => $context['country_id'] ?? null,
            'review_status' => 'confirmed',
        ];
    }

    /**
     * Backwards-compatible single-value entry point.
     *
     * @throws MissingValueException
     */
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
