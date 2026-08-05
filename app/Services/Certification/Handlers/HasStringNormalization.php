<?php

declare(strict_types=1);

namespace App\Services\Certification\Handlers;

trait HasStringNormalization
{
    private function normalizeString(string $string): string
    {
        return strtolower((string) preg_replace('/\s+/', '', $string));
    }
}
