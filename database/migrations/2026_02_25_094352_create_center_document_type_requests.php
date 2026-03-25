<?php
// filePath: database\migrations\2026_02_25_094352_create_center_document_type_requests.php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('center_document_type_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certified_center_id')->constrained()->cascadeOnDelete();
            $table->json('requested_document_types');
            $table->string('status')->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('certified_center_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('center_document_type_requests');
    }
};
