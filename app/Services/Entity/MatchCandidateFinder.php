<?php

declare(strict_types=1);

namespace App\Services\Entity;

use App\Models\EntityMergeCandidate;
use App\Support\Text\NameNormalizer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

final class MatchCandidateFinder
{
    private const MAX_BLOCK_SIZE = 250;

    private const VOWELS = ['a', 'e', 'i', 'o', 'u', 'y', 'w', 'h'];

    /**
     * @return array{pairs: int, scanned: int, skipped_blocks: array<string, int>}
     */
    public function scan(string $entityType, string $table, float $floor = 0.86, int $limit = 5_000): array
    {
        /** @var array<string, array<string, list<int>>> $blocks */
        $blocks = ['exact' => [], 'prefix' => [], 'consonants' => []];
        /** @var array<int, string> $names */
        $names = [];
        $scanned = 0;

        DB::table($table)
            ->select(['id', 'name_normalized', 'name_key'])
            ->whereNotNull('name_key')
            ->where('name_key', '!=', '')
            ->orderBy('id')
            ->chunk(5_000, function ($rows) use (&$blocks, &$names, &$scanned): void {
                foreach ($rows as $row) {
                    $id = (int) $row->id;
                    $display = (string) ($row->name_normalized ?: $row->name_key);

                    $names[$id] = $display;
                    $scanned++;

                    $block = NameNormalizer::blockKey($display);

                    if ($block === '') {
                        continue;
                    }

                    $blocks['exact'][$block][] = $id;
                    $blocks['prefix'][mb_substr($block, 0, 4, 'UTF-8')][] = $id;
                    $blocks['consonants'][$this->consonantSkeleton($block)][] = $id;
                }
            });

        $pairs = [];
        $skipped = ['exact' => 0, 'prefix' => 0, 'consonants' => 0];

        foreach ($blocks as $strategy => $groups) {
            foreach ($groups as $members) {
                $count = count($members);

                if ($count < 2) {
                    continue;
                }

                if ($count > self::MAX_BLOCK_SIZE) {
                    $skipped[$strategy]++;

                    continue;
                }

                for ($i = 0; $i < $count - 1; $i++) {
                    for ($j = $i + 1; $j < $count; $j++) {
                        [$low, $high] = $members[$i] < $members[$j]
                            ? [$members[$i], $members[$j]]
                            : [$members[$j], $members[$i]];

                        $pairKey = $low.':'.$high;

                        if (isset($pairs[$pairKey])) {
                            continue;
                        }

                        $score = FuzzyMatcher::score($names[$low], $names[$high]);

                        if ($score >= $floor) {
                            $pairs[$pairKey] = [
                                'low' => $low,
                                'high' => $high,
                                'score' => $score,
                                'strategy' => $strategy,
                            ];
                        }
                    }
                }
            }
        }

        uasort($pairs, static fn (array $a, array $b): int => $b['score'] <=> $a['score']);
        $pairs = array_slice($pairs, 0, $limit, true);

        $this->persist($entityType, $pairs, $names);

        return ['pairs' => count($pairs), 'scanned' => $scanned, 'skipped_blocks' => $skipped];
    }

    private function consonantSkeleton(string $value): string
    {
        $letters = preg_split('//u', $value, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        $consonants = array_values(array_unique(array_filter(
            $letters,
            static fn (string $c): bool => ! in_array($c, self::VOWELS, true) && preg_match('/\p{L}/u', $c) === 1,
        )));

        sort($consonants);

        return implode('', $consonants);
    }

    /**
     * @param  array<string, array{low: int, high: int, score: float, strategy: string}>  $pairs
     * @param  array<int, string>  $names
     */
    private function persist(string $entityType, array $pairs, array $names): void
    {
        if ($pairs === []) {
            return;
        }

        $now = Carbon::now();

        foreach (array_chunk($pairs, 500) as $chunk) {
            $rows = [];

            foreach ($chunk as $pair) {
                $rows[] = [
                    'entity_type' => $entityType,
                    'primary_id' => $pair['low'],
                    'duplicate_id' => $pair['high'],
                    'primary_name' => mb_substr($names[$pair['low']], 0, 255),
                    'duplicate_name' => mb_substr($names[$pair['high']], 0, 255),
                    'score' => round($pair['score'], 4),
                    'strategy' => $pair['strategy'],
                    'status' => EntityMergeCandidate::STATUS_PENDING,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('entity_merge_candidates')->upsert(
                $rows,
                ['entity_type', 'primary_id', 'duplicate_id'],
                ['score', 'strategy', 'primary_name', 'duplicate_name', 'updated_at']
            );
        }
    }
}
