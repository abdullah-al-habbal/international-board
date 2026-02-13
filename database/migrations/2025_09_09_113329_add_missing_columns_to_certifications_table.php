<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certifications', function (Blueprint $table) {
            // Add certificate_type column (enum in code, string in DB)
            $table->string('certificate_type', 50)->nullable()->after('certified_center_id')->index();

            // Add paper_received status column
            $table->string('paper_received', 10)->nullable()->after('notes');

            // Add denormalized fields for legacy data support
            // These store the direct names even when IDs are present
            $table->string('trainee_name', 255)->nullable()->after('trainee_id')->index();
            $table->string('trainer_name', 255)->nullable()->after('trainer_id')->index();
            $table->string('nationality', 255)->nullable()->after('country_id')->index();

            // Add composite index for common queries
            $table->index(['certificate_type', 'paper_received']);
        });
    }


    public function down(): void
    {
        Schema::table('certifications', function (Blueprint $table) {
            $table->dropIndex(['certificate_type', 'paper_received']);
            $table->dropIndex(['certificate_type']);

            $table->dropColumn([
                'certificate_type',
                'paper_received',
                'trainee_name',
                'trainer_name',
                'nationality',
            ]);
        });
    }
};
