<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Certification;
use App\Models\DocumentType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MigrateDocumentTypes extends Command
{
    protected $signature = 'document-types:migrate {--dry-run : Show what would be changed without making changes}';
    protected $description = 'Migrate existing certifications.document_type to document_types table';

    private bool $dryRun = false;
    private int $processedCount = 0;
    private int $linkedCount = 0;

    public function handle(): int
    {
        $this->dryRun = $this->option('dry-run');

        $this->info('🚀 Starting Document Types Migration...');
        if ($this->dryRun) {
            $this->warn('🔍 DRY RUN MODE - No actual changes will be made');
        }
        $this->newLine();

        if (!$this->documentTypesTableExists()) {
            return $this->failWithMigrationHint();
        }

        DB::beginTransaction();

        try {
            $this->migrateDocumentTypes();

            if ($this->dryRun) {
                DB::rollBack();
                $this->info('🔄 Transaction rolled back (dry run)');
            } else {
                DB::commit();
                $this->info('✅ Transaction committed');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error during migration: ' . $e->getMessage());
            Log::error('Document types migration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }

        $this->displayStats();
        return Command::SUCCESS;
    }

    private function documentTypesTableExists(): bool
    {
        try {
            return DB::getSchemaBuilder()->hasTable('document_types');
        } catch (\Exception $e) {
            return false;
        }
    }

    private function failWithMigrationHint(): int
    {
        $this->error('❌ Document types table does not exist.');
        $this->info('💡 Please run migrations first: php artisan migrate');
        return Command::FAILURE;
    }

    private function migrateDocumentTypes(): void
    {
        $this->info('🔗 Linking Certifications to Document Types...');

        $certifications = Certification::whereNotNull('document_type')
            ->where('document_type', '!=', '')
            ->get();

        foreach ($certifications as $certification) {
            $this->processedCount++;

            if ($this->linkCertificationToDocumentType($certification)) {
                $this->linkedCount++;
            }

            if ($this->processedCount % 100 === 0) {
                $this->line("  • Processed {$this->processedCount} certifications...");
            }
        }

        $this->info("✅ Processed {$this->processedCount} certifications, linked {$this->linkedCount}");
        $this->newLine();
    }

    private function linkCertificationToDocumentType(Certification $certification): bool
    {
        $documentTypeString = trim($certification->document_type);
        $key = Str::snake(Str::lower($documentTypeString));

        $documentType = DocumentType::where('key', $key)->first();

        if (!$documentType) {
            $this->warn("  • Document type not found for: '{$documentTypeString}' (key: {$key})");
            return false;
        }

        if ($certification->document_type_id === $documentType->id) {
            return false; // Already linked
        }

        if (!$this->dryRun) {
            $certification->update(['document_type_id' => $documentType->id]);
        }

        $this->line("  • Linked '{$documentTypeString}' → {$documentType->name['en']} (ID: {$documentType->id})");
        return true;
    }

    private function displayStats(): void
    {
        $this->newLine();
        $this->info('📊 MIGRATION STATISTICS');
        $this->line('=======================');

        $this->table(['Metric', 'Count'], [
            ['Certifications Processed', $this->processedCount],
            ['Successfully Linked', $this->linkedCount],
            ['Already Linked', $this->processedCount - $this->linkedCount],
        ]);

        if ($this->dryRun) {
            $this->warn('⚠️  This was a dry run - no actual changes were made');
            $this->info('💡 Run without --dry-run to apply changes');
        } else {
            $this->info('✅ Document types migration completed successfully!');
        }
    }
}
