<?php
// filePath: database\migrations\2026_02_25_094257_create_certified_center_document_types.php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certified_center_document_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certified_center_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_type_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['certified_center_id', 'document_type_id'], 'center_document_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certified_center_document_types');
    }
};
