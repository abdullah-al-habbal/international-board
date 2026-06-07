<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrainerDocumentTypeRequests\Pages;

use App\Filament\Admin\Resources\TrainerDocumentTypeRequests\TrainerDocumentTypeRequestResource;
use Filament\Resources\Pages\ListRecords;

class ListTrainerDocumentTypeRequests extends ListRecords
{
    protected static string $resource = TrainerDocumentTypeRequestResource::class;
}
