<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entity_aliases', function (Blueprint $table): void {
            $table->id();

            $table->string('aliasable_type');
            $table->unsignedBigInteger('aliasable_id');

            $isSqlite = DB::connection()->getDriverName() === 'sqlite';

            $aliasKey = $table->string('alias_key', 255);

            if (! $isSqlite) {
                $aliasKey->collation('utf8mb4_bin');
            }

            $table->string('alias_label', 255)->nullable();

            $table->string('source', 20)->default('manual');

            $table->foreignId('created_by')->nullable();
            $table->timestamps();

            $table->unique(['aliasable_type', 'alias_key'], 'entity_aliases_type_key_unique');
            $table->index(['aliasable_type', 'aliasable_id'], 'entity_aliases_owner_idx');
        });

        Schema::create('entity_merge_candidates', function (Blueprint $table): void {
            $table->id();

            $table->string('entity_type');
            $table->unsignedBigInteger('primary_id');
            $table->unsignedBigInteger('duplicate_id');

            $table->string('primary_name', 255)->nullable();
            $table->string('duplicate_name', 255)->nullable();

            $table->decimal('score', 5, 4)->default(0);

            $table->string('strategy', 20)->default('fuzzy');

            $table->string('status', 20)->default('pending')->index();

            $table->foreignId('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->unique(
                ['entity_type', 'primary_id', 'duplicate_id'],
                'entity_merge_candidates_pair_unique'
            );
            $table->index(['entity_type', 'status', 'score'], 'entity_merge_candidates_queue_idx');
        });

        Schema::create('import_unresolved_values', function (Blueprint $table): void {
            $table->id();

            $table->string('entity_type');
            $table->string('raw_value', 255);
            $table->string('normalized_value', 255)->nullable();

            $table->string('resolution', 20)->default('created');
            $table->unsignedBigInteger('created_entity_id')->nullable();

            $table->json('suggestions')->nullable();

            $table->unsignedInteger('occurrences')->default(1);
            $table->string('status', 20)->default('pending')->index();
            $table->timestamps();

            $table->unique(['entity_type', 'raw_value'], 'import_unresolved_values_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_unresolved_values');
        Schema::dropIfExists('entity_merge_candidates');
        Schema::dropIfExists('entity_aliases');
    }
};
