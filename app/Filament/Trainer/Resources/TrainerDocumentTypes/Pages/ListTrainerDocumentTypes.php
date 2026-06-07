<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerDocumentTypes\Pages;

use App\Filament\Trainer\Resources\TrainerDocumentTypes\TrainerDocumentTypeResource;
use Filament\Resources\Pages\ListRecords;

class ListTrainerDocumentTypes extends ListRecords
{
    protected static string $resource = TrainerDocumentTypeResource::class;
}
