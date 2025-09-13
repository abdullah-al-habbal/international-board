<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // --- Step 0: Fill null paper_received with 0 (false) ---
        DB::table('certifications')
            ->whereNull('paper_received')
            ->update(['paper_received' => '0']);

        // --- Step 1: Normalize document_type ---
        DB::table('certifications')
            ->whereNull('document_type')
            ->orWhere('document_type', '')
            ->update(['document_type' => 'UNKNOWN']);

        // --- Step 2: Fill null accreditation_date with a safe default ---
        DB::table('certifications')
            ->whereNull('accreditation_date')
            ->update(['accreditation_date' => '1900-01-01']);

        // --- Step 3: Apply schema changes ---
        Schema::table('certifications', function (Blueprint $table) {
            // Add foreign keys if missing
            if (!Schema::hasColumn('certifications', 'trainer_id')) {
                $table->foreignId('trainer_id')
                    ->nullable()
                    ->after('trainer_name')
                    ->constrained()
                    ->onDelete('set null');
            }

            if (!Schema::hasColumn('certifications', 'country_id')) {
                $table->foreignId('country_id')
                    ->nullable()
                    ->after('nationality')
                    ->constrained()
                    ->onDelete('set null');
            }

            // Core columns
            $table->string('trainee_name', 255)->nullable(false)->change();
            $table->string('accredited_serial_number', 100)->nullable(false)->change();
            $table->string('document_code', 50)->nullable()->change();
            $table->string('accreditation_number', 100)->nullable()->change();
            $table->boolean('paper_received')->default(false)->nullable(false)->change();
            $table->string('document_type', 100)->nullable(false)->change();
            $table->date('accreditation_date')->nullable(false)->change();

            // Indexes
            $table->index(['trainee_name', 'accreditation_date'], 'idx_trainee_date');
            $table->index(['trainer_id', 'document_type'], 'idx_trainer_doc_type');
            $table->index(['country_id', 'accreditation_date'], 'idx_country_date');
            $table->index(['accreditation_date', 'certificate_type'], 'idx_date_cert_type');
            $table->index(['paper_received', 'accreditation_date'], 'idx_paper_date');
        });

        // --- Step 4: Add check constraints ---
        foreach (
            [
                'chk_trainee_name_length'    => 'CHAR_LENGTH(trainee_name) >= 2',
                'chk_accreditation_date_min' => 'accreditation_date >= "1900-01-01"',
                'chk_accreditation_date_max' => 'accreditation_date <= CURDATE()',
                'chk_serial_length'          => 'CHAR_LENGTH(accredited_serial_number) >= 3',
            ] as $name => $condition
        ) {
            try {
                DB::statement("ALTER TABLE certifications ADD CONSTRAINT {$name} CHECK ({$condition})");
            } catch (\Throwable $e) {
                // ignore if already exists
            }
        }
    }

    public function down(): void
    {
        foreach (
            [
                'chk_trainee_name_length',
                'chk_accreditation_date_min',
                'chk_accreditation_date_max',
                'chk_serial_length',
            ] as $constraint
        ) {
            try {
                DB::statement("ALTER TABLE certifications DROP CONSTRAINT {$constraint}");
            } catch (\Throwable $e) {
                // ignore
            }
        }

        Schema::table('certifications', function (Blueprint $table) {
            if (Schema::hasColumn('certifications', 'trainer_id')) {
                $table->dropForeign(['trainer_id']);
                $table->dropColumn('trainer_id');
            }

            if (Schema::hasColumn('certifications', 'country_id')) {
                $table->dropForeign(['country_id']);
                $table->dropColumn('country_id');
            }

            foreach (
                [
                    'idx_trainee_date',
                    'idx_trainer_doc_type',
                    'idx_country_date',
                    'idx_date_cert_type',
                    'idx_paper_date',
                ] as $index
            ) {
                try {
                    $table->dropIndex($index);
                } catch (\Throwable $e) {
                    // ignore
                }
            }

            $table->string('trainee_name')->nullable()->change();
            $table->string('accredited_serial_number')->nullable()->change();
            $table->string('paper_received')->nullable()->change(); // revert to string
            $table->string('document_type')->nullable()->change();
            $table->date('accreditation_date')->nullable()->change();
        });
    }
};
