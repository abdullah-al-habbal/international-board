<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Certifications\Pages;

use App\Filament\Admin\Resources\Certifications\CertificationResource;
use App\Imports\CertificationsImportHandler;
use App\Services\Certification\CertificationExportHandler;
use App\Services\Csv\CsvImportHandler;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

final class ListCertifications extends ListRecords
{
    protected static string $resource = CertificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_csv')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    return app(CertificationExportHandler::class)
                        ->exportForAdmin();
                }),

            Action::make('import_csv')
                ->label('Import CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    FileUpload::make('file')
                        ->label('CSV File')
                        ->acceptedFileTypes(['.csv'])
                        ->required(),
                ])
                ->action(function (array $data) {
                    $importHandler = new CertificationsImportHandler();

                    $stats = app(CsvImportHandler::class)->import(
                        $data['file']->getPathname(),
                        [$importHandler, 'processRow'],
                        [
                            'has_header' => true,
                            'batch_inserter' => [$importHandler, 'batchInsert'],
                            'batch_size' => 500,
                            'transaction' => true,
                        ]
                    );

                    Notification::make()
                        ->title("Imported {$stats['success']} of {$stats['total']} rows")
                        ->body("Failed: {$stats['failed']}")
                        ->success()
                        ->send();
                }),

            CreateAction::make()
                ->label(__('app.create_certification')),
        ];
    }
}
