<?php
declare(strict_types=1);
namespace App\Exports\Contracts;

interface StatExportable
{
    public function query(): \Illuminate\Database\Eloquent\Builder;
    public function headings(): array;
    public function label(): string;
    public function filename(): string;
}
