<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerDocumentTypeRequests\Pages;

use App\Filament\Trainer\Resources\TrainerDocumentTypeRequests\TrainerDocumentTypeRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTrainerDocumentTypeRequests extends ListRecords
{
    protected static string $resource = TrainerDocumentTypeRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
