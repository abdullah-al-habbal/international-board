<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Country;
use App\Models\DocumentType;
use App\Models\Trainee;
use App\Models\Trainer;
use App\Support\Text\NameNormalizer;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class BackfillNameKeysCommand extends Command
{
    protected $signature = 'entities:backfill-keys
                            {--table= : Restrict to one table}
                            {--chunk=1000 : Rows per batch}
                            {--dry-run : Report only, write nothing}';

    protected $description = 'Compute normalisation keys for trainees, trainers, countries and document types';

    /** @var array<string, array{type: class-string, json: bool}> */
    private const TABLES = [
        'trainees' => ['type' => Trainee::class, 'json' => false],
        'trainers' => ['type' => Trainer::class, 'json' => false],
        'countries' => ['type' => Country::class, 'json' => true],
        'board_document_types' => ['type' => DocumentType::class, 'json' => true],
    ];

    public function handle(): int
    {
        $only = $this->option('table');
        $chunk = max(100, (int) $this->option('chunk'));
        $dryRun = (bool) $this->option('dry-run');

        foreach (self::TABLES as $table => $config) {
            if ($only !== null && $only !== $table) {
                continue;
            }

            $this->components->task(
                "Backfilling {$table}",
                fn (): bool => $this->backfill($table, $config['type'], $config['json'], $chunk, $dryRun)
            );

            $this->reportCollisions($table);
        }

        if ($dryRun) {
            $this->components->warn('Dry run — no changes were written.');
        }

        return self::SUCCESS;
    }

    private function backfill(string $table, string $entityType, bool $jsonNames, int $chunk, bool $dryRun): bool
    {
        $updated = 0;
        $aliases = [];

        DB::table($table)
            ->select(['id', 'name'])
            ->orderBy('id')
            ->chunk($chunk, function ($rows) use ($table, $entityType, $jsonNames, $dryRun, &$updated, &$aliases): void {
                foreach ($rows as $row) {
                    $variants = $jsonNames ? $this->decode((string) $row->name) : [(string) $row->name];
                    $primary = $variants[0] ?? '';

                    if (trim($primary) === '') {
                        continue;
                    }

                    if (! $dryRun) {
                        DB::table($table)->where('id', $row->id)->update([
                            'name_normalized' => NameNormalizer::normalize($primary),
                            'name_key' => NameNormalizer::key($primary),
                        ]);
                    }

                    $updated++;

                    foreach (array_slice($variants, 1) as $variant) {
                        $key = NameNormalizer::key($variant);

                        if ($key === '' || $key === NameNormalizer::key($primary)) {
                            continue;
                        }

                        $aliases[$entityType.'|'.$key] = [
                            'aliasable_type' => $entityType,
                            'aliasable_id' => (int) $row->id,
                            'alias_key' => $key,
                            'alias_label' => mb_substr($variant, 0, 255),
                            'source' => 'seed',
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ];
                    }
                }
            });

        if (! $dryRun && $aliases !== []) {
            foreach (array_chunk(array_values($aliases), 500) as $batch) {
                DB::table('entity_aliases')->upsert(
                    $batch,
                    ['aliasable_type', 'alias_key'],
                    ['alias_label', 'updated_at']
                );
            }
        }

        $this->line("  <fg=gray>{$updated} rows, ".count($aliases).' aliases</>');

        return true;
    }

    private function reportCollisions(string $table): void
    {
        $collisions = DB::table($table)
            ->select('name_key', DB::raw('COUNT(*) AS total'), DB::raw('GROUP_CONCAT(id) AS ids'))
            ->whereNotNull('name_key')
            ->where('name_key', '!=', '')
            ->groupBy('name_key')
            ->havingRaw('COUNT(*) > 1')
            ->orderByDesc('total')
            ->limit(25)
            ->get();

        if ($collisions->isEmpty()) {
            $this->components->info("{$table}: no key collisions — ready for the unique index.");

            return;
        }

        $this->components->warn("{$table}: ".$collisions->count().' colliding key(s). These must be merged first:');

        $this->table(
            ['name_key', 'rows', 'ids'],
            $collisions->map(static fn ($row): array => [
                $row->name_key,
                $row->total,
                $row->ids,
            ])->all()
        );
    }

    /**
     * @return list<string>
     */
    private function decode(string $json): array
    {
        $decoded = json_decode($json, true);

        if (! is_array($decoded)) {
            return [$json];
        }

        $ordered = [];

        if (isset($decoded['en']) && is_string($decoded['en'])) {
            $ordered[] = $decoded['en'];
        }

        foreach ($decoded as $locale => $value) {
            if ($locale !== 'en' && is_string($value) && $value !== '') {
                $ordered[] = $value;
            }
        }

        return array_values(array_unique(array_filter($ordered, static fn (string $v): bool => trim($v) !== '')));
    }
}
