<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerDocumentTypes\Pages;

use App\Filament\Trainer\Resources\TrainerDocumentTypes\TrainerDocumentTypeResource;
use Filament\Resources\Pages\EditRecord;

class EditTrainerDocumentType extends EditRecord
{
    protected static string $resource = TrainerDocumentTypeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
