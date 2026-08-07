<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Country;
use App\Models\DocumentType;
use App\Models\EntityMergeCandidate;
use App\Models\Trainee;
use App\Models\Trainer;
use App\Services\Entity\EntityMerger;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

#[Signature('entities:merge
            {--entity=all : trainees|trainers|countries|document-types|all}
            {--auto-exact : Merge byte-identical name_key collisions without asking}
            {--review : Step through pending fuzzy candidates interactively}
            {--alias : Register a spelling for an existing entity}
            {--id= : Target entity id (with --alias)}
            {--spelling= : Raw spelling to register (with --alias)}
            {--min-score=0.90 : Lowest score to surface in --review}
            {--dry-run : Show what would happen, change nothing}')]
#[Description('Merge duplicate entities and teach the resolver new spellings')]
final class MergeEntitiesCommand extends Command
{
    /** @var array<string, array{type: class-string, table: string}> */
    private const ENTITIES = [
        'trainees' => ['type' => Trainee::class, 'table' => 'trainees'],
        'trainers' => ['type' => Trainer::class, 'table' => 'trainers'],
        'countries' => ['type' => Country::class, 'table' => 'countries'],
        'document-types' => ['type' => DocumentType::class, 'table' => 'board_document_types'],
    ];

    public function handle(EntityMerger $merger): int
    {
        if ($this->option('alias')) {
            return $this->registerAlias($merger);
        }

        if ($this->option('auto-exact')) {
            return $this->mergeExactCollisions($merger);
        }

        if ($this->option('review')) {
            return $this->review($merger);
        }

        $this->components->error('Pick a mode: --auto-exact, --review, or --alias.');

        return self::FAILURE;
    }

    private function registerAlias(EntityMerger $merger): int
    {
        $entity = (string) $this->option('entity');
        $id = (int) $this->option('id');
        $spelling = (string) $this->option('spelling');

        if (! isset(self::ENTITIES[$entity]) || $id <= 0 || trim($spelling) === '') {
            $this->components->error('--alias needs --entity, --id and --spelling.');

            return self::FAILURE;
        }

        if ($this->option('dry-run')) {
            $this->components->info("Would alias \"{$spelling}\" to {$entity} #{$id}.");

            return self::SUCCESS;
        }

        $merger->addAlias(self::ENTITIES[$entity]['type'], $id, $spelling);
        $this->components->info("\"{$spelling}\" now resolves to {$entity} #{$id} on every future import.");

        return self::SUCCESS;
    }

    private function mergeExactCollisions(EntityMerger $merger): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $target = (string) $this->option('entity');
        $total = 0;

        foreach (self::ENTITIES as $name => $config) {
            if ($target !== 'all' && $target !== $name) {
                continue;
            }

            $groups = DB::table($config['table'])
                ->select('name_key', DB::raw('MIN(id) AS survivor'), DB::raw('COUNT(*) AS total'))
                ->whereNotNull('name_key')
                ->where('name_key', '!=', '')
                ->groupBy('name_key')
                ->havingRaw('COUNT(*) > 1')
                ->get();

            foreach ($groups as $group) {
                $duplicates = DB::table($config['table'])
                    ->where('name_key', $group->name_key)
                    ->where('id', '!=', $group->survivor)
                    ->pluck('id');

                foreach ($duplicates as $duplicateId) {
                    $this->line("  {$name}: #{$duplicateId} -> #{$group->survivor}  <fg=gray>({$group->name_key})</>");

                    if ($dryRun) {
                        $total++;

                        continue;
                    }

                    try {
                        $merger->merge($config['type'], (int) $group->survivor, (int) $duplicateId);
                        $total++;
                    } catch (Throwable $e) {
                        $this->components->error("  failed: {$e->getMessage()}");
                    }
                }
            }
        }

        $this->components->info(($dryRun ? 'Would merge ' : 'Merged ')."{$total} exact-key duplicate(s).");

        return self::SUCCESS;
    }

    private function review(EntityMerger $merger): int
    {
        $minScore = (float) $this->option('min-score');
        $target = (string) $this->option('entity');

        $query = EntityMergeCandidate::query()
            ->pending()
            ->where('score', '>=', $minScore)
            ->orderByDesc('score');

        if ($target !== 'all' && isset(self::ENTITIES[$target])) {
            $query->where('entity_type', self::ENTITIES[$target]['type']);
        }

        $candidates = $query->limit(200)->get();

        if ($candidates->isEmpty()) {
            $this->components->info('Nothing pending above '.$minScore.'.');

            return self::SUCCESS;
        }

        $this->components->info($candidates->count().' candidate(s). "keep separate" is always the safe answer.');

        foreach ($candidates as $candidate) {
            $this->newLine();
            $this->line("  <options=bold>{$candidate->primary_name}</>  <fg=gray>(#{$candidate->primary_id})</>");
            $this->line("  <options=bold>{$candidate->duplicate_name}</>  <fg=gray>(#{$candidate->duplicate_id})</>");
            $this->line('  <fg=gray>score '.number_format($candidate->score, 3)." via {$candidate->strategy}</>");

            $choice = $this->choice('Same entity?', ['keep separate', 'merge', 'stop'], 'keep separate');

            if ($choice === 'stop') {
                break;
            }

            if ($choice === 'keep separate') {

                $candidate->update([
                    'status' => EntityMergeCandidate::STATUS_REJECTED,
                    'reviewed_at' => Carbon::now(),
                ]);

                continue;
            }

            if ($this->option('dry-run')) {
                $this->components->info('  (dry run — not merged)');

                continue;
            }

            try {
                $merger->merge($candidate->entity_type, $candidate->primary_id, $candidate->duplicate_id);

                $candidate->update([
                    'status' => EntityMergeCandidate::STATUS_MERGED,
                    'reviewed_at' => Carbon::now(),
                ]);

                $this->components->info('  merged — the losing spelling is now an alias.');
            } catch (Throwable $e) {
                $this->components->error("  failed: {$e->getMessage()}");
            }
        }

        return self::SUCCESS;
    }
}
