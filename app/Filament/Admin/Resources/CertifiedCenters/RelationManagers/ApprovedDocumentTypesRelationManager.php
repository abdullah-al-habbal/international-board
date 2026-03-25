<?php

namespace App\Filament\Admin\Resources\CertifiedCenters\RelationManagers;

use App\Filament\Admin\Resources\CertifiedCenters\CertifiedCenterResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class ApprovedDocumentTypesRelationManager extends RelationManager
{
    protected static string $relationship = 'approvedDocumentTypes';

    protected static ?string $relatedResource = CertifiedCenterResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
