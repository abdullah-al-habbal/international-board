<?php

declare(strict_types=1);

namespace App\Services\Entity;

use App\Support\Text\NameNormalizer;

/**
 * Similarity scoring for entity names.
 *
 * IMPORTANT — read before wiring this into an import path.
 *
 * This class exists to RANK candidates for a human, not to decide anything.
 * Measured against a real country list, the score distributions of "same entity"
 * and "different entity" pairs overlap completely:
 *
 *     libanon   -> lebanon    0.914   SAME       (want to match)
 *     ireland   -> iceland    0.914   DIFFERENT  (must not match)
 *     qater     -> qatar      0.907   SAME       (want to match)
 *     austria   -> australia  0.927   DIFFERENT  (must not match)
 *     allebanon -> lebanon    0.831   SAME       (want to match)
 *     niger     -> nigeria    0.943   DIFFERENT  (must not match)
 *
 * Any threshold low enough to catch "libanon" also merges Ireland into Iceland and
 * Niger into Nigeria. There is no safe cut-off, so there is no auto-merge here.
 * Human names are worse, not better: "Ali Hassan" and "Ali Hussain" are one edit
 * apart and are usually two people.
 *
 * Complexity note: score() is O(n·m) on string length. Never call it in a loop over
 * a full table during import — use MatchCandidateFinder, which blocks first.
 */
final class FuzzyMatcher
{
    /** Beyond this length the pair is too long to be a plausible typo of each other. */
    private const LEVENSHTEIN_LIMIT = 255;

    /**
     * Composite similarity in [0.0, 1.0]. Higher is more similar.
     *
     * Three signals are combined by taking the maximum, because each catches a
     * failure mode the others miss:
     *   - Jaro-Winkler   : transpositions and shared prefixes ("egpyt"/"egypt")
     *   - Levenshtein    : insertions and deletions ("moroco"/"morocco")
     *   - Token Jaccard  : reordering ("Habal Abdullah"/"Abdullah Habal")
     */
    public static function score(string $a, string $b): float
    {
        $left = NameNormalizer::normalize($a);
        $right = NameNormalizer::normalize($b);

        if ($left === '' || $right === '') {
            return 0.0;
        }

        if ($left === $right) {
            return 1.0;
        }

        $flatLeft = str_replace(' ', '', $left);
        $flatRight = str_replace(' ', '', $right);

        return max(
            self::jaroWinkler($flatLeft, $flatRight),
            self::levenshteinRatio($flatLeft, $flatRight),
            self::tokenJaccard($left, $right),
        );
    }

    /**
     * Rank a needle against a candidate pool. Returns candidates sorted by score,
     * descending, keeping only those at or above $floor.
     *
     * @param  iterable<int|string, string>  $candidates  id/key => name
     * @return list<array{key: int|string, name: string, score: float}>
     */
    public static function rank(string $needle, iterable $candidates, float $floor = 0.72, int $limit = 5): array
    {
        $scored = [];

        foreach ($candidates as $key => $name) {
            $score = self::score($needle, $name);

            if ($score >= $floor) {
                $scored[] = ['key' => $key, 'name' => $name, 'score' => round($score, 4)];
            }
        }

        usort($scored, static fn (array $x, array $y): int => $y['score'] <=> $x['score']);

        return array_slice($scored, 0, $limit);
    }

    public static function jaroWinkler(string $a, string $b, float $scale = 0.1): float
    {
        $jaro = self::jaro($a, $b);

        // Winkler's prefix bonus is only defined for already-similar strings.
        if ($jaro < 0.7) {
            return $jaro;
        }

        $prefix = 0;
        $max = min(4, min(strlen($a), strlen($b)));

        while ($prefix < $max && $a[$prefix] === $b[$prefix]) {
            $prefix++;
        }

        return $jaro + $prefix * $scale * (1 - $jaro);
    }

    public static function jaro(string $a, string $b): float
    {
        $lenA = strlen($a);
        $lenB = strlen($b);

        if ($lenA === 0 || $lenB === 0) {
            return 0.0;
        }

        if ($a === $b) {
            return 1.0;
        }

        $window = max(0, intdiv(max($lenA, $lenB), 2) - 1);
        $matchedA = array_fill(0, $lenA, false);
        $matchedB = array_fill(0, $lenB, false);
        $matches = 0;

        for ($i = 0; $i < $lenA; $i++) {
            $low = max(0, $i - $window);
            $high = min($i + $window + 1, $lenB);

            for ($j = $low; $j < $high; $j++) {
                if ($matchedB[$j] || $a[$i] !== $b[$j]) {
                    continue;
                }

                $matchedA[$i] = true;
                $matchedB[$j] = true;
                $matches++;
                break;
            }
        }

        if ($matches === 0) {
            return 0.0;
        }

        $transpositions = 0;
        $k = 0;

        for ($i = 0; $i < $lenA; $i++) {
            if (! $matchedA[$i]) {
                continue;
            }

            while (! $matchedB[$k]) {
                $k++;
            }

            if ($a[$i] !== $b[$k]) {
                $transpositions++;
            }

            $k++;
        }

        $transpositions = intdiv($transpositions, 2);

        return ($matches / $lenA + $matches / $lenB + ($matches - $transpositions) / $matches) / 3;
    }

    public static function levenshteinRatio(string $a, string $b): float
    {
        $max = max(strlen($a), strlen($b));

        if ($max === 0) {
            return 1.0;
        }

        // PHP's levenshtein() is byte-based and hard-capped; multibyte names that
        // exceed it are not typo-variants of each other anyway.
        if ($max > self::LEVENSHTEIN_LIMIT) {
            return 0.0;
        }

        return 1.0 - levenshtein($a, $b) / $max;
    }

    /** Order-independent overlap of whole tokens. */
    public static function tokenJaccard(string $a, string $b): float
    {
        $left = array_unique(explode(' ', $a));
        $right = array_unique(explode(' ', $b));

        if ($left === [] || $right === []) {
            return 0.0;
        }

        $intersection = count(array_intersect($left, $right));
        $union = count(array_unique(array_merge($left, $right)));

        return $union === 0 ? 0.0 : $intersection / $union;
    }
}
