<?php

namespace App\Filament\Admin\Resources\CertifiedCenters\Pages;

use App\Filament\Admin\Resources\CertifiedCenters\CertifiedCenterResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCertifiedCenters extends ListRecords
{
    protected static string $resource = CertifiedCenterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            \Filament\Actions\Action::make('export')
                ->label('Export Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    return \App\Exports\CertifiedCentersExport::make()->download('certified-centers.xlsx');
                }),
        ];
    }
}
