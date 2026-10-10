<?php

declare(strict_types=1);

namespace App\Services\Certification\Handlers;

use App\Support\Text\NameNormalizer;

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
