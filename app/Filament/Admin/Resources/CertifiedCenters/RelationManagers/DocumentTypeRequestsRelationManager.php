<?php

namespace App\Filament\Admin\Resources\CertifiedCenters\RelationManagers;

use App\Filament\Admin\Resources\CenterDocumentTypeRequests\CenterDocumentTypeRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class DocumentTypeRequestsRelationManager extends RelationManager
{
    protected static string $relationship = 'documentTypeRequests';

    protected static ?string $relatedResource = CenterDocumentTypeRequestResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
