<?php
// app/Filament/Admin/Resources/Certifications/Pages/ListCertifications.php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Certifications\Pages;

use App\Filament\Admin\Resources\Certifications\CertificationResource;
use App\Services\Certification\CertificationExportHandler;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
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

            CreateAction::make()
                ->label(__('app.create_certification')),
        ];
    }
}
