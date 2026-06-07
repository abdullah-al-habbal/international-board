<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerDocumentTypeRequests\Pages;

use App\Filament\Trainer\Resources\TrainerDocumentTypeRequests\TrainerDocumentTypeRequestResource;
use Filament\Resources\Pages\EditRecord;

class EditTrainerDocumentTypeRequest extends EditRecord
{
    protected static string $resource = TrainerDocumentTypeRequestResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
