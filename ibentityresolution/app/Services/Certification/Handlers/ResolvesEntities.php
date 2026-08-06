<?php

declare(strict_types=1);

namespace App\Services\Certification\Handlers;

use App\Models\EntityAlias;
use App\Services\Entity\FuzzyMatcher;
use App\Support\Text\NameNormalizer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Base class for every "given a messy string, give me an id" resolver.
 *
 * Two things here matter more than anything else in this change set.
 *
 * 1. RESOLUTION IS BULK, NOT PER-ROW.
 *    The previous design called warmUp() at the start of every chunk job, which
 *    read the entire trainees table into a PHP array. With 200 chunk jobs that is
 *    200 full table scans and 200 copies of the table in memory, and it gets worse
 *    every time the table grows. Here, each batch of ~500 rows issues exactly two
 *    SELECTs (main table + aliases) restricted to the keys that batch actually
 *    mentions, and at most one write statement. Cost is proportional to the batch,
 *    not to the table.
 *
 * 2. THE DATABASE IS THE DEDUPLICATOR, NOT THE ARRAY.
 *    A per-process cache cannot deduplicate across parallel workers — two chunk
 *    jobs that both meet a new trainee will both insert. The unique index on
 *    name_key is the real guarantee; upsert() makes the loser of that race read
 *    back the winner's id instead of throwing. The cache is only a way to avoid
 *    repeating work.
 *
 * The resolution ladder is strictly ordered, and every rung is EXACT:
 *    exact key -> alias -> noise-token-stripped -> article-stripped -> create.
 * Fuzzy matching never appears in it. See FuzzyMatcher for the measurements that
 * rule it out as an automatic step.
 */
abstract class ResolvesEntities
{
    /**
     * Guards a long-lived queue worker from unbounded growth. Resolution stays
     * correct when the cache is dropped — it just costs one more SELECT.
     */
    private const MAX_CACHED_KEYS = 50_000;

    /** @var array<string, int> name_key => id */
    protected array $cache = [];

    /** @var array<string, true> keys already known to be absent from the DB */
    protected array $known = [];

    protected bool $warmed = false;

    abstract protected function table(): string;

    /** Morph type recorded on aliases and review rows. */
    abstract protected function entityType(): string;

    /**
     * Build the insert payload for a brand-new entity.
     *
     * @param  array<string, mixed>  $context  extra per-row data (e.g. country_id)
     * @return array<string, mixed>
     */
    abstract protected function newEntityAttributes(string $rawName, string $normalized, string $key, array $context): array;

    /**
     * Small, curated vocabularies (countries, document types) are worth loading
     * once in full: they are a few hundred rows and every miss is interesting.
     * Open-ended sets (people) must never be preloaded.
     */
    protected function isClosedSet(): bool
    {
        return false;
    }

    /**
     * Junk tokens that appear around a real value in dirty exports. Stripping them
     * is deterministic and exact — "like Syria" becomes "syria" and then matches on
     * the normal key. This is not fuzzy matching and carries none of its risk.
     *
     * @return list<string>
     */
    protected function noiseTokens(): array
    {
        return [];
    }

    /** Whether an unmatched value should create a row or be reported and skipped. */
    protected function autoCreates(): bool
    {
        return true;
    }

    // -----------------------------------------------------------------
    // Bulk API — this is what the importer should call.
    // -----------------------------------------------------------------

    /**
     * Resolve a whole batch of raw names at once.
     *
     * @param  list<string>  $rawNames
     * @param  array<string, array<string, mixed>>  $contextByName  raw name => extra insert attributes
     * @return array<string, int> raw name => id
     */
    public function resolveMany(array $rawNames, array $contextByName = []): array
    {
        $wanted = [];

        foreach ($rawNames as $raw) {
            $raw = trim($raw);

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

            // Deduplicate within the batch itself: five rows naming the same new
            // trainee must produce one insert, not five.
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

    /**
     * Single-value convenience wrapper. Still correct, but prefer resolveMany()
     * inside loops — this issues queries per call on a cache miss.
     *
     * @param  array<string, mixed>  $context
     */
    public function resolve(string $rawName, array $context = []): ?int
    {
        $rawName = trim($rawName);

        if ($rawName === '') {
            return null;
        }

        return $this->resolveMany([$rawName], [$rawName => $context])[$rawName] ?? null;
    }

    /**
     * Full preload. Only sensible for closed sets; a no-op guard keeps a stray call
     * on trainees from quietly reintroducing the full-table-scan problem.
     */
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

    // -----------------------------------------------------------------
    // Internals
    // -----------------------------------------------------------------

    /**
     * Two indexed SELECTs restricted to the keys this batch mentions.
     *
     * @param  list<string>  $keys
     */
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

    /**
     * The resolution ladder. Every rung is an exact lookup; order is what keeps it
     * safe. "algeria" hits rung 1 and therefore never reaches the article-stripping
     * rung that would otherwise turn it into "geria".
     */
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

                // Remember the spelling so the next import resolves it on rung 1.
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

    /**
     * Insert everything the batch is missing in one statement, then read the ids
     * back in one more.
     *
     * upsert() rather than insert(): if a sibling worker inserted the same key a
     * millisecond ago, the unique index turns our row into a no-op update instead
     * of an exception, and the SELECT that follows hands us their id. That is the
     * whole concurrency story — no locks, no retries, no duplicate rows.
     *
     * @param  array<string, array{raw: string, context: array<string, mixed>}>  $pending
     * @return array<string, int> name_key => id
     */
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

    /**
     * @param  array<string, array{raw: string, context: array<string, mixed>}>  $pending
     * @param  array<string, int>  $created
     */
    protected function afterCreate(array $pending, array $created): void
    {
        // Overridden by closed-set resolvers to file a review row.
    }

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

    /**
     * File an unresolved value with ranked suggestions, so a human can turn it into
     * an alias later. Called for closed sets only — a new trainee is normal, a new
     * country almost never is.
     *
     * @param  array<string, int>  $created  name_key => id
     */
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

    /**
     * Candidate names shown to the reviewer. Only meaningful for closed sets.
     *
     * @return array<int, string>
     */
    protected function suggestionPool(): array
    {
        return [];
    }
}
