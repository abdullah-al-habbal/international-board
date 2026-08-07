<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrainerAgentPersons\Pages;

use App\Filament\Admin\Resources\TrainerAgentPersons\TrainerAgentPersonResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTrainerAgentPersons extends ListRecords
{
    protected static string $resource = TrainerAgentPersonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
