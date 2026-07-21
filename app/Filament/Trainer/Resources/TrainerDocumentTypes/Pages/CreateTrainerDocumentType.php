<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerDocumentTypes\Pages;

use App\Filament\Trainer\Resources\TrainerDocumentTypes\TrainerDocumentTypeResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateTrainerDocumentType extends CreateRecord
{
    use RedirectsToShowPage;

    protected static string $resource = TrainerDocumentTypeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['trainer_id'] = Auth::id();

        return $data;
    }
}
