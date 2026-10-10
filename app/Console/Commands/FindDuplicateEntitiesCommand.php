<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Country;
use App\Models\DocumentType;
use App\Models\Trainee;
use App\Models\Trainer;
use App\Services\Entity\MatchCandidateFinder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('entities:find-duplicates
            {--entity=all : trainees|trainers|countries|document-types|all}
            {--floor=0.86 : Minimum similarity to record a candidate}
            {--limit=5000 : Maximum candidates to record per entity type}')]
#[Description('Find likely duplicate entities and queue them for human review')]
final class FindDuplicateEntitiesCommand extends Command
{
    /** @var array<string, array{type: class-string, table: string}> */
    private const ENTITIES = [
        'trainees' => ['type' => Trainee::class, 'table' => 'trainees'],
        'trainers' => ['type' => Trainer::class, 'table' => 'trainers'],
        'countries' => ['type' => Country::class, 'table' => 'countries'],
        'document-types' => ['type' => DocumentType::class, 'table' => 'board_document_types'],
    ];

    public function handle(MatchCandidateFinder $finder): int
    {
        $target = (string) $this->option('entity');
        $floor = (float) $this->option('floor');
        $limit = (int) $this->option('limit');

        foreach (self::ENTITIES as $name => $config) {
            if ($target !== 'all' && $target !== $name) {
                continue;
            }

            $this->components->info("Scanning {$name} (floor {$floor})");

            $result = $finder->scan($config['type'], $config['table'], $floor, $limit);

            $this->line("  <fg=gray>{$result['scanned']} rows scanned, {$result['pairs']} candidates queued</>");

            foreach ($result['skipped_blocks'] as $strategy => $count) {
                if ($count > 0) {
                    $this->components->warn(
                        "  {$count} oversized '{$strategy}' block(s) skipped — those records were NOT compared. "
                        .'Narrow the scan or raise MatchCandidateFinder::MAX_BLOCK_SIZE if you need them.'
                    );
                }
            }
        }

        $this->newLine();
        $this->components->info('Review with: php artisan entities:merge --review');

        return self::SUCCESS;
    }
}
