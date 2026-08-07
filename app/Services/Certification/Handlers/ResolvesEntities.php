<?php

declare(strict_types=1);

namespace App\Services\Certification\Handlers;

use App\Models\EntityAlias;
use App\Services\Entity\FuzzyMatcher;
use App\Support\Text\NameNormalizer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

abstract class ResolvesEntities
{
    private const MAX_CACHED_KEYS = 50_000;

    /** @var array<string, int> name_key => id */
    protected array $cache = [];

    /** @var array<string, true> keys already known to be absent from the DB */
    protected array $known = [];

    protected bool $warmed = false;

    abstract protected function table(): string;

    abstract protected function entityType(): string;

    abstract protected function newEntityAttributes(string $rawName, string $normalized, string $key, array $context): array;

    protected function isClosedSet(): bool
    {
        return false;
    }

    protected function noiseTokens(): array
    {
        return [];
    }

    protected function autoCreates(): bool
    {
        return true;
    }

    public function resolveMany(array $rawNames, array $contextByName = []): array
    {
        $wanted = [];

        foreach ($rawNames as $raw) {
            $raw = trim((string) $raw);

            if ($raw === '') {
                continue;
            }

            $key = NameNormalizer::key($raw);

            if ($key !== '') {
                $wanted[$raw] = $key;
            }
        }

        if ($wanted === []) {
            return [];
        }

        $this->primeKeys(array_values(array_unique($wanted)));

        $resolved = [];
        $toCreate = [];

        foreach ($wanted as $raw => $key) {
            $id = $this->lookup($raw, $key);

            if ($id !== null) {
                $resolved[$raw] = $id;

                continue;
            }
            $toCreate[$key] ??= ['raw' => $raw, 'context' => $contextByName[$raw] ?? []];
        }

        if ($toCreate !== [] && $this->autoCreates()) {
            $created = $this->createMissing($toCreate);

            foreach ($wanted as $raw => $key) {
                if (! isset($resolved[$raw]) && isset($created[$key])) {
                    $resolved[$raw] = $created[$key];
                }
            }
        }

        return $resolved;
    }

    public function resolve(string $rawName, array $context = []): ?int
    {
        $rawName = trim((string) $rawName);

        if ($rawName === '') {
            return null;
        }

        return $this->resolveMany([$rawName], [$rawName => $context])[$rawName] ?? null;
    }

    public function warmUp(): void
    {
        if ($this->warmed || ! $this->isClosedSet()) {
            return;
        }

        DB::table($this->table())
            ->select(['id', 'name_key'])
            ->whereNotNull('name_key')
            ->orderBy('id')
            ->chunk(5_000, function ($rows): void {
                foreach ($rows as $row) {
                    $this->cache[(string) $row->name_key] = (int) $row->id;
                }
            });

        EntityAlias::query()
            ->where('aliasable_type', $this->entityType())
            ->select(['alias_key', 'aliasable_id'])
            ->chunk(5_000, function ($rows): void {
                foreach ($rows as $row) {
                    $this->cache[(string) $row->alias_key] ??= (int) $row->aliasable_id;
                }
            });

        $this->warmed = true;
    }

    public function flush(): void
    {
        $this->cache = [];
        $this->known = [];
        $this->warmed = false;
    }

    protected function primeKeys(array $keys): void
    {
        if ($this->isClosedSet()) {
            $this->warmUp();

            return;
        }

        $missing = array_values(array_filter(
            $keys,
            fn (string $key): bool => ! isset($this->cache[$key]) && ! isset($this->known[$key]),
        ));

        if ($missing === []) {
            return;
        }

        if (count($this->cache) > self::MAX_CACHED_KEYS) {
            $this->flush();
        }

        DB::table($this->table())
            ->select(['id', 'name_key'])
            ->whereIn('name_key', $missing)
            ->get()
            ->each(function ($row): void {
                $this->cache[(string) $row->name_key] = (int) $row->id;
            });

        EntityAlias::query()
            ->where('aliasable_type', $this->entityType())
            ->whereIn('alias_key', $missing)
            ->select(['alias_key', 'aliasable_id'])
            ->get()
            ->each(function ($row): void {
                $this->cache[(string) $row->alias_key] ??= (int) $row->aliasable_id;
            });

        foreach ($missing as $key) {
            if (! isset($this->cache[$key])) {
                $this->known[$key] = true;
            }
        }
    }

    protected function lookup(string $raw, string $key): ?int
    {
        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        foreach ($this->derivedKeys($raw) as $strategy => $derived) {
            if ($derived === null || $derived === '' || $derived === $key) {
                continue;
            }

            $id = $this->cache[$derived] ?? $this->fetchByKey($derived);

            if ($id !== null) {
                Log::channel('import')->info('Entity resolved by derived key', [
                    'entity' => $this->entityType(),
                    'raw' => $raw,
                    'strategy' => $strategy,
                    'derived_key' => $derived,
                    'id' => $id,
                ]);

                $this->rememberAlias($id, $key, $raw, 'import');
                $this->cache[$key] = $id;

                return $id;
            }
        }

        return null;
    }

    /**
     * @return array<string, string|null>
     */
    protected function derivedKeys(string $raw): array
    {
        return [
            'noise' => NameNormalizer::keyWithoutNoise($raw, $this->noiseTokens()),
            'article' => NameNormalizer::articleStrippedKey($raw),
        ];
    }

    protected function fetchByKey(string $key): ?int
    {
        $id = DB::table($this->table())->where('name_key', $key)->value('id');

        if ($id === null) {
            $id = EntityAlias::query()
                ->where('aliasable_type', $this->entityType())
                ->where('alias_key', $key)
                ->value('aliasable_id');
        }

        return $id === null ? null : (int) $id;
    }

    protected function createMissing(array $pending): array
    {
        $now = Carbon::now();
        $rows = [];

        foreach ($pending as $key => $item) {
            $rows[] = $this->newEntityAttributes(
                $item['raw'],
                NameNormalizer::normalize($item['raw']),
                $key,
                $item['context'],
            ) + ['created_at' => $now, 'updated_at' => $now];
        }

        DB::table($this->table())->upsert($rows, ['name_key'], ['name_key']);

        $ids = DB::table($this->table())
            ->select(['id', 'name_key'])
            ->whereIn('name_key', array_keys($pending))
            ->pluck('id', 'name_key');

        $created = [];

        foreach ($ids as $key => $id) {
            $created[(string) $key] = (int) $id;
            $this->cache[(string) $key] = (int) $id;
            unset($this->known[(string) $key]);
        }

        $this->afterCreate($pending, $created);

        return $created;
    }

    protected function afterCreate(array $pending, array $created): void {}

    protected function rememberAlias(int $id, string $key, string $label, string $source): void
    {
        if ($key === '') {
            return;
        }

        DB::table('entity_aliases')->upsert(
            [[
                'aliasable_type' => $this->entityType(),
                'aliasable_id' => $id,
                'alias_key' => $key,
                'alias_label' => mb_substr($label, 0, 255),
                'source' => $source,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]],
            ['aliasable_type', 'alias_key'],
            ['aliasable_id', 'alias_label']
        );
    }

    protected function reportUnresolved(string $raw, ?int $createdId): void
    {
        $suggestions = FuzzyMatcher::rank($raw, $this->suggestionPool(), 0.72, 5);

        DB::table('import_unresolved_values')->upsert(
            [[
                'entity_type' => $this->entityType(),
                'raw_value' => mb_substr($raw, 0, 255),
                'normalized_value' => NameNormalizer::normalize($raw),
                'resolution' => $createdId === null ? 'skipped' : 'created',
                'created_entity_id' => $createdId,
                'suggestions' => json_encode($suggestions, JSON_UNESCAPED_UNICODE),
                'occurrences' => 1,
                'status' => 'pending',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]],
            ['entity_type', 'raw_value'],
            ['suggestions', 'updated_at']
        );
    }

    protected function suggestionPool(): array
    {
        return [];
    }
}
