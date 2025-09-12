<?php

namespace App\Filament\Admin\Resources\CertifiedCenters\Pages;

use App\Filament\Admin\Resources\CertifiedCenters\CertifiedCenterResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCertifiedCenter extends ViewRecord
{
    protected static string $resource = CertifiedCenterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
