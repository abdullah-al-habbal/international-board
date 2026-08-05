<?php

// app/Filament/Admin/Resources/Certifications/Pages/ListCertifications.php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Certifications\Pages;

use App\Filament\Admin\Resources\Certifications\CertificationResource;
use App\Jobs\ImportCertificationsJob;
use App\Services\Certification\CertificationExportHandler;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;

final class ListCertifications extends ListRecords
{
    protected static string $resource = CertificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('import')
                ->label(__('app.import.actions.import_csv'))
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    FileUpload::make('csv_file')
                        ->label(__('app.import.fields.csv_file'))
                        ->disk('local')
                        ->directory('imports')
                        ->acceptedFileTypes(['text/csv'])
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $filePath = Storage::disk('local')->path($data['csv_file']);

                    ImportCertificationsJob::dispatch($filePath, auth()->id());

                    Notification::make()
                        ->title(__('app.import.notifications.started_title'))
                        ->body(__('app.import.notifications.started_body'))
                        ->info()
                        ->send();
                }),

            Action::make('export_csv')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    return app(CertificationExportHandler::class)
                        ->exportForAdmin();
                }),

            CreateAction::make()
                ->label(__('app.create_certification')),
        ];
    }
}
