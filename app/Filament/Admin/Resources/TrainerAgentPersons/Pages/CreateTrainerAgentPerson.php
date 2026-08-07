<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrainerAgentPersons\Pages;

use App\Filament\Admin\Resources\TrainerAgentPersons\TrainerAgentPersonResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Resources\Pages\CreateRecord;

class CreateTrainerAgentPerson extends CreateRecord
{
    use RedirectsToShowPage;

    protected static string $resource = TrainerAgentPersonResource::class;
}
