<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerAccreditationRequests\Pages;

use App\Filament\Trainer\Resources\TrainerAccreditationRequests\TrainerAccreditationRequestResource;
use Filament\Resources\Pages\EditRecord;

class EditTrainerAccreditationRequest extends EditRecord
{
    protected static string $resource = TrainerAccreditationRequestResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
