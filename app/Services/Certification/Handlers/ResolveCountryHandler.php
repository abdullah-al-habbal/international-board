<?php

declare(strict_types=1);

namespace App\Services\Certification\Handlers;

use App\Models\Country;
use Illuminate\Support\Facades\DB;

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
                        $this->pool[(int) $row->id] = $name;
                    }
                }
            });

        return $this->pool;
    }

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
