<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrainerDocumentTypes\Pages;

use App\Filament\Admin\Resources\TrainerDocumentTypes\TrainerDocumentTypeResource;
use App\Filament\Admin\Resources\TrainerDocumentTypes\Tables\TrainerDocumentTypesTable;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;

class ListTrainerDocumentTypes extends ListRecords
{
    protected static string $resource = TrainerDocumentTypeResource::class;

    public function table(Table $table): Table
    {
        return TrainerDocumentTypesTable::configure($table);
    }
}
