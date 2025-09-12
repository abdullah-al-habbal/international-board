<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Certifications\Pages;

use App\Exports\CertificationsExport;
use App\Filament\Admin\Resources\Certifications\CertificationResource;
use App\Imports\CertificationsImport;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

final class ListCertifications extends ListRecords
{
    protected static string $resource = CertificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->importAction(),
            $this->exportAction(),
        ];
    }

    private function exportAction(): Action
    {
        return Action::make('export')
            ->label('Export Excel')
            ->icon('heroicon-o-arrow-down-tray')
            ->action(function () {
                try {
                    return Excel::download(new CertificationsExport(), 'admin-certifications.xlsx');
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Export failed')
                        ->body('An error occurred during export: ' . $e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }

    private function importAction(): Action
    {
        return Action::make('import')
            ->label('Quick Import')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('warning')
            ->form([
                FileUpload::make('file')
                    ->required()
                    ->acceptedFileTypes([
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-excel',
                        'text/csv',
                    ])
                    ->label('Excel/CSV File')
                    ->helperText('Quick import with minimal options. For advanced import features, use the dedicated Import page.')
                    ->maxSize(10240)
                    ->directory('imports/certifications'),
            ])
            ->action(function (array $data): void {
                try {
                    $startTime = microtime(true);

                    $import = new CertificationsImport();
                    Excel::import($import, $data['file']);

                    $stats = $import->getSummaryReport();
                    $endTime = microtime(true);
                    $executionTime = round($endTime - $startTime, 2);

                    Notification::make()
                        ->title('Import Completed')
                        ->body(sprintf(
                            "Imported %d out of %d rows (%.1f%%) in %.2fs",
                            $stats['successful_imports'],
                            $stats['total_rows'],
                            $stats['success_rate'],
                            $executionTime
                        ))
                        ->success()
                        ->duration(8000)
                        ->send();

                    $this->redirect(request()->header('Referer'));
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Import Failed')
                        ->body('Error: ' . $e->getMessage())
                        ->danger()
                        ->duration(10000)
                        ->send();
                }
            })
            ->tooltip('Quick import from this page. For advanced options, use the dedicated Import page.')
            ->modalWidth('lg');
    }
}
