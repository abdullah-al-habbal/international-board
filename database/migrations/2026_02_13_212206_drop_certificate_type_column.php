<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Log::warning('⚠ This migration will permanently drop the certificate_type column');
        Log::info('Verifying migration safety...');

        $unmigrated = DB::table('certifications')
            ->whereNotNull('certificate_type')
            ->where('certificate_type', '!=', '')
            ->whereNull('document_type_id')
            ->count();

        if ($unmigrated > 0) {
            throw new Exception(
                "Cannot drop certificate_type column: {$unmigrated} records still have certificate_type " .
                'but no document_type_id. Run data migration first.'
            );
        }

        if (!Schema::hasColumn('certifications', 'document_type_id')) {
            throw new Exception('document_type_id column does not exist. Cannot proceed with migration.');
        }

        Log::info('Safety checks passed. Dropping certificate_type column...');

        Schema::table('certifications', function (Blueprint $table) {
            $table->dropColumn('certificate_type');
        });

        $totalRecords = DB::table('certifications')->count();
        Log::info("✓ certificate_type column dropped successfully");
        Log::info("  Total certifications preserved: {$totalRecords}");
    }

    public function down(): void
    {
        Log::info('Restoring certificate_type column...');

        Schema::table('certifications', function (Blueprint $table) {
            $table->string('certificate_type', 50)
                ->nullable()
                ->after('certified_center_id')
                ->comment('Legacy enum column - use document_type_id instead');

            $table->index('certificate_type');
        });

        Log::info('✓ certificate_type column restored');
        Log::warn('Note: Column data is empty. Run rollback of data migration to restore values.');
    }
};
