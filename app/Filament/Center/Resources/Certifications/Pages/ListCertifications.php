<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\Certifications\Pages;

use App\Filament\Center\Resources\Certifications\CertificationResource;
use App\Imports\CertificationsImportHandler;
use App\Models\Certification;
use App\Services\Csv\CsvExportHandler;
use App\Services\Csv\CsvImportHandler;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

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
                    $query = Certification::with([
                        'certifiedCenter',
                        'documentType',
                        'trainer',
                        'country',
                        'trainee',
                    ])->where('certified_center_id', Auth::id())
                      ->orderByDesc('created_at');

                    $headers = [
                        'ID',
                        'Serial Number',
                        'Trainee Name',
                        'Center',
                        'Document Code',
                        'Document Type',
                        'Accreditation Number',
                        'Accreditation Date',
                        'Trainer Name',
                        'Nationality',
                        'Paper Received',
                        'Country',
                        'Notes',
                        'Created At',
                    ];

                    $formatter = fn (Certification $certification): array => [
                        $certification->id,
                        $certification->accredited_serial_number,
                        $certification->trainee?->name,
                        $certification->certifiedCenter?->name,
                        $certification->document_code,
                        $certification->documentType?->name,
                        $certification->accreditation_number,
                        $certification->accreditation_date?->format('Y-m-d'),
                        $certification->trainer?->name,
                        $certification->nationality,
                        $certification->paper_received ? 'YES' : 'NO',
                        $certification->country?->name,
                        $certification->notes,
                        $certification->created_at?->format('Y-m-d H:i:s'),
                    ];

                    return app(CsvExportHandler::class)->export($query, $headers, $formatter, 'certifications.csv');
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

                    $stats = app(CsvImportHandler::class)->import($data['file']->getPathname(), [$importHandler, 'processRow'], [
                        'has_header' => true,
                        'batch_inserter' => [$importHandler, 'batchInsert'],
                        'batch_size' => 500,
                        'transaction' => true,
                    ]);

                    Notification::make()
                        ->title("Imported {$stats['success']} of {$stats['total']} rows")
                        ->body("Failed: {$stats['failed']}")
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
