<?php

declare(strict_types=1);

namespace App\Services\Certification\Handlers;

use App\Support\Text\NameNormalizer;

/**
 * Backwards-compatibility shim.
 *
 * The old trait held the normalisation rules inline. They now live in
 * NameNormalizer so that the importer, the backfill command, the duplicate finder
 * and any admin UI all key off exactly the same implementation — three copies of
 * "roughly the same regex" is how a dataset ends up with keys that disagree.
 *
 * Keep this only until the last caller is migrated, then delete it.
 *
 * @deprecated Use App\Support\Text\NameNormalizer directly.
 */
trait HasStringNormalization
{
    public function normalizeString(string $string): string
    {
        return NameNormalizer::key($string);
    }

    public function canonicalString(string $string): string
    {
        return NameNormalizer::normalize($string);
    }
}
