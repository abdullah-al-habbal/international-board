<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certifications', function (Blueprint $table) {
            // Add foreign key columns
            $table->foreignId('trainer_id')->nullable()->after('trainer_name')->constrained()->onDelete('set null');
            $table->foreignId('country_id')->nullable()->after('nationality')->constrained()->onDelete('set null');

            // Add constraints and improve existing columns
            $table->string('trainee_name', 255)->nullable(false)->change();
            $table->string('accredited_serial_number', 100)->unique()->nullable(false)->change();
            $table->string('document_code', 50)->nullable()->change();
            $table->string('accreditation_number', 100)->nullable()->change();
            $table->enum('paper_received', ['YES', 'NO', 'PENDING'])->nullable()->change();
            $table->string('document_type', 100)->nullable(false)->change();
            $table->date('accreditation_date')->nullable(false)->change();

            // Add new indexes for better performance
            $table->index(['trainee_name', 'accreditation_date'], 'idx_trainee_date');
            $table->index(['trainer_id', 'document_type'], 'idx_trainer_doc_type');
            $table->index(['country_id', 'accreditation_date'], 'idx_country_date');
            $table->index(['accreditation_date', 'certificate_type'], 'idx_date_cert_type');
            $table->index(['paper_received', 'accreditation_date'], 'idx_paper_date');

            // Add check constraints for MySQL
            $table->check('LENGTH(trainee_name) >= 2', 'chk_trainee_name_length');
            $table->check('accreditation_date >= "1900-01-01"', 'chk_accreditation_date_min');
            $table->check('accreditation_date <= CURDATE()', 'chk_accreditation_date_max');
            $table->check('LENGTH(accredited_serial_number) >= 3', 'chk_serial_length');
        });
    }

    public function down(): void
    {
        Schema::table('certifications', function (Blueprint $table) {
            // Remove foreign key constraints
            $table->dropForeign(['trainer_id']);
            $table->dropForeign(['country_id']);

            // Remove columns
            $table->dropColumn(['trainer_id', 'country_id']);

            // Remove indexes
            $table->dropIndex('idx_trainee_date');
            $table->dropIndex('idx_trainer_doc_type');
            $table->dropIndex('idx_country_date');
            $table->dropIndex('idx_date_cert_type');
            $table->dropIndex('idx_paper_date');

            // Revert column changes
            $table->string('trainee_name')->nullable()->change();
            $table->string('accredited_serial_number')->nullable()->change();
            $table->string('paper_received')->nullable()->change();
            $table->string('document_type')->nullable()->change();
            $table->date('accreditation_date')->nullable()->change();
        });
    }
};
