<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerAccreditationRequests\Pages;

use App\Filament\Trainer\Resources\TrainerAccreditationRequests\TrainerAccreditationRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTrainerAccreditationRequests extends ListRecords
{
    protected static string $resource = TrainerAccreditationRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
