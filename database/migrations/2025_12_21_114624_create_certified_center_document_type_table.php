<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certified_center_document_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certified_center_id')
                ->constrained('certified_centers')
                ->cascadeOnDelete();
            $table->foreignId('document_type_id')
                ->constrained('document_types')
                ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['certified_center_id', 'document_type_id'], 'cc_doc_type_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certified_center_document_type');
    }
};
