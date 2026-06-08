<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainer_accreditation_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainer_id')->constrained('trainers')->cascadeOnDelete();
            $table->text('request_notes')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'under_review'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('reviewed_at')->nullable();
            $table->dateTime('accreditation_start_date')->nullable();
            $table->dateTime('accreditation_end_date')->nullable();
            $table->timestamps();
            $table->index(['trainer_id', 'status'], 'idx_trainer_active_request_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainer_accreditation_requests');
    }
};
