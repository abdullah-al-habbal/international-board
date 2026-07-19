<?php

namespace App\Filament\Admin\Resources\CertifiedCenters\RelationManagers;

use App\Filament\Admin\Resources\CertifiedCenterDocumentTypes\CertifiedCenterDocumentTypeResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class ApprovedDocumentTypesRelationManager extends RelationManager
{
    protected static string $relationship = 'approvedDocumentTypes';

    protected static ?string $relatedResource = CertifiedCenterDocumentTypeResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([]);
    }
}
