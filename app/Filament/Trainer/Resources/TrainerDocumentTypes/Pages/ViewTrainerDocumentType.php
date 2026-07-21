<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerDocumentTypes\Pages;

use App\Filament\Trainer\Resources\TrainerDocumentTypes\TrainerDocumentTypeResource;
use Filament\Resources\Pages\ViewRecord;

class ViewTrainerDocumentType extends ViewRecord
{
    protected static string $resource = TrainerDocumentTypeResource::class;
}
