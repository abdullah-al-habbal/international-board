<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrainerAgentPersons\Pages;

use App\Filament\Admin\Resources\TrainerAgentPersons\TrainerAgentPersonResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTrainerAgentPerson extends ViewRecord
{
    protected static string $resource = TrainerAgentPersonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
