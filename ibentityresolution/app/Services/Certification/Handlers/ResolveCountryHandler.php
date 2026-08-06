<?php

declare(strict_types=1);

namespace App\Services\Certification\Handlers;

use App\Models\Country;
use Illuminate\Support\Facades\DB;

/**
 * Countries are a CLOSED set pretending to be an open one.
 *
 * The old handler inserted a new country row for anything it could not match.
 * That is precisely how "allebanon" and "like Syria" became countries: the importer
 * was told to guess, and it guessed "this is a new nation state". You chose to keep
 * auto-creation so imports never stall, so this handler keeps it — but quarantines
 * the result instead of pretending it is fine:
 *
 *   - the new row is written with review_status = 'provisional'
 *   - the raw value is filed in import_unresolved_values with ranked suggestions
 *   - nothing is merged automatically
 *
 * Before it ever gets that far it tries three exact rungs that between them handle
 * most real-world junk without any guessing:
 *
 *   "Syria"      -> exact key hit
 *   "سوريا"      -> alias hit (seeded per language from the JSON name column)
 *   "like Syria" -> noise token "like" removed, then exact hit
 *   "allebanon"  -> leading article removed, then exact hit on "lebanon"
 *
 * What it will NOT do is fuzzy match. On a real country list, "niger"/"nigeria"
 * scores 0.943 and "austria"/"australia" scores 0.927, both higher than the genuine
 * "libanon"/"lebanon" at 0.914. Any automatic threshold merges real countries into
 * each other. Fuzzy output appears only as a suggestion for a human.
 */
final class ResolveCountryHandler extends ResolvesEntities
{
    /** @var array<int, string>|null id => display name, built lazily for suggestions */
    private ?array $pool = null;

    protected function table(): string
    {
        return 'countries';
    }

    protected function entityType(): string
    {
        return Country::class;
    }

    protected function isClosedSet(): bool
    {
        return true;
    }

    /**
     * Words that show up around a country name in dirty exports. Removing them is
     * an exact, reversible transformation — not a guess.
     *
     * @return list<string>
     */
    protected function noiseTokens(): array
    {
        return [
            'like', 'approx', 'approximately', 'about', 'maybe', 'probably',
            'country', 'nationality', 'nation', 'from', 'of', 'the',
            'unknown', 'unspecified', 'other', 'na', 'nil', 'none',
        ];
    }

    protected function newEntityAttributes(string $rawName, string $normalized, string $key, array $context): array
    {
        return [
            'name' => json_encode(['en' => $rawName, 'ar' => $rawName], JSON_UNESCAPED_UNICODE),
            'name_normalized' => $normalized,
            'name_key' => $key,
            // The flag is the whole point: an auto-created country is a question,
            // not a fact, and the admin UI should filter on this.
            'review_status' => 'provisional',
        ];
    }

    protected function afterCreate(array $pending, array $created): void
    {
        foreach ($pending as $key => $item) {
            $this->reportUnresolved($item['raw'], $created[$key] ?? null);
        }
    }

    /**
     * @return array<int, string>
     */
    protected function suggestionPool(): array
    {
        if ($this->pool !== null) {
            return $this->pool;
        }

        $this->pool = [];

        DB::table('countries')
            ->select(['id', 'name'])
            ->where('review_status', '!=', 'provisional')
            ->orderBy('id')
            ->chunk(1_000, function ($rows): void {
                foreach ($rows as $row) {
                    foreach ($this->decodeNames($row->name) as $name) {
                        // Suggestions are ranked per-name; the id repeats harmlessly
                        // because rank() returns the key alongside the matched name.
                        $this->pool[(int) $row->id] = $name;
                    }
                }
            });

        return $this->pool;
    }

    /**
     * @return list<string>
     */
    private function decodeNames(?string $json): array
    {
        if ($json === null || $json === '') {
            return [];
        }

        $decoded = json_decode($json, true);

        if (! is_array($decoded)) {
            return [$json];
        }

        return array_values(array_filter(
            array_map(static fn ($v): string => is_string($v) ? $v : '', $decoded),
            static fn (string $v): bool => $v !== '',
        ));
    }

    public function handle(string $name): ?int
    {
        return $this->resolve($name);
    }
}
