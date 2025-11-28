<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages;

use App\Imports\CertificationsImport;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use UnitEnum;

class CertificationImport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-up-tray';

    protected static ?string $title = 'Import Certifications';

    protected static ?string $navigationLabel = 'Import Certifications';

    protected static string|UnitEnum|null $navigationGroup = 'Certifications';

    protected string $view = 'filament.admin.pages.certification-import';

    protected static ?int $navigationSort = 30;

    protected function getHeaderActions(): array
    {
        return [
            $this->importAction(),
        ];
    }

    private function importAction(): Action
    {
        return Action::make('import')
            ->label('Import Excel File')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('primary')
            ->size('lg')
            ->form([
                FileUpload::make('file')
                    ->required()
                    ->acceptedFileTypes([
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-excel',
                        'text/csv',
                    ])
                    ->label('Excel/CSV File')
                    ->helperText('Supports .xlsx, .xls, and .csv files with Arabic headers')
                    ->maxSize(51200)
                    ->directory('imports/certifications')
                    ->preserveFilenames(),
            ])
            ->action(function (array $data): void {
                $this->performImport($data);
            })
            ->modalWidth('lg')
            ->modalAlignment(Alignment::Center);
    }

    private function performImport(array $data): void
    {
        try {
            $startTime = microtime(true);

            // Use database transaction for import
            DB::beginTransaction();

            $import = new CertificationsImport;
            Excel::import($import, $data['file']);

            $stats = $import->getSummaryReport();
            $endTime = microtime(true);
            $executionTime = round($endTime - $startTime, 2);

            // Commit transaction if successful
            DB::commit();

            // Create detailed success notification
            $message = sprintf(
                "Import completed successfully!\n\n".
                    "📊 Statistics:\n".
                    "• Total rows processed: %d\n".
                    "• Successfully imported: %d\n".
                    "• Skipped rows: %d\n".
                    "• Failed imports: %d\n".
                    "• Countries created: %d\n".
                    "• Trainers created: %d\n".
                    "• Success rate: %.1f%%\n".
                    '• Execution time: %.2fs',
                $stats['total_rows'],
                $stats['successful_imports'],
                $stats['skipped_rows'],
                $stats['failed_imports'],
                $stats['countries_created'],
                $stats['trainers_created'],
                $stats['success_rate'],
                $executionTime
            );

            Notification::make()
                ->title('Import Completed Successfully')
                ->body($message)
                ->success()
                ->duration(12000)
                ->send();

            // Log successful import
            Log::info('Certification import completed', [
                'file' => $data['file'],
                'stats' => $stats,
                'execution_time' => $executionTime,
            ]);

            $this->redirect('/admin/certifications');
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();

            $errorMessage = 'Import failed: '.$e->getMessage();

            // Log detailed error
            Log::error('Certification import failed', [
                'file' => $data['file'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            Notification::make()
                ->title('Import Failed')
                ->body($errorMessage)
                ->danger()
                ->duration(15000)
                ->send();
        }
    }
}
