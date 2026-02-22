<?php

// filePath: app/Http/Controllers/Admin/StatExportController.php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Exports\StatExportRegistry;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class StatExportController
{
    public function __construct(private readonly StatExportRegistry $registry) {}

    public function download(Request $request, string $type): BinaryFileResponse
    {
        abort_unless($this->registry->isValid($type), 404, 'Invalid export type.');

        $export = $this->registry->resolve($type);

        return Excel::download($export, $export->filename());
    }
}
