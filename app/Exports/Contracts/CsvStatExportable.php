<?php

declare(strict_types=1);

namespace App\Exports\Contracts;

use Symfony\Component\HttpFoundation\StreamedResponse;

interface CsvStatExportable
{
    public function export(): StreamedResponse;

    public function label(): string;
}
