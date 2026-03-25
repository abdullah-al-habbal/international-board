<?php
// filePath: app/Filament/Admin/Resources/CenterDocumentTypeRequests/Pages/ListCenterDocumentTypeRequests.php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CenterDocumentTypeRequests\Pages;

use App\Filament\Admin\Resources\CenterDocumentTypeRequests\CenterDocumentTypeRequestResource;
use App\Filament\Admin\Resources\CenterDocumentTypeRequests\Tables\CenterDocumentTypeRequestsTable;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;

class ListCenterDocumentTypeRequests extends ListRecords
{
    protected static string $resource = CenterDocumentTypeRequestResource::class;

    public function table(Table $table): Table
    {
        return CenterDocumentTypeRequestsTable::configure($table);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
