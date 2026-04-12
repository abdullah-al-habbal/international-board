<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrainerAccreditationRequests\Pages;

use App\Filament\Admin\Resources\TrainerAccreditationRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrainerAccreditationRequest extends EditRecord
{
    protected static string $resource = TrainerAccreditationRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
