<?php

namespace App\Filament\Admin\Resources\CertifiedCenters\Pages;

use App\Exports\CertifiedCentersExport;
use App\Filament\Admin\Resources\CertifiedCenters\CertifiedCenterResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListCertifiedCenters extends ListRecords
{
    protected static string $resource = CertifiedCenterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->exportAction(),
        ];
    }

    private function exportAction(): Action
    {
        return Action::make('export')
            ->label(__('app.export_excel'))
            ->icon('heroicon-o-arrow-down-tray')
            ->action(function () {
                try {
                    return Excel::download(new CertifiedCentersExport, 'certified-centers.xlsx');
                } catch (\Exception $e) {
                    Notification::make()
                        ->title(__('app.export_failed'))
                        ->body(__('app.export_error_message', ['error' => $e->getMessage()]))
                        ->danger()
                        ->send();
                }
            });
    }
}
