<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerAccreditationRequests\Pages;

use App\Filament\Trainer\Resources\TrainerAccreditationRequests\TrainerAccreditationRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTrainerAccreditationRequests extends ListRecords
{
    protected static string $resource = TrainerAccreditationRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn () => TrainerAccreditationRequestResource::canCreate()),
        ];
    }
}
