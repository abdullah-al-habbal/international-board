<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Step 1 of 3.
 *
 * Adds the two resolution columns to every table the importer resolves against.
 * NO unique index is created here on purpose — existing rows almost certainly
 * contain duplicates already. Run `entities:backfill-keys` and then
 * `entities:merge` before applying the unique-index migration.
 */
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
        foreach (self::TABLES as $table => $nameColumn) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table): void {
                if (! Schema::hasColumn($table, 'name_normalized')) {
                    // Human-readable canonical form. Default collation is fine —
                    // this column is for display and fuzzy input, never for identity.
                    $blueprint->string('name_normalized', 255)
                        ->nullable()
                        ->after('name');
                }

                if (! Schema::hasColumn($table, 'name_key')) {
                    // Identity key. utf8mb4_bin so comparison is exact bytes —
                    // we do NOT want a collation quietly deciding two keys are equal
                    // on top of the normalisation we already applied.
                    $blueprint->string('name_key', 255)
                        ->nullable()
                        ->collation('utf8mb4_bin')
                        ->after('name_normalized');
                }
            });

            Schema::table($table, function (Blueprint $blueprint) use ($table): void {
                $blueprint->index('name_key', "{$table}_name_key_idx");
                $blueprint->index('name_normalized', "{$table}_name_normalized_idx");
            });
        }

        // Auto-created rows are quarantined rather than blocked, so the import keeps
        // running while a human decides whether they are real.
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
