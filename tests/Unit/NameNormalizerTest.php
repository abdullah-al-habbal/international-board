<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\Entity\FuzzyMatcher;
use App\Support\Text\NameNormalizer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * These tests are the specification for what the importer will and will not treat
 * as the same entity. If a rule changes here, `entities:backfill-keys` must be
 * re-run, because every stored name_key is derived from this class.
 */
final class NameNormalizerTest extends TestCase
{
    /**
     * @return array<string, array{list<string>}>
     */
    public static function equivalentSpellings(): array
    {
        return [
            'latin casing, hyphen, spacing' => [[
                'AbdUllah al Habal',
                'abdullah al-habal',
                'abdullah alhabal',
                'abdullah  AL   HABAL ',
                'ABDULLAH AL_HABAL',
                "'Abdullah al-Habal'",
                'Abdullah Al‑Habal',   // U+2011 non-breaking hyphen
                'Abdullah  Al . Habal',
            ]],
            'arabic diacritics and spacing' => [[
                'عبد الله الحبال',
                'عَبْدُ اللهِ الحَبّال',
                'عبدالله الحبال',
                'عبــد الله الحبال',    // tatweel
            ]],
            'arabic hamza carriers' => [[
                'أحمد',
                'احمد',
                'إحمد',
                'آحمد',
            ]],
            'latin accents' => [['Café', 'CAFÉ', 'cafe']],
            'arabic-indic digits' => [['١٢٣', '123']],
        ];
    }

    #[Test]
    #[DataProvider('equivalentSpellings')]
    public function it_collapses_equivalent_spellings_to_one_key(array $spellings): void
    {
        $keys = array_unique(array_map(NameNormalizer::key(...), $spellings));

        $this->assertCount(
            1,
            $keys,
            'Expected one identity key, got: '.implode(' | ', $keys)
        );
    }

    /**
     * The other half of the contract: things that must NOT collapse. A normaliser
     * that merges these is worse than no normaliser at all.
     *
     * @return array<string, array{string, string}>
     */
    public static function distinctEntities(): array
    {
        return [
            'different surnames' => ['Abdullah Al-Habal', 'Abdullah Al-Halabi'],
            'similar countries' => ['Niger', 'Nigeria'],
            'similar countries 2' => ['Austria', 'Australia'],
            'similar countries 3' => ['Iceland', 'Ireland'],
            'one-letter given name' => ['Ali Hassan', 'Ali Hussain'],
            'reordered names stay distinct' => ['Habal, Abdullah Al', 'Abdullah Al-Habal'],
        ];
    }

    #[Test]
    #[DataProvider('distinctEntities')]
    public function it_keeps_distinct_entities_distinct(string $a, string $b): void
    {
        $this->assertNotSame(NameNormalizer::key($a), NameNormalizer::key($b));
    }

    #[Test]
    public function it_produces_a_readable_canonical_form(): void
    {
        $this->assertSame('abdullah al habal', NameNormalizer::normalize('AbdUllah  al-Habal '));
        $this->assertSame('abdullahalhabal', NameNormalizer::key('AbdUllah  al-Habal '));
    }

    #[Test]
    public function block_key_is_order_independent_for_review_candidates(): void
    {
        // Reordering must not auto-merge (see distinctEntities) but must still be
        // nominated for a human to look at.
        $this->assertSame(
            NameNormalizer::blockKey('Habal, Abdullah Al'),
            NameNormalizer::blockKey('Abdullah Al-Habal'),
        );
    }

    #[Test]
    public function it_strips_a_leading_article_only_as_a_secondary_candidate(): void
    {
        $this->assertSame('lebanon', NameNormalizer::articleStrippedKey('allebanon'));

        // "Ali" must not be mangled into "i" — too short after stripping.
        $this->assertNull(NameNormalizer::articleStrippedKey('Ali'));

        // "Algeria" is a real name; the exact key wins before this is ever consulted,
        // and the stripped candidate has to miss on an exact lookup to be discarded.
        $this->assertSame('geria', NameNormalizer::articleStrippedKey('Algeria'));
        $this->assertSame('algeria', NameNormalizer::key('Algeria'));
    }

    #[Test]
    public function it_removes_noise_tokens_exactly(): void
    {
        $noise = ['like', 'approx', 'unknown'];

        $this->assertSame('syria', NameNormalizer::keyWithoutNoise('like Syria', $noise));
        $this->assertSame('syria', NameNormalizer::keyWithoutNoise('Syria (approx)', $noise));

        // Nothing removed -> null, so the caller skips a pointless second lookup.
        $this->assertNull(NameNormalizer::keyWithoutNoise('Syria', $noise));

        // "South Sudan" has no noise tokens and must stay whole — collapsing it to
        // "Sudan" would be a different country.
        $this->assertNull(NameNormalizer::keyWithoutNoise('South Sudan', $noise));
    }

    #[Test]
    public function long_names_are_capped_without_colliding(): void
    {
        $a = str_repeat('abdullah ', 60).'one';
        $b = str_repeat('abdullah ', 60).'two';

        $this->assertLessThanOrEqual(255, mb_strlen(NameNormalizer::key($a)));
        $this->assertNotSame(NameNormalizer::key($a), NameNormalizer::key($b));
    }

    /**
     * Guards the decision NOT to auto-merge on similarity. If this test ever starts
     * failing it means the scores separated, and only then is a threshold worth
     * revisiting.
     */
    #[Test]
    public function similarity_scores_of_same_and_different_entities_overlap(): void
    {
        $trueMatch = FuzzyMatcher::score('libanon', 'lebanon');
        $falseMatch = FuzzyMatcher::score('ireland', 'iceland');

        $this->assertGreaterThanOrEqual(
            $trueMatch,
            $falseMatch,
            'Distinct countries still score at least as high as a genuine misspelling — '
            .'no similarity threshold can separate them, so auto-merge stays off.'
        );
    }
}
