<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\Certifications\Pages;

use App\Filament\Center\Resources\Certifications\CertificationResource;
use App\Models\Certification;
use App\Models\CertifiedCenter;
use App\Services\Csv\CsvExportHandler;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
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
                        'creator',
                        'assignedTrainer',
                        'country',
                        'trainee',
                    ])->where('creator_type', CertifiedCenter::class)
                      ->where('creator_id', Auth::id())
                      ->orderByDesc('created_at');

                    $headers = [
                        'ID',
                        'Serial Number',
                        'Trainee Name',
                        'Issued By',
                        'Assigned Trainer',
                        'Document Code',
                        'Accreditation Number',
                        'Accreditation Date',
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
                        $certification->creator?->name,
                        $certification->assignedTrainer?->name,
                        $certification->document_code,
                        $certification->accreditation_number,
                        $certification->accreditation_date?->format('Y-m-d'),
                        $certification->country?->nationality,
                        $certification->paper_received ? 'YES' : 'NO',
                        $certification->country?->name,
                        $certification->notes,
                        $certification->created_at?->format('Y-m-d H:i:s'),
                    ];

                    return app(CsvExportHandler::class)->export($query, $headers, $formatter, 'certifications.csv');
                }),
            CreateAction::make(),
        ];
    }
}
