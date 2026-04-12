<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerDocumentTypeRequests\Pages;

use App\Filament\Trainer\Resources\TrainerDocumentTypeRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTrainerDocumentTypeRequest extends CreateRecord
{
    protected static string $resource = TrainerDocumentTypeRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['trainer_id'] = auth()->id();
        return $data;
    }
}
