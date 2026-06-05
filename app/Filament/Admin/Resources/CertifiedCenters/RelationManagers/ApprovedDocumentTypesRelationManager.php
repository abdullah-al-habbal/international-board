<?php

namespace App\Filament\Admin\Resources\CertifiedCenters\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use App\Filament\Admin\Resources\CertifiedCenterDocumentTypes\CertifiedCenterDocumentTypeResource;

class ApprovedDocumentTypesRelationManager extends RelationManager
{
    protected static string $relationship = 'approvedDocumentTypes';

    protected static ?string $relatedResource = CertifiedCenterDocumentTypeResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
