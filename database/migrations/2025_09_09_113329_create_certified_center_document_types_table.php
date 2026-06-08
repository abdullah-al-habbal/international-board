<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certified_center_document_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certified_center_id')->constrained('certified_centers')->cascadeOnDelete();
            $table->string('key', 255);
            $table->json('name');
            $table->string('status')->default('pending');
            $table->text('admin_notes')->nullable();
            $table->foreignId('reviewed_by_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['certified_center_id', 'key'], 'center_document_unique');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certified_center_document_types');
    }
};
