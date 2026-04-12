<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerAccreditationRequests\Pages;

use App\Filament\Trainer\Resources\TrainerAccreditationRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTrainerAccreditationRequest extends CreateRecord
{
    protected static string $resource = TrainerAccreditationRequestResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
