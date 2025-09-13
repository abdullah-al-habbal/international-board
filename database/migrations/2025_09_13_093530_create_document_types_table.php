<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // snake_case unique identifier
            $table->json('name');            // {"en": "...", "ar": "..."}
            $table->timestamps();
        });

        Schema::table('certifications', function (Blueprint $table) {
            $table->foreignId('document_type_id')
                ->nullable()
                ->after('document_type')
                ->constrained('document_types')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('certifications', function (Blueprint $table) {
            $table->dropForeign(['document_type_id']);
            $table->dropColumn('document_type_id');
        });

        Schema::dropIfExists('document_types');
    }
};
