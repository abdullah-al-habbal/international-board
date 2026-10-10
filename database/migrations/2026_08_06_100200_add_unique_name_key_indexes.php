<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLES = ['trainees', 'trainers', 'countries', 'board_document_types'];

    public function up(): void
    {
        foreach (self::TABLES as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'name_key')) {
                continue;
            }

            $this->guardAgainstDuplicates($table);

            Schema::table($table, function (Blueprint $blueprint) use ($table): void {
                $blueprint->unique('name_key', "{$table}_name_key_unique");
            });
        }
    }

    public function down(): void
    {
        foreach (self::TABLES as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table): void {
                $blueprint->dropUnique("{$table}_name_key_unique");
            });
        }
    }

    private function guardAgainstDuplicates(string $table): void
    {
        $duplicates = DB::table($table)
            ->select('name_key', DB::raw('COUNT(*) AS total'))
            ->whereNotNull('name_key')
            ->where('name_key', '!=', '')
            ->groupBy('name_key')
            ->havingRaw('COUNT(*) > 1')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        if ($duplicates->isEmpty()) {
            return;
        }

        $sample = $duplicates
            ->map(static fn ($row): string => "{$row->name_key} (×{$row->total})")
            ->implode(', ');

        throw new RuntimeException(
            "Cannot add a unique index to `{$table}`.`name_key`: duplicate keys still exist. "
            ."Sample: {$sample}. "
            .'Run `php artisan entities:find-duplicates` then `php artisan entities:merge --auto-exact` first.'
        );
    }
};
