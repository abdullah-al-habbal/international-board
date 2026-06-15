<?php

declare(strict_types=1);

namespace App\Exports\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface StatExportable
{
    public function query(): Builder;

    public function headings(): array;

    public function label(): string;

    public function filename(): string;
}
