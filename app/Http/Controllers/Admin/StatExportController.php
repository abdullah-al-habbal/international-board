<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Exports\StatExportRegistry;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class StatExportController
{
    public function __construct(
        private readonly StatExportRegistry $registry,
    ) {}

    public function download(string $type): StreamedResponse
    {
        abort_unless($this->registry->isValid($type), 404, 'Invalid export type.');

        return $this->registry->resolve($type)->export();
    }
}
