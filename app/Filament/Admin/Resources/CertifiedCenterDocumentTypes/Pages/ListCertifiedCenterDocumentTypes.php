<?php
// filePath: app/Filament/Admin/Resources/CertifiedCenterDocumentTypes/Pages/ListCertifiedCenterDocumentTypes.php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CertifiedCenterDocumentTypes\Pages;

use App\Filament\Admin\Resources\CertifiedCenterDocumentTypes\CertifiedCenterDocumentTypeResource;
use App\Filament\Admin\Resources\CertifiedCenterDocumentTypes\Tables\CertifiedCenterDocumentTypesTable;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;

class ListCertifiedCenterDocumentTypes extends ListRecords
{
    protected static string $resource = CertifiedCenterDocumentTypeResource::class;

    public function table(Table $table): Table
    {
        return CertifiedCenterDocumentTypesTable::configure($table);
    }
}
