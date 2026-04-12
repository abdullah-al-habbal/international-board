<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerDocumentTypeRequests\Pages;

use App\Filament\Trainer\Resources\TrainerDocumentTypeRequestResource;
use Filament\Resources\Pages\ListRecords;

class ListTrainerDocumentTypeRequests extends ListRecords
{
    protected static string $resource = TrainerDocumentTypeRequestResource::class;
}
