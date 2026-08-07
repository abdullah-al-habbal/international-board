<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrainerAgentPersons\Pages;

use App\Filament\Admin\Resources\TrainerAgentPersons\TrainerAgentPersonResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTrainerAgentPerson extends EditRecord
{
    use RedirectsToShowPage;

    protected static string $resource = TrainerAgentPersonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
