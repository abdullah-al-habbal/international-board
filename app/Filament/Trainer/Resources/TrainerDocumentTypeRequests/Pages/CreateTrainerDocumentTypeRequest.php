<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerDocumentTypeRequests\Pages;

use App\Filament\Trainer\Resources\TrainerDocumentTypeRequests\TrainerDocumentTypeRequestResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateTrainerDocumentTypeRequest extends CreateRecord
{
    protected static string $resource = TrainerDocumentTypeRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['trainer_id'] = Auth::id();
        return $data;
    }
}
