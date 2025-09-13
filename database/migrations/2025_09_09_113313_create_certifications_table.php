<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certified_center_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('trainee_name')->nullable();
            $table->string('accredited_serial_number')->nullable();
            $table->string('document_code')->nullable();
            $table->string('accreditation_number')->nullable();
            $table->string('document_type')->nullable();
            $table->date('accreditation_date')->nullable();
            $table->string('trainer_name')->nullable();
            $table->string('nationality')->nullable();
            $table->string('paper_received')->nullable();
            $table->text('notes')->nullable();
            $table->string('certificate_type')->nullable();
            $table->timestamps();
            $table->index(['certified_center_id', 'certificate_type']);
            $table->index('accredited_serial_number');
            $table->index('trainee_name');
            $table->index('document_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certifications');
    }
};
