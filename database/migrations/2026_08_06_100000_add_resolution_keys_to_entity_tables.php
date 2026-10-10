<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** @var array<string, string> table => existing name column */
    private const TABLES = [
        'trainees' => 'name',
        'trainers' => 'name',
        'countries' => 'name',
        'board_document_types' => 'name',
    ];

    public function up(): void
    {

        $isSqlite = DB::connection()->getDriverName() === 'sqlite';

        foreach (self::TABLES as $table => $nameColumn) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table, $isSqlite): void {
                if (! Schema::hasColumn($table, 'name_normalized')) {

                    $blueprint->string('name_normalized', 255)
                        ->nullable()
                        ->after('name');
                }

                if (! Schema::hasColumn($table, 'name_key')) {

                    $column = $blueprint->string('name_key', 255)
                        ->nullable()
                        ->after('name_normalized');

                    if (! $isSqlite) {
                        $column->collation('utf8mb4_bin');
                    }
                }
            });

            Schema::table($table, function (Blueprint $blueprint) use ($table): void {
                $blueprint->index('name_key', "{$table}_name_key_idx");
                $blueprint->index('name_normalized', "{$table}_name_normalized_idx");
            });
        }

        foreach (['countries', 'board_document_types', 'trainees', 'trainers'] as $table) {
            if (! Schema::hasTable($table) || Schema::hasColumn($table, 'review_status')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->string('review_status', 20)
                    ->default('confirmed')
                    ->index();
            });
        }
    }

    public function down(): void
    {
        foreach (array_keys(self::TABLES) as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table): void {
                $blueprint->dropIndex("{$table}_name_key_idx");
                $blueprint->dropIndex("{$table}_name_normalized_idx");
                $blueprint->dropColumn(['name_normalized', 'name_key', 'review_status']);
            });
        }
    }
};
