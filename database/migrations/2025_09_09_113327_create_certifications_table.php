<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certifications', function (Blueprint $table) {
            $table->id();
            $table->string('source')->default('board');
            $table->foreignId('certified_center_id')->nullable()->constrained('certified_centers')->nullOnDelete();
            $table->string('accredited_serial_number', 100)->index();
            $table->string('document_code', 50);
            $table->string('accreditation_number', 100)->nullable();
            $table->foreignId('document_type_id')->nullable()->constrained('board_document_types')->nullOnDelete();
            $table->date('accreditation_date')->nullable()->index();
            $table->foreignId('trainer_id')->nullable()->constrained('trainers')->nullOnDelete();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->foreignId('trainee_id')->nullable()->constrained('trainees')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->string('paper_received', 10)->nullable();
            $table->timestamps();
            $table->unique(['accredited_serial_number', 'document_code'], 'certifications_serial_doc_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certifications');
    }
};
