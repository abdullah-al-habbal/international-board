<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration {
    public function up(): void
    {
        Log::info('Starting certificate_type to document_type_id migration...');

        DB::transaction(function () {
            $basicId = DB::table('document_types')->where('key', 'certificate_basic')->value('id');
            $advancedId = DB::table('document_types')->where('key', 'certificate_advanced')->value('id');
            $professionalId = DB::table('document_types')->where('key', 'certificate_professional')->value('id');
            $specialistId = DB::table('document_types')->where('key', 'certificate_specialist')->value('id');

            if (!$basicId || !$advancedId || !$professionalId || !$specialistId) {
                throw new Exception('Default certificate document types not found. Run the previous migration first.');
            }

            $totalBefore = DB::table('certifications')->count();
            Log::info("Total certifications before migration: {$totalBefore}");


            $updated = 0;

            $count = DB::table('certifications')
                ->whereRaw('LOWER(certificate_type) = ?', ['basic'])
                ->whereNull('document_type_id')
                ->update(['document_type_id' => $basicId]);
            $updated += $count;
            Log::info("Mapped {$count} 'basic' certificates");

            $count = DB::table('certifications')
                ->whereRaw('LOWER(certificate_type) = ?', ['advanced'])
                ->whereNull('document_type_id')
                ->update(['document_type_id' => $advancedId]);
            $updated += $count;
            Log::info("Mapped {$count} 'advanced' certificates");

            $count = DB::table('certifications')
                ->whereRaw('LOWER(certificate_type) = ?', ['professional'])
                ->whereNull('document_type_id')
                ->update(['document_type_id' => $professionalId]);
            $updated += $count;
            Log::info("Mapped {$count} 'professional' certificates");

            $count = DB::table('certifications')
                ->whereRaw('LOWER(certificate_type) = ?', ['specialist'])
                ->whereNull('document_type_id')
                ->update(['document_type_id' => $specialistId]);
            $updated += $count;
            Log::info("Mapped {$count} 'specialist' certificates");

            $totalAfter = DB::table('certifications')->count();

            if ($totalBefore !== $totalAfter) {
                throw new Exception("Data loss detected! Before: {$totalBefore}, After: {$totalAfter}");
            }

            $withDocType = DB::table('certifications')->whereNotNull('document_type_id')->count();
            $withoutDocType = DB::table('certifications')->whereNull('document_type_id')->count();

            Log::info("\nMigration Statistics:");
            Log::info("- Total records: {$totalAfter}");
            Log::info("- Records updated: {$updated}");
            Log::info("- With document_type_id: {$withDocType}");
            Log::info("- Without document_type_id: {$withoutDocType}");

            if ($withoutDocType > 0) {
                Log::warning("\nWarning: {$withoutDocType} records still have NULL document_type_id");
                Log::warning('These records had NULL certificate_type and need manual review');
                Log::warning("Certificate type migration: {$withoutDocType} records without document_type_id", [
                    'total' => $totalAfter,
                    'updated' => $updated,
                ]);
            }

            Log::info("\n✓ Migration completed successfully - all records preserved");
        });
    }

    public function down(): void
    {
        Log::info('Rolling back certificate_type migration...');

        DB::transaction(function () {
            $basicId = DB::table('document_types')->where('key', 'certificate_basic')->value('id');
            $advancedId = DB::table('document_types')->where('key', 'certificate_advanced')->value('id');
            $professionalId = DB::table('document_types')->where('key', 'certificate_professional')->value('id');
            $specialistId = DB::table('document_types')->where('key', 'certificate_specialist')->value('id');

            if ($basicId) {
                DB::table('certifications')
                    ->where('document_type_id', $basicId)
                    ->whereNull('certificate_type')
                    ->update(['certificate_type' => 'basic']);
            }

            if ($advancedId) {
                DB::table('certifications')
                    ->where('document_type_id', $advancedId)
                    ->whereNull('certificate_type')
                    ->update(['certificate_type' => 'advanced']);
            }

            if ($professionalId) {
                DB::table('certifications')
                    ->where('document_type_id', $professionalId)
                    ->whereNull('certificate_type')
                    ->update(['certificate_type' => 'professional']);
            }

            if ($specialistId) {
                DB::table('certifications')
                    ->where('document_type_id', $specialistId)
                    ->whereNull('certificate_type')
                    ->update(['certificate_type' => 'specialist']);
            }

            DB::table('certifications')
                ->whereIn('document_type_id', array_filter([
                    $basicId,
                    $advancedId,
                    $professionalId,
                    $specialistId,
                ]))
                ->update(['document_type_id' => null]);

            Log::info('✓ Rollback completed');
        });
    }
};
