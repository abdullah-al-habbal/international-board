<?php

namespace App\Filament\Admin\Resources\DocumentTypes\RelationManagers;

use App\Filament\Admin\Resources\CertifiedCenters\CertifiedCenterResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class ApprovedCentersRelationManager extends RelationManager
{
    protected static string $relationship = 'approvedCenters';

    protected static ?string $relatedResource = CertifiedCenterResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
