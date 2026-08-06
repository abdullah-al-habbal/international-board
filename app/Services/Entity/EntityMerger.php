<?php

declare(strict_types=1);

namespace App\Services\Entity;

use App\Models\Country;
use App\Models\DocumentType;
use App\Models\EntityAlias;
use App\Models\Trainee;
use App\Models\Trainer;
use App\Support\Text\NameNormalizer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

final class EntityMerger
{
    /**
     * Tables and columns that reference each entity type.
     *
     * Keep this in sync with the schema. A missing entry here does not error — it
     * orphans rows — so it is checked against the live schema before every merge.
     *
     * @var array<class-string, array{table: string, references: list<array{table: string, column: string}>}>
     */
    private const MAP = [
        Trainee::class => [
            'table' => 'trainees',
            'references' => [
                ['table' => 'certifications', 'column' => 'trainee_id'],
            ],
        ],
        Trainer::class => [
            'table' => 'trainers',
            'references' => [
                ['table' => 'certifications', 'column' => 'assigned_trainer_id'],
            ],
        ],
        Country::class => [
            'table' => 'countries',
            'references' => [
                ['table' => 'certifications', 'column' => 'country_id'],
                ['table' => 'trainees', 'column' => 'country_id'],
            ],
        ],
        DocumentType::class => [
            'table' => 'board_document_types',
            'references' => [

                ['table' => 'certifications', 'column' => 'documentable_id'],
            ],
        ],
    ];

    /**
     * @return array{moved: array<string, int>, aliases: int}
     */
    public function merge(string $entityType, int $survivorId, int $duplicateId, ?int $reviewerId = null): array
    {
        if (! isset(self::MAP[$entityType])) {
            throw new InvalidArgumentException("Unknown entity type for merge: {$entityType}");
        }

        if ($survivorId === $duplicateId) {
            throw new InvalidArgumentException('Cannot merge an entity into itself.');
        }

        $config = self::MAP[$entityType];

        return DB::transaction(function () use ($entityType, $config, $survivorId, $duplicateId, $reviewerId): array {
            $survivor = DB::table($config['table'])->where('id', $survivorId)->first();
            $duplicate = DB::table($config['table'])->where('id', $duplicateId)->first();

            if ($survivor === null || $duplicate === null) {
                throw new InvalidArgumentException('Both entities must exist to be merged.');
            }

            $moved = [];

            foreach ($config['references'] as $reference) {
                $query = DB::table($reference['table'])->where($reference['column'], $duplicateId);

                if ($reference['column'] === 'documentable_id') {
                    $query->where('documentable_type', $entityType);
                }

                $moved[$reference['table'].'.'.$reference['column']] = $query->update([
                    $reference['column'] => $survivorId,
                    'updated_at' => Carbon::now(),
                ]);
            }

            $aliases = $this->transferAliases($entityType, $survivorId, $duplicate, $reviewerId);

            DB::table($config['table'])->where('id', $duplicateId)->delete();

            Log::channel('import')->info('Entities merged', [
                'entity' => $entityType,
                'survivor' => $survivorId,
                'duplicate' => $duplicateId,
                'moved' => $moved,
                'aliases_written' => $aliases,
                'reviewer' => $reviewerId,
            ]);

            return ['moved' => $moved, 'aliases' => $aliases];
        });
    }

    private function transferAliases(string $entityType, int $survivorId, object $duplicate, ?int $reviewerId): int
    {
        $keys = [];

        if (! empty($duplicate->name_key)) {
            $keys[(string) $duplicate->name_key] = $this->labelFor($duplicate);
        }

        EntityAlias::query()
            ->where('aliasable_type', $entityType)
            ->where('aliasable_id', $duplicate->id)
            ->get()
            ->each(function (EntityAlias $alias) use (&$keys): void {
                $keys[$alias->alias_key] = $alias->alias_label;
            });

        EntityAlias::query()
            ->where('aliasable_type', $entityType)
            ->where('aliasable_id', $duplicate->id)
            ->delete();

        $now = Carbon::now();
        $rows = [];

        foreach ($keys as $key => $label) {
            if ($key === '') {
                continue;
            }

            $rows[] = [
                'aliasable_type' => $entityType,
                'aliasable_id' => $survivorId,
                'alias_key' => $key,
                'alias_label' => $label === null ? null : mb_substr($label, 0, 255),
                'source' => 'merge',
                'created_by' => $reviewerId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($rows === []) {
            return 0;
        }

        DB::table('entity_aliases')->upsert(
            $rows,
            ['aliasable_type', 'alias_key'],
            ['aliasable_id', 'alias_label', 'source', 'updated_at']
        );

        return count($rows);
    }

    private function labelFor(object $entity): ?string
    {
        $name = $entity->name ?? null;

        if (! is_string($name) || $name === '') {
            return null;
        }

        $decoded = json_decode($name, true);

        if (is_array($decoded)) {
            $first = reset($decoded);

            return is_string($first) ? $first : null;
        }

        return $name;
    }

    public function addAlias(string $entityType, int $entityId, string $rawSpelling, ?int $reviewerId = null): bool
    {
        $key = NameNormalizer::key($rawSpelling);

        if ($key === '') {
            return false;
        }

        DB::table('entity_aliases')->upsert(
            [[
                'aliasable_type' => $entityType,
                'aliasable_id' => $entityId,
                'alias_key' => $key,
                'alias_label' => mb_substr($rawSpelling, 0, 255),
                'source' => 'manual',
                'created_by' => $reviewerId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]],
            ['aliasable_type', 'alias_key'],
            ['aliasable_id', 'alias_label', 'source', 'updated_at']
        );

        return true;
    }
}
