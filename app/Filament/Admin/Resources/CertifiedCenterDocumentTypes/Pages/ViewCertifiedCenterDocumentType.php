<?php

namespace App\Filament\Admin\Resources\CertifiedCenterDocumentTypes\Pages;

use App\Filament\Admin\Resources\CertifiedCenterDocumentTypes\CertifiedCenterDocumentTypeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCertifiedCenterDocumentType extends ViewRecord
{
    protected static string $resource = CertifiedCenterDocumentTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
