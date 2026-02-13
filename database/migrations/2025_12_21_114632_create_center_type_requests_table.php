<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('center_type_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certified_center_id')
                ->constrained('certified_centers')
                ->cascadeOnDelete();
            $table->foreignId('document_type_id')
                ->nullable()
                ->constrained('document_types')
                ->nullOnDelete();
            $table->string('requested_name');
            $table->text('requested_description')->nullable();
            $table->string('type');
            $table->string('status')->default('pending');
            $table->text('rejection_message')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('certified_center_id');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('center_type_requests');
    }
};
