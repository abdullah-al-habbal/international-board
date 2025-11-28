<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\Certifications\Pages;

use App\Exports\CertificationsExport;
use App\Filament\Center\Resources\Certifications\CertificationResource;
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

    private function importAction(): Action
    {
        return Action::make('import')
            ->label('Import Excel')
            ->icon('heroicon-o-arrow-up-tray')
            ->form([
                FileUpload::make('file')
                    ->required()
                    ->acceptedFileTypes([
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-excel',
                    ])
                    ->label('Excel File'),
            ])
            ->action(function (array $data): void {
                try {
                    Excel::import(new CertificationsImport, $data['file']);

                    Notification::make()
                        ->title('Import completed successfully')
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Import failed')
                        ->body('An error occurred during import: '.$e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }

    private function exportAction(): Action
    {
        return Action::make('export')
            ->label('Export Excel')
            ->icon('heroicon-o-arrow-down-tray')
            ->action(function () {
                try {
                    return Excel::download(new CertificationsExport, 'my-certifications.xlsx');
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Export failed')
                        ->body('An error occurred during export: '.$e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }
}
