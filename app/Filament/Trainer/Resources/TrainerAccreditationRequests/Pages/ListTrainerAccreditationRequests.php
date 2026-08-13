<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerAccreditationRequests\Pages;

use App\Filament\Trainer\Resources\TrainerAccreditationRequests\TrainerAccreditationRequestResource;
use App\Models\Trainer;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTrainerAccreditationRequests extends ListRecords
{
    protected static string $resource = TrainerAccreditationRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->disabled(! TrainerAccreditationRequestResource::canCreate())
                ->tooltip($this->getCreateDisabledTooltip()),
        ];
    }

    private function getCreateDisabledTooltip(): ?string
    {
        if (TrainerAccreditationRequestResource::canCreate()) {
            return null;
        }

        /** @var Trainer|null $trainer */
        $trainer = auth('trainer')->user();

        return $trainer instanceof Trainer
            ? $trainer->accreditationBlockMessage()
            : null;
    }
}
