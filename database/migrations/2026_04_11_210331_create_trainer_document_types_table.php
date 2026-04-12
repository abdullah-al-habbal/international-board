<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainer_document_types', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('trainer_id')->constrained('trainers')->cascadeOnDelete();
            $table->foreignId('document_type_id')->constrained('document_types')->cascadeOnDelete();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->unique(['trainer_id', 'document_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainer_document_types');
    }
};
